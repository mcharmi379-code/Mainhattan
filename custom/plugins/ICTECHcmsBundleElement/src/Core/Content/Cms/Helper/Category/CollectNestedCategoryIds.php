<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Category;

use Shopware\Core\Content\Category\CategoryEntity;

final class CollectNestedCategoryIds
{
    /**
     * @param array<int, mixed> $elements
     *
     * @return array<int, string>
     */
    public function collect(array $elements): array
    {
        $ids = [];

        foreach ($elements as $element) {
            if (! $element instanceof CategoryEntity) {
                continue;
            }

            $ids[] = $element->getId();

            $children = $element->getChildren();

            if ($children !== null && $children->count() > 0) {
                $ids = array_merge($ids, $this->collect($children->getElements()));
            }
        }

        return array_values(array_unique($ids));
    }
}
