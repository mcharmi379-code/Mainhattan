<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1686559140BlogCanonicalUrl extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1686559140;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `s_plugin_netzp_blog`
            ADD `canonicalUrl` varchar(255) DEFAULT NULL;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
