<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\BlogProduct;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class BlogProductDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('blog_id', 'blogId', BlogDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new ReferenceVersionField(ProductDefinition::class))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new ManyToOneAssociationField('blog', 'blog_id', BlogDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class))->addFlags(new ApiAware()),
            (new CreatedAtField())->addFlags(new ApiAware())
        ]);
    }
}
