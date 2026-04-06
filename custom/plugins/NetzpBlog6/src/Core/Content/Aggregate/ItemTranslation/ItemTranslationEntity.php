<?php declare(strict_types=1);

namespace NetzpBlog6\Core\Content\Aggregate\ItemTranslation;

use NetzpBlog6\Core\Content\Item\ItemEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class ItemTranslationEntity extends TranslationEntity
{
    protected ?string $itemId = null;
    protected ?string $title = null;
    protected ?string $content = null;
    protected ?ItemEntity $item = null;

    public function getItemId(): ?string { return $this->itemId; }
    public function setItemId(string $value): void { $this->itemId = $value; }

    public function getItem(): ?ItemEntity { return $this->item; }
    public function setItem(ItemEntity $value): void { $this->item = $value; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $value): void { $this->title = $value; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $value): void { $this->content = $value; }
}
