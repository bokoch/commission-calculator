<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\CountryCodeProviders;

use Bokoch\CommissionCalculator\Exceptions\CountryCodeNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final readonly class BinListCountryCodeProvider implements CountryCodeProvider
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function getCountryCode(string $bin): string
    {
        try {
            $response = $this->client->get($bin);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (! isset($responseData['country']['alpha2'])) {
                throw new CountryCodeNotFoundException(
                    sprintf('Country code for bin "%s" was not found', $bin)
                );
            }

            return $responseData['country']['alpha2'];
        } catch (GuzzleException $e) {
            throw new CountryCodeNotFoundException(
                sprintf('Failed fetching of country code from "%s"', $this->client->getConfig('base_uri')),
                $e->getCode(),
                $e
            );
        }
    }
}