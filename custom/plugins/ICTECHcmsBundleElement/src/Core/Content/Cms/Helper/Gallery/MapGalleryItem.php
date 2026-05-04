<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Gallery;

use Shopware\Core\Content\Media\MediaEntity;

final class MapGalleryItem
{
    public function __construct(
        private readonly ResolveGalleryMedia $mediaResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array{media: MediaEntity, mediaId: string, mediaUrl: string, title: string}|null
     */
    public function map(array $item, mixed $mediaCollection): ?array
    {
        $mediaId = $item['mediaId'] ?? null;

        if (! \is_string($mediaId) || $mediaId === '') {
            return null;
        }

        $media = $this->mediaResolver->resolve($mediaId, $mediaCollection);

        if ($media === null) {
            return null;
        }

        return [
            'media' => $media,
            'mediaId' => $mediaId,
            'mediaUrl' => $media->getUrl(),
            'title' => \is_string($item['title'] ?? null) ? $item['title'] : '',
        ];
    }
}
