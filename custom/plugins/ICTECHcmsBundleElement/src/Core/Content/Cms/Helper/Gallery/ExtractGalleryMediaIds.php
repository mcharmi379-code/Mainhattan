<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Gallery;

final class ExtractGalleryMediaIds
{
    /**
     * @param array<array-key, mixed> $rawItems
     *
     * @return list<string>
     */
    public function extract(array $rawItems): array
    {
        $ids = array_filter(
            array_map(static fn (mixed $item): string => \is_array($item) && \is_string($item['mediaId'] ?? null) ? $item['mediaId'] : '', $rawItems),
            static fn (string $id): bool => $id !== '',
        );

        return array_values(array_unique($ids));
    }
}
