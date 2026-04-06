<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1609840338BlogMedia extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1609840338;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_media` (
    `id` binary(16) NOT NULL,
    `blog_id` BINARY(16) NOT NULL,

    `number` tinyint(1) NOT NULL DEFAULT 0,
    `media_id` BINARY(16) NULL,

    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,

    PRIMARY KEY (`id`),
    CONSTRAINT `fk.s_plugin_netzp_blog_media.media_id` FOREIGN KEY (`media_id`)
        REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
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
