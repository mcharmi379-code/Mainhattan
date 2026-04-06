<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\AuthorTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(AuthorTranslationEntity $entity)
 * @method void            set(string $key, AuthorTranslationEntity $entity)
 * @method AuthorTranslationEntity[]    getIterator()
 * @method AuthorTranslationEntity[]    getElements()
 * @method AuthorTranslationEntity|null get(string $key)
 * @method AuthorTranslationEntity|null first()
 * @method AuthorTranslationEntity|null last()
 */
class AuthorTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AuthorTranslationEntity::class;
    }
}
