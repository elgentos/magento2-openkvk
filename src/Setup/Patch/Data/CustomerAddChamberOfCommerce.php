<?php

declare(strict_types=1);

namespace Elgentos\OpenKvk\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CustomerAddChamberOfCommerce implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private ModuleDataSetupInterface $moduleDataSetup;

    /** @var CustomerSetupFactory */
    private CustomerSetupFactory $customerSetupFactory;

    /** @var SetFactory */
    private SetFactory $attributeSetFactory;

    /**
     * CustomerAddChamberOfCommerce constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory     $customerSetupFactory
     * @param SetFactory               $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup      = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory  = $attributeSetFactory;
    }

    /**
     * Add new attribute for the Chamber of Commerce number.
     *
     * @return CustomerAddChamberOfCommerce
     */
    public function apply(): CustomerAddChamberOfCommerce
    {
        $this->moduleDataSetup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /** @var Customer $customerEntity */
        $customerEntity   = $customerSetup->getEavConfig()
            ->getEntityType(Customer::ENTITY);
        $attributeSetId   = $customerEntity->getData('default_attribute_set_id');
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
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

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, 'coc');
        $attribute->addData(
            [
                'used_in_forms' => [
                    'adminhtml_customer',
                    'customer_account'
                ]
            ]
        );
        $attribute->addData(
            [
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId
            ]
        );

        $attribute->save();

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
