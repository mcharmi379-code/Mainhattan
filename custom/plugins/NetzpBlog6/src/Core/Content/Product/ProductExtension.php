<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Product;

use NetzpBlog6\Core\Content\Aggregate\BlogProduct\BlogProductDefinition;
use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                'blogs',
                BlogDefinition::class,
                BlogProductDefinition::class,
                'product_id',
                'blog_id'
            ))->addFlags(new Inherited())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
