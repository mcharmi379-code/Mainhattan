<?php declare(strict_types=1);

namespace NetzpBlog6\Controller;

use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use NetzpBlog6\Storefront\Page\BlogPageLoader;
use NetzpBlog6\Helper\BlogHelper;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends StorefrontController
{
    public function __construct(private readonly BlogHelper $helper,
                                private readonly BlogPageLoader $blogPageLoader,
                                private readonly SystemConfigService $config)
    {
    }

    #[Route(path: '/blog.rss', name: 'frontend.blog.feed', methods: ['GET'], defaults: ['_routeScope' => ['storefront']])]
    public function getFeed(Request $request, SalesChannelContext $salesChannelContext, Context $context)
    {
        $posts = $this->helper->getPublicBlogPosts($salesChannelContext, $context, null, null, true);

        $response = $this->renderStorefront('storefront/page/blog/feed.html.twig', [
            'posts' => $posts
        ]);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    #[Route(path: '/blog/{postId}', name: 'frontend.blog.post', methods: ['GET'], defaults: ['_routeScope' => ['storefront']])]
    public function getPost(Request $request, SalesChannelContext $salesChannelContext, Context $context, $postId)
    {
        $shariffIsActive = $this->helper->isPluginActive('NetzpShariff6', $context);
        $config = $this->config->get('NetzpBlog6.config', $salesChannelContext->getSalesChannel()->getId());

        $page = $this->blogPageLoader->load($request, $salesChannelContext, $postId);
        $template = $page->isCmsPage() ? 'storefront/page/blog/cms-post.html.twig' : 'storefront/page/blog/post.html.twig';

        return $this->renderStorefront($template, [
            'page'               => $page,
            'post'               => $page->getPost(),
            'config'             => $config,
            'netzpShariffActive' => $shariffIsActive
        ]);
    }
}
