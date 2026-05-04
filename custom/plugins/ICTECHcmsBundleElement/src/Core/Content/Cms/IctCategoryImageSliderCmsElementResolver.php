<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category\BuildCategoryCard;
use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category\ExtractCategoryIds;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctCategoryImageSliderCmsElementResolver extends AbstractCmsElementResolver
{
    private const TYPE = 'ict-category-image-slider';

    public function __construct(
        private readonly ExtractCategoryIds $extractor,
        private readonly BuildCategoryCard $builder,
    ) {
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $cards = array_values($slot->getFieldConfig()->get('cards')?->getArrayValue() ?? []);
        $categoryIds = $this->extractor->extract($cards);

        if ($categoryIds === []) {
            return null;
        }

        $criteria = new Criteria($categoryIds);
        $criteria->addAssociation('media');

        $collection = new CriteriaCollection();
        $collection->add('category_' . $slot->getUniqueIdentifier(), CategoryDefinition::class, $criteria);

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $cards = array_values($slot->getFieldConfig()->get('cards')?->getArrayValue() ?? []);
        $categoriesResult = $result->get('category_' . $slot->getUniqueIdentifier());

        $slot->setData(new ArrayStruct([
            'items' => $this->builder->buildAll($cards, $categoriesResult),
        ]));
    }
}
