<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Core\Content\Cms\DataResolver\Element;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Struct\ArrayStruct;

final class IctAlertHintCmsElementResolver extends AbstractCmsElementResolver
{
    private const ALLOWED_TYPES = ['success', 'danger', 'warning', 'info', 'primary', 'secondary', 'light', 'dark'];
    private const ALLOWED_LAYOUTS = ['custom-callout', 'shopware-standard'];
    private const TYPE_ALIASES = [
        'error' => 'danger',
        'information' => 'info',
    ];
    private const LAYOUT_ALIASES = [
        'shopwarestandard' => 'shopware-standard',
        'shopware standard' => 'shopware-standard',
        'customcallout' => 'custom-callout',
        'custom callout' => 'custom-callout',
    ];

    public function getType(): string
    {
        return 'ict-alert-hint';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();

        $alertType = $this->sanitizeOption(
            $this->normalizeAlertType($this->toScalarString($config->get('alertType')?->getValue(), 'info')),
            self::ALLOWED_TYPES,
            'info',
        );

        $layoutType = $this->sanitizeOption(
            $this->normalizeLayoutType($this->toScalarString($config->get('layoutType')?->getValue(), 'custom-callout')),
            self::ALLOWED_LAYOUTS,
            'custom-callout',
        );

        $activateIconRaw = $config->get('activateIcon')?->getValue();
        $activateIcon = $this->toBool($activateIconRaw, true);

        $slot->setData(new ArrayStruct([
            'alertType'    => $alertType,
            'layoutType'   => $layoutType,
            'activateIcon' => $activateIcon,
            'title'        => $this->toScalarString($config->get('title')?->getValue(), ''),
            'content'      => $this->toScalarString($config->get('content')?->getValue(), ''),
        ]));
    }

    /**
     * @param list<string> $allowed
     */
    private function sanitizeOption(string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function normalizeAlertType(string $value): string
    {
        $normalized = strtolower(trim($value));

        return self::TYPE_ALIASES[$normalized] ?? $normalized;
    }

    private function normalizeLayoutType(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = str_replace(['_', '-'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if (isset(self::LAYOUT_ALIASES[$normalized])) {
            return self::LAYOUT_ALIASES[$normalized];
        }

        // canonical values already include a hyphen
        return str_replace(' ', '-', $normalized);
    }

    /**
     * @param array<mixed>|bool|float|int|string|null $value
     */
    private function toScalarString(mixed $value, string $default): string
    {
        if ($value === null || is_array($value)) {
            return $default;
        }

        return (string) $value;
    }

    /**
     * @param array<mixed>|bool|float|int|string|null $value
     */
    private function toBool(mixed $value, bool $default): bool
    {
        if ($value === null || is_array($value)) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value !== 0;
        }

        if (is_string($value)) {
            $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            return $filtered ?? $default;
        }

        return (bool) $value;
    }
}
