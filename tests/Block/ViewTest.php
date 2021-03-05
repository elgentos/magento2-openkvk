<?php

namespace Elgentos\OpenKvk\Tests\Block;

use Elgentos\OpenKvk\Block\View;
use Elgentos\OpenKvk\Model\Config;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Elgentos\OpenKvk\Block\View
 */
class ViewTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isEnabled
     *
     * @return void
     */
    public function testIsEnabled(): void
    {
        $subject = new View(
            $this->createMock(Context::class),
            $this->createMock(Config::class)
        );

        $this->assertIsBool($subject->isEnabled());
    }
}
