<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\ItemTranslation;

use NetzpBlog6\Core\Content\Item\ItemDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ItemTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 's_plugin_netzp_blog_item_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ItemTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return ItemTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return ItemDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new StringField('title', 'title'),
            (new LongTextField('content', 'content'))->addFlags(new AllowHtml())
        ]);
    }
}
