<?php declare(strict_types=1);

namespace NetzpBlog6\Helper;

use NetzpBlog6\Core\Content\Blog\BlogCollection;
use NetzpBlog6\Storefront\Page\BlogPage;
use Shopware\Core\Content\Sitemap\Provider\AbstractUrlProvider;
use Shopware\Core\Content\Sitemap\Struct\Url;
use Shopware\Core\Content\Sitemap\Struct\UrlResult;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\DBAL\Connection;

class SitemapProvider extends AbstractUrlProvider
{
    final public const CHANGE_FREQ = 'daily';
    final public const PRIORITY = 1.0;

    public function __construct(private readonly RouterInterface $router,
                                private readonly Connection $connection,
                                private readonly BlogHelper $helper,
                                private readonly SystemConfigService $config)
    {
    }

    public function getDecorated(): AbstractUrlProvider
    {
        throw new DecorationPatternException(self::class);
    }

    public function getName(): string
    {
        return 'netzp_blog6';
    }

    public function getUrls(SalesChannelContext $context, int $limit, ?int $offset = null): UrlResult
    {
        $excludeFromSitemap = $this->config->get(
            'NetzpBlog6.config.excludesitemap',
            $context->getSalesChannelId()
        );

        if ($excludeFromSitemap)
        {
            return new UrlResult([], null);
        }

        $posts = $this->getPublicBlogPosts($context, $limit, $offset);

        if ($posts->count() === 0)
        {
            return new UrlResult([], null);
        }

        $seoUrls = $this->getSeoUrls($posts->getIds(), 'frontend.blog.post', $context, $this->connection);
        $seoUrls = FetchModeHelper::groupUnique($seoUrls);

        $urls = [];
        foreach ($posts as $post)
        {
            $url = new Url();
            $url->setLastmod($post->getUpdatedAt() ?? new \DateTime());
            $url->setChangefreq(self::CHANGE_FREQ);
            $url->setPriority(self::PRIORITY);
            $url->setResource(BlogPage::class);
            $url->setIdentifier($post->getId());

            if (isset($seoUrls[$post->getId()]))
            {
                $url->setLoc($seoUrls[$post->getId()]['seo_path_info']);
            }
            else
            {
                $url->setLoc(
                    $this->router->generate(
                        'frontend.blog.post',
                        ['postId' => $post->getId()],
                        UrlGeneratorInterface::ABSOLUTE_PATH
                    )
                );
            }

            $urls[] = $url;
        }

        if (\count($urls) < $limit)
        { // last run
            $nextOffset = null;
        }
        elseif ($offset === null)
        { // first run
            $nextOffset = $limit;
        }
        else
        { // 1+n run
            $nextOffset = $offset + $limit;
        }

        return new UrlResult($urls, $nextOffset);
    }

    private function getPublicBlogPosts(SalesChannelContext $salesChannelContext,
                                        int $limit, ?int $offset): BlogCollection
    {
        return $this->helper->getPublicBlogPosts(
            $salesChannelContext,
            $salesChannelContext->getContext(),
            $limit,
            $offset
        );
    }
}
