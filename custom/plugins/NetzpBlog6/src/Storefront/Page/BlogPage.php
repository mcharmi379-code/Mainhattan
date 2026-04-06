<?php declare(strict_types=1);

namespace NetzpBlog6\Storefront\Page;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Storefront\Page\Page;
use NetzpBlog6\Core\Content\Blog\BlogEntity;

class BlogPage extends Page
{
    protected $post = null;
    protected $cmsPage = null;
    protected $category = null;

    public function getPost(): BlogEntity
    {
        return $this->post;
    }

    public function setPost(BlogEntity $post): void
    {
        $this->post = $post;
    }

    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    public function isCmsPage(): bool
    {
        return $this->cmsPage != null;
    }

    public function setCmsPage(?CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?CategoryEntity $category): void
    {
        $this->category = $category;
    }
}
