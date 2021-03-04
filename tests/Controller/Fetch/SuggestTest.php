<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\OpenKvk\Tests\Block;

use Elgentos\OpenKvk\Controller\Fetch\Suggest;
use Elgentos\OpenKvk\Model\Config;
use Elgentos\OpenKvk\Model\Fetch;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Elgentos\OpenKvk\Controller\Fetch\Suggest
 */
class SuggestTest extends TestCase
{
    /** @var string  */
    private string $validJsonResult = '{
        "_embedded": {
            "bedrijf": [
                {
                    "_links": {
                        "self": {
                            "href": "/openkvk/hoofdvestiging-02082474-0000-hoeske-van-thais-joaptje"
                        }
                    },
                    "dossiernummer": "02082474",
                    "handelsnaam": "Hoeske van Thais Joaptje",
                    "postcode": "9998XD",
                    "subdossiernummer": "0000"
                },
                {
                    "_links": {
                        "self": {
                            "href": "/openkvk/nevenvestiging-17234501-0001-proattivo-consultancy"
                        }
                    },
                    "dossiernummer": "17234501",
                    "handelsnaam": "ProAttivo Consultancy",
                    "postcode": "1052XD",
                    "subdossiernummer": "0001"
                },
                {
                    "_links": {
                        "self": {
                            "href": "/openkvk/hoofdvestiging-58179895-0000-wonderland-industry"
                        }
                    },
                    "dossiernummer": "58179895",
                    "handelsnaam": "Wonderland Industry",
                    "postcode": "1016XD",
                    "subdossiernummer": "0000"
                },
            ]
        },
        "_links": {
            "first": {
                "href": "/openkvk?query=*XD&queryfields[0]=postcode&fields[0]=postcode&page=1"
            },
            "last": {
                "href": "/openkvk?query=*XD&queryfields[0]=postcode&fields[0]=postcode&page=10"
            },
            "next": {
                "href": "/openkvk?query=*XD&queryfields[0]=postcode&fields[0]=postcode&page=2"
            },
            "self": {
                "href": "/openkvk?query=*XD&queryfields%5B%5D=postcode&fields%5B%5D=postcode"
            }
        },
        "pageCount": 54,
        "size": 100,
        "totalItemCount": 5394
}
';

    /**
     * Test the whole controller for fetching company data from OpenKvk.
     *
     * @param array       $postData
     * @param bool        $isAjaxRequest
     * @param bool        $isModuleEnabled
     * @param string|null $jsonString
     *
     * @return void
     *
     * @covers ::__construct
     * @covers ::execute
     *
     * @dataProvider setDataProvider
     */
    public function testExecute(
        array $postData,
        bool $isAjaxRequest,
        bool $isModuleEnabled,
        ?string $jsonString
    ): void {
        $request     = $this->createMock(Http::class);
        $jsonResult  = $this->createMock(JsonResult::class);
        $configModel = $this->createMock(Config::class);
        $jsonFactory = $this->createMock(JsonFactory::class);
        $fetcher     = $this->createMock(Fetch::class);

        $subject = new Suggest(
            $request,
            $configModel,
            $jsonFactory,
            $fetcher
        );

        $request->expects(self::atMost(1))
            ->method('isAjax')
            ->willReturn($isAjaxRequest);

        $getPostExpects = $isAjaxRequest && $isModuleEnabled
            ? self::once()
            : self::never();

        $request->expects($getPostExpects)
            ->method('getPost')
            ->with('openkvk')
            ->willReturn($postData);

        $configModel->expects(self::atMost(1))
            ->method('isEnabled')
            ->willReturn($isModuleEnabled);

        $jsonFactory->expects(self::atMost(1))
            ->method('create')
            ->willReturn($jsonResult);

        $jsonResult->expects(self::atMost(1))
            ->method('setData')
            ->willReturn('string');

        $subject->execute();
    }

    /**
     * Create the data provider used in the tests.
     *
     * @return array
     */
    public function setDataProvider(): array
    {
        return [
            // If module is disabled or the request is not an AJAX request
            [
                [],
                false, // Is not an AJAX call
                true,
                null
            ],
            [
                [],
                true,
                false, // Module is disabled
                null
            ],

            // Chamber of Commerce is filled in, different results
            [
                ['coc' => '12345678', 'postcode' => '', 'housenumber' => ''],
                true,
                true,
                null
            ],
            [
                ['coc' => '12345678', 'postcode' => '', 'housenumber' => ''],
                true,
                true,
                'String that is not actual JSON'
            ],
            [
                ['coc' => '12345678', 'postcode' => '', 'housenumber' => ''],
                true,
                true,
                $this->validJsonResult
            ],

            // Postcode and house number are filled in, different results
            [
                ['coc' => '', 'postcode' => '1234AA', 'housenumber' => '1'],
                true,
                true,
                null
            ],
            [
                ['coc' => '', 'postcode' => '1234AA', 'housenumber' => '1'],
                true,
                true,
                'String that is not actual JSON'
            ],
            [
                ['coc' => '', 'postcode' => '1234AA', 'housenumber' => '1'],
                true,
                true,
                $this->validJsonResult
            ],
        ];
    }
}
