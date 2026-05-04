<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctImageComparisonCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-image-comparison';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();
        $collection = new CriteriaCollection();
        $hasMedia = false;

        $beforeId = $config->get('beforeMediaId')?->getValue();
        if (\is_string($beforeId) && $beforeId !== '') {
            $collection->add('img_comparison_before_' . $uid, MediaDefinition::class, new Criteria([$beforeId]));
            $hasMedia = true;
        }

        $afterId = $config->get('afterMediaId')?->getValue();
        if (\is_string($afterId) && $afterId !== '') {
            $collection->add('img_comparison_after_' . $uid, MediaDefinition::class, new Criteria([$afterId]));
            $hasMedia = true;
        }

        return $hasMedia ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        $beforeResult = $result->get('img_comparison_before_' . $uid);
        $afterResult = $result->get('img_comparison_after_' . $uid);

        $layout = $config->get('layout')?->getStringValue() ?? 'horizontal';
        if (!\in_array($layout, ['horizontal', 'vertical'], true)) {
            $layout = 'horizontal';
        }

        $slot->setData(new ArrayStruct([
            'layout'      => $layout,
            'beforeMedia' => $beforeResult?->first(),
            'afterMedia'  => $afterResult?->first(),
        ]));
    }
}
