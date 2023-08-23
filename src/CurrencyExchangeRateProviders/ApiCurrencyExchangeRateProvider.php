<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator\CurrencyExchangeRateProviders;

use Bokoch\CommissionCalculator\Exceptions\ExchangeRateNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

final readonly class ApiCurrencyExchangeRateProvider implements CurrencyExchangeRateProvider
{
    public function __construct(
        private string $baseUri,
        private string $accessKey,
    ) {
    }

    public function getExchangeRate(string $baseCurrency, string $targetCurrency): float
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        try {
            $response = $client->get('latest', [
                RequestOptions::QUERY => [
                    'access_key' => $this->accessKey,
                    'base' => $baseCurrency,
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (! isset($responseData['rates'][$targetCurrency])) {
                throw new ExchangeRateNotFoundException(
                    sprintf('Rate for currency "%s" was not found', $targetCurrency)
                );
            }

            return (float) $responseData['rates'][$targetCurrency];
        } catch (GuzzleException $e) {
            throw new ExchangeRateNotFoundException(
                sprintf('Failed fetching of exchange rates from "%s"', $this->baseUri),
                $e->getCode(),
                $e
            );
        }
    }
}
