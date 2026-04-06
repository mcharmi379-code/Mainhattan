<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Author;

use NetzpBlog6\Core\Content\Aggregate\AuthorTranslation\AuthorTranslationCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class AuthorEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    protected ?MediaEntity $image = null;
    protected ?string $imageid = null;

    protected ?string $name = null;
    protected ?string $bio = null;
    protected ?string $link = null;

    public function getImageid(): ?string { return $this->imageid; }
    public function setImageid(string $value): void { $this->imageid = $value; }

    public function getImage(): ?MediaEntity { return $this->image; }
    public function setImage(MediaEntity $value): void { $this->image = $value; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $value): void { $this->name = $value; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(string $value): void { $this->bio = $value; }

    public function getLink(): ?string { return $this->link; }
    public function setLink(string $value): void { $this->link = $value; }

    public function getTranslations(): ?AuthorTranslationCollection { return $this->translations; }
    public function setTranslations(?AuthorTranslationCollection $translations): void { $this->translations = $translations; }
}
