<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\BlogMedia;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(BlogMediaEntity $entity)
 * @method void            set(string $key, BlogMediaEntity $entity)
 * @method BlogMediaEntity[]    getIterator()
 * @method BlogMediaEntity[]    getElements()
 * @method BlogMediaEntity|null get(string $key)
 * @method BlogMediaEntity|null first()
 * @method BlogMediaEntity|null last()
 */
class BlogMediaCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BlogMediaEntity::class;
    }
}
