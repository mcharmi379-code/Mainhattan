<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Item;

use NetzpBlog6\Core\Content\Aggregate\ItemTranslation\ItemTranslationCollection;
use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ItemEntity extends Entity
{
    use EntityIdTrait;

    protected ?int $number = null;

    protected ?string $imageid = null;
    protected ?MediaEntity $image = null;

    protected ?string $blogId = null;
    protected ?BlogEntity $blog = null;

    protected ?string $title = null;
    protected ?string $content = null;

    public function getNumber(): ?int { return $this->number; }
    public function setNumber(int $value): void { $this->number = $value; }

    public function getImageId(): ?string { return $this->imageid; }
    public function setImageId(string $value): void { $this->imageid = $value; }

    public function getImage(): ?MediaEntity { return $this->image; }
    public function setImage(MediaEntity $value): void { $this->image = $value; }

    public function getBlogId(): ?string { return $this->blogId; }
    public function setBlogId(string $value): void { $this->blogId = $value; }

    public function getBlog(): ?BlogEntity { return $this->blog; }
    public function setBlog(BlogEntity $value): void { $this->blog = $value; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $value): void { $this->title = $value; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $value): void { $this->content = $value; }

    public function getTranslations(): ?ItemTranslationCollection { return $this->translations; }
    public function setTranslations(?ItemTranslationCollection $translations): void { $this->translations = $translations; }
}
