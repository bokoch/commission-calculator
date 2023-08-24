<?php

namespace Tests\CurrencyExchangeRateProviders;

use Bokoch\CommissionCalculator\CurrencyExchangeRateProviders\ApiCurrencyExchangeRateProvider;
use Bokoch\CommissionCalculator\Exceptions\ExchangeRateNotFoundException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\AbstractTestCase;

class ApiCurrencyExchangeRateProviderTest extends AbstractTestCase
{
    #[Test]
    public function it_can_fetch_exchange_rates_from_api_and_return_requested_currency_rate(): void
    {
        $contentMock = Mockery::mock(StreamInterface::class);
        $contentMock->shouldReceive('getContents')
            ->andReturn('{"rates": {"baz": 2.2, "xyz": 3.3}}');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($contentMock);

        $clientMock = Mockery::mock(Client::class);
        $clientMock
            ->shouldReceive('get')
            ->andReturn($responseMock);

        $exchangeRateProvider = new ApiCurrencyExchangeRateProvider($clientMock, 'test');
        $actualRate = $exchangeRateProvider->getExchangeRate('bar', 'baz');

        $this->assertSame(expected: 2.2, actual: $actualRate);
    }

    #[Test]
    public function it_throw_exception_when_api_response_does_not_contain_requested_currency_rate(): void
    {
        $contentMock = Mockery::mock(StreamInterface::class);
        $contentMock->shouldReceive('getContents')
            ->andReturn('{"rates": {"xyz": 3.3}}');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($contentMock);

        $clientMock = Mockery::mock(Client::class);
        $clientMock
            ->shouldReceive('get')
            ->andReturn($responseMock);

        $exchangeRateProvider = new ApiCurrencyExchangeRateProvider($clientMock, 'test');

        $this->expectException(ExchangeRateNotFoundException::class);
        $exchangeRateProvider->getExchangeRate('bar', 'baz');
    }

    #[Test]
    public function it_throw_exception_when_api_response_body_is_invalid(): void
    {
        $contentMock = Mockery::mock(StreamInterface::class);
        $contentMock->shouldReceive('getContents')
            ->andReturn('foo bar');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($contentMock);

        $clientMock = Mockery::mock(Client::class);
        $clientMock
            ->shouldReceive('get')
            ->andReturn($responseMock);

        $exchangeRateProvider = new ApiCurrencyExchangeRateProvider($clientMock, 'test');

        $this->expectException(ExchangeRateNotFoundException::class);
        $exchangeRateProvider->getExchangeRate('bar', 'baz');
    }

    #[Test]
    public function it_throw_exception_when_api_request_failed(): void
    {
        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('getConfig');

        $clientMock
            ->shouldReceive('get')
            ->andThrow(
                new class extends Exception implements GuzzleException {}
            );

        $exchangeRateProvider = new ApiCurrencyExchangeRateProvider($clientMock, 'test');

        $this->expectException(ExchangeRateNotFoundException::class);
        $exchangeRateProvider->getExchangeRate('bar', 'baz');
    }
}
