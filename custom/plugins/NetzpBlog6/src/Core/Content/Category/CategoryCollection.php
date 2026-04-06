<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Category;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(CategoryEntity $entity)
 * @method void            set(string $key, CategoryEntity $entity)
 * @method CategoryEntity[]    getIterator()
 * @method CategoryEntity[]    getElements()
 * @method CategoryEntity|null get(string $key)
 * @method CategoryEntity|null first()
 * @method CategoryEntity|null last()
 */
class CategoryCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CategoryEntity::class;
    }
}
