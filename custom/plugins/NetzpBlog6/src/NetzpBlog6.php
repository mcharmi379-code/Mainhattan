<?php declare(strict_types=1);

namespace NetzpBlog6;

use NetzpBlog6\Core\Content\Blog\BlogDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Doctrine\DBAL\Connection;

class NetzpBlog6 extends Plugin
{
    final public const BLOG_MEDIAFOLDER_NAME = 'Blog Media';
    final public const BLOG_MEDIAFOLDER_ID   = '59D4F5B90E944D44B997ED0A60804034';

    public function install(InstallContext $installContext): void
    {
        $this->createMediaFolder($installContext->getContext());
    }

    public function update(UpdateContext $updateContext): void
    {
        if (version_compare($updateContext->getCurrentPluginVersion(), '1.1.0', '<')) {
            $this->createMediaFolder($updateContext->getContext());
        }

        // NE 29.11.2021 - set `associationFields` to 'netzpBlogMedia' to ensure correct media deletion via bin/console media:delete-unused
        $this->fixMediaFolder($updateContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->removeMediaFolder();
        $this->removeMigrations();

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_categories`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_product`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_category_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_category`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_author_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_author`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_tag`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_item_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_item`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_media`');

        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `s_plugin_netzp_blog`');

        try {
            $connection->executeStatement('ALTER TABLE `product` DROP COLUMN `blogs`');
        }
        catch (\Exception) {
            //
        }

        try {
            $connection->executeStatement('DELETE FROM `seo_url_template` WHERE route_name = "frontend.blog.post"');
        }
        catch (\Exception) {
            //
        }
    }

    private function removeMediaFolder(): void
    {
        $connection = $this->container->get(Connection::class);
        try {
            $defaultFolderId = $connection->fetchOne('SELECT HEX(id) FROM media_default_folder WHERE HEX(id) = ?', [
                strtolower(self::BLOG_MEDIAFOLDER_ID)
            ]);
            if( ! $defaultFolderId) {
                return;
            }

            $defaultConfigurationId = $connection->fetchOne(
                'SELECT HEX(media_folder_configuration_id) FROM media_folder WHERE HEX(default_folder_id) = ?',
                [$defaultFolderId]
            );
            if( ! $defaultConfigurationId) {
                return;
            }

            $connection->executeStatement('DELETE FROM `media_folder_configuration` WHERE HEX(id) = ?', [
                $defaultConfigurationId
            ]);
            $connection->executeStatement('DELETE FROM `media_folder` WHERE HEX(default_folder_id) = ?', [
                $defaultFolderId
            ]);
            $connection->executeStatement('DELETE FROM `media_default_folder` WHERE HEX(id) = ?', [
                strtolower(self::BLOG_MEDIAFOLDER_ID)
            ]);
        }
        catch (\Exception) {
            //
        }
    }

    public function createMediaFolder(Context $context): void
    {
        try {
            $connection = $this->container->get(Connection::class);
            $thumbnailIds = $connection->fetchAllAssociative(
                'SELECT LOWER(HEX(id)) AS id from `media_thumbnail_size` WHERE width in (400, 800, 1920)'
            );

            $repo = $this->container->get('media_default_folder.repository');
            $repo->upsert([
                [
                    'id'                => strtolower(self::BLOG_MEDIAFOLDER_ID),
                    'entity'            => BlogDefinition::ENTITY_NAME,
                    'associationFields' => ['netzpBlogMedia'],
                    'folder'            => [
                        'name'                   => self::BLOG_MEDIAFOLDER_NAME,
                        'useParentConfiguration' => false,
                        'configuration'          => [
                            'createThumbnails'    => true,
                            'mediaThumbnailSizes' => $thumbnailIds
                        ]
                    ]
                ]
            ], $context);
        }
        catch (\Exception) {
            //
        }
    }

    public function fixMediaFolder(Context $context): void
    {
        try {
            $repo = $this->container->get('media_default_folder.repository');
            $repo->upsert([
                [
                    'id'                => strtolower(self::BLOG_MEDIAFOLDER_ID),
                    'associationFields' => ['netzpBlogMedia']
                ]
            ], $context);
        }
        catch (\Exception) {
            //
        }
    }
}
