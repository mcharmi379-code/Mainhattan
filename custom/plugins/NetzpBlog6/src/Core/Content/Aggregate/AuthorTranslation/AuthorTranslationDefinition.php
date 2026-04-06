<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\AuthorTranslation;

use NetzpBlog6\Core\Content\Author\AuthorDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class AuthorTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_author_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return AuthorTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return AuthorTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return AuthorDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required(), new ApiAware()),
            (new LongTextField('bio', 'bio'))->addFlags(new AllowHtml(), new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware())
        ]);
    }
}
