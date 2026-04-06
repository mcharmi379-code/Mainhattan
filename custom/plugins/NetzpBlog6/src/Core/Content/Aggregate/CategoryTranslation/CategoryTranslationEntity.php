<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\CategoryTranslation;

use NetzpBlog6\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class CategoryTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected ?string $categoryId = null;
    protected ?string $title = null;
    protected ?string $teaser = null;
    protected ?CategoryEntity $category = null;

    public function getCategoryId(): ?string { return $this->categoryId; }
    public function setCategoryId(string $value): void { $this->categoryId = $value; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $value): void { $this->title = $value; }

    public function getTeaser(): ?string { return $this->teaser; }
    public function setTeaser(string $value): void { $this->teaser = $value; }

    public function getCategory(): CategoryEntity { return $this->category; }
    public function setCategory(CategoryEntity $value): void { $this->category = $value; }
}
