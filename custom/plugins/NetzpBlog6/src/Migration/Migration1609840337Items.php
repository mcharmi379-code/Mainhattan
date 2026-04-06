<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1609840337Items extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1609840337;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_item` (
    `id` binary(16) NOT NULL,
    `blog_id` BINARY(16) NOT NULL,

    `number` tinyint(1) NOT NULL DEFAULT 0,
    `image_id` BINARY(16) NULL,

    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,

    PRIMARY KEY (`id`),
    CONSTRAINT `fk.s_plugin_netzp_blog_item.image_id` FOREIGN KEY (`image_id`)
        REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_item_translation` (
    `s_plugin_netzp_blog_item_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,

    `title` varchar(255) DEFAULT NULL,
    `content` longtext DEFAULT NULL,

    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),

    PRIMARY KEY (`s_plugin_netzp_blog_item_id`, `language_id`),
      CONSTRAINT `fk.s_plugin_netzp_blog_item_translation.language_id` FOREIGN KEY (`language_id`)
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk.s_plugin_netzp_blog_item_translation.blog_item_id` FOREIGN KEY (`s_plugin_netzp_blog_item_id`)
        REFERENCES `s_plugin_netzp_blog_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
