<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category;

final class ExtractCategoryIds
{
    /**
     * @param array<array-key, mixed> $cards
     *
     * @return list<string>
     */
    public function extract(array $cards): array
    {
        $ids = array_filter(
            array_map(static fn (mixed $card): string => \is_array($card) && \is_string($card['categoryId'] ?? null) ? $card['categoryId'] : '', $cards),
            static fn (string $id): bool => $id !== '',
        );

        return array_values(array_unique($ids));
    }
}
