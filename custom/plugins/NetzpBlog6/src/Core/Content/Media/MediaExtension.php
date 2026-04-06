<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Media;

use NetzpBlog6\Core\Content\BlogMedia\BlogMediaDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MediaExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'netzpBlogMedia',
                BlogMediaDefinition::class,
                'media_id'))
                ->addFlags(new RestrictDelete())
        );
    }
}
