<?php declare(strict_types=1);

namespace MainhattanWheels\Core\Content\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\ArrayStruct;

#[Package('discovery')]
final class MainhattanThreeCardCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @var list<string>
     */
    private const MEDIA_FIELDS = [
        'cardOneImage',
        'cardTwoImage',
        'cardThreeImage',
    ];

    public function getType(): string
    {
        return 'mainhattan-three-card';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteriaCollection = new CriteriaCollection();
        $hasCriteria = false;

        foreach (self::MEDIA_FIELDS as $fieldName) {
            $mediaConfig = $slot->getFieldConfig()->get($fieldName);

            if (!$mediaConfig instanceof FieldConfig || !$mediaConfig->isStatic() || $mediaConfig->getValue() === null) {
                continue;
            }

            $criteriaCollection->add(
                $this->buildCriteriaKey($slot, $fieldName),
                MediaDefinition::class,
                new Criteria([$mediaConfig->getStringValue()])
            );
            $hasCriteria = true;
        }

        if ($hasCriteria === false) {
            return null;
        }

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = [];

        foreach (self::MEDIA_FIELDS as $fieldName) {
            $mediaConfig = $slot->getFieldConfig()->get($fieldName);

            if (!$mediaConfig instanceof FieldConfig || !$mediaConfig->isStatic() || $mediaConfig->getValue() === null) {
                $data[$fieldName] = null;

                continue;
            }

            $searchResult = $result->get($this->buildCriteriaKey($slot, $fieldName));

            if ($searchResult === null) {
                $data[$fieldName] = null;

                continue;
            }

            $media = $searchResult->get($mediaConfig->getStringValue());
            $data[$fieldName] = $media instanceof MediaEntity ? $media : null;
        }

        $slot->setData(new ArrayStruct($data));
    }

    private function buildCriteriaKey(CmsSlotEntity $slot, string $fieldName): string
    {
        return sprintf('%s_%s', $fieldName, $slot->getUniqueIdentifier());
    }
}
