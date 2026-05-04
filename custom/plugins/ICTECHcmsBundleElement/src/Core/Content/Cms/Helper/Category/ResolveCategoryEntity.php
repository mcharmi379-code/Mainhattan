<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

final class ResolveCategoryEntity
{
    public function resolve(string $categoryId, mixed $categoriesResult): ?CategoryEntity
    {
        if (! $categoriesResult instanceof EntitySearchResult) {
            return null;
        }

        $candidate = $categoriesResult->get($categoryId);

        if (! $candidate instanceof CategoryEntity) {
            return null;
        }

        if ($candidate->getMedia() === null) {
            return null;
        }

        return $candidate;
    }
}
