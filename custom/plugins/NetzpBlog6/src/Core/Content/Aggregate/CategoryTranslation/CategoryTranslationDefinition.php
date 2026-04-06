<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\CategoryTranslation;

use NetzpBlog6\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CategoryTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_category_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CategoryTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return CategoryTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return CategoryDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new Required()),
            (new LongTextField('teaser', 'teaser'))->addFlags(new AllowHtml()),
            new CustomFields()
        ]);
    }
}
