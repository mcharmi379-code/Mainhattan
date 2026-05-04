<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Media\BuildMediaCriteria;
use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Media\ResolveMediaEntry;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctTwoColumnThreeImageElementDataResolver extends AbstractCmsElementResolver
{
    private const MEDIA_CONFIGS = [
        'leftImage' => ['prefix' => 'leftMedia_', 'dataKey' => 'leftMedia'],
        'leftButtonIcon' => ['prefix' => 'leftButtonIcon_', 'dataKey' => 'leftButtonIcon'],
        'rightTopImage' => ['prefix' => 'rightTopMedia_', 'dataKey' => 'rightTopMedia'],
        'rightTopButtonIcon' => [
            'prefix' => 'rightTopButtonIcon_',
            'dataKey' => 'rightTopButtonIcon',
        ],
        'rightBottomImage' => ['prefix' => 'rightBottomMedia_', 'dataKey' => 'rightBottomMedia'],
        'rightBottomButtonIcon' => [
            'prefix' => 'rightBottomButtonIcon_',
            'dataKey' => 'rightBottomButtonIcon',
        ],
    ];

    public function __construct(
        private readonly BuildMediaCriteria $criteriaBuilder,
        private readonly ResolveMediaEntry $mediaResolver,
    ) {
    }

    public function getType(): string
    {
        return 'ict-two-column-three-image';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return $this->criteriaBuilder->build($slot, self::MEDIA_CONFIGS);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $slot->setData(new ArrayStruct(
            $this->mediaResolver->resolveAll($slot, $result, self::MEDIA_CONFIGS)
        ));
    }
}
