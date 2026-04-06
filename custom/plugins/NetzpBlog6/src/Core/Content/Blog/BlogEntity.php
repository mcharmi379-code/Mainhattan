<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Blog;

use DateTimeImmutable;
use NetzpBlog6\Core\Content\Author\AuthorEntity;
use NetzpBlog6\Core\Content\BlogMedia\BlogMediaCollection;
use NetzpBlog6\Core\Content\Category\CategoryCollection;
use NetzpBlog6\Core\Content\Category\CategoryEntity;
use NetzpBlog6\Core\Content\Item\ItemCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\ProductStream\ProductStreamEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Tag\TagCollection;
use NetzpBlog6\Core\Content\Aggregate\BlogTranslation\BlogTranslationCollection;

class BlogEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    protected ?DateTimeImmutable $postdate = null;
    protected ?DateTimeImmutable $showfrom = null;
    protected ?DateTimeImmutable $showuntil = null;
    protected ?bool $sticky = null;
    protected ?bool $noindex = null;
    protected ?string $canonicalUrl = null;

    protected ?BlogTranslationCollection $translations = null;
    protected ?ItemCollection $items = null;
    protected ?BlogMediaCollection $blogmedia = null;

    protected ?string $imageid = null;
    protected ?MediaEntity $image = null;

    protected ?string $imagepreviewid = null;
    protected ?MediaEntity $imagepreview = null;

    protected ?string $categoryid = null;
    protected ?CategoryEntity $category = null;

    protected ?string $authorid = null;
    protected ?AuthorEntity $author = null;

    protected ?bool $isproductstream = null;

    protected ?ProductStreamEntity $productstream = null;
    protected ?string $productstreamid  = null;

    protected ?CategoryCollection $categories = null;
    protected ?ProductCollection $products = null;

    protected ?TagCollection $tags = null;

    protected ?string $title = null;
    protected ?string $slug = null;
    protected ?string $contents = null;
    protected ?string $teaser = null;
    protected ?string $custom = null;
    protected ?string $metatitle = null;
    protected ?string $metadescription = null;

    public function getPostdate(): ?DateTimeImmutable { return $this->postdate; }
    public function setPostdate(DateTimeImmutable $value): void { $this->postdate = $value; }

    public function getShowfrom(): ?DateTimeImmutable { return $this->showfrom; }
    public function setShowfrom(DateTimeImmutable $value): void { $this->showfrom = $value; }

    public function getShowuntil(): ?DateTimeImmutable { return $this->showuntil; }
    public function setShowuntil(DateTimeImmutable $value): void { $this->showuntil = $value; }

    public function getNoindex(): ?bool { return $this->noindex; }
    public function setNoindex(?bool $value): void { $this->noindex = $value; }

    public function getCanonicalUrl(): ?string { return $this->canonicalUrl; }
    public function setCanonicalUrl(string $value): void { $this->canonicalUrl = $value; }

    public function getSticky(): ?bool { return $this->sticky; }
    public function setSticky(?bool $value): void { $this->sticky = $value; }

    public function getIsProductStream(): ?bool { return $this->isproductstream; }
    public function setIsProductStream(?bool $value): void { $this->isproductstream = $value; }

    public function getImageid(): ?string { return $this->imageid; }
    public function setImageid(string $value): void { $this->imageid = $value; }

    public function getImage(): ?MediaEntity { return $this->image; }
    public function setImage(MediaEntity $value): void { $this->image = $value; }

    public function getImagepreviewid(): ?string { return $this->imagepreviewid; }
    public function setImagepreviewid(string $value): void { $this->imagepreviewid = $value; }

    public function getImagepreview(): ?MediaEntity { return $this->imagepreview; }
    public function setImagepreview(MediaEntity $value): void { $this->imagepreview = $value; }

    public function getCategoryid(): ?string { return $this->categoryid; }
    public function setCategoryid(string $value): void { $this->categoryid = $value; }

    public function getCategory(): ?CategoryEntity { return $this->category;}
    public function setCategory(CategoryEntity $value): void { $this->category = $value; }

    public function getAuthorid(): ?string { return $this->authorid; }
    public function setAuthorid(string $value): void { $this->authorid = $value; }

    public function getAuthor(): ?AuthorEntity { return $this->author;}
    public function setAuthor(AuthorEntity $value): void { $this->author = $value; }

    public function getCategories(): ?CategoryCollection { return $this->categories; }
    public function setCategories(CategoryCollection $categories): void { $this->categories = $categories; }

    public function getProducts(): ?ProductCollection { return $this->products; }
    public function setProducts(ProductCollection $products): void { $this->products = $products; }

    public function getProductStreamId(): ?string { return $this->productstreamid; }
    public function setProductStreamId(string $value): void { $this->productstreamid = $value; }

    public function getProductStream(): ?ProductStreamEntity { return $this->productstream; }
    public function setProductStream(ProductStreamEntity $value): void { $this->productstream = $value; }

    public function getTags(): ?TagCollection { return $this->tags; }
    public function setTags(TagCollection $tags): void { $this->tags = $tags; }

    public function getItems(): ?ItemCollection { return $this->items; }
    public function setItems(ItemCollection $value): void { $this->items = $value; }

    public function getBlogMedia(): ?BlogMediaCollection { return $this->blogmedia; }
    public function setBlogMedia(BlogMediaCollection $value): void { $this->blogmedia = $value; }

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

    public function getTranslations(): ?BlogTranslationCollection { return $this->translations; }
    public function setTranslations(?BlogTranslationCollection $translations): void { $this->translations = $translations; }
}
