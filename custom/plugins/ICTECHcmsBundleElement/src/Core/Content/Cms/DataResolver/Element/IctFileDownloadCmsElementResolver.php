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

final class IctFileDownloadCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-file-download';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        $collection = new CriteriaCollection();

        foreach (['mediaId' => 'file_download_media_', 'previewImageId' => 'file_download_preview_'] as $key => $prefix) {
            $field = $config->get($key);
            $value = $field?->getValue();

            if (is_string($value) && $value !== '') {
                $collection->add($prefix . $uid, MediaDefinition::class, new Criteria([$value]));
            }
        }

        return $collection->all() !== [] ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        $media = $result->get('file_download_media_' . $uid);
        $previewMedia = $result->get('file_download_preview_' . $uid);

        $slot->setData(new ArrayStruct([
            'title' => $config->get('title')?->getStringValue() ?? '',
            'subtitle' => $config->get('subtitle')?->getStringValue() ?? '',
            'buttonText' => $config->get('buttonText')?->getStringValue() ?? '',
            'showSpecifications' => $config->get('showSpecifications')?->getBoolValue() ?? false,
            'mediaId' => $config->get('mediaId')?->getStringValue() ?? '',
            'previewImageId' => $config->get('previewImageId')?->getStringValue() ?? '',
            'media' => $media?->first(),
            'previewMedia' => $previewMedia?->first(),
        ]));
    }
}
