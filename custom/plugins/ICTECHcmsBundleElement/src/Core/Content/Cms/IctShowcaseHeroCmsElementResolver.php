<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

final class IctShowcaseHeroCmsElementResolver extends ImageCmsElementResolver
{
    private const TYPE = 'ict-showcase-hero';

    public function getType(): string
    {
        return self::TYPE;
    }
}
