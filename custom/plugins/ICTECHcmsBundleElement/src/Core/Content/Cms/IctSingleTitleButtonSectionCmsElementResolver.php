<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctSingleTitleButtonSectionCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * Maps field-config keys to their result-collection prefix and data key.
     *
     * @var array<string, array{prefix: string, dataKey: string}>
     */
    private const MEDIA_CONFIGS = [
        'backgroundImage' => ['prefix' => 'media_', 'dataKey' => 'media'],
        'backgroundVideo' => ['prefix' => 'video_', 'dataKey' => 'video'],
        'leftButtonIcon' => [
            'prefix' => 'leftButtonIcon_',
            'dataKey' => 'leftButtonIcon',
        ],
        'rightButtonIcon' => [
            'prefix' => 'rightButtonIcon_',
            'dataKey' => 'rightButtonIcon',
        ],
    ];

    public function getType(): string
    {
        return 'ict-single-title-button-section';
    }

    public function collect(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext,
    ): ?CriteriaCollection {
        $collection = new CriteriaCollection();
        $uid = $slot->getUniqueIdentifier();

        foreach (self::MEDIA_CONFIGS as $configKey => $conf) {
            $value = $slot->getFieldConfig()->get($configKey)?->getValue();

            if (! is_string($value) || $value === '') {
                continue;
            }

            $collection->add(
                $conf['prefix'] . $uid,
                MediaDefinition::class,
                new Criteria([$value]),
            );
        }

        return $collection->all() !== [] ? $collection : null;
    }

    public function enrich(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext,
        ElementDataCollection $result,
    ): void {
        $data = [];
        $uid = $slot->getUniqueIdentifier();

        foreach (self::MEDIA_CONFIGS as $configKey => $conf) {
            $value = $slot->getFieldConfig()->get($configKey)?->getValue();

            if (! is_string($value) || $value === '') {
                continue;
            }

            $media = $result->get($conf['prefix'] . $uid);
            $firstMedia = $media?->first();

            if ($firstMedia !== null) {
                $data[$conf['dataKey']] = $firstMedia;
            }
        }

        $slot->setData(new ArrayStruct($data));
    }
}
