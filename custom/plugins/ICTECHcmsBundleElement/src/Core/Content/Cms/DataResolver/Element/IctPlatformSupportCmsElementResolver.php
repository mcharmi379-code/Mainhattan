<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctPlatformSupportCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-platform-support';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $collection = new CriteriaCollection();

        $this->collectBackgroundMedia($slot, $collection);
        $this->collectPlatformIcons($slot, $collection);

        return $collection->all() !== [] ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = $this->enrichBackgroundMedia($slot, $result);
        $data = array_merge($data, $this->enrichPlatformItems($slot, $result));

        $slot->setData(new ArrayStruct($data));
    }

    private function collectBackgroundMedia(CmsSlotEntity $slot, CriteriaCollection $collection): void
    {
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        foreach (['backgroundImage' => 'background_image_', 'backgroundVideo' => 'background_video_'] as $key => $prefix) {
            $field = $config->get($key);
            if ($field !== null && is_string($field->getValue()) && $field->getValue() !== '') {
                $collection->add($prefix . $uid, MediaDefinition::class, new Criteria([$field->getValue()]));
            }
        }
    }

    private function collectPlatformIcons(CmsSlotEntity $slot, CriteriaCollection $collection): void
    {
        $platforms = $slot->getFieldConfig()->get('platformItems');
        if ($platforms === null || ! is_array($platforms->getValue())) {
            return;
        }

        $uid = $slot->getUniqueIdentifier();
        foreach ($platforms->getValue() as $index => $platform) {
            $this->addPlatformIconCriteria($platform, $index, $uid, $collection);
        }
    }

    private function addPlatformIconCriteria(mixed $platform, int|string $index, string $uid, CriteriaCollection $collection): void
    {
        if (! is_array($platform)) {
            return;
        }
        $icon = $platform['platformIcon'] ?? null;
        if (is_string($icon) && $icon !== '') {
            $collection->add('platform_icon_' . $index . '_' . $uid, MediaDefinition::class, new Criteria([$icon]));
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function enrichBackgroundMedia(CmsSlotEntity $slot, ElementDataCollection $result): array
    {
        $data = [];
        $config = $slot->getFieldConfig();
        $uid = $slot->getUniqueIdentifier();

        foreach (['backgroundImage' => 'background_image_', 'backgroundVideo' => 'background_video_'] as $key => $prefix) {
            $media = $this->resolveBackgroundMediaEntry($config->get($key), $prefix . $uid, $result);
            if ($media !== null) {
                $data[$key] = $media;
            }
        }

        return $data;
    }

    private function resolveBackgroundMediaEntry(?FieldConfig $field, string $resultKey, ElementDataCollection $result): mixed
    {
        if ($field === null || ! $field->getValue()) {
            return null;
        }
        $media = $result->get($resultKey);
        return $media !== null ? $media->first() : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function enrichPlatformItems(CmsSlotEntity $slot, ElementDataCollection $result): array
    {
        $platforms = $slot->getFieldConfig()->get('platformItems');
        if ($platforms === null || ! is_array($platforms->getValue())) {
            return [];
        }

        $platformData = [];
        $platformIcons = [];
        $uid = $slot->getUniqueIdentifier();

        foreach ($platforms->getValue() as $index => $platform) {
            if (! is_array($platform)) {
                continue;
            }
            [$platform, $platformIcons] = $this->resolvePlatformIcon($platform, $index, $uid, $result, $platformIcons);
            $platformData[] = $platform;
        }

        return ['platformItems' => $platformData, 'platformIcons' => $platformIcons];
    }

    /**
     * @param array<mixed> $platform
     *
     * @param array<int|string, mixed> $platformIcons
     *
     * @return array{0: array<mixed>, 1: array<int|string, mixed>}
     */
    private function resolvePlatformIcon(array $platform, int|string $index, string $uid, ElementDataCollection $result, array $platformIcons): array
    {
        $icon = $platform['platformIcon'] ?? null;
        if (! is_string($icon) || $icon === '') {
            return [$platform, $platformIcons];
        }

        $media = $result->get('platform_icon_' . $index . '_' . $uid);
        $firstMedia = $media?->first();

        if ($firstMedia !== null) {
            $platform['iconMedia'] = $firstMedia;
            $platformIcons[$index] = $firstMedia;
        }

        return [$platform, $platformIcons];
    }
}
