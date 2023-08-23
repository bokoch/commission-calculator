<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\ConfigRepositories;

final readonly class InMemoryConfigRepository implements ConfigRepository
{
    public function get(string $key): mixed
    {
        $keyParts = explode('.', $key);
        $target = $this->all();

        foreach ($keyParts as $keySegment) {
            if ($keySegment === null) {
                return null;
            }

            if (is_array($target) && array_key_exists($keySegment, $target)) {
                $target = $target[$keySegment];
            } else {
                return null;
            }
        }

        return $target;
    }

    public function all(): array
    {
        return [
            'base_currency' => 'EUR',
            'bin_list' => [
                'base_uri' => 'https://lookup.binlist.net/',
            ],
            'exchange_rates_api' => [
                'base_uri' => 'http://api.exchangeratesapi.io/',
                'access_key' => 'your_key',
            ],
        ];
    }
}
