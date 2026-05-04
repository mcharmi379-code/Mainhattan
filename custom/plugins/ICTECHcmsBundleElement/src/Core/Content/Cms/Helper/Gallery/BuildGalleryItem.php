<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Gallery;

use Shopware\Core\Content\Media\MediaEntity;

final class BuildGalleryItem
{
    public function __construct(
        private readonly MapGalleryItem $mapper,
    ) {
    }

    /**
     * @param list<mixed> $rawItems
     *
     * @return list<array{media: MediaEntity, mediaId: string, mediaUrl: string, title: string}>
     */
    public function buildAll(array $rawItems, mixed $mediaCollection): array
    {
        $items = [];

        foreach ($rawItems as $item) {
            if (! is_array($item)) {
                continue;
            }

            $mapped = $this->mapper->map($this->toStringKeyedArray($item), $mediaCollection);

            if ($mapped !== null) {
                $items[] = $mapped;
            }
        }

        return $items;
    }

    /**
     * Converts an array with mixed keys to array<string, mixed> by casting
     * all keys to strings, which is safe for config arrays from Shopware CMS.
     *
     * @param array<array-key, mixed> $input
     *
     * @return array<string, mixed>
     */
    private function toStringKeyedArray(array $input): array
    {
        /** @var array<string, mixed> $result */
        $result = [];

        foreach ($input as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }
}
