<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category\CollectNestedCategoryIds;
use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category\MapSubcategoryTree;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Bucket\TermsResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctSubcategoryNavigationCmsElementResolver extends AbstractCmsElementResolver
{
    private const RESULT_KEY = 'ict_subcategories_';

    /**
     * @param EntityRepository<ProductCollection> $productRepository
     */
    public function __construct(
        private readonly EntityRepository $productRepository,
        private readonly CollectNestedCategoryIds $categoryIdsCollector,
        private readonly MapSubcategoryTree $subcategoryTreeMapper,
    ) {
    }

    public function getType(): string
    {
        return 'ict-subcategory-navigation';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $categoryCriteria = $this->buildCategoryCriteria($slot, $resolverContext);

        if ($categoryCriteria === null) {
            return null;
        }

        $collection = new CriteriaCollection();
        $collection->add(
            self::RESULT_KEY . $slot->getUniqueIdentifier(),
            CategoryDefinition::class,
            $categoryCriteria,
        );

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $depth = $this->getDepth($slot);
        $productCounts = $this->loadProductCounts($slot, $resolverContext, $result->get(self::RESULT_KEY . $slot->getUniqueIdentifier()));

        $slot->setData(new ArrayStruct([
            'subcategories' => $this->subcategoryTreeMapper->map(
                $result->get(self::RESULT_KEY . $slot->getUniqueIdentifier()),
                $depth,
                $productCounts,
            ),
        ]));
    }

    private function buildCategoryCriteria(CmsSlotEntity $slot, ResolverContext $resolverContext): ?Criteria
    {
        $entryPoint = $this->getEntryPoint($slot);

        if ($entryPoint === 'manual') {
            $ids = $this->getManualCategoryIds($slot);

            if ($ids === []) {
                return null;
            }

            return $this->buildManualCriteria($ids, $slot);
        }

        $parentId = $this->resolveParentIdForTree($slot, $resolverContext);

        if ($parentId === null) {
            return null;
        }

        return $this->buildTreeCriteria($parentId, $slot);
    }

    private function resolveParentIdForTree(CmsSlotEntity $slot, ResolverContext $resolverContext): ?string
    {
        $entryPoint = $this->getEntryPoint($slot);

        if ($entryPoint === 'custom') {
            $entryCategoryId = $this->getEntryCategoryId($slot);
            return $entryCategoryId !== '' ? $entryCategoryId : null;
        }

        return $this->resolveCurrentCategoryId($resolverContext);
    }

    private function resolveCurrentCategoryId(ResolverContext $resolverContext): ?string
    {
        if (! $resolverContext instanceof EntityResolverContext) {
            return null;
        }

        $entity = $resolverContext->getEntity();

        return $entity instanceof CategoryEntity ? $entity->getId() : null;
    }

    private function shouldShowProductCount(CmsSlotEntity $slot): bool
    {
        $show = $slot->getFieldConfig()->get('showProductCount');

        return $show !== null && (bool) $show->getValue();
    }

    private function getEntryPoint(CmsSlotEntity $slot): string
    {
        $entryPoint = $slot->getFieldConfig()->get('entryPoint');
        $raw = $entryPoint !== null ? $entryPoint->getValue() : null;
        $value = is_scalar($raw) ? (string) $raw : '';

        return $value !== '' ? $value : 'current';
    }

    private function getEntryCategoryId(CmsSlotEntity $slot): string
    {
        $entryCategory = $slot->getFieldConfig()->get('entryCategory');
        $raw = $entryCategory !== null ? $entryCategory->getValue() : null;

        return is_scalar($raw) ? (string) $raw : '';
    }

    /**
     * @return array<int, string>
     */
    private function getManualCategoryIds(CmsSlotEntity $slot): array
    {
        $manualCategories = $slot->getFieldConfig()->get('manualCategories');
        $raw = $manualCategories !== null ? $manualCategories->getValue() : null;

        if (! is_array($raw)) {
            return [];
        }

        return $this->filterStringIds($raw);
    }

    /**
     * @param array<mixed> $raw
     *
     * @return array<int, string>
     */
    private function filterStringIds(array $raw): array
    {
        $ids = [];

        foreach ($raw as $id) {
            if (is_string($id) && $id !== '') {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function getDepth(CmsSlotEntity $slot): int
    {
        $levels = $slot->getFieldConfig()->get('categoryLevels');

        return $levels !== null ? max(1, (int) $levels->getValue()) : 1;
    }

    private function buildTreeCriteria(string $parentId, CmsSlotEntity $slot): Criteria
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentId', $parentId));
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addAssociation('media');
        $criteria->addSorting(new FieldSorting('autoIncrement', FieldSorting::ASCENDING));

        $depth = $this->getDepth($slot);

        // Ensure nested children are loaded up to the configured depth.
        // Root result items represent level 1, so we need (depth - 1) "children" hops.
        for ($level = 2; $level <= $depth; $level++) {
            $association = str_repeat('children.', $level - 2) . 'children';
            $criteria->addAssociation($association);
            $criteria->addAssociation($association . '.media');
        }

        return $criteria;
    }

    /**
     * @param array<int, string> $ids
     */
    private function buildManualCriteria(array $ids, CmsSlotEntity $slot): Criteria
    {
        $criteria = new Criteria($ids);
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addAssociation('media');
        $criteria->addSorting(new FieldSorting('autoIncrement', FieldSorting::ASCENDING));

        $depth = $this->getDepth($slot);

        for ($level = 2; $level <= $depth; $level++) {
            $association = str_repeat('children.', $level - 2) . 'children';
            $criteria->addAssociation($association);
            $criteria->addAssociation($association . '.media');
        }

        return $criteria;
    }

    /**
     * @param EntitySearchResult<ProductCollection>|null $result
     *
     * @return array<string, int>
     */
    private function extractProductCounts(?EntitySearchResult $result): array
    {
        if ($result === null) {
            return [];
        }

        $aggregation = $result->getAggregations()->get('counts');

        if (! $aggregation instanceof TermsResult) {
            return [];
        }

        $counts = [];

        foreach ($aggregation->getBuckets() as $bucket) {
            $counts[(string) $bucket->getKey()] = $bucket->getCount();
        }

        return $counts;
    }

    /**
     * @param EntitySearchResult<\Shopware\Core\Framework\DataAbstractionLayer\EntityCollection>|null $categoryResult
     *
     * @return array<string, int>
     */
    private function loadProductCounts(CmsSlotEntity $slot, ResolverContext $resolverContext, ?EntitySearchResult $categoryResult): array
    {
        if (! $this->shouldShowProductCount($slot) || $this->getEntryPoint($slot) === 'manual') {
            return [];
        }

        if ($categoryResult === null) {
            return [];
        }

        $ids = $this->categoryIdsCollector->collect($categoryResult->getElements());

        if ($ids === []) {
            return [];
        }

        $criteria = new Criteria();
        $criteria->setLimit(0);
        $criteria->addAggregation(new TermsAggregation('counts', 'categoriesRo.id'));
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsAnyFilter('categoriesRo.id', array_values($ids)));

        $result = $this->productRepository->search($criteria, $resolverContext->getSalesChannelContext()->getContext());

        return $this->extractProductCounts($result);
    }
}
