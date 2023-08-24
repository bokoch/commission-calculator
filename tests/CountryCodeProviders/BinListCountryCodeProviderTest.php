<?php

namespace Tests\CountryCodeProviders;

use Bokoch\CommissionCalculator\CountryCodeProviders\BinListCountryCodeProvider;
use Bokoch\CommissionCalculator\Exceptions\CountryCodeNotFoundException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\AbstractTestCase;

class BinListCountryCodeProviderTest extends AbstractTestCase
{
    #[Test]
    public function it_can_fetch_country_code_from_api(): void
    {
        $contentMock = Mockery::mock(StreamInterface::class);
        $contentMock->shouldReceive('getContents')
            ->andReturn('{"country": {"alpha2": "foo"}}');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($contentMock);

        $clientMock = Mockery::mock(Client::class);
        $clientMock
            ->shouldReceive('get')
            ->andReturn($responseMock);

        $countryCodeProvider = new BinListCountryCodeProvider($clientMock);
        $actualCountryCode = $countryCodeProvider->getCountryCode('1234');

        $this->assertSame(expected: 'foo', actual: $actualCountryCode);
    }

    #[Test]
    public function it_throw_exception_when_api_response_does_not_contain_requested_country_code(): void
    {
        $contentMock = Mockery::mock(StreamInterface::class);
        $contentMock->shouldReceive('getContents')
            ->andReturn('{"country": {}');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock
            ->shouldReceive('getBody')
            ->andReturn($contentMock);

        $clientMock = Mockery::mock(Client::class);
        $clientMock
            ->shouldReceive('get')
            ->andReturn($responseMock);

        $countryCodeProvider = new BinListCountryCodeProvider($clientMock);

        $this->expectException(CountryCodeNotFoundException::class);
        $countryCodeProvider->getCountryCode('1234');
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

        $countryCodeProvider = new BinListCountryCodeProvider($clientMock);

        $this->expectException(CountryCodeNotFoundException::class);
        $countryCodeProvider->getCountryCode('1234');
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

        $countryCodeProvider = new BinListCountryCodeProvider($clientMock);

        $this->expectException(CountryCodeNotFoundException::class);
        $countryCodeProvider->getCountryCode('1234');
    }
}