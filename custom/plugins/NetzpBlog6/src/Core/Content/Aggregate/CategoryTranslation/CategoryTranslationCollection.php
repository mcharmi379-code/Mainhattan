<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\CategoryTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(CategoryTranslationEntity $entity)
 * @method void            set(string $key, CategoryTranslationEntity $entity)
 * @method CategoryTranslationEntity[]    getIterator()
 * @method CategoryTranslationEntity[]    getElements()
 * @method CategoryTranslationEntity|null get(string $key)
 * @method CategoryTranslationEntity|null first()
 * @method CategoryTranslationEntity|null last()
 */
class CategoryTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CategoryTranslationEntity::class;
    }
}
