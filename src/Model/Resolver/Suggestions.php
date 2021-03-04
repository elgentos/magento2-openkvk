<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Model\Resolver;

use Elgentos\OpenKvk\Model\Config;
use Elgentos\OpenKvk\Model\Fetch;
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

    /** @var Fetch */
    private Fetch $fetcher;

    /**
     * Constructor.
     *
     * @param Config      $configModel
     * @param Fetch       $fetcher
     */
    public function __construct(
        Config $configModel,
        Fetch $fetcher
    ) {
        $this->configModel       = $configModel;
        $this->fetcher           = $fetcher;
    }

    /**
     * @param Field            $field
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     * @param array|null       $value
     * @param array|null       $args
     *
     * @return Value|mixed|void
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['coc']) && !isset($args['postcode'])) {
            throw new GraphQlInputException(__('No data provided.'));
        }

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
