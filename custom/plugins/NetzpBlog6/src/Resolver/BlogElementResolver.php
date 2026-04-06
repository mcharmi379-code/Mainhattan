<?php
namespace NetzpBlog6\Resolver;

use NetzpBlog6\Controller\StoreApi\BlogListing\AbstractBlogListingRoute;
use NetzpBlog6\Helper\BlogHelper;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class BlogElementResolver extends AbstractCmsElementResolver
{
    final public const ELEMENT_TYPE = 'netzp-blog6';

    public function getType(): string
    {
        return self::ELEMENT_TYPE;
    }

    public function __construct(private readonly SystemConfigService $systemConfig,
                                private readonly AbstractBlogListingRoute $blogListingRoute)
    {
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    private function getPageNumber(Request $request): int
    {
        $pageNumber = $request->query->getInt('p', 1);
        if ($request->isMethod(Request::METHOD_POST)) {
            $pageNumber = $request->query->getInt('p', $pageNumber);
        }

        return $pageNumber <= 0 ? 1 : $pageNumber;
    }

    private function setPagination(Criteria $criteria, $page, $maxNumberOfPosts)
    {
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
        $criteria->setOffset(($page - 1) * $maxNumberOfPosts);
        $criteria->setLimit($maxNumberOfPosts);
    }

    private function setCategoryFilter(Criteria $criteria, Request $request)
    {
        $criteria->addAggregation(
            new EntityAggregation('categories', 'category.id', 's_plugin_netzp_blog_category')
        );

        $ids = $this->getCategoryIds($request);
        if (empty($ids)) {
            return;
        }

        $criteria->addPostFilter(
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsAnyFilter('category.id', $ids),
                new EqualsAnyFilter('categories.id', $ids)
            ])
        );
    }

    private function setAuthorFilter(Criteria $criteria, Request $request)
    {
        $criteria->addAggregation(
            new EntityAggregation('authors', 'author.id', 's_plugin_netzp_blog_author')
        );

        $ids = $this->getAuthorIds($request);
        if (empty($ids)) {
            return;
        }

        $criteria->addPostFilter(
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsAnyFilter('author.id', $ids)
            ])
        );
    }

    private function setTagsFilter(Criteria $criteria, Request $request)
    {
        $criteria->addAggregation(
            new EntityAggregation('tags', 'tags.id', 'tag')
        );

        $ids = $this->getTagIds($request);
        if (empty($ids)) {
            return;
        }

        $criteria->addPostFilter(
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsAnyFilter('tags.id', $ids)
            ])
        );
    }

    private function getCategoryIds(Request $request)
    {
        $ids = $request->query->get('categories', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->query->get('categories', '');
        }
        $ids = explode('|', $ids);

        return array_filter($ids);
    }

    private function getAuthorIds(Request $request)
    {
        $ids = $request->query->get('authors', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->query->get('authors', '');
        }
        $ids = explode('|', $ids);

        return array_filter($ids);
    }

    private function getTagIds(Request $request)
    {
        $ids = $request->query->get('tags', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->query->get('tags', '');
        }
        $ids = explode('|', $ids);

        return array_filter($ids);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $salesChannelId = $resolverContext->getSalesChannelContext()->getSalesChannel()->getId();
        $config = $slot->getFieldConfig();
        $pluginConfig = $this->systemConfig->get('NetzpBlog6.config', $salesChannelId);

        $criteria = new Criteria();
        $criteria->setTitle('cms::netzp-blog-listing');

        $categoryId = $config->has('category') ? $config->get('category')->getValue() : BlogHelper::EMPTY_UUID;
        $categoryId = $categoryId != null ? $categoryId : BlogHelper::EMPTY_UUID;
        $categoryId = $resolverContext->getRequest()->query->get('c', $categoryId);

        $authorId = $config->has('author') ? $config->get('author')->getValue() : BlogHelper::EMPTY_UUID;
        $authorId = $authorId != null ? $authorId : BlogHelper::EMPTY_UUID;
        $authorId = $resolverContext->getRequest()->query->get('a', $authorId);

        $tags = $config->has('tags') && $config->get('tags')->getValue() !== null ? $config->get('tags')->getValue() : [];
        $sortOrder = $config->get('sortOrder')?->getValue();
        $noPagination = $config->get('noPagination')?->getValue();
        $blogPostId = $config->get('blogPost')?->getValue();

        $maxNumberOfPosts = (int)$config->get('numberOfPosts')->getValue();
        if($maxNumberOfPosts < 1) {
            $maxNumberOfPosts = 99999; // show "all"
        }

        if($noPagination)
        {
            $this->setPagination($criteria, 1, $maxNumberOfPosts);
            $this->setSpecificBlogPost($criteria, $blogPostId, $maxNumberOfPosts);
        }
        else
        {
            $page = $this->getPageNumber($resolverContext->getRequest());

            $this->setPagination($criteria, $page, $maxNumberOfPosts);
            $this->setCategoryFilter($criteria, $resolverContext->getRequest());
            $this->setAuthorFilter($criteria, $resolverContext->getRequest());
            $this->setTagsFilter($criteria, $resolverContext->getRequest());

            $this->setCriteria($criteria, $categoryId, $authorId, $tags); // set criteria early so that criteriaHash in cached route is set
        }

        $blogListingResponse = $this->blogListingRoute->load(
            $slot->getId(),
            $criteria, $resolverContext->getSalesChannelContext(),
            $categoryId, $authorId, $tags, $sortOrder);
        $searchResult = $blogListingResponse->getPosts();

        $data = new ArrayEntity();
        $data->setUniqueIdentifier(Uuid::randomHex()); // prevent "Notice: Undefined index: id" when HTTP Cache is enabled ;-(
        $slot->setData($data);

        $data->set('result', $searchResult);
        $data->set('pluginConfig', $pluginConfig);
    }

    private function setCriteria(Criteria $criteria, ?string $categoryId, ?string $authorId, array $tags)
    {
        if($categoryId && $categoryId != BlogHelper::EMPTY_UUID) {
            $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsFilter('categoryid', $categoryId),
                new EqualsAnyFilter('categories.id', [$categoryId]),
            ]));
        }

        if($authorId && $authorId != BlogHelper::EMPTY_UUID) {
            $criteria->addFilter(
                new EqualsFilter('authorid', $authorId)
            );
        }

        if($tags != null && is_array($tags) && count($tags) > 0) {
            $criteria->addFilter(new EqualsAnyFilter('tags.id', $tags));
        }
    }

    private function setSpecificBlogPost(Criteria $criteria, ?string $blogPostId, int $numberOfPosts)
    {
        if($numberOfPosts == 1 &&
            $blogPostId && $blogPostId != BlogHelper::EMPTY_UUID)
        {
            $criteria->addFilter(
                new EqualsFilter('id', $blogPostId)
            );
        }
    }
}
