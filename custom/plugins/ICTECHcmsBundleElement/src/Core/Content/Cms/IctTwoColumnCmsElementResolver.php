<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

final class IctTwoColumnCmsElementResolver extends ImageCmsElementResolver
{
    private const TYPE = 'ict-two-column';

    public function getType(): string
    {
        return self::TYPE;
    }
}
