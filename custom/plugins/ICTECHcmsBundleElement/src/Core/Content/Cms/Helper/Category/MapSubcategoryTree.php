<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

final class MapSubcategoryTree
{
    /**
     * @param EntitySearchResult<\Shopware\Core\Framework\DataAbstractionLayer\EntityCollection>|null $result
     * @param array<string, int> $productCounts
     *
     * @return array<int, array<string, mixed>>
     */
    public function map(?EntitySearchResult $result, int $depth, array $productCounts): array
    {
        if ($result === null) {
            return [];
        }

        $items = [];

        foreach ($result->getElements() as $category) {
            if (! $category instanceof CategoryEntity) {
                continue;
            }

            $items[] = $this->mapCategory($category, $depth, 1, $productCounts);
        }

        return $items;
    }

    /**
     * @param array<string, int> $productCounts
     *
     * @return array<string, mixed>
     */
    private function mapCategory(CategoryEntity $category, int $maxDepth, int $currentDepth, array $productCounts): array
    {
        $name = $category->getTranslation('name');

        return [
            'id' => $category->getId(),
            'name' => is_string($name) ? $name : (string) $category->getName(),
            'description' => $this->resolveDescription($category),
            'media' => $category->getMedia(),
            'childCount' => $category->getChildCount(),
            'productCount' => $productCounts[$category->getId()] ?? null,
            'children' => $this->mapChildren($category, $maxDepth, $currentDepth, $productCounts),
        ];
    }

    /**
     * @param array<string, int> $productCounts
     *
     * @return array<int, array<string, mixed>>
     */
    private function mapChildren(CategoryEntity $category, int $maxDepth, int $currentDepth, array $productCounts): array
    {
        if ($currentDepth >= $maxDepth) {
            return [];
        }

        $children = $category->getChildren();

        if ($children === null) {
            return [];
        }

        $mapped = [];

        foreach ($children as $child) {
            if (! $child->getActive()) {
                continue;
            }

            $mapped[] = $this->mapCategory($child, $maxDepth, $currentDepth + 1, $productCounts);
        }

        return $mapped;
    }

    private function resolveDescription(CategoryEntity $category): string
    {
        $translated = $category->getTranslation('description');

        if (is_string($translated)) {
            return $translated;
        }

        return (string) $category->getDescription();
    }
}
