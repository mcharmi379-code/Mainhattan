<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Category;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use NetzpBlog6\Core\Content\Aggregate\CategoryTranslation\CategoryTranslationDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class CategoryDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CategoryEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CategoryCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),

            (new FkField('saleschannel_id', 'saleschannelid', SalesChannelDefinition::class))->addFlags(new ApiAware()),
            (new FkField('customergroup_id', 'customergroupid', CustomerGroupDefinition::class))->addFlags(new ApiAware()),
            (new FkField('navigationcategoryid', 'navigationcategoryid', CategoryDefinition::class))->addFlags(new ApiAware()),

            (new TranslatedField('title'))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('teaser'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),

            (new StringField('cmspageid', 'cmspageid'))->addFlags(new ApiAware()),
            (new BoolField('onlyloggedin', 'onlyloggedin'))->addFlags(new ApiAware()),
            (new BoolField('includeinrss', 'includeinrss'))->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('saleschannel', 'saleschannel_id', SalesChannelDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('customergroup', 'customergroup_id', CustomerGroupDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('navigationcategory', 'navigationcategoryid', CategoryDefinition::class, 'id', true))->addFlags(new ApiAware()),

            new TranslationsAssociationField(CategoryTranslationDefinition::class, 's_plugin_netzp_blog_category_id')
        ]);
    }
}
