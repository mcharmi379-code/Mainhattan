<?php declare(strict_types=1);

namespace NetzpBlog6\SeoUrlRoute;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlogPostIndexer extends EntityIndexer
{
    public function __construct(private readonly IteratorFactory $iteratorFactory,
                                private readonly EntityRepository $blogRepository,
                                private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getName(): string
    {
        return 'netzp.blogpost.indexer';
    }

    public function iterate(?array $offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->blogRepository->getDefinition(), $offset);
        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new BlogPostIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $updates = $event->getPrimaryKeys(BlogDefinition::ENTITY_NAME);
        if (empty($updates)) {
            return null;
        }

        return new BlogPostIndexingMessage(array_values($updates), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();
        $ids = array_unique(array_filter($ids));

        if (empty($ids)) {
            return;
        }

        $this->eventDispatcher->dispatch(new BlogPostIndexerEvent($ids, $message->getContext()));
    }

    public function getTotal(): int
    {
        return $this->iteratorFactory->createIterator($this->blogRepository->getDefinition())->fetchCount();
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(static::class);
    }
}
