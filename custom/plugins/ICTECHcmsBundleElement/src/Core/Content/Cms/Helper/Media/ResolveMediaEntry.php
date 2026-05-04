<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Media;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;

final class ResolveMediaEntry
{
    /**
     * @param array<string, array{prefix: string, dataKey: string}> $mediaConfigs
     *
     * @return array<string, mixed>
     */
    public function resolveAll(CmsSlotEntity $slot, ElementDataCollection $result, array $mediaConfigs): array
    {
        $data = [];
        $uniqueId = $slot->getUniqueIdentifier();

        foreach ($mediaConfigs as $configKey => $conf) {
            if (! $slot->getFieldConfig()->get($configKey)?->getValue()) {
                continue;
            }

            $media = $result->get($conf['prefix'] . $uniqueId)?->first();

            if ($media !== null) {
                $data[$conf['dataKey']] = $media;
            }
        }

        return $data;
    }
}
