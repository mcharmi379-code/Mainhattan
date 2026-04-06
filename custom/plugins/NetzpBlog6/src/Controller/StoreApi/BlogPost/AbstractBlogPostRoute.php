<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogPost;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractBlogPostRoute
{
    abstract public function getDecorated(): AbstractBlogPostRoute;

    abstract public function load(string $postId, Criteria $criteria, SalesChannelContext $context): BlogPostRouteResponse;
}
