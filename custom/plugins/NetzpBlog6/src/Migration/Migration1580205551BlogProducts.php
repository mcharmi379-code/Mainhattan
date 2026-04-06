<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1580205551BlogProducts extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1580205551;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `s_plugin_netzp_blog_product` (
              `blog_id` BINARY(16) NOT NULL,
              `product_id` BINARY(16) NOT NULL,
              `product_version_id` BINARY(16) NOT NULL,
              `created_at` DATETIME(3) NOT NULL,
              PRIMARY KEY (`blog_id`, `product_id`, `product_version_id`),
              CONSTRAINT `fk.s_plugin_netzp_blog_product.blog_id` FOREIGN KEY (`blog_id`)
                REFERENCES `s_plugin_netzp_blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.s_plugin_netzp_blog_product.product_id__product_version_id` FOREIGN KEY (`product_id`, `product_version_id`)
                REFERENCES `product` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            )
        ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4
        COLLATE=utf8mb4_unicode_ci
SQL;
        $connection->executeStatement($sql);

        try {
            $this->updateInheritance($connection, 'product', 'blogs');
        }
        catch (\Exception) { }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
