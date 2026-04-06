<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Author;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(AuthorEntity $entity)
 * @method void            set(string $key, AuthorEntity $entity)
 * @method AuthorEntity[]    getIterator()
 * @method AuthorEntity[]    getElements()
 * @method AuthorEntity|null get(string $key)
 * @method AuthorEntity|null first()
 * @method AuthorEntity|null last()
 */
class AuthorCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AuthorEntity::class;
    }
}
