<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogListing;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractBlogListingRoute
{
    abstract public function getDecorated(): AbstractBlogListingRoute;

    abstract public function load(?string $navigationId,
                                  Criteria $criteria,
                                  SalesChannelContext $salesChannelContext,
                                  ?string $categoryId,
                                  ?string $authorId,
                                  ?array $tags,
                                  ?string $sortOrder): BlogListingRouteResponse;
}
