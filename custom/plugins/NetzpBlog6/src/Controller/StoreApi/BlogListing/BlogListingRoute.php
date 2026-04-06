<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogListing;

use NetzpBlog6\Helper\BlogHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

class BlogListingRoute extends AbstractBlogListingRoute
{
    public function __construct(private readonly EntityRepository $blogRepository,
                                private readonly BlogHelper $helper)
    {
    }

    public function getDecorated(): never
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/bloglisting/{navigationId?}', name: 'store-api.s_plugin_netzp_blog_listing.load', methods: ['GET', 'POST'], defaults: ['_entity' => 's_plugin_netzp_blog', '_routeScope' => ['store-api']])]
    public function load($navigationId, Criteria $criteria, SalesChannelContext $salesChannelContext,
                         ?string $categoryId, ?string $authorId, ?array $tags, ?string $sortOrder): BlogListingRouteResponse
    {
        $posts = $this->helper->getBlogPosts(
            $criteria, $salesChannelContext,
            $categoryId, $authorId, $tags, $sortOrder);

        return new BlogListingRouteResponse($posts);
    }
}
