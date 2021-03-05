<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Tests\Model\Resolver;

use Elgentos\OpenKvk\Model\Config;
use Elgentos\OpenKvk\Model\Resolver\Suggestions;
use Elgentos\OpenKvk\Service\Fetcher;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Elgentos\OpenKvk\Model\Resolver\Suggestions
 */
class SuggestionsTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::resolve
     *
     * @dataProvider dataProvider
     *
     * @param array $args
     *
     * @return void
     */
    public function testResolve(array $args)
    {
        $subject = new Suggestions(
            $this->createMock(Config::class),
            $this->createMock(Fetcher::class)
        );

        $result = $subject->resolve(
            $this->createMock(Field::class),
            $this->createMock(ContextInterface::class),
            $this->createMock(ResolveInfo::class),
            [],
            $args
        );

        $this->assertIsArray($result);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                []
            ],
            [
                ['coc' => '123456789']
            ],
            [
                ['postcode' => '1234AB', 'housenumber' => '1']
            ],
            [
                ['foo' => 'bar']
            ]
        ];
    }
}
