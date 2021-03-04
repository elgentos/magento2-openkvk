<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\OpenKvk\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

class Fetch
{
    private const OPENKVK_BASE_URL = 'https://api.overheid.io/',
        OPENKVK_SUGGEST_SIZE       = 25;

    /** @var Config */
    private Config $configModel;

    /** @var Curl */
    private Curl $client;

    /** @var Json */
    private Json $serializer;

    /**
     * Constructor.
     *
     * @param Curl   $client
     * @param Config $configModel
     * @param Json   $serializer
     */
    public function __construct(
        Curl $client,
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
        $url      = self::OPENKVK_BASE_URL . 'openkvk/';
        $params   = [
            'size' => self::OPENKVK_SUGGEST_SIZE,
            'fields' => [
                'handelsnaam',
                'postcode',
                'dossiernummer',
                'plaats',
                'straat',
                'huisnummer'
            ],
            'queryfields' => [
                'dossiernummer'
            ],
            'query' => $query
        ];

        return $this->fetch($url, $params);
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
        $url    = self::OPENKVK_BASE_URL . 'openkvk/';
        $params = [
            'size' => self::OPENKVK_SUGGEST_SIZE,
            'fields' => [
                'handelsnaam',
                'postcode',
                'dossiernummer',
                'plaats',
                'straat',
                'huisnummer'
            ],
            'filters' => [
                'postcode' => $postcode,
                'huisnummer' => $houseNumber
            ]
        ];

        return $this->fetch($url, $params);
    }

    /**
     * Get the list of headers for the API calls.
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return ['ovio-api-key' => $this->configModel->getApiKey()];
    }

    /**
     * Do the call to the API to fetch the data and normalize it.
     *
     * @param string $url
     * @param array  $params
     *
     * @return array
     */
    private function fetch(string $url, array $params): array
    {
        $this->client->setHeaders($this->getHeaders());
        $this->client->get($url . '?' . http_build_query($params));

        $result   = [];
        $response = $this->client->getBody();

        if (empty($response)) {
            return ['error' => __('No results found.')];
        }

        try {
            $response = $this->serializer->unserialize($response);

            if (isset($response['_embedded']) && isset($response['_embedded']['bedrijf'])) {
                foreach ($response['_embedded']['bedrijf'] as $item) {
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
        } catch (\InvalidArgumentException $e) {
            $result = ['error' => __('Unable to fetch results')];
        }

        return $result;
    }
}
