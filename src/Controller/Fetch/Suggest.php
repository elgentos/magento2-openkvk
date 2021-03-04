<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\OpenKvk\Controller\Fetch;

use Elgentos\OpenKvk\Model\Config;
use Elgentos\OpenKvk\Model\Fetch;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Suggest implements HttpPostActionInterface
{
    /** @var Http */
    private Http $request;

    /** @var Config */
    private Config $configModel;

    /** @var JsonFactory */
    private JsonFactory $resultJsonFactory;

    /** @var Fetch */
    private Fetch $fetcher;

    /**
     * Constructor.
     *
     * @param Http        $request
     * @param Config      $configModel
     * @param JsonFactory $resultJsonFactory
     * @param Fetch       $fetcher
     */
    public function __construct(
        Http $request,
        Config $configModel,
        JsonFactory $resultJsonFactory,
        Fetch $fetcher
    ) {
        $this->request           = $request;
        $this->configModel       = $configModel;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fetcher           = $fetcher;
    }

    /**
     * Execute the command to get the results.
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $data   = [];
        $result = $this->resultJsonFactory->create();

        if ($this->request->isAjax() && $this->configModel->isEnabled()) {
            $formData = $this->request->getPost('openkvk');

            if (!empty($formData) && (empty($formData['coc']) || empty($formData['postcode']))) {
                if (!empty($formData['coc'])) {
                    $data = $this->fetcher->fetchSuggestResultsByCoc($formData['coc']);
                }

                if (!empty($formData['postcode'])) {
                    $data = $this->fetcher->fetchSuggestResultsByAddress(
                        $formData['postcode'],
                        $formData['houseNumber'] ?? null
                    );
                }
            }
        }

        return $result->setData($data);
    }
}
