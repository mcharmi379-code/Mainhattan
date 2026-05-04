<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

final class IctThreeColumnCmsElementResolver extends ImageCmsElementResolver
{
    private const TYPE = 'ict-three-column';

    public function getType(): string
    {
        return self::TYPE;
    }
}
