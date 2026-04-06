<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1615974753BlogMultipleCategories extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1615974753;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_categories` (
      `blog_id` BINARY(16) NOT NULL,
      `category_id` BINARY(16) NOT NULL,
      `created_at` DATETIME(3) NOT NULL,
      PRIMARY KEY (`blog_id`, `category_id`),
      CONSTRAINT `fk.s_plugin_netzp_blog_categories.blog_id` FOREIGN KEY (`blog_id`)
        REFERENCES `s_plugin_netzp_blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk.s_plugin_netzp_blog_categories.category_id` FOREIGN KEY (`category_id`)
        REFERENCES `s_plugin_netzp_blog_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8mb4
    COLLATE=utf8mb4_unicode_ci
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
