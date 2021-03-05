<?php

namespace Elgentos\OpenKvk\Tests\Model;

use Elgentos\OpenKvk\Model\Config;
use Magento\Framework\App\Config as ScopeConfig;
use Magento\Framework\App\Config\ScopeCodeResolver;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Elgentos\OpenKvk\Model\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isEnabled
     *
     * @return void
     */
    public function testIsEnabled()
    {
        $scopeConfig = new ScopeConfig(
            $this->createMock(ScopeCodeResolver::class)
        );

        $subject = new Config($scopeConfig);

        $this->assertIsBool($subject->isEnabled());
    }

    /**
     * @covers ::__construct
     * @covers ::getApiKey
     *
     * @return void
     */
    public function testGetApiKey()
    {
        $scopeConfig = $this->createMock(ScopeConfig::class);
        $scopeConfig->expects(self::once())
            ->method('getValue')
            ->willReturn('foobar');

        $subject = new Config($scopeConfig);

        $this->assertIsString($subject->getApiKey());
    }
}
