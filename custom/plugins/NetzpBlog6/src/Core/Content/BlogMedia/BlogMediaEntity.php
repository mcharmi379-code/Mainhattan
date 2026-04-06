<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\BlogMedia;

use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class BlogMediaEntity extends Entity
{
    use EntityIdTrait;

    protected ?int $number = null;

    protected ?string $mediaId = null;
    protected ?MediaEntity $media = null;

    protected ?string $blogId = null;
    protected ?BlogEntity $blog = null;

    public function getNumber(): ?int { return $this->number; }
    public function setNumber(int $value): void { $this->number = $value; }

    public function getMediaId(): ?string { return $this->mediaId; }
    public function setMediaId(string $value): void { $this->mediaId = $value; }

    public function getMedia(): ?MediaEntity { return $this->media; }
    public function setMedia(MediaEntity $value): void { $this->media = $value; }

    public function getBlogId(): ?string { return $this->blogId; }
    public function setBlogId(string $value): void { $this->blogId = $value; }

    public function getBlog(): ?BlogEntity { return $this->blog; }
    public function setBlog(BlogEntity $value): void { $this->blog = $value; }
}
