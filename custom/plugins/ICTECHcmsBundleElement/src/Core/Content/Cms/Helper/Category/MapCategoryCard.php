<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category;

use Shopware\Core\Content\Category\CategoryEntity;

final class MapCategoryCard
{
    public function __construct(
        private readonly ResolveCategoryEntity $categoryResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $card
     *
     * @return array{category: CategoryEntity, title: string}|null
     */
    public function map(array $card, mixed $categoriesResult): ?array
    {
        $categoryId = $card['categoryId'] ?? null;

        if (! \is_string($categoryId) || $categoryId === '') {
            return null;
        }

        $category = $this->categoryResolver->resolve($categoryId, $categoriesResult);

        if ($category === null) {
            return null;
        }

        return [
            'category' => $category,
            'title' => \is_string($card['title'] ?? null) ? $card['title'] : '',
        ];
    }
}
