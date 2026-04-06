<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1603787193BlogTag extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1603787193;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_tag` (
              `blog_id` BINARY(16) NOT NULL,
              `tag_id` BINARY(16) NOT NULL,
              PRIMARY KEY (`blog_id`, `tag_id`),
              CONSTRAINT `fk.s_plugin_netzp_blog_tag.blog_id` FOREIGN KEY (`blog_id`)
                REFERENCES `s_plugin_netzp_blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.s_plugin_netzp_blog_tag.tag_id` FOREIGN KEY (`tag_id`)
                REFERENCES `tag` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            ALTER TABLE `s_plugin_netzp_blog`
                    ADD `tag_ids` JSON NULL,
                    ADD `tags` BINARY(16) NULL
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
