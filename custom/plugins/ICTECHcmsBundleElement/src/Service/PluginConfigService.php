<?php

declare(strict_types=1);

namespace ICTECHcmsBundleElement\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;

final class PluginConfigService
{
    private const CONFIG_PREFIX = 'ICTECHcmsBundleElement.config.';

    public function __construct(
        private readonly SystemConfigService $systemConfigService,
    ) {
    }

    public function get(string $key, ?string $salesChannelId = null): mixed
    {
        return $this->systemConfigService->get($this->buildConfigKey($key), $salesChannelId);
    }

    /**
     * @param array<mixed>|bool|float|int|string|null $value
     */
    public function set(string $key, array|bool|float|int|string|null $value, ?string $salesChannelId = null): void
    {
        $this->systemConfigService->set($this->buildConfigKey($key), $value, $salesChannelId);
    }

    public function getString(string $key, ?string $salesChannelId = null, string $default = ''): string
    {
        $value = $this->get($key, $salesChannelId);

        return is_scalar($value) ? (string) $value : $default;
    }

    public function getBool(string $key, ?string $salesChannelId = null, bool $default = false): bool
    {
        $value = $this->get($key, $salesChannelId);

        return is_bool($value) ? $value : $default;
    }

    public function getInt(string $key, ?string $salesChannelId = null, int $default = 0): int
    {
        $value = $this->get($key, $salesChannelId);

        return is_int($value) ? $value : $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAll(?string $salesChannelId = null): array
    {
        return $this->systemConfigService->getDomain(self::CONFIG_PREFIX, $salesChannelId, true);
    }

    public function delete(string $key, ?string $salesChannelId = null): void
    {
        $this->systemConfigService->delete($this->buildConfigKey($key), $salesChannelId);
    }

    public function deleteAll(?string $salesChannelId = null): void
    {
        foreach (array_keys($this->getAll($salesChannelId)) as $configKey) {
            $this->systemConfigService->delete($configKey, $salesChannelId);
        }
    }

    private function buildConfigKey(string $key): string
    {
        return self::CONFIG_PREFIX . $key;
    }
}
