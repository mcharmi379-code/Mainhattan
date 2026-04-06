<?php declare(strict_types=1);

namespace NetzpBlog6\SeoUrlRoute;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlogPostUpdater implements EventSubscriberInterface
{
    public function __construct(private readonly SeoUrlUpdater $seoUrlUpdater)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            's_plugin_netzp_blog.written' => 'blogPostWritten',
            's_plugin_netzp_blog.deleted' => 'blogPostDeleted',
            BlogPostIndexerEvent::class   => 'handleIndexerEvent'
        ];
    }

    public function blogPostWritten(EntityWrittenEvent $event)
    {
        $this->seoUrlUpdater->update(BlogPostSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    public function blogPostDeleted(EntityDeletedEvent $event)
    {
        $this->seoUrlUpdater->update(BlogPostSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    public function handleIndexerEvent(BlogPostIndexerEvent $event)
    {
        $this->seoUrlUpdater->update(BlogPostSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }
}
