<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Author;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
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
use NetzpBlog6\Core\Content\Aggregate\AuthorTranslation\AuthorTranslationDefinition;

class AuthorDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_author';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return AuthorEntity::class;
    }

    public function getCollectionClass(): string
    {
        return AuthorCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),

            (new FkField('imageid', 'imageid', MediaDefinition::class))->addFlags(new ApiAware()),

            (new StringField('link', 'link'))->addFlags(new ApiAware()),

            (new TranslatedField('name'))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('bio'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('image', 'imageid', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            new TranslationsAssociationField(AuthorTranslationDefinition::class, 's_plugin_netzp_blog_author_id')
        ]);
    }
}
