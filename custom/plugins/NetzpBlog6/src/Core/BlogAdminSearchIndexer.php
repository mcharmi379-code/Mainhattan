<?php declare(strict_types=1);

namespace NetzpBlog6\Core;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Elasticsearch\Admin\Indexer\AbstractAdminIndexer;
use NetzpBlog6\Core\Content\Blog\BlogDefinition;

class BlogAdminSearchIndexer extends AbstractAdminIndexer
{
    public function __construct(private readonly Connection $connection,
                                private readonly IteratorFactory $factory,
                                private readonly EntityRepository $repository,
                                private readonly int $indexingBatchSize)
    {
        //
    }

    public function getDecorated(): AbstractAdminIndexer
    {
        throw new DecorationPatternException(self::class);
    }

    public function getEntity(): string
    {
        return BlogDefinition::ENTITY_NAME;
    }

    public function getName(): string
    {
        return 's_plugin_netzp_blog';
    }

    public function getIterator(): IterableQuery
    {
        return $this->factory->createIterator($this->getEntity(), null, $this->indexingBatchSize);
    }

    public function globalData(array $result, Context $context): array
    {
        $ids = array_column($result['hits'], 'id');

        return [
            'total' => (int) $result['total'],
            'data' => $this->repository->search(new Criteria($ids), $context)->getEntities(),
        ];
    }

    public function fetch(array $ids): array
    {
        $data = $this->connection->fetchAllAssociative('
            SELECT LOWER(HEX(s_plugin_netzp_blog.id)) as id,
                   GROUP_CONCAT(DISTINCT s_plugin_netzp_blog_translation.title) AS title
              FROM s_plugin_netzp_blog
             INNER JOIN s_plugin_netzp_blog_translation
                     ON s_plugin_netzp_blog.id = s_plugin_netzp_blog_translation.s_plugin_netzp_blog_id
             WHERE s_plugin_netzp_blog.id IN (:ids)
             GROUP BY s_plugin_netzp_blog.id
            ',
            ['ids' => Uuid::fromHexToBytesList($ids)],
            ['ids' => ArrayParameterType::BINARY]
        );

        $mapped = [];
        foreach ($data as $row)
        {
            $id = (string)$row['id'];
            $text = \implode(' ', array_filter($row));
            $mapped[$id] = ['id' => $id, 'text' => \strtolower($text)];
        }

        return $mapped;
    }
}
