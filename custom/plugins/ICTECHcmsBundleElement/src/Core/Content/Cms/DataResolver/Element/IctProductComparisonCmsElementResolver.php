<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\ProductStream\Service\ProductStreamBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctProductComparisonCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(
        private readonly ProductStreamBuilderInterface $productStreamBuilder,
    ) {
    }

    public function getType(): string
    {
        return 'ict-product-comparison';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $assignmentType = $this->extractStringValue($slot, 'productAssignmentType', 'manual');
        $criteria = $assignmentType === 'stream'
            ? $this->buildStreamCriteria($slot, $resolverContext)
            : $this->buildManualCriteria($slot);

        if ($criteria === null) {
            return null;
        }

        $criteria->addAssociation('cover.media');
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('properties.group');

        $collection = new CriteriaCollection();
        $collection->add(
            'products_' . $slot->getUniqueIdentifier(),
            ProductDefinition::class,
            $criteria,
        );

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $assignmentType = $this->extractStringValue($slot, 'productAssignmentType', 'manual');
        $productIds = $assignmentType === 'manual'
            ? $this->extractStringArrayValue($slot, 'productIds')
            : [];

        $productResult = $result->get('products_' . $slot->getUniqueIdentifier());
        $entities = $productResult !== null ? $productResult->getEntities() : null;

        $orderedProducts = $assignmentType === 'manual'
            ? $this->orderByIds($entities, $productIds)
            : $this->toProductList($entities);

        $slot->setData(new ArrayStruct([
            'products' => $orderedProducts,
            'defaultProductValues' => $this->extractStringArrayValue($slot, 'defaultProductValues'),
            'propertyGroups' => $this->extractStringArrayValue($slot, 'propertyGroups'),
            'highlightFirstProduct' => $this->extractBoolValue($slot, 'highlightFirstProduct', false),
        ]));
    }

    /**
     * @param list<string> $productIds
     *
     * @return list<ProductEntity>
     */
    private function orderByIds(?object $entities, array $productIds): array
    {
        /** @var array<string, ProductEntity> $byId */
        $byId = [];
        if ($entities instanceof EntityCollection) {
            /** @var ProductEntity $entity */
            foreach ($entities as $entity) {
                $byId[$entity->getId()] = $entity;
            }
        }

        $ordered = [];
        foreach ($productIds as $id) {
            if (isset($byId[$id])) {
                $ordered[] = $byId[$id];
            }
        }

        return $ordered;
    }

    /**
     * @return list<ProductEntity>
     */
    private function toProductList(?object $entities): array
    {
        if (! $entities instanceof EntityCollection) {
            return [];
        }

        $list = [];
        /** @var ProductEntity $entity */
        foreach ($entities as $entity) {
            $list[] = $entity;
        }

        return $list;
    }

    private function buildManualCriteria(CmsSlotEntity $slot): ?Criteria
    {
        $productIds = $this->extractStringArrayValue($slot, 'productIds');
        if ($productIds === []) {
            return null;
        }

        return new Criteria($productIds);
    }

    private function buildStreamCriteria(CmsSlotEntity $slot, ResolverContext $resolverContext): ?Criteria
    {
        $streamId = $this->extractStringValue($slot, 'productStreamId', '');
        if ($streamId === '') {
            return null;
        }

        $filters = $this->productStreamBuilder->buildFilters(
            $streamId,
            $resolverContext->getSalesChannelContext()->getContext(),
        );

        $criteria = new Criteria();
        $criteria->addFilter(...$filters);
        $criteria->setLimit($this->extractIntValue($slot, 'productStreamLimit', 10));

        $sorting = $this->extractStringValue($slot, 'productStreamSorting', 'name:ASC');
        $this->applyStreamSorting($criteria, $sorting);

        return $criteria;
    }

    private function applyStreamSorting(Criteria $criteria, string $sorting): void
    {
        [$field, $direction] = array_pad(explode(':', $sorting, 2), 2, '');

        $direction = strtoupper($direction) === 'DESC' ? FieldSorting::DESCENDING : FieldSorting::ASCENDING;
        $field = $field === 'price' ? 'price' : 'name';

        $criteria->addSorting(new FieldSorting($field, $direction));
    }

    /**
     * @return list<string>
     */
    private function extractStringArrayValue(CmsSlotEntity $slot, string $key): array
    {
        $value = $this->readConfigValue($slot, $key);

        if (! is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (is_string($item) && $item !== '') {
                $result[] = $item;
            }
        }

        return array_values(array_unique($result));
    }

    private function extractStringValue(CmsSlotEntity $slot, string $key, string $default): string
    {
        $value = $this->readConfigValue($slot, $key);

        return is_string($value) && $value !== '' ? $value : $default;
    }

    private function extractIntValue(CmsSlotEntity $slot, string $key, int $default): int
    {
        $value = $this->readConfigValue($slot, $key);

        return $this->castToPositiveInt($value, $default);
    }

    private function castToPositiveInt(mixed $value, int $default): int
    {
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            $int = (int) $value;
            return $int > 0 ? $int : $default;
        }

        return $default;
    }

    private function extractBoolValue(CmsSlotEntity $slot, string $key, bool $default): bool
    {
        $value = $this->readConfigValue($slot, $key);

        return is_bool($value) ? $value : $default;
    }

    private function readConfigValue(CmsSlotEntity $slot, string $key): mixed
    {
        $config = $slot->getConfig();

        if (! is_array($config)) {
            return null;
        }

        $entry = $config[$key] ?? null;

        if (! is_array($entry)) {
            return null;
        }

        return $entry['value'] ?? null;
    }
}
