<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement;

use ICTECHcmsBundleElement\Service\PluginConfigService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

final class ICTECHcmsBundleElement extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        if ($this->container === null) {
            return;
        }

        /** @var PluginConfigService $pluginConfigService */
        $pluginConfigService = $this->container->get(PluginConfigService::class);
        $pluginConfigService->deleteAll();

        // No custom database tables to drop for this plugin.
        // CMS slots referencing our element types will remain in the DB
        // but become inert once the plugin is removed.
    }
}
