<?php declare(strict_types=1);
namespace NetzpBlog6\Core;

use Shopware\Core\Framework\Struct\Struct;

class SearchResult extends Struct
{
    protected $type;
    protected $id;
    protected $title;
    protected $description;
    protected $media;
    protected $total;

    public function getType() { return $this->type; }
    public function setType($value) { $this->type = $value; }

    public function getId() { return $this->id; }
    public function setId($value) { $this->id = $value; }

    public function getTitle() { return $this->title; }
    public function setTitle($value) { $this->title = $value; }

    public function getDescription() { return $this->description; }
    public function setDescription($value) { $this->description = $value; }

    public function getMedia() { return $this->media; }
    public function setMedia($value) { $this->media = $value; }

    public function getTotal() { return $this->total; }
    public function setTotal($value) { $this->total = $value; }
}
