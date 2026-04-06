<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1608909056BlogAddSeoTemplate extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1608909056;
    }

    public function update(Connection $connection): void
    {
        $connection->insert(
            'seo_url_template',
            [
                'id'            => Uuid::randomBytes(),
                'route_name'    => 'frontend.blog.post',
                'entity_name'   => 's_plugin_netzp_blog',
                'template'      => 'blog/{{ blogpost.translated.slug }}',
                'is_valid'      => 1,
                'created_at'    => date(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]
        );
        $this->registerIndexer($connection, 'netzp.blogpost.indexer');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
