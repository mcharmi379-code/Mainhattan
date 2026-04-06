<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogPost;

use NetzpBlog6\Helper\BlogHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

class BlogPostRoute extends AbstractBlogPostRoute
{
    public function __construct(private readonly BlogHelper $helper)
    {
    }

    public function getDecorated(): never
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/blogpost/{postId}', name: 'store-api.s_plugin_netzp_blog.load', methods: ['GET', 'POST'], defaults: ['_entity' => 's_plugin_netzp_blog', '_routeScope' => ['store-api']])]
    public function load(string $postId, Criteria $criteria, SalesChannelContext $salesChannelContext): BlogPostRouteResponse
    {
        $post = $this->helper->getBlogPost($postId, $salesChannelContext, $salesChannelContext->getContext());

        return new BlogPostRouteResponse($post);
    }
}
