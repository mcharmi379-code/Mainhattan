<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Category;

use NetzpBlog6\Core\Content\Aggregate\CategoryTranslation\CategoryTranslationCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class CategoryEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    protected ?string $cmspageid = null;
    protected ?bool $onlyloggedin = null;
    protected ?bool $includeinrss = null;

    protected ?string $saleschannelid = null;
    protected ?SalesChannelEntity $saleschannel = null;

    protected ?string $customergroupid = null;
    protected ?CustomerGroupEntity $customergroup = null;

    protected ?string $navigationcategoryid = null;
    protected ?CategoryEntity $navigationcategory = null;

    protected ?string $title = null;
    protected ?string $teaser = null;

    public function getCmspageid(): ?string { return $this->cmspageid; }
    public function setCmspageid(?string $value): void { $this->cmspageid = $value; }

    public function getSaleschannelid(): ?string { return $this->saleschannelid; }
    public function setSaleschannelid(?string $value): void { $this->saleschannelid = $value; }

    public function getCustomergroupid(): ?string { return $this->customergroupid; }
    public function setCustomergroupid(?string $value): void { $this->customergroupid = $value; }

    public function getOnlyloggedin(): ?bool { return $this->onlyloggedin; }
    public function setOnlyloggedin(?bool $value): void { $this->onlyloggedin = $value; }

    public function getIncludeinrss(): ?bool { return $this->includeinrss; }
    public function setIncludeinrss(?bool $value): void { $this->includeinrss = $value; }

    public function getSalesChannel(): ?SalesChannelEntity { return $this->saleschannel; }
    public function setSalesChannel(SalesChannelEntity $value): void { $this->saleschannel = $value; }

    public function getCustomerGroup(): ?CustomerGroupEntity { return $this->customergroup; }
    public function setCustomerGroup(CustomerGroupEntity $value): void { $this->customergroup = $value; }

    public function getNavigationcategoryid(): ?string { return $this->navigationcategoryid; }
    public function setNavigationcategoryid(string $value): void { $this->navigationcategoryid = $value; }

    public function getNavigationcategory(): ?CategoryEntity { return $this->navigationcategory;}
    public function setNavigationcategory(CategoryEntity $value): void { $this->navigationcategory = $value; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $value): void { $this->title = $value; }

    public function getTeaser(): ?string { return $this->teaser; }
    public function setTeaser(string $value): void { $this->teaser = $value; }

    public function getTranslations(): ?CategoryTranslationCollection { return $this->translations; }
    public function setTranslations(?CategoryTranslationCollection $translations): void { $this->translations = $translations; }
}
