<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1610290148CustomFields extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1610290148;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `s_plugin_netzp_blog_translation`
        ADD `custom_fields` JSON NULL,
        ADD CONSTRAINT `json.s_plugin_netzp_blog_translation.custom_fields`
                 CHECK (JSON_VALID(`custom_fields`));

ALTER TABLE `s_plugin_netzp_blog_category_translation`
        ADD `custom_fields` JSON NULL,
        ADD CONSTRAINT `json.s_plugin_netzp_blog_category_translation.custom_fields`
                 CHECK (JSON_VALID(`custom_fields`));

ALTER TABLE `s_plugin_netzp_blog_author_translation`
        ADD `custom_fields` JSON NULL,
        ADD CONSTRAINT `json.s_plugin_netzp_blog_author_translation.custom_fields`
                 CHECK (JSON_VALID(`custom_fields`));
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
