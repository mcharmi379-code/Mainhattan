<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1608909058AddCmsPageToCategory extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1608909058;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `s_plugin_netzp_blog_category`
        ADD `cmspageid` varchar(255) DEFAULT NULL;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
