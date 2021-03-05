<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Tests\Setup\Patch\Data;

use Elgentos\OpenKvk\Setup\Patch\Data\CustomerAddChamberOfCommerce;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Elgentos\OpenKvk\Setup\Patch\Data\CustomerAddChamberOfCommerce
 */
class CustomerAddChamberOfCommerceTest extends TestCase
{
    /**
     * @covers ::apply
     * @covers ::__construct
     *
     * @return void
     */
    public function testApply()
    {
        $moduleDataSetup = $this
            ->createMock(ModuleDataSetupInterface::class);

        $moduleDataSetup->expects(self::once())
            ->method('startSetup');

        $moduleDataSetup->expects(self::once())
            ->method('endSetup');

        $customerSetupFactory = $this
            ->getMockBuilder('\Magento\Customer\Setup\CustomerSetupFactory')
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->setMethods(['create'])
            ->getMock();

        $customerEntity = $this->createMock(Customer::class);
        $customerEntity->expects(self::once())
            ->method('getData')
            ->with('default_attribute_set_id')
            ->willReturn(1);

        $attribute = $this->createMock(Attribute::class);
        $attribute->expects(self::exactly(2))
            ->method('addData')
            ->withConsecutive(
                [['used_in_forms' => ['adminhtml_customer', 'customer_account']]],
                [['attribute_set_id' => 1, 'attribute_group_id' => 1]]
            );

        $eavConfig = $this->createMock(\Magento\Eav\Model\Config::class);
        $eavConfig->expects(self::once())
            ->method('getEntityType')
            ->willReturn($customerEntity);

        $eavConfig->expects(self::once())
            ->method('getAttribute')
            ->with(Customer::ENTITY, 'coc')
            ->willReturn($attribute);

        $customerSetup = $this->createMock(CustomerSetup::class);
        $customerSetup->expects(self::exactly(2))
            ->method('getEavConfig')
            ->willReturn($eavConfig);

        $customerSetup->expects(self::once())
            ->method('addAttribute')
            ->with(
                Customer::ENTITY,
                'coc',
                [
                    'type' => 'varchar',
                    'label' => 'Chamber of Commerce',
                    'input' => 'text',
                    'required' => false,
                    'position' => 103,
                    'visible' => true,
                    'system' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'is_searchable_in_grid' => false,
                ]
            );

        $customerSetupFactory->expects(self::once())
            ->method('create')
            ->willReturn($customerSetup);

        $attributeSetFactory = $this
            ->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\SetFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $attributeSet = $this->createMock(Set::class);
        $attributeSet->expects(self::once())
            ->method('getDefaultGroupId')
            ->willReturn(1);

        $attributeSetFactory->expects(self::once())
            ->method('create')
            ->willReturn($attributeSet);

        $subject = new CustomerAddChamberOfCommerce(
            $moduleDataSetup,
            $customerSetupFactory,
            $attributeSetFactory
        );

        $subject->apply();
    }

    /**
     * @covers ::getDependencies
     *
     * @return void
     */
    public function testGetDependencies(): void
    {
        $result = CustomerAddChamberOfCommerce::getDependencies();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @covers ::__construct
     * @covers ::getAliases
     *
     * @return void
     */
    public function testGetAliases(): void
    {
        $customerSetupFactory = $this
            ->getMockBuilder('\Magento\Customer\Setup\CustomerSetupFactory')
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();

        $customerSetup = $this->createMock(CustomerSetup::class);

        $attributeSetFactory = $this
            ->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\SetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new CustomerAddChamberOfCommerce(
            $this->createMock(ModuleDataSetupInterface::class),
            $customerSetupFactory,
            $attributeSetFactory
        );

        $result = $subject->getAliases();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
