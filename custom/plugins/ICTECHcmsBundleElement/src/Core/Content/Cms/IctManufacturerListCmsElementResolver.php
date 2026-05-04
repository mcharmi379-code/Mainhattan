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
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctManufacturerListCmsElementResolver extends AbstractCmsElementResolver
{
    private const TYPE = 'ict-manufacturer-list';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): CriteriaCollection
    {
        $criteria = new Criteria();
        $criteria->setLimit(500);
        $criteria->addAssociation('media');
        $criteria->addSorting(new FieldSorting('name', FieldSorting::ASCENDING, true));

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
        $groups = $this->createEmptyGroups();
        $manufacturersResult = $result->get('manufacturers_' . $slot->getUniqueIdentifier());

        if ($manufacturersResult instanceof EntitySearchResult) {
            $this->populateGroups($manufacturersResult, $groups);
        }

        $slot->setData(new ArrayStruct(
            $this->buildAlphabetData($groups, $slot->getUniqueIdentifier()),
        ));
    }

    /**
     * @param EntitySearchResult<\Shopware\Core\Framework\DataAbstractionLayer\EntityCollection> $result
     *
     * @param array<string, array<int, array<string, mixed>>> $groups
     */
    private function populateGroups(EntitySearchResult $result, array &$groups): void
    {
        foreach ($result->getElements() as $manufacturer) {
            if (! $manufacturer instanceof ProductManufacturerEntity) {
                continue;
            }

            $name = $this->resolveManufacturerName($manufacturer);

            if ($name === '') {
                continue;
            }

            $groups[$this->resolveGroupKey($name)][] = [
                'id' => $manufacturer->getId(),
                'name' => $name,
                'link' => $this->normalizeManufacturerLink($manufacturer->getLink()),
                'media' => $manufacturer->getMedia(),
            ];
        }
    }

    private function resolveManufacturerName(ProductManufacturerEntity $manufacturer): string
    {
        $translatedName = $manufacturer->getTranslation('name');

        return trim(is_string($translatedName) ? $translatedName : (string) $manufacturer->getName());
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $groups
     *
     * @return array<string, mixed>
     */
    private function buildAlphabetData(array $groups, string $uniqueIdentifier): array
    {
        $alphabet = [];
        $groupedManufacturers = [];

        foreach ($groups as $key => $items) {
            $anchor = $this->buildAnchor($uniqueIdentifier, $key);
            $alphabet[] = $this->buildAlphabetEntry($key, $anchor, $items);

            if ($items !== []) {
                $groupedManufacturers[] = ['key' => $key, 'label' => $key, 'anchor' => $anchor, 'items' => $items];
            }
        }

        return ['alphabet' => $alphabet, 'groups' => $groupedManufacturers];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<string, mixed>
     */
    private function buildAlphabetEntry(string $key, string $anchor, array $items): array
    {
        return ['key' => $key, 'label' => $key, 'anchor' => $items !== [] ? $anchor : null];
    }

    private function buildAnchor(string $uniqueIdentifier, string $key): string
    {
        return sprintf(
            'ict-manufacturer-list-%s-%s',
            $uniqueIdentifier,
            strtolower($key === '#' ? 'numeric' : $key),
        );
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function createEmptyGroups(): array
    {
        $groups = ['#' => []];

        foreach (range('A', 'Z') as $letter) {
            $groups[$letter] = [];
        }

        return $groups;
    }

    private function resolveGroupKey(string $name): string
    {
        $firstCharacter = mb_strtoupper(mb_substr($name, 0, 1));

        return preg_match('/[A-Z]/', $firstCharacter) === 1 ? $firstCharacter : '#';
    }

    private function normalizeManufacturerLink(?string $link): ?string
    {
        $normalizedLink = trim((string) $link);

        if ($normalizedLink === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $normalizedLink) === 1) {
            return $normalizedLink;
        }

        return 'https://' . ltrim($normalizedLink, '/');
    }
}
