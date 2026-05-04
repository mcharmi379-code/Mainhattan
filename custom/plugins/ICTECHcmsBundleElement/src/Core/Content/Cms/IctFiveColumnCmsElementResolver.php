<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

final class IctFiveColumnCmsElementResolver extends ImageCmsElementResolver
{
    private const TYPE = 'ict-five-column';

    public function getType(): string
    {
        return self::TYPE;
    }
}
