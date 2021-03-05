<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Model\Resolver;

use Elgentos\OpenKvk\Model\Config;
use Elgentos\OpenKvk\Service\Fetcher;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Suggestions implements ResolverInterface
{
    /** @var Config */
    private Config $configModel;

    /** @var Fetcher */
    private Fetcher $fetcher;

    /**
     * Constructor.
     *
     * @param Config  $configModel
     * @param Fetcher $fetcher
     */
    public function __construct(
        Config $configModel,
        Fetcher $fetcher
    ) {
        $this->configModel = $configModel;
        $this->fetcher     = $fetcher;
    }

    /**
     * @param Field            $field
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     * @param array|null       $value
     * @param array|null       $args
     *
     * @return Value|array
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $coc         = $args['coc'] ?? null;
        $postcode    = $args['postcode'] ?? null;
        $houseNumber = $args['housenumber'] ?? null;

        if ($coc) {
            return $this->fetcher->fetchSuggestResultsByCoc($args['coc']);
        } elseif ($postcode) {
            return $this->fetcher->fetchSuggestResultsByAddress($postcode, $houseNumber);
        }

        return [];
    }
}
