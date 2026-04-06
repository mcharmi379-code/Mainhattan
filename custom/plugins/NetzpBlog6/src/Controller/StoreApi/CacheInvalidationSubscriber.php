<?php declare(strict_types=1);

namespace NetzpBlog6\Controller\StoreApi;

use NetzpBlog6\Controller\StoreApi\BlogListing\CachedBlogListingRoute;
use NetzpBlog6\Controller\StoreApi\BlogPost\CachedBlogPostRoute;
use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use NetzpBlog6\Resolver\BlogElementResolver;
use Shopware\Core\Content\Category\Event\CategoryRouteCacheKeyEvent;
use Shopware\Core\Content\Cms\Aggregate\CmsSlotTranslation\CmsSlotTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;

class CacheInvalidationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheInvalidator $cacheInvalidator,
                                private readonly EntityRepository $cmsSlotsRepository,
                                private readonly EntityRepository $categoryRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => [ ['invalidate', 2001] ],
            CategoryRouteCacheKeyEvent::class  => 'onCategoryRouteCacheKey'
        ];
    }

    public function onCategoryRouteCacheKey(CategoryRouteCacheKeyEvent $event): void
    {
        $criteria = (new Criteria([$event->getNavigationId()]));
        $criteria->addAssociation('cmsPage.sections.blocks.slots.config');
        $category = $this->categoryRepository->search($criteria, $event->getContext()->getContext())->getEntities()->first();
        if( ! $category) {
            return;
        }
        $page = $category->getCmsPage();
        if ( ! $page) {
            return;
        }

        $blogElements = $page->getElementsOfType(BlogElementResolver::ELEMENT_TYPE);
        if(! empty($blogElements))
        {
            $customerId = $event->getContext()->getCustomer()?->getId();
            $customerGroupId = $event->getContext()->getCustomer()?->getGroupId();
            if ($customerId) {
                $event->addPart('customer_' . $customerId);
            }
            if ($customerGroupId) {
                $event->addPart('customergroup_' . $customerGroupId);
            }
        }
    }

    public function invalidate(EntityWrittenContainerEvent $event): void
    {
        $changesPost = $event->getPrimaryKeys(BlogDefinition::ENTITY_NAME);
        $changesCmsSlots = $event->getPrimaryKeys(CmsSlotTranslationDefinition::ENTITY_NAME);
        $changesNavigation = $event->getPrimaryKeys('category');

        $mustInvalidateBlogListing = false;

        if ( ! empty($changesPost)) {
            $blogPostRoutes = [];
            foreach ($changesPost as $postId) {
                $blogPostRoutes[] = CachedBlogPostRoute::buildName($postId);
            }

            $this->cacheInvalidator->invalidate($blogPostRoutes);
            $mustInvalidateBlogListing = true;
        }

        if ( ! empty($changesNavigation)) {
            $blogListingRoutes = [];
            foreach ($changesNavigation as $navigationId) {
                $blogListingRoutes[] = CachedBlogListingRoute::buildName($navigationId);
            }

            $this->cacheInvalidator->invalidate($blogListingRoutes);
        }

        if( ! empty($changesCmsSlots)) {
            $cmsSlotIds = array_map(function($item) {
                if(is_array($item) && array_key_exists('cmsSlotId', $item)) {
                    return $item['cmsSlotId'];
                }
                return $item;
            }, $changesCmsSlots);

            if( ! empty($cmsSlotIds)) {
                $criteriaSlots = new Criteria($cmsSlotIds);
                $slots = $this->cmsSlotsRepository->search($criteriaSlots, $event->getContext())->getEntities();

                foreach($slots as $slot) {
                    if($slot->getType() == BlogElementResolver::ELEMENT_TYPE) {
                        $mustInvalidateBlogListing = true;
                        break;
                    }
                }
            }
        }

        if($mustInvalidateBlogListing) {
            $this->cacheInvalidator->invalidate([
                CachedBlogListingRoute::buildName('')
            ]);
        }
    }
}
