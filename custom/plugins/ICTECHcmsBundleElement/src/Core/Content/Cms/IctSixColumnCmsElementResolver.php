<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

final class IctSixColumnCmsElementResolver extends ImageCmsElementResolver
{
    private const TYPE = 'ict-six-column';

    public function getType(): string
    {
        return self::TYPE;
    }
}
