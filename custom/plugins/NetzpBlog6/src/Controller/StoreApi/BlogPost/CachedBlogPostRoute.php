<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi\BlogPost;

use Shopware\Core\Framework\Util\Json;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Adapter\Cache\AbstractCacheTracer;
use Shopware\Core\Framework\Adapter\Cache\CacheCompressor;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class CachedBlogPostRoute extends AbstractBlogPostRoute
{
    private array $states = [];

    public function __construct(private readonly AbstractBlogPostRoute $decorated,
                                private readonly TagAwareAdapterInterface $cache,
                                private readonly EntityCacheKeyGenerator $generator,
                                private readonly AbstractCacheTracer $tracer,
                                private readonly LoggerInterface $logger)
    {
    }

    public function getDecorated(): AbstractBlogPostRoute
    {
        return $this->decorated;
    }

    #[Route(path: '/store-api/blogpost/{postId}', name: 'store-api.s_plugin_netzp_blog.load', methods: ['GET', 'POST'], defaults: ['_entity' => 's_plugin_netzp_blog', '_routeScope' => ['store-api']])]
    public function load(string $postId, Criteria $criteria, SalesChannelContext $context): BlogPostRouteResponse
    {
        if ($context->hasState(...$this->states)) {
            return $this->getDecorated()->load($postId, $criteria, $context);
        }

        $item = $this->cache->getItem(
            $this->generateKey($postId, $context, $criteria)
        );

        try {
            if ($item->isHit() && $item->get()) {
                return CacheCompressor::uncompress($item);
            }
        }
        catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        $name = self::buildName($postId);
        $response = $this->tracer->trace($name, fn() => $this->getDecorated()->load($postId, $criteria, $context));

        $item = CacheCompressor::compress($item, $response);

        $item->tag(array_merge(
            $this->tracer->get(self::buildName($postId)),
            [self::buildName($postId)]
        ));

        $this->cache->save($item);

        return $response;
    }

    public static function buildName(string $postId): string
    {
        return 'blog-post-route-' . $postId;
    }

    private function generateKey(string $postId,
                                 SalesChannelContext $context,
                                 Criteria $criteria): string
    {
        $customerId = $context->getCustomer()?->getId();

        $parts = [
            self::buildName($postId),
            $this->generator->getCriteriaHash($criteria),
            $this->generator->getSalesChannelContextHash($context)
        ];

        if($customerId != null) {
            $parts[] = $customerId;
        }

        return md5(Json::encode($parts));
    }
}
