<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category;

use Shopware\Core\Content\Category\CategoryEntity;

final class BuildCategoryCard
{
    public function __construct(
        private readonly MapCategoryCard $mapper,
    ) {
    }

    /**
     * @param list<mixed> $cards
     *
     * @return list<array{category: CategoryEntity, title: string}>
     */
    public function buildAll(array $cards, mixed $categoriesResult): array
    {
        $items = [];

        foreach ($cards as $card) {
            if (! is_array($card)) {
                continue;
            }

            $mapped = $this->mapper->map($this->toStringKeyedArray($card), $categoriesResult);

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
