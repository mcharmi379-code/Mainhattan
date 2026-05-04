<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Gallery\BuildGalleryItem;
use ICTECHcmsBundleElement\Core\Content\Cms\Helper\Gallery\ExtractGalleryMediaIds;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctImageGalleryCmsElementResolver extends AbstractCmsElementResolver
{
    private const TYPE = 'ict-image-gallery';

    public function __construct(
        private readonly ExtractGalleryMediaIds $extractor,
        private readonly BuildGalleryItem $builder,
    ) {
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $rawItems = array_values($slot->getFieldConfig()->get('galleryItems')?->getArrayValue() ?? []);
        $mediaIds = $this->extractor->extract($rawItems);

        if ($mediaIds === []) {
            return null;
        }

        $collection = new CriteriaCollection();
        $collection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, new Criteria($mediaIds));

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $rawItems = array_values($config->get('galleryItems')?->getArrayValue() ?? []);
        $mediaCollection = $result->get('media_' . $slot->getUniqueIdentifier());
        $columns = $this->normalizeColumns($config->get('columns')?->getValue());

        $slot->setData(new ArrayStruct([
            'galleryTitle' => $config->get('galleryTitle')?->getStringValue() ?? '',
            'columns' => $columns,
            'galleryItems' => $this->builder->buildAll($rawItems, $mediaCollection),
        ]));
    }

    private function normalizeColumns(mixed $columns): int
    {
        $value = is_scalar($columns) || $columns === null ? (int) $columns : 0;

        return max(1, min(6, $value));
    }
}
