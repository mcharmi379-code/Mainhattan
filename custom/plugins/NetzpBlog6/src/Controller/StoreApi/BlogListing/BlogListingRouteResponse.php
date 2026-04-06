<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogListing;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class BlogListingRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getPosts(): EntitySearchResult
    {
        return $this->object;
    }
}
