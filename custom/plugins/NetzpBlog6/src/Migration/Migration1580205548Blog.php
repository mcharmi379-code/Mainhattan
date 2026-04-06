<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1580205548Blog extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1580205548;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog` (
    `id` binary(16) NOT NULL,

    `postdate` date DEFAULT NULL,
    `showfrom` date DEFAULT NULL,
    `showuntil` date DEFAULT NULL,
    `imageid` binary(16) DEFAULT NULL,

    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_translation` (
    `s_plugin_netzp_blog_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,

    `title` varchar(255),
    `teaser` varchar(255),
    `slug` varchar(255),
    `contents` longtext DEFAULT NULL,
    `metatitle` varchar(255),
    `metadescription` varchar(255),

    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),

    PRIMARY KEY (`s_plugin_netzp_blog_id`, `language_id`),
      CONSTRAINT `fk.s_plugin_netzp_blog_translation.language_id` FOREIGN KEY (`language_id`)
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk.s_plugin_netzp_blog_translation.blog_id` FOREIGN KEY (`s_plugin_netzp_blog_id`)
        REFERENCES `s_plugin_netzp_blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE

)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
