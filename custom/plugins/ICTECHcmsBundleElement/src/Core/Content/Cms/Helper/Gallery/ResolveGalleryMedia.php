<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Gallery;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

final class ResolveGalleryMedia
{
    public function resolve(string $mediaId, mixed $mediaCollection): ?MediaEntity
    {
        if (! $mediaCollection instanceof EntitySearchResult) {
            return null;
        }

        $candidate = $mediaCollection->get($mediaId);

        return $candidate instanceof MediaEntity ? $candidate : null;
    }
}
