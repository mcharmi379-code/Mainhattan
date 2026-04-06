<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogPost;

use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class BlogPostRouteResponse extends StoreApiResponse
{
    protected $object;

    public function __construct(BlogEntity $object)
    {
        parent::__construct($object);
    }

    public function getBlogPost(): BlogEntity
    {
        return $this->object;
    }
}
