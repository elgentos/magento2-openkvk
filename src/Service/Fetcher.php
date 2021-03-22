<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Service;

use Elgentos\OpenKvk\Model\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Magento\Framework\Serialize\Serializer\Json;

class Fetcher
{
    public const OPENKVK_BASE_URL = 'https://api.overheid.io/openkvk/',
        OPENKVK_SUGGEST_SIZE      = 99;

    /** @var Config */
    private Config $configModel;

    /** @var Client */
    private Client $client;

    /** @var Json */
    private Json $serializer;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param Config $configModel
     * @param Json   $serializer
     */
    public function __construct(
        Client $client,
        Config $configModel,
        Json $serializer
    ) {
        $this->client      = $client;
        $this->configModel = $configModel;
        $this->serializer  = $serializer;
    }

    /**
     * Fetch companies by the entered Chamber of Commerce number.
     *
     * @param string $query
     *
     * @return array
     */
    public function fetchSuggestResultsByCoc(string $query): array
    {
        $params = [
            'size' => self::OPENKVK_SUGGEST_SIZE,
            'fields' => [
                'handelsnaam',
                'postcode',
                'dossiernummer',
                'plaats',
                'straat',
                'huisnummer',
                'actief'
            ],
            'queryfields' => [
                'dossiernummer'
            ],
            'query' => $query
        ];

        return $this->fetch($params);
    }

    /**
     * Fetch companies by the entered address.
     *
     * @param string      $postcode
     * @param string|null $houseNumber
     *
     * @return array
     */
    public function fetchSuggestResultsByAddress(string $postcode, ?string $houseNumber): array
    {
        $params = [
            'size' => self::OPENKVK_SUGGEST_SIZE,
            'fields' => [
                'handelsnaam',
                'postcode',
                'dossiernummer',
                'plaats',
                'straat',
                'huisnummer',
                'actief'
            ],
            'filters' => [
                'postcode' => str_replace(' ', '', $postcode)
            ]
        ];

        if (!empty($houseNumber)) {
            $params['filters']['huisnummer'] = $houseNumber;
        }

        return $this->fetch($params);
    }

    /**
     * Get the list of headers for the API calls.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return ['ovio-api-key' => $this->configModel->getApiKey()];
    }

    /**
     * Do the call to the API to fetch the data and normalize it.
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    private function fetch(array $params): array
    {
        try {
            $result   = [];
            $response = $this->client->request(
                'GET',
                self::OPENKVK_BASE_URL . '?' . http_build_query($params),
                [
                    'headers' => $this->getHeaders()
                ]
            );

            $responseBody = $response->getBody();

            if ($response->getStatusCode() !== 200 || empty($responseBody)) {
                return ['error' => __('No results found.')];
            }

            $response = $this->serializer->unserialize($responseBody);

            if (isset($response['_embedded']) && isset($response['_embedded']['bedrijf'])) {
                foreach ($response['_embedded']['bedrijf'] as $item) {
                    if (!isset($item['actief']) || $item['actief'] === false) {
                        continue;
                    }

                    $result[] = [
                        'company' => $item['handelsnaam'] ?? null,
                        'coc' => $item['dossiernummer'] ?? null,
                        'city' => $item['plaats'] ?? null,
                        'zip' => $item['postcode'] ?? null,
                        'street_1' => $item['straat'] ?? null,
                        'street_2' => $item['huisnummer'] ?? null,
                    ];
                }
            }
        } catch (InvalidArgumentException $e) {
            $result = ['error' => __('Unable to fetch results')];
        }

        return $result;
    }
}
