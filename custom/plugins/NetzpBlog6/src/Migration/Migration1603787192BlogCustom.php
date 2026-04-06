<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1603787192BlogCustom extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1603787192;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `s_plugin_netzp_blog_translation`
        ADD `custom` longtext DEFAULT NULL;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
