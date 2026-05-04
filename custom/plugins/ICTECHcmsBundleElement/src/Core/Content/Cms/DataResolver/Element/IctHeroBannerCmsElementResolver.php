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

final class IctHeroBannerCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-hero-banner';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();
        $collection = new CriteriaCollection();
        $hasMedia = false;

        $logoId = $config->get('logoMediaId')?->getValue();
        if (\is_string($logoId) && $logoId !== '') {
            $collection->add('hero_banner_logo_' . $uid, MediaDefinition::class, new Criteria([$logoId]));
            $hasMedia = true;
        }

        $heroId = $config->get('heroMediaId')?->getValue();
        if (\is_string($heroId) && $heroId !== '') {
            $collection->add('hero_banner_hero_' . $uid, MediaDefinition::class, new Criteria([$heroId]));
            $hasMedia = true;
        }

        return $hasMedia ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        $logoResult = $result->get('hero_banner_logo_' . $uid);
        $heroResult = $result->get('hero_banner_hero_' . $uid);

        $slot->setData(new ArrayStruct([
            'headline'        => $config->get('headline')?->getStringValue() ?? '',
            'backgroundColor' => $config->get('backgroundColor')?->getStringValue() ?? '#111111',
            'logoMedia'       => $logoResult?->first(),
            'heroMedia'       => $heroResult?->first(),
        ]));
    }
}
