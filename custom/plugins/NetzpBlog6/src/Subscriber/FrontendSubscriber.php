<?php declare(strict_types=1);

namespace NetzpBlog6\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ArrayParameterType;
use Shopware\Core\Content\Media\Event\UnusedMediaSearchEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Struct\StructCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use NetzpBlog6\Core\SearchResult;
use NetzpBlog6\Helper\BlogHelper;

class FrontendSubscriber implements EventSubscriberInterface
{
    final public const SEARCH_TYPE_BLOG = 10;

    public function __construct(private readonly SystemConfigService $config,
                                private readonly Connection $connection,
                                private readonly EntityRepository $productRepository,
                                private readonly EntityRepository $blogRepository,
                                private readonly BlogHelper $helper)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageCriteriaEvent::class    => 'onProductCriteriaLoaded',
            ProductPageLoadedEvent::class      => 'loadProductPage',
            UnusedMediaSearchEvent::class      => 'removeUnusedMedia',
            'netzp.search.register'            => 'registerSearchProvider'
        ];
    }

    public function onProductCriteriaLoaded(ProductPageCriteriaEvent $event): void
    {
        if( ! (bool)$this->config->get('NetzpBlog6.config',
                                        $event->getSalesChannelContext()->getSalesChannelId())['showblogposts']) {
            return;
        }

        $this->addBlogCriteria($event->getCriteria(), $event->getSalesChannelContext()->getSalesChannelId());
    }

    public function loadProductPage(ProductPageLoadedEvent $event): void
    {
        if( ! (bool)$this->config->get('NetzpBlog6.config',
                                        $event->getSalesChannelContext()->getSalesChannelId())['showblogposts']) {
            return;
        }

        $product = $event->getPage()->getProduct();
        $productBlogs = $product->getExtension('blogs');
        $parentId = $product->getParentId();

        if($parentId)
        {
            $criteria = new Criteria([$parentId]);
            $this->addBlogCriteria($criteria, $event->getSalesChannelContext()->getSalesChannelId());
            $parentProduct = $this->productRepository->search($criteria, $event->getContext())->getEntities()->first();
            $parentBlogs = $parentProduct->getExtension('blogs');

            if($parentBlogs && $parentBlogs->count() > 0)
            {
                $product->addExtension(
                    'blogs',
                    new StructCollection(
                        array_merge(
                            $parentBlogs->getElements(),
                            $productBlogs->getElements()
                        )
                    )
                );
            }
        }
    }

    private function addBlogCriteria(Criteria $criteria, string $salesChannelId)
    {
        $criteria->addAssociation('blogs');
        $blogAssociation = $criteria->getAssociation('blogs');
        $blogAssociation->addAssociation('tags');
        $blogAssociation->addAssociation('items');
        $blogAssociation->addAssociation('blogmedia');

        $this->helper->addBlogDateFilterAndSorting($blogAssociation, true);

        /*
         * NE 26.09.23 - removed - caused problems (very long query > 10s) on trendhosting.ch (#14962)
         *               moved filtering to views (
        $blogAssociation->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('category.saleschannelid', null),
            new EqualsFilter('category.saleschannelid', '00000000000000000000000000000000'),
            new EqualsFilter('category.saleschannelid', $salesChannelId),
        ]));
        */
    }

    public function registerSearchProvider(Event $event)
    {
        if( ! (bool)$this->config->get('NetzpBlog6.config',
                                        $event->getContext()->getSalesChannelId())['searchBlog']) {
            return;
        }

        $event->setData([
            'key'      => 'blog',
            'label'    => 'netzp.blog.searchLabel',
            'function' => [$this, 'doSearch']
        ]);
    }

    public function doSearch(string $query, SalesChannelContext $salesChannelContext,
                             bool $isSuggest = false,
                             bool $andLogic = false,
                             array $excludedTerms = []): array
    {
        $results = [];
        $blogEntries = $this->getBlogEntries($query, $salesChannelContext, $isSuggest, $andLogic, $excludedTerms);

        if($blogEntries) {
            foreach ($blogEntries->getEntities() as $blogEntry) {
                $tmpResult = new SearchResult();
                $tmpResult->setType(static::SEARCH_TYPE_BLOG);
                $tmpResult->setId($blogEntry->getId());
                $tmpResult->setTitle($blogEntry->getTranslated()['title'] ?? '');
                $tmpResult->setDescription($blogEntry->getTranslated()['teaser'] ?? '');

                if ($blogEntry->getImagepreview()) {
                    $tmpResult->setMedia($blogEntry->getImagepreview());
                } elseif ($blogEntry->getImage()) {
                    $tmpResult->setMedia($blogEntry->getImage());
                }

                $tmpResult->setTotal($blogEntries->getTotal());
                $tmpResult->addExtension(
                    'slug',
                    new ArrayStruct(['value' => $blogEntry->getTranslated()['slug'] ?? ''])
                );

                $results[] = $tmpResult;
            }
        }

        return $results;
    }

    private function getBlogEntries($query, SalesChannelContext $salesChannelContext,
                                    bool $isSuggest = false,
                                    bool $andLogic = false,
                                    array $excludedTerms = [])
    {
        $words = $this->explodeSearchQuery($query, $andLogic, $excludedTerms);

        if (count($words) > 0)
        {
            $criteria = new Criteria();
            $criteria->addAssociation('category');
            $criteria->addAssociation('image');
            $criteria->addAssociation('imagepreview');

            $this->helper->addBlogDateFilterAndSorting($criteria, true);
            $this->helper->addRestrictionsFilter($criteria, $salesChannelContext);

            if($isSuggest) {
                $criteria->setLimit(10);
                $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
            }

            $filter = [];
            foreach ($words as $word) {
                $filter[] = new ContainsFilter('title', $word);
                $filter[] = new ContainsFilter('teaser', $word);
                $filter[] = new ContainsFilter('contents', $word);
            }
            $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, $filter));

            return $this->blogRepository->search($criteria, $salesChannelContext->getContext());
        }

        return null;
    }

    private function explodeSearchQuery(string $query, bool $andLogic = false, array $excludedTerms = []): array
    {
        $query = trim((string)$query);

        if($andLogic)
        {
            return [ $query ];
        }
        elseif(str_starts_with($query, '"') && str_ends_with($query, '"'))
        {
            return [ trim($query, '"') ];
        }

        $searchTerms = [];
        foreach(explode(' ', $query) as $term)
        {
            if(! in_array($term, $excludedTerms))
            {
                $searchTerms[] = $term;
            }
        }

        return $searchTerms;
    }

    public function removeUnusedMedia(UnusedMediaSearchEvent $event): void
    {
        $doNotDeleteTheseIds = $this->getUsedMediaIds($event->getUnusedIds());
        $event->markAsUsed($doNotDeleteTheseIds);
    }

    private function getUsedMediaIds(array $mediaIds): array
    {
        $sql = 'SELECT LOWER(HEX(imageid)) AS id1, LOWER(HEX(imagepreviewid)) AS id2
                  FROM s_plugin_netzp_blog
                 WHERE LOWER(hex(imageid)) IN (:ids)
                    OR LOWER(hex(imagepreviewid)) IN (:ids)

                 UNION
                SELECT LOWER(HEX(imageid)) AS id1, NULL AS id2
                  FROM s_plugin_netzp_blog_author
                 WHERE LOWER(hex(imageid)) IN (:ids)

                 UNION
                SELECT LOWER(HEX(image_id)) AS id1, NULL AS id2
                  FROM s_plugin_netzp_blog_item
                 WHERE LOWER(hex(image_id)) IN (:ids)

                 UNION
                SELECT LOWER(HEX(media_id)) AS id1, NULL AS id2
                  FROM s_plugin_netzp_blog_media
                 WHERE LOWER(hex(media_id)) IN (:ids)
        ';

        $foundIds = $this->connection->fetchAllAssociative(
            $sql,
            ['ids' => $mediaIds],
            ['ids' => ArrayParameterType::STRING]
        );

        $idsToKeep = [];
        foreach($foundIds as $thisId)
        {
            if($thisId['id1']) {
                $idsToKeep[] = $thisId['id1'];
            }
            if($thisId['id2']) {
                $idsToKeep[] = $thisId['id2'];
            }
        }

        return array_unique($idsToKeep);
    }
}
