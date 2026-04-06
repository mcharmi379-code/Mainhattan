<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1616854566BlogNoIndex extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1616854566;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `s_plugin_netzp_blog`
            ADD `noindex` tinyint(1) NOT NULL DEFAULT 0;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
