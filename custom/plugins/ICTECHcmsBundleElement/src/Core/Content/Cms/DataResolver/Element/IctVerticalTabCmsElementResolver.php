<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctVerticalTabCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-vertical-tab';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();

        /** @var array<int, array<string, mixed>> $tabs */
        $tabs = $config->get('tabs')?->getValue() ?? [];

        $slot->setData(new ArrayStruct(['tabs' => $this->normalizeTabs($tabs)]));
    }

    /**
     * @param array<int, mixed> $tabs
     *
     * @return array<int, array<string, mixed>>
     */
    private function normalizeTabs(array $tabs): array
    {
        $normalized = [];

        foreach ($tabs as $tab) {
            if (! is_array($tab)) {
                continue;
            }

            $normalized[] = $this->normalizeTab($tab);
        }

        return $normalized;
    }

    /**
     * @param array<array-key, mixed> $tab
     *
     * @return array<string, mixed>
     */
    private function normalizeTab(array $tab): array
    {
        return [
            'title' => $this->readString($tab, 'title', ''),
            'header' => $this->readString($tab, 'header', ''),
            'content' => $this->readString($tab, 'content', ''),
            'buttonText' => $this->readString($tab, 'buttonText', ''),
            'linkType' => $this->readString($tab, 'linkType', 'url'),
            'linkUrl' => $this->readString($tab, 'linkUrl', ''),
            'openInNewTab' => $this->readBool($tab, 'openInNewTab', false),
        ];
    }

    /**
     * @param array<array-key, mixed> $tab
     */
    private function readString(array $tab, string $key, string $default): string
    {
        $value = $tab[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * @param array<array-key, mixed> $tab
     */
    private function readBool(array $tab, string $key, bool $default): bool
    {
        if (! array_key_exists($key, $tab)) {
            return $default;
        }

        return (bool) $tab[$key];
    }
}
