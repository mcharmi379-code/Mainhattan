<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\BlogTag;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\System\Tag\TagDefinition;

class BlogTagDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_tag';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('blog_id', 'blogId', BlogDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new FkField('tag_id', 'tagId', TagDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),

            (new ManyToOneAssociationField('blog', 'blog_id', BlogDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('tag', 'tag_id', TagDefinition::class, 'id', false))->addFlags(new ApiAware()),
        ]);
    }
}
