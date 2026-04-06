<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\AuthorTranslation;

use NetzpBlog6\Core\Content\Author\AuthorEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class AuthorTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected ?string $authorId = null;
    protected ?string $name = null;
    protected ?string $bio = null;

    protected ?AuthorEntity $author = null;

    public function getAuthorId(): ?string { return $this->authorId; }
    public function setAuthorId(string $value): void { $this->authorId = $value; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $value): void { $this->name = $value; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(string $value): void { $this->bio = $value; }

    public function getAuthor(): AuthorEntity { return $this->author; }
    public function setAuthor(AuthorEntity $value): void { $this->author = $value; }
}
