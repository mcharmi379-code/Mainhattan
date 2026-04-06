<?php declare(strict_types=1);

namespace NetzpBlog6\SeoUrlRoute;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class BlogPostSeoUrlRoute implements SeoUrlRouteInterface
{
    final public const ROUTE_NAME = 'frontend.blog.post';
    final public const DEFAULT_TEMPLATE = 'blog/{{ blogpost.translated.slug }}';

    public function __construct(private readonly BlogDefinition $blogDefinition)
    {
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->blogDefinition,
            self::ROUTE_NAME,
            self::DEFAULT_TEMPLATE
        );
    }

    public function prepareCriteria(Criteria $criteria, SalesChannelEntity $salesChannel): void
    {
    }

    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        if ( ! $entity instanceof BlogEntity) {
            throw new \InvalidArgumentException('Expected BlogEntity');
        }

        return new SeoUrlMapping(
            $entity,
            ['postId' => $entity->getId()],
            ['blogpost' => $entity->jsonSerialize()]
        );
    }

    public function getSeoVariables(): array
    {
        return [];
    }
}
