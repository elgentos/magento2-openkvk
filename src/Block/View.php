<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Block;

use Elgentos\OpenKvk\Model\Config;
use Magento\Framework\View\Element\Template;

class View extends Template
{
    /** @var Config */
    private Config $configModel;

    /**
     * Constructor.
     *
     * @param Template\Context $context
     * @param Config           $configModel
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Config $configModel,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configModel = $configModel;
    }

    /**
     * Return if the module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configModel->isEnabled();
    }
}
