<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctProductMatrixCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-product-matrix';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $productId = $this->extractProductId($slot);

        if ($productId === null) {
            return null;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentId', $productId));
        $criteria->addAssociation('cover.media');
        $criteria->addAssociation('options.group');
        $criteria->addAssociation('deliveryTime');
        $criteria->addAssociation('unit');
        $criteria->addSorting(new FieldSorting('productNumber', FieldSorting::ASCENDING));
        $criteria->setLimit(50);

        $collection = new CriteriaCollection();
        $collection->add(
            'variants_' . $slot->getUniqueIdentifier(),
            ProductDefinition::class,
            $criteria,
        );

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $productId = $this->extractProductId($slot);

        if ($productId === null) {
            $slot->setData(new ArrayStruct(['variants' => [], 'productId' => null]));
            return;
        }

        $variantResult = $result->get('variants_' . $slot->getUniqueIdentifier());
        $variants = $variantResult !== null ? $variantResult->getEntities() : null;

        $slot->setData(new ArrayStruct([
            'productId' => $productId,
            'variants' => $variants,
            'displayMode' => $this->extractStringValue($slot->getConfig() ?? [], 'displayMode', 'standard'),
        ]));
    }

    private function extractProductId(CmsSlotEntity $slot): ?string
    {
        $config = $slot->getConfig();

        if (! is_array($config)) {
            return null;
        }

        $entry = $config['product'] ?? null;

        if (! is_array($entry)) {
            return null;
        }

        $value = $entry['value'] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function extractStringValue(array $config, string $key, string $default): string
    {
        $entry = $config[$key] ?? null;

        if (! is_array($entry)) {
            return $default;
        }

        $value = $entry['value'] ?? null;

        return is_string($value) && $value !== '' ? $value : $default;
    }
}
