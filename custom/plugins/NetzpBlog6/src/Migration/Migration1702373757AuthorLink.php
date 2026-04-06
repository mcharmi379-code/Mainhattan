<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1702373757AuthorLink extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1702373757;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `s_plugin_netzp_blog_author`
            ADD `link` varchar(255) DEFAULT NULL;
SQL;
        try {
            $connection->executeStatement($sql);
        }
        catch (\Exception) { }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
