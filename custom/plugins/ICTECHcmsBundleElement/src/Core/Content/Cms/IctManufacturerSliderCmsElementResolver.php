<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctManufacturerSliderCmsElementResolver extends AbstractCmsElementResolver
{
    private const TYPE = 'ict-manufacturer-slider';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $manufacturerIds = $this->getManufacturerIds(
            $slot->getFieldConfig()->get('manufacturerIds')?->getArrayValue() ?? [],
        );

        if ($manufacturerIds === []) {
            return null;
        }

        $criteria = new Criteria($manufacturerIds);
        $criteria->addAssociation('media');

        $collection = new CriteriaCollection();
        $collection->add(
            'manufacturers_' . $slot->getUniqueIdentifier(),
            ProductManufacturerDefinition::class,
            $criteria,
        );

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $manufacturerIds = $this->getManufacturerIds(
            $config->get('manufacturerIds')?->getArrayValue() ?? [],
        );

        $slot->setData(new ArrayStruct([
            'manufacturers' => $this->buildManufacturers($manufacturerIds, $result, $slot),
            'showManufacturerName' => $config->get('showManufacturerName')?->getBoolValue() ?? true,
            'maxWidth' => $this->normalizeDimension($config->get('maxWidth')?->getValue(), 180),
            'maxHeight' => $this->normalizeDimension($config->get('maxHeight')?->getValue(), 100),
            'desktopItems' => $this->normalizeItems($config->get('desktopItems')?->getValue(), 4),
            'tabletItems' => $this->normalizeItems($config->get('tabletItems')?->getValue(), 2),
            'mobileItems' => $this->normalizeItems($config->get('mobileItems')?->getValue(), 1),
        ]));
    }

    /**
     * @param list<string> $manufacturerIds
     *
     * @return list<array<string, mixed>>
     */
    private function buildManufacturers(
        array $manufacturerIds,
        ElementDataCollection $result,
        CmsSlotEntity $slot,
    ): array {
        $manufacturerResult = $result->get('manufacturers_' . $slot->getUniqueIdentifier());

        if (! $manufacturerResult instanceof EntitySearchResult) {
            return [];
        }

        $manufacturers = [];

        foreach ($manufacturerIds as $manufacturerId) {
            $manufacturer = $manufacturerResult->get($manufacturerId);

            if (! $manufacturer instanceof ProductManufacturerEntity) {
                continue;
            }

            $manufacturers[] = [
                'id' => $manufacturer->getId(),
                'name' => $manufacturer->getTranslation('name') ?? $manufacturer->getName() ?? '',
                'link' => $manufacturer->getLink(),
                'media' => $manufacturer->getMedia(),
            ];
        }

        return $manufacturers;
    }

    /**
     * @param array<int|string, mixed> $manufacturerIds
     *
     * @return list<string>
     */
    private function getManufacturerIds(array $manufacturerIds): array
    {
        $ids = array_filter(
            $manufacturerIds,
            static fn (mixed $id): bool => \is_string($id) && $id !== '',
        );

        return array_values(array_unique($ids));
    }

    private function normalizeDimension(mixed $value, int $defaultValue): int
    {
        $dimension = is_numeric($value) ? (int) $value : 0;

        return $dimension > 0 ? $dimension : $defaultValue;
    }

    private function normalizeItems(mixed $value, int $defaultValue): int
    {
        $items = is_numeric($value) ? (int) $value : 0;

        return $items > 0 ? min($items, 12) : $defaultValue;
    }
}
