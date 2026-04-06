<?php declare(strict_types=1);

namespace NetzpBlog6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

class Migration1641641753BlogMediaCascadingDelete extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1641641753;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        DELETE
          FROM s_plugin_netzp_blog_media
         WHERE s_plugin_netzp_blog_media.blog_id
        NOT IN (SELECT id FROM s_plugin_netzp_blog);

        DELETE
          FROM s_plugin_netzp_blog_item
         WHERE s_plugin_netzp_blog_item.blog_id
        NOT IN (SELECT id FROM s_plugin_netzp_blog);

        ALTER TABLE s_plugin_netzp_blog_media
                ADD CONSTRAINT `fk.s_plugin_netzp_blog_media.blog_id`
                    FOREIGN KEY (`blog_id`)
                    REFERENCES `s_plugin_netzp_blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

        ALTER TABLE s_plugin_netzp_blog_item
                ADD CONSTRAINT `fk.s_plugin_netzp_blog_item.blog_id`
                    FOREIGN KEY (`blog_id`)
                    REFERENCES `s_plugin_netzp_blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;

        try {
            $connection->executeStatement($sql);
        }
        catch (\Exception) {
            //
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
