<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\BlogTranslation;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BlogTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return BlogTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return BlogTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return BlogDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new Required()),
            new StringField('teaser', 'teaser'),
            (new StringField('slug', 'slug'))->addFlags(new Required()),
            (new LongTextField('contents', 'contents'))->addFlags(new Required(), new AllowHtml()),
            (new LongTextField('custom', 'custom'))->addFlags(new AllowHtml()),
            new StringField('metatitle', 'metatitle'),
            new StringField('metadescription', 'metadescription'),
            new CustomFields()
        ]);
    }
}
