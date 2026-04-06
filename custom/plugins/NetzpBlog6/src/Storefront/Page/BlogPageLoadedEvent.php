<?php declare(strict_types=1);

namespace NetzpBlog6\Storefront\Page;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class BlogPageLoadedEvent extends PageLoadedEvent
{
    /**
     * @var BlogPage
     */
    protected $page;

    public function __construct(BlogPage $page, SalesChannelContext $salesChannelContext, Request $request)
    {
        $this->page = $page;
        parent::__construct($salesChannelContext, $request);
    }

    public function getPage(): BlogPage
    {
        return $this->page;
    }
}
