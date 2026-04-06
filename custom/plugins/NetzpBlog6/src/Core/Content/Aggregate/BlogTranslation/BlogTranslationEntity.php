<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\BlogTranslation;

use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class BlogTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected ?string $blogId = null;
    protected ?string $title = null;
    protected ?string $slug = null;
    protected ?string $contents = null;
    protected ?string $teaser = null;
    protected ?string $custom = null;
    protected ?string $metatitle = null;
    protected ?string $metadescription = null;
    protected ?BlogEntity $blog = null;

    public function getBlogId(): ?string { return $this->blogId; }
    public function setBlogId(string $value): void { $this->blogId = $value; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $value): void { $this->title = $value; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $value): void { $this->slug = $value; }

    public function getContents(): ?string { return $this->contents; }
    public function setContents(string $value): void { $this->contents = $value; }

    public function getTeaser(): ?string { return $this->teaser; }
    public function setTeaser(string $value): void { $this->teaser = $value; }

    public function getCustom(): ?string { return $this->custom; }
    public function setCustom(string $value): void { $this->custom = $value; }

    public function getMetatitle(): ?string { return $this->metatitle; }
    public function setMetatitle(string $value): void { $this->metatitle = $value; }

    public function getMetadescription(): ?string { return $this->metadescription; }
    public function setMetadescription(string $value): void { $this->metadescription = $value; }

    public function getBlog(): BlogEntity { return $this->blog; }
    public function setBlog(BlogEntity $blog): void { $this->blog = $blog; }
}
