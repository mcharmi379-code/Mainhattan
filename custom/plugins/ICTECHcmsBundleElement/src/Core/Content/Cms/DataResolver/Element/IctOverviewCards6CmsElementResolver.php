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

final class IctOverviewCards6CmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-overview-cards-6';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getConfig();

        if ($config === null) {
            return null;
        }

        $collection = new CriteriaCollection();
        $mediaIds = [];

        $this->collectBackgroundMediaIds($config, $mediaIds);
        $this->collectCardMediaIds($config, $mediaIds);

        if ($mediaIds === []) {
            return null;
        }

        $collection->add(
            'media_' . $slot->getUniqueIdentifier(),
            MediaDefinition::class,
            new Criteria($mediaIds)
        );

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getConfig();

        if ($config === null) {
            $slot->setData(new ArrayStruct([]));
            return;
        }

        $mediaEntities = $this->buildMediaEntityMap($slot, $result);
        $data = $this->enrichBackgroundMedia($config, $mediaEntities);

        $cards = $this->getArrayValue($config, 'cards');
        if ($cards !== []) {
            $data = array_merge($data, $this->enrichCards($cards, $mediaEntities));
        }

        $slot->setData(new ArrayStruct($data));
    }

    // -------------------------------------------------------------------------
    // Collect helpers
    // -------------------------------------------------------------------------

    /**
     * @param array<string, mixed> $config
     * @param list<string> $mediaIds
     */
    private function collectBackgroundMediaIds(array $config, array &$mediaIds): void
    {
        $this->addStringValue($config, 'backgroundImage', $mediaIds);
        $this->addStringValue($config, 'backgroundVideo', $mediaIds);
    }

    /**
     * @param array<string, mixed> $config
     * @param list<string> $mediaIds
     */
    private function collectCardMediaIds(array $config, array &$mediaIds): void
    {
        $cards = $this->getArrayValue($config, 'cards');

        foreach ($cards as $card) {
            if (! is_array($card)) {
                continue;
            }

            /** @var array<string, mixed> $card */
            $this->collectSingleCardMediaIds($card, $mediaIds);
        }
    }

    /**
     * @param array<string, mixed> $card
     * @param list<string> $mediaIds
     */
    private function collectSingleCardMediaIds(array $card, array &$mediaIds): void
    {
        foreach (['iconImage', 'cardBackgroundImage', 'cardBackgroundVideo', 'cardMainBackgroundImage', 'cardMainBackgroundVideo'] as $field) {
            $value = $card[$field] ?? null;
            if (is_string($value) && $value !== '') {
                $mediaIds[] = $value;
            }
        }

        $this->collectButtonIconIds($card, $mediaIds);
        $this->collectFeaturePointIconIds($card, $mediaIds);
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param list<string> $mediaIds
     */
    private function collectButtonIconIds(array $card, array &$mediaIds): void
    {
        $buttons = $card['buttons'] ?? null;

        if (is_array($buttons)) {
            $this->collectIconsFromButtons($buttons, $mediaIds);
            return;
        }

        $legacyIcon = $card['buttonIcon'] ?? null;
        if (is_string($legacyIcon) && $legacyIcon !== '') {
            $mediaIds[] = $legacyIcon;
        }
    }

    /**
     * @param array<mixed> $buttons
     *
     * @param list<string> $mediaIds
     */
    private function collectIconsFromButtons(array $buttons, array &$mediaIds): void
    {
        foreach ($buttons as $button) {
            if (! is_array($button)) {
                continue;
            }

            $icon = $button['buttonIcon'] ?? null;
            if (is_string($icon) && $icon !== '') {
                $mediaIds[] = $icon;
            }
        }
    }

    /**
     * @param array<string, mixed> $card
     * @param list<string> $mediaIds
     */
    private function collectFeaturePointIconIds(array $card, array &$mediaIds): void
    {
        $points = $card['featurePoints'] ?? null;

        if (! is_array($points)) {
            return;
        }

        $this->collectIconsFromPoints($points, $mediaIds);
    }

    /**
     * @param array<mixed> $points
     *
     * @param list<string> $mediaIds
     */
    private function collectIconsFromPoints(array $points, array &$mediaIds): void
    {
        foreach ($points as $point) {
            if (! is_array($point)) {
                continue;
            }
            $icon = $point['icon'] ?? null;
            if (is_string($icon) && $icon !== '') {
                $mediaIds[] = $icon;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Enrich helpers
    // -------------------------------------------------------------------------

    /**
     * @return array<string, object>
     */
    private function buildMediaEntityMap(CmsSlotEntity $slot, ElementDataCollection $result): array
    {
        $mediaResult = $result->get('media_' . $slot->getUniqueIdentifier());
        $mediaEntities = [];

        if ($mediaResult === null) {
            return $mediaEntities;
        }

        foreach ($mediaResult->getEntities() as $media) {
            if (method_exists($media, 'getId') && is_string($media->getId())) {
                $mediaEntities[$media->getId()] = $media;
            }
        }

        return $mediaEntities;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @param array<string, object> $mediaEntities
     *
     * @return array<string, mixed>
     */
    private function enrichBackgroundMedia(array $config, array $mediaEntities): array
    {
        $data = [];

        $bgImage = $this->getConfigStringValue($config, 'backgroundImage');
        if ($bgImage !== null && isset($mediaEntities[$bgImage])) {
            $data['backgroundMedia'] = $mediaEntities[$bgImage];
        }

        $bgVideo = $this->getConfigStringValue($config, 'backgroundVideo');
        if ($bgVideo !== null && isset($mediaEntities[$bgVideo])) {
            $data['backgroundVideoMedia'] = $mediaEntities[$bgVideo];
        }

        return $data;
    }

    /**
     * @param list<mixed> $cards
     *
     * @param array<string, object> $mediaEntities
     *
     * @return array<string, array<int|string, object>>
     */
    private function enrichCards(array $cards, array $mediaEntities): array
    {
        /** @var array<string, array<int|string, object>> $data */
        $data = [
            'iconImages' => [],
            'buttonIcons' => [],
            'cardImages' => [],
            'cardVideos' => [],
            'cardMainImages' => [],
            'cardMainVideos' => [],
            'featureIcons' => [],
        ];

        foreach ($cards as $cardIndex => $card) {
            if (! is_array($card)) {
                continue;
            }

            /** @var array<string, mixed> $card */
            $this->enrichSingleCard($card, $cardIndex, $mediaEntities, $data);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichSingleCard(array $card, int|string $cardIndex, array $mediaEntities, array &$data): void
    {
        $this->enrichCardSimpleMedia($card, $cardIndex, $mediaEntities, $data);
        $this->enrichCardButtonIcons($card, $cardIndex, $mediaEntities, $data);
        $this->enrichCardFeatureIcons($card, $cardIndex, $mediaEntities, $data);
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichCardSimpleMedia(array $card, int|string $cardIndex, array $mediaEntities, array &$data): void
    {
        $fieldMap = [
            'iconImage' => 'iconImages',
            'cardBackgroundImage' => 'cardImages',
            'cardBackgroundVideo' => 'cardVideos',
            'cardMainBackgroundImage' => 'cardMainImages',
            'cardMainBackgroundVideo' => 'cardMainVideos',
        ];

        foreach ($fieldMap as $cardField => $dataKey) {
            $value = $card[$cardField] ?? null;
            if (is_string($value) && $value !== '' && isset($mediaEntities[$value])) {
                $data[$dataKey][$cardIndex] = $mediaEntities[$value];
            }
        }
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichCardButtonIcons(array $card, int|string $cardIndex, array $mediaEntities, array &$data): void
    {
        $buttons = $card['buttons'] ?? null;

        if (is_array($buttons)) {
            $this->enrichButtonsFromList($buttons, $cardIndex, $mediaEntities, $data);
            return;
        }

        $legacyIcon = $card['buttonIcon'] ?? null;
        if (is_string($legacyIcon) && $legacyIcon !== '' && isset($mediaEntities[$legacyIcon])) {
            $data['buttonIcons'][$cardIndex] = $mediaEntities[$legacyIcon];
        }
    }

    /**
     * @param array<mixed> $buttons
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichButtonsFromList(array $buttons, int|string $cardIndex, array $mediaEntities, array &$data): void
    {
        foreach ($buttons as $buttonIndex => $button) {
            if (! is_array($button)) {
                continue;
            }

            $icon = $button['buttonIcon'] ?? null;
            if (is_string($icon) && $icon !== '' && isset($mediaEntities[$icon])) {
                $this->assignButtonIcon($data, $cardIndex, $buttonIndex, $mediaEntities[$icon]);
            }
        }
    }

    /**
     * @param array<string, array<int|string, object>> $data
     */
    private function assignButtonIcon(array &$data, int|string $cardIndex, int|string $buttonIndex, object $media): void
    {
        $data['buttonIcons'][$cardIndex . '-' . $buttonIndex] = $media;

        if ($buttonIndex === 0) {
            $data['buttonIcons'][$cardIndex] = $media;
        }
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichCardFeatureIcons(array $card, int|string $cardIndex, array $mediaEntities, array &$data): void
    {
        $points = $card['featurePoints'] ?? null;

        if (! is_array($points)) {
            return;
        }

        $this->enrichFeatureIconsFromPoints($points, $cardIndex, $mediaEntities, $data);
    }

    /**
     * @param array<mixed> $points
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichFeatureIconsFromPoints(array $points, int|string $cardIndex, array $mediaEntities, array &$data): void
    {
        foreach ($points as $pointIndex => $point) {
            if (! is_array($point)) {
                continue;
            }

            $this->assignFeatureIcon($point, $cardIndex, $pointIndex, $mediaEntities, $data);
        }
    }

    /**
     * @param array<mixed> $point
     *
     * @param array<string, object> $mediaEntities
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function assignFeatureIcon(array $point, int|string $cardIndex, int|string $pointIndex, array $mediaEntities, array &$data): void
    {
        $icon = $point['icon'] ?? null;
        if (is_string($icon) && $icon !== '' && isset($mediaEntities[$icon])) {
            $data['featureIcons'][$cardIndex . '-' . $pointIndex] = $mediaEntities[$icon];
        }
    }

    // -------------------------------------------------------------------------
    // Utility helpers
    // -------------------------------------------------------------------------

    /**
     * Safely extract a non-empty string from $config[$key]['value'].
     *
     * @param array<string, mixed> $config
     */
    private function getConfigStringValue(array $config, string $key): ?string
    {
        $entry = $config[$key] ?? null;

        if (! is_array($entry)) {
            return null;
        }

        $value = $entry['value'] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Safely extract the 'value' array from $config[$key]['value'].
     *
     * @param array<string, mixed> $config
     *
     * @return list<mixed>
     */
    private function getArrayValue(array $config, string $key): array
    {
        $entry = $config[$key] ?? null;

        if (! is_array($entry)) {
            return [];
        }

        $value = $entry['value'] ?? null;

        return is_array($value) ? array_values($value) : [];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @param list<string> $mediaIds
     */
    private function addStringValue(array $config, string $key, array &$mediaIds): void
    {
        $value = $this->getConfigStringValue($config, $key);

        if ($value !== null) {
            $mediaIds[] = $value;
        }
    }
}
