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

final class IctFeatureGridCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-feature-grid';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getConfig();
        if ($config === null) {
            return null;
        }

        $mediaIds = $this->collectAllMediaIds($config);
        if ($mediaIds === []) {
            return null;
        }

        $collection = new CriteriaCollection();
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

        $mediaMap = $this->buildMediaMap($slot, $result);
        $cards = $this->extractCards($config);

        $slot->setData(new ArrayStruct($this->buildData($cards, $mediaMap)));
    }

    // ── Collect ───────────────────────────────────────────────────────

    /**
     * @param array<string, mixed> $config
     *
     * @return list<string>
     */
    private function collectAllMediaIds(array $config): array
    {
        $ids = [];
        $cards = $this->extractCards($config);

        foreach ($cards as $card) {
            if (! is_array($card)) {
                continue;
            }

            /** @var array<string, mixed> $card */
            $this->collectCardMediaIds($card, $ids);
        }

        return $ids;
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param list<string> $ids
     */
    private function collectCardMediaIds(array $card, array &$ids): void
    {
        foreach (['iconImage', 'cardBackgroundImage', 'cardBackgroundVideo'] as $field) {
            $value = $card[$field] ?? null;
            if (is_string($value) && $value !== '') {
                $ids[] = $value;
            }
        }

        $this->collectButtonIconIds($card, $ids);
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param list<string> $ids
     */
    private function collectButtonIconIds(array $card, array &$ids): void
    {
        $buttons = $card['buttons'] ?? null;
        if (! is_array($buttons)) {
            return;
        }

        foreach ($buttons as $button) {
            if (! is_array($button)) {
                continue;
            }

            $icon = $button['buttonIcon'] ?? null;
            if (is_string($icon) && $icon !== '') {
                $ids[] = $icon;
            }
        }
    }

    // ── Enrich ────────────────────────────────────────────────────────

    /**
     * @return array<string, object>
     */
    private function buildMediaMap(CmsSlotEntity $slot, ElementDataCollection $result): array
    {
        $mediaResult = $result->get('media_' . $slot->getUniqueIdentifier());
        if ($mediaResult === null) {
            return [];
        }

        $map = [];
        foreach ($mediaResult->getEntities() as $media) {
            if (method_exists($media, 'getId') && is_string($media->getId())) {
                $map[$media->getId()] = $media;
            }
        }

        return $map;
    }

    /**
     * @param list<mixed> $cards
     *
     * @param array<string, object> $mediaMap
     *
     * @return array<string, array<int|string, object>>
     */
    private function buildData(array $cards, array $mediaMap): array
    {
        $data = [
            'iconImages' => [],
            'cardImages' => [],
            'cardVideos' => [],
            'buttonIcons' => [],
        ];

        foreach ($cards as $index => $card) {
            if (! is_array($card)) {
                continue;
            }

            /** @var array<string, mixed> $card */
            $this->enrichCard($card, $index, $mediaMap, $data);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param array<string, object> $mediaMap
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichCard(array $card, int|string $index, array $mediaMap, array &$data): void
    {
        $simpleFields = [
            'iconImage' => 'iconImages',
            'cardBackgroundImage' => 'cardImages',
            'cardBackgroundVideo' => 'cardVideos',
        ];

        foreach ($simpleFields as $cardField => $dataKey) {
            $value = $card[$cardField] ?? null;
            if (is_string($value) && $value !== '' && isset($mediaMap[$value])) {
                $data[$dataKey][$index] = $mediaMap[$value];
            }
        }

        $this->enrichButtonIcons($card, $index, $mediaMap, $data);
    }

    /**
     * @param array<string, mixed> $card
     *
     * @param array<string, object> $mediaMap
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function enrichButtonIcons(array $card, int|string $cardIndex, array $mediaMap, array &$data): void
    {
        $buttons = $card['buttons'] ?? null;
        if (! is_array($buttons)) {
            return;
        }

        foreach ($buttons as $buttonIndex => $button) {
            if (! is_array($button)) {
                continue;
            }

            $this->assignButtonIconIfExists($button, $cardIndex, $buttonIndex, $mediaMap, $data);
        }
    }

    /**
     * @param array<mixed> $button
     *
     * @param array<string, object> $mediaMap
     *
     * @param array<string, array<int|string, object>> $data
     */
    private function assignButtonIconIfExists(array $button, int|string $cardIndex, int|string $buttonIndex, array $mediaMap, array &$data): void
    {
        $icon = $button['buttonIcon'] ?? null;
        if (! is_string($icon) || $icon === '' || ! isset($mediaMap[$icon])) {
            return;
        }

        $data['buttonIcons'][$cardIndex . '-' . $buttonIndex] = $mediaMap[$icon];
        if ($buttonIndex === 0) {
            $data['buttonIcons'][$cardIndex] = $mediaMap[$icon];
        }
    }

    // ── Utilities ─────────────────────────────────────────────────────

    /**
     * @param array<string, mixed> $config
     *
     * @return list<mixed>
     */
    private function extractCards(array $config): array
    {
        $entry = $config['cards'] ?? null;
        if (! is_array($entry)) {
            return [];
        }

        $value = $entry['value'] ?? null;
        return is_array($value) ? array_values($value) : [];
    }
}
