<?php
namespace NetzpBlog6\Helper;

use NetzpBlog6\Core\Content\Blog\BlogEntity;
use Shopware\Core\Content\ProductStream\Service\ProductStreamBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Grouping\FieldGrouping;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogHelper
{
    final public const MAX_PRODUCTSTREAM_ITEMS = 25; // make sure not to load too many products for performance reasons
    final public const EMPTY_UUID = '00000000000000000000000000000000';

    public function __construct(private readonly ProductStreamBuilderInterface $productStreamBuilder,
                                private readonly EntityRepository $productRepository,
                                private readonly SalesChannelRepository $salesChannelProductRepository,
                                private readonly EntityRepository $pluginRepository,
                                private readonly EntityRepository $blogRepository)
    {
    }

    public function getPublicBlogPosts(SalesChannelContext $salesChannelContext,
                                       Context $context, $limit = null, $offset = null,
                                       $rssCategoryOnly = false)
    {
        $criteria = new Criteria();
        $this->addBlogDateFilterAndSorting($criteria, true);
        $criteria->addAssociation('category');
        $criteria->addAssociation('tags');
        $criteria->addAssociation('items');
        $criteria->addAssociation('blogmedia');

        $criteria->addFilter(new EqualsFilter('noindex', false));

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('categoryid', self::EMPTY_UUID),
            new EqualsFilter('category.onlyloggedin', false),
        ]));

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('category.saleschannelid', null),
            new EqualsFilter('category.saleschannelid', self::EMPTY_UUID),
            new EqualsFilter('category.saleschannelid', $salesChannelContext->getSalesChannel()->getId()),
        ]));

        if($rssCategoryOnly) {
            $criteria->addFilter(new EqualsFilter('category.includeinrss', true));
        }

        $criteria->addSorting(new FieldSorting('items.number', FieldSorting::ASCENDING));
        $criteria->addSorting(new FieldSorting('blogmedia.number', FieldSorting::ASCENDING));

        if($limit) {
            $criteria->setLimit($limit);
        }
        if($offset) {
            $criteria->setOffset($offset);
        }
        return $this->blogRepository->search($criteria, $context)->getEntities();
    }

    public function isPluginActive($pluginName, Context $context)
    {
        $pluginCriteria = new Criteria();
        $pluginCriteria->addFilter(new EqualsFilter('name', $pluginName));
        $plugin = $this->pluginRepository->search($pluginCriteria, $context)->getEntities()->first();

        return $plugin && $plugin->getActive();
    }

    public function getBlogPosts(Criteria $criteria, SalesChannelContext $salesChannelContext,
                                 ?string $categoryId, ?string $authorId, ?array $tags, ?string $sortOrder)
    {
        $criteria->addAssociation('category');
        $criteria->addAssociation('categories');
        $criteria->addAssociation('tags');

        $this->addBlogDateFilterAndSorting($criteria, false); // without sorting
        $this->addRestrictionsFilter($criteria, $salesChannelContext);

        if($categoryId && $categoryId != self::EMPTY_UUID) {
            $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsFilter('categoryid', $categoryId),
                new EqualsAnyFilter('categories.id', [$categoryId]),
            ]));
        }

        if($authorId && $authorId != self::EMPTY_UUID) {
            $criteria->addFilter(
                new EqualsFilter('authorid', $authorId)
            );
        }

        if($tags != null && is_array($tags) && count($tags) > 0) {
            $criteria->addFilter(new EqualsAnyFilter('tags.id', $tags));
        }

        $criteria->addSorting(new FieldSorting('sticky', FieldSorting::DESCENDING));
        $criteria->addSorting(new FieldSorting(
                'postdate', $sortOrder == 'asc' ? FieldSorting::ASCENDING : FieldSorting::DESCENDING)
        );
        $criteria->addSorting(new FieldSorting('title', FieldSorting::ASCENDING));

        return $this->blogRepository->search($criteria, $salesChannelContext->getContext());
    }

    public function getBlogPost($postId, SalesChannelContext $salesChannelContext, Context $context)
    {
        if( ! Uuid::isValid($postId)) {
            throw new NotFoundHttpException('No valid UUID');
        }

        $criteria = new Criteria();
        $criteria->addAssociation('products.event');
        $criteria->addAssociation('category');
        $criteria->addAssociation('categories');
        $criteria->addAssociation('tags');
        $criteria->addAssociation('items');
        $criteria->getAssociation('items')->addSorting(new FieldSorting('number', FieldSorting::ASCENDING));
        $criteria->addAssociation('blogmedia');
        $criteria->getAssociation('blogmedia')->addSorting(new FieldSorting('number', FieldSorting::ASCENDING));
        $criteria->addAssociation('blogmedia.media');

        $criteria->addFilter(new EqualsFilter('id', $postId));

        $this->addRestrictionsFilter($criteria, $salesChannelContext);
        $this->addBlogDateFilterAndSorting($criteria);

        $post = $this->blogRepository->search($criteria, $context)->getEntities()->first();
        if( ! $post) {
            throw new NotFoundHttpException('Blog post not found');
        }

        $assignedProducts = [];
        $tmpProducts = [];
        if($post->getIsProductStream() && $post->getProductStreamId() != null) {
            try {
                $tmpProducts = $this->collectByProductStream($salesChannelContext, $post);
            }
            catch(\Exception) {
                //
            }
        }
        else {
            $tmpProducts = $post->getProducts();
        }

        foreach ($tmpProducts as $product)
        {
            array_push($assignedProducts, $product->getId());
        }

        if(count($assignedProducts) > 0)
        {
            $criteria2 = new Criteria($assignedProducts);
            $criteria2->addAssociation('cover');
            $criteria2->addAssociation('event');

            $products = $this->salesChannelProductRepository->search($criteria2, $salesChannelContext)->getEntities();

            $post->setProducts($products);
        }

        return $post;
    }

    public function addRestrictionsFilter(Criteria $criteria, SalesChannelContext $salesChannelContext)
    {
        $userLoggedIn = $salesChannelContext->getCustomer() != null;
        $userCustomerGroupId = $salesChannelContext->getCurrentCustomerGroup()->getId();

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('category.saleschannelid', null),
            new EqualsFilter('category.saleschannelid', self::EMPTY_UUID),
            new EqualsFilter('category.saleschannelid', $salesChannelContext->getSalesChannel()->getId()),
        ]));

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('categoryid', self::EMPTY_UUID),
            new EqualsFilter('category.onlyloggedin', false),

            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('category.onlyloggedin', true),
                new EqualsFilter('category.onlyloggedin', $userLoggedIn)
            ])
        ]));

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('category.customergroupid', null),
            new EqualsFilter('category.customergroupid', self::EMPTY_UUID),

            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new NotFilter(NotFilter::CONNECTION_AND, [
                    new EqualsFilter('category.customergroupid', self::EMPTY_UUID)
                ]),
                new EqualsFilter('category.customergroupid', $userCustomerGroupId),
            ])
        ]));
    }

    public function addBlogDateFilterAndSorting(Criteria $criteria, bool $addSorting = false)
    {
        $now = (new \DateTime())->format('Y-m-d');
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_OR, [
                    new RangeFilter('showfrom', ['lte' => $now]),
                    new EqualsFilter('showfrom', null)
                ]
            )
        );
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_OR, [
                    new RangeFilter('showuntil', ['gte' => $now]),
                    new EqualsFilter('showuntil', null)
                ]
            )
        );

        if($addSorting) {
            $criteria->addSorting(new FieldSorting('sticky', FieldSorting::DESCENDING));
            $criteria->addSorting(new FieldSorting('postdate', 'desc'));
            $criteria->addSorting(new FieldSorting('title', FieldSorting::ASCENDING));
        }
    }

    private function collectByProductStream(SalesChannelContext $salesChannelContext, BlogEntity $blogPost): EntityCollection
    {
        if (!$blogPost->getIsProductStream() || $blogPost->getProductStreamId() == null) {
            return new EntityCollection();
        }

        $filters = $this->productStreamBuilder->buildFilters($blogPost->getProductStreamId(), $salesChannelContext->getContext());

        $criteria = new Criteria();
        $criteria->addAssociation('options.group');
        $criteria->addFilter(...$filters);
        $criteria->setLimit(self::MAX_PRODUCTSTREAM_ITEMS);

        // Ensure storefront presentation settings of product variants, see ProductSliderCmsElementResolver
        $criteria->addGroupField(new FieldGrouping('displayGroup'));
        $criteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_AND,
                [new EqualsFilter('displayGroup', null)]
            )
        );

        $criteria->addSorting(new FieldSorting('name', FieldSorting::ASCENDING));

        return $this->salesChannelProductRepository->search($criteria, $salesChannelContext)->getEntities();
    }
}
