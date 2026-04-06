<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Blog;

use NetzpBlog6\Core\Content\Aggregate\BlogCategory\BlogCategoryDefinition;
use NetzpBlog6\Core\Content\Aggregate\BlogProduct\BlogProductDefinition;
use NetzpBlog6\Core\Content\Aggregate\BlogTag\BlogTagDefinition;
use NetzpBlog6\Core\Content\Author\AuthorDefinition;
use NetzpBlog6\Core\Content\BlogMedia\BlogMediaDefinition;
use NetzpBlog6\Core\Content\Category\CategoryDefinition;
use NetzpBlog6\Core\Content\Item\ItemDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\ProductStream\ProductStreamDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use NetzpBlog6\Core\Content\Aggregate\BlogTranslation\BlogTranslationDefinition;
use Shopware\Core\System\Tag\TagDefinition;

class BlogDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return BlogEntity::class;
    }

    public function getCollectionClass(): string
    {
        return BlogCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),

            (new DateField('postdate', 'postdate'))->addFlags(new Required(), new ApiAware()),
            (new DateField('showfrom', 'showfrom'))->addFlags(new ApiAware()),
            (new DateField('showuntil', 'showuntil'))->addFlags(new ApiAware()),
            (new BoolField('noindex', 'noindex'))->addFlags(new ApiAware()),
            (new StringField('canonicalUrl', 'canonicalUrl'))->addFlags(new ApiAware()),
            (new BoolField('sticky', 'sticky'))->addFlags(new ApiAware()),
            (new BoolField('isproductstream', 'isproductstream'))->addFlags(new ApiAware()),

            (new FkField('imageid', 'imageid', MediaDefinition::class))->addFlags(new ApiAware()),
            (new FkField('imagepreviewid', 'imagepreviewid', MediaDefinition::class))->addFlags(new ApiAware()),
            (new FkField('categoryid', 'categoryid', CategoryDefinition::class))->addFlags(new ApiAware()),
            (new FkField('authorid', 'authorid', AuthorDefinition::class))->addFlags(new ApiAware()),
            (new FkField('productstreamid', 'productstreamid', ProductStreamDefinition::class))->addFlags(new ApiAware()),

            (new TranslatedField('title'))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('teaser'))->addFlags(new ApiAware()),
            (new TranslatedField('slug'))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('contents'))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('custom'))->addFlags(new ApiAware()),
            (new TranslatedField('metatitle'))->addFlags(new ApiAware()),
            (new TranslatedField('metadescription'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('category', 'categoryid', CategoryDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('author', 'authorid', AuthorDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('image', 'imageid', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('imagepreview', 'imagepreviewid', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),

            (new ManyToManyAssociationField('categories', CategoryDefinition::class, BlogCategoryDefinition::class, 'blog_id', 'category_id'))->addFlags(new ApiAware()),
            (new ManyToManyAssociationField('products', ProductDefinition::class, BlogProductDefinition::class, 'blog_id', 'product_id'))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('productstream', 'productstreamid', ProductStreamDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToManyAssociationField('tags', TagDefinition::class, BlogTagDefinition::class, 'blog_id', 'tag_id'))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('items', ItemDefinition::class, 'blog_id'))->addFlags(new CascadeDelete())->addFlags(new ApiAware()),
            (new OneToManyAssociationField('blogmedia', BlogMediaDefinition::class, 'blog_id'))->addFlags(new CascadeDelete())->addFlags(new ApiAware()),

            new TranslationsAssociationField(BlogTranslationDefinition::class, 's_plugin_netzp_blog_id'),
        ]);
    }
}
