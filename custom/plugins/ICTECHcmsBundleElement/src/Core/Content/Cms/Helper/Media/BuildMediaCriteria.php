<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\Helper\Media;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

final class BuildMediaCriteria
{
    /**
     * @param array<string, array{prefix: string, dataKey: string}> $mediaConfigs
     */
    public function build(CmsSlotEntity $slot, array $mediaConfigs): ?CriteriaCollection
    {
        $collection = new CriteriaCollection();
        $uniqueId = $slot->getUniqueIdentifier();

        foreach ($mediaConfigs as $configKey => $conf) {
            $mediaValue = $slot->getFieldConfig()->get($configKey)?->getValue();

            if (! \is_string($mediaValue) || $mediaValue === '') {
                continue;
            }

            $collection->add($conf['prefix'] . $uniqueId, MediaDefinition::class, new Criteria([$mediaValue]));
        }

        return $collection->all() !== [] ? $collection : null;
    }
}
