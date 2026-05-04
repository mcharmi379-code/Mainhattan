<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctHeroSectionElementDataResolver extends AbstractCmsElementResolver
{
    private const SCALAR_MEDIA_FIELDS = [
        'backgroundImage' => 'media',
        'backgroundVideo' => 'video',
        'primaryButtonIcon' => 'primaryButtonIcon',
        'secondaryButtonIcon' => 'secondaryButtonIcon',
    ];

    public function getType(): string
    {
        return 'ict-hero-section';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();

        if ($config->count() === 0) {
            return null;
        }

        $mediaIds = array_merge(
            $this->collectScalarMediaIds($config),
            $this->collectFeatureIconIds($config),
        );

        if ($mediaIds === []) {
            return null;
        }

        $collection = new CriteriaCollection();
        $collection->add('media_' . $this->getType(), MediaDefinition::class, new Criteria($mediaIds));

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $mediaCollection = $result->get('media_' . $this->getType());
        $data = new ArrayStruct();

        $this->enrichScalarFields($config, $mediaCollection, $data);
        $this->enrichFeatureIcons($config, $mediaCollection, $data);

        $featureItems = $config->get('featureItems');
        if ($featureItems !== null && $featureItems->getValue() !== null) {
            $data->set('featureItems', $featureItems->getValue());
        }

        $slot->setData($data);
    }

    /**
     * @return list<string>
     */
    private function collectScalarMediaIds(FieldConfigCollection $config): array
    {
        $ids = [];

        foreach (array_keys(self::SCALAR_MEDIA_FIELDS) as $field) {
            $value = $config->get($field)?->getValue();

            if (is_string($value) && $value !== '') {
                $ids[] = $value;
            }
        }

        return $ids;
    }

    /**
     * @return list<string>
     */
    private function collectFeatureIconIds(FieldConfigCollection $config): array
    {
        $featureItems = $config->get('featureItems');

        if ($featureItems === null || ! is_array($featureItems->getValue())) {
            return [];
        }

        return $this->extractIconIdsFromItems($featureItems->getValue());
    }

    /**
     * @param array<mixed> $items
     *
     * @return list<string>
     */
    private function extractIconIdsFromItems(array $items): array
    {
        $ids = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $icon = $item['icon'] ?? null;

            if (is_string($icon) && $icon !== '') {
                $ids[] = $icon;
            }
        }

        return $ids;
    }

    /**
     * @param EntitySearchResult<\Shopware\Core\Framework\DataAbstractionLayer\EntityCollection>|null $mediaCollection
     */
    private function enrichScalarFields(
        FieldConfigCollection $config,
        ?EntitySearchResult $mediaCollection,
        ArrayStruct $data,
    ): void {
        foreach (self::SCALAR_MEDIA_FIELDS as $field => $dataKey) {
            $value = $config->get($field)?->getValue();

            if (! is_string($value)) {
                continue;
            }

            $data->set($dataKey, $mediaCollection?->get($value));
        }
    }

    /**
     * @param EntitySearchResult<\Shopware\Core\Framework\DataAbstractionLayer\EntityCollection>|null $mediaCollection
     */
    private function enrichFeatureIcons(
        FieldConfigCollection $config,
        ?EntitySearchResult $mediaCollection,
        ArrayStruct $data,
    ): void {
        $featureItems = $config->get('featureItems');

        if ($featureItems === null || ! is_array($featureItems->getValue())) {
            $data->set('featureIcons', []);
            return;
        }

        $data->set('featureIcons', $this->buildFeatureIconMap($featureItems->getValue(), $mediaCollection));
    }

    /**
     * @param array<mixed> $items
     * @param EntitySearchResult<\Shopware\Core\Framework\DataAbstractionLayer\EntityCollection>|null $mediaCollection
     *
     * @return array<int|string, mixed>
     */
    private function buildFeatureIconMap(array $items, ?EntitySearchResult $mediaCollection): array
    {
        $featureIcons = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $icon = $item['icon'] ?? null;

            if (is_string($icon) && $icon !== '') {
                $featureIcons[$index] = $mediaCollection?->get($icon);
            }
        }

        return $featureIcons;
    }
}
