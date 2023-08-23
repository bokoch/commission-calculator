<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\ConfigRepositories;

interface ConfigRepository
{
    /**
     * @param string $key Path to config separated with dots.
     * Example: 'service.base_uri' is equal to ['service']['base_uri']
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * @return array<string, mixed>
     */
    public function all(): array;
}
