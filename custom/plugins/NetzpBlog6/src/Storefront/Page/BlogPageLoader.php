<?php declare(strict_types=1);

namespace NetzpBlog6\Storefront\Page;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use NetzpBlog6\Controller\StoreApi\BlogPost\AbstractBlogPostRoute;
use NetzpBlog6\Core\Content\Blog\BlogEntity;

class BlogPageLoader
{
    public function __construct(private readonly GenericPageLoaderInterface $genericPageLoader,
                                private readonly SalesChannelCmsPageLoaderInterface $cmsPageLoader,
                                private readonly EventDispatcherInterface $eventDispatcher,
                                private readonly AbstractBlogPostRoute $blogRoute,
                                private readonly SystemConfigService $config,
                                private readonly EntityRepository $categoryRepository
    )
    {
    }

    public function load(Request $request, SalesChannelContext $context, string $postId): BlogPage
    {
        $criteria = new Criteria([$postId]);
        $post = $this->blogRoute->load($postId, $criteria, $context)->getBlogPost();
        if(! $post) {
            throw new NotFoundHttpException('Blog post not found');
        }

        $category = $post->getCategory();
        if($category && $category->getCmspageid()) {
            $cmsPageId = $category->getCmspageid();
        }
        else {
            $cmsPageId = $this->config->get(
                'NetzpBlog6.config.cmspage', $context->getSalesChannelId()
            );
        }

        $navigationCategoryId = $this->config->get(
            'NetzpBlog6.config.navigationcategory', $context->getSalesChannelId()
        );
        if($category && $category->getNavigationcategoryid())
        {
            $navigationCategoryId = $category->getNavigationcategoryid();
        }
        $navigationCategory = $this->getNavigationCategory($navigationCategoryId, $context->getContext());

        if($cmsPageId !== null || $cmsPageId != '') {
            $page = $this->loadCmsPage($request, $context, $cmsPageId);
        }
        else {
            $page = $this->loadNormalPage($request, $context);
        }

        $page->setPost($post);
        $page->setMetaInformation($this->getMetaInformation($post));
        $page->setCategory($navigationCategory); // for breadcrumb

        $this->eventDispatcher->dispatch(
            new BlogPageLoadedEvent($page, $context, $request)
        );

        return $page;
    }

    private function loadCmsPage(Request $request,
                                 SalesChannelContext $salesChannelContext,
                                 $cmsPageId)
    {
        $cmsPage = $this->cmsPageLoader->load($request, new Criteria([$cmsPageId]), $salesChannelContext)->first();
        $page = BlogPage::createFrom($this->genericPageLoader->load($request, $salesChannelContext));

        $page->setCmsPage($cmsPage);

        return $page;
    }

    private function loadNormalPage(Request $request,
                                    SalesChannelContext $salesChannelContext)
    {
        $page = $this->genericPageLoader->load($request, $salesChannelContext);
        $page = BlogPage::createFrom($page);

        return $page;
    }

    private function getMetaInformation(BlogEntity $post)
    {
        $meta = new MetaInformation();

        $meta->setMetaTitle($post->getTranslation('metatitle') ?? $post->getTranslation('title'));
        $meta->setMetaDescription($post->getTranslation('metadescription') ?? '');

        if ($post->getAuthor()) {
            $meta->setAuthor($post->getAuthor()->getTranslation('name') ?? '');
        }

        if ($post->getNoindex()) {
            $meta->setRobots('noindex, nofollow');
        }
        else {
            $meta->setRobots('all');
        }

        return $meta;
    }

    private function getNavigationCategory(?string $categoryId, Context $context)
    {
        if( ! $categoryId)
        {
            return null;
        }

        return $this->categoryRepository->search(new Criteria([$categoryId]), $context)
            ?->getEntities()
            ?->first();
    }
}
