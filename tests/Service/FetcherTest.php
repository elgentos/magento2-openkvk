<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Tests\Model;

use Elgentos\OpenKvk\Model\Config;
use Elgentos\OpenKvk\Service\Fetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Elgentos\OpenKvk\Service\Fetcher
 */
class FetcherTest extends TestCase
{
    /** @var string  */
    private string $validJsonResult = '{
        "_embedded": {
            "bedrijf": [
                {
                    "dossiernummer": "02082474",
                    "handelsnaam": "Hoeske van Thais Joaptje",
                    "postcode": "9998XD",
                    "subdossiernummer": "0000"
                },
                {
                    "dossiernummer": "17234501",
                    "handelsnaam": "ProAttivo Consultancy",
                    "postcode": "1052XD",
                    "subdossiernummer": "0001"
                },
                {
                    "dossiernummer": "58179895",
                    "handelsnaam": "Wonderland Industry",
                    "postcode": "1016XD",
                    "subdossiernummer": "0000"
                }
            ]
        }
    }';

    /**
     * @param string            $postcode
     * @param string            $houseNumber
     * @param string|null|array $jsonString
     *
     * @return void
     *
     * @dataProvider setAddressDataProvider
     *
     * @covers ::__construct
     * @covers ::fetchSuggestResultsByAddress
     * @covers ::fetchSuggestResultsByCoc
     * @covers ::getHeaders
     * @covers ::fetch
     */
    public function testFetchSuggestResultsByAddress(
        string $postcode,
        string $houseNumber,
        $jsonString
    ): void {
        $this->createSubjectInstance($jsonString)
            ->fetchSuggestResultsByAddress($postcode, $houseNumber);
    }

    /**
     * @param string            $coc
     * @param string|null|array $jsonString
     *
     * @return void
     *
     * @dataProvider setAddressDataProvider
     *
     * @covers ::__construct
     * @covers ::fetchSuggestResultsByAddress
     * @covers ::fetchSuggestResultsByCoc
     * @covers ::getHeaders
     * @covers ::fetch
     */
    public function testFetchSuggestResultsByCoc(
        string $coc,
        $jsonString
    ): void {
        $this->createSubjectInstance($jsonString)
            ->fetchSuggestResultsByCoc($coc);
    }

    /**
     * Create an instance of the class that is used in the tests.
     *
     * @param string|null|array $jsonString
     *
     * @return Fetcher
     */
    private function createSubjectInstance($jsonString): Fetcher
    {
        $guzzleClient   = $this->createMock(Client::class);
        $configModel    = $this->createMock(Config::class);
        $jsonSerializer = new Json();

        $configModel->expects(self::once())
            ->method('getApiKey')
            ->willReturn('foobar');

        $subject = new Fetcher(
            $guzzleClient,
            $configModel,
            $jsonSerializer
        );

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($jsonString);

        $guzzleClient->expects(self::once())
            ->method('request')
            ->willReturn($response);

        return $subject;
    }

    /**
     * Create the data provider used in the tests.
     *
     * @return array
     */
    public function setAddressDataProvider(): array
    {
        return [
            [
                '',
                '',
                null
            ],
            [
                '',
                '',
                'Just a random string'
            ],
            [
                '1234AB',
                '1',
                $this->validJsonResult
            ]
        ];
    }
}
