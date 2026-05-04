<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

final class IctTitleActionPanelCmsElementResolver extends ImageCmsElementResolver
{
    private const TYPE = 'ict-title-action-panel';

    public function getType(): string
    {
        return self::TYPE;
    }
}
