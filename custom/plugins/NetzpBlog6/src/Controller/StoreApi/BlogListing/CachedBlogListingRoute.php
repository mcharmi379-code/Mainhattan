<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogListing;

use Shopware\Core\Framework\Util\Json;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Adapter\Cache\AbstractCacheTracer;
use Shopware\Core\Framework\Adapter\Cache\CacheCompressor;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Routing\Attribute\Route;

class CachedBlogListingRoute extends AbstractBlogListingRoute
{
    private array $states = [];

    public function __construct(private readonly AbstractBlogListingRoute $decorated,
                                private readonly TagAwareAdapterInterface $cache,
                                private readonly EntityCacheKeyGenerator $generator,
                                private readonly AbstractCacheTracer $tracer,
                                private readonly LoggerInterface $logger)
    {
    }

    public function getDecorated(): AbstractBlogListingRoute
    {
        return $this->decorated;
    }

    #[Route(path: '/store-api/bloglisting/{navigationId?}', name: 'store-api.s_plugin_netzp_blog_listing.load', methods: ['GET', 'POST'], defaults: ['_entity' => 's_plugin_netzp_blog', '_routeScope' => ['store-api']])]
    public function load(?string $navigationId,
                         Criteria $criteria,
                         SalesChannelContext $salesChannelContext,
                         ?string $categoryId,
                         ?string $authorId,
                         ?array $tags,
                         ?string $sortOrder): BlogListingRouteResponse
    {
        if($navigationId == null) {
            $navigationId = '';
        }

        if ($salesChannelContext->hasState(...$this->states))
        {
            return $this->getDecorated()->load(
                $navigationId,
                $criteria,
                $salesChannelContext,
                $categoryId,
                $authorId,
                $tags,
                $sortOrder);
        }

        $item = $this->cache->getItem(
            $this->generateKey($navigationId, $salesChannelContext, $criteria)
        );

        try {
            if ($item->isHit() && $item->get()) {
                return CacheCompressor::uncompress($item);
            }
        }
        catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        $name = self::buildName($navigationId);
        $response = $this->tracer->trace($name, fn() => $this->getDecorated()->load(
            $navigationId,
            $criteria,
            $salesChannelContext,
            $categoryId,
            $authorId,
            $tags,
            $sortOrder
        ));

        $item = CacheCompressor::compress($item, $response);

        $item->tag(array_merge(
            $this->tracer->get(self::buildName($navigationId)),
            [
                self::buildName($navigationId),
                self::buildName('')
            ]
        ));

        $this->cache->save($item);

        return $response;
    }

    public static function buildName(string $navigationId): string
    {
        return 'blog-listing-route-' . $navigationId;
    }

    private function generateKey(string $navigationId,
                                 SalesChannelContext $context,
                                 Criteria $criteria): string
    {
        $customerId = $context->getCustomer()?->getId();

        $parts = [
            self::buildName($navigationId),
            $this->generator->getCriteriaHash($criteria),
            $this->generator->getSalesChannelContextHash($context),
        ];

        if($customerId != null) {
            $parts[] = $customerId;
        }

        return md5(Json::encode($parts));
    }
}
