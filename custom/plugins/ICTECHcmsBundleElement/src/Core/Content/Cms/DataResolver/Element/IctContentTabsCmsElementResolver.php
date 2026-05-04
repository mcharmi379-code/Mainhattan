<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctContentTabsCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'ict-content-tabs';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getConfig();

        if ($config === null) {
            $slot->setData(new ArrayStruct(['tabs' => []]));
            return;
        }

        $slot->setData(new ArrayStruct([
            'tabs' => $this->extractTabs($config),
            'displayMode' => $this->extractStringValue($config, 'displayMode', 'topbar'),
            'tabStyle' => $this->extractStringValue($config, 'tabStyle', 'buttons'),
            'verticalAlign' => $this->extractStringValue($config, 'verticalAlign', ''),
        ]));
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return list<array{label: string, content: string}>
     */
    private function extractTabs(array $config): array
    {
        $entry = $config['tabs'] ?? null;
        if (! is_array($entry)) {
            return [];
        }

        $value = $entry['value'] ?? null;
        if (! is_array($value)) {
            return [];
        }

        $result = [];
        foreach (array_values($value) as $tab) {
            if (! is_array($tab)) {
                continue;
            }
            /** @var array<string, mixed> $tab */
            $result[] = [
                'label' => is_string($tab['label'] ?? null) ? $tab['label'] : '',
                'content' => is_string($tab['content'] ?? null) ? $tab['content'] : '',
            ];
        }

        return $result;
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
