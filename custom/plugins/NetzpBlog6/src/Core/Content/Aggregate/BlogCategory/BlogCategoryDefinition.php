<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\BlogCategory;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use NetzpBlog6\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class BlogCategoryDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_categories';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('blog_id', 'blogId', BlogDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new FkField('category_id', 'categoryId', CategoryDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new ManyToOneAssociationField('blog', 'blog_id', BlogDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('category', 'category_id', CategoryDefinition::class))->addFlags(new ApiAware()),
            (new CreatedAtField())->addFlags(new ApiAware())
        ]);
    }
}
