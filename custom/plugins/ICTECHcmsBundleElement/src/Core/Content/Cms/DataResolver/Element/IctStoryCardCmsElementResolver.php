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

final class IctStoryCardCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-story-card';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        $mediaId = $config->get('mediaId')?->getValue();
        if (!is_string($mediaId) || $mediaId === '') {
            return null;
        }

        $collection = new CriteriaCollection();
        $collection->add('story_card_media_' . $uid, MediaDefinition::class, new Criteria([$mediaId]));

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        $media = $result->get('story_card_media_' . $uid);

        $slot->setData(new ArrayStruct([
            'title' => $config->get('title')?->getStringValue() ?? '',
            'subline' => $config->get('subline')?->getStringValue() ?? '',
            'subtitle' => $config->get('subtitle')?->getStringValue() ?? '',
            'linkType' => $config->get('linkType')?->getStringValue() ?? 'url',
            'linkUrl' => $config->get('linkUrl')?->getStringValue() ?? '',
            'openInNewTab' => $config->get('openInNewTab')?->getBoolValue() ?? false,
            'mediaId' => $config->get('mediaId')?->getStringValue() ?? '',
            'media' => $media?->first(),
        ]));
    }
}

