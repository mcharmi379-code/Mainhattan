<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\ItemTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(ItemTranslationEntity $entity)
 * @method void            set(string $key, ItemTranslationEntity $entity)
 * @method ItemTranslationEntity[]    getIterator()
 * @method ItemTranslationEntity[]    getElements()
 * @method ItemTranslationEntity|null get(string $key)
 * @method ItemTranslationEntity|null first()
 * @method ItemTranslationEntity|null last()
 */
class ItemTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ItemTranslationEntity::class;
    }
}
