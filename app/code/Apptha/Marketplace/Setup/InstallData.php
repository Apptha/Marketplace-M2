<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category     Apptha
 * @package      Apptha_Marketplace
 * @version      1.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
namespace Apptha\Marketplace\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
class InstallData implements InstallDataInterface {
    protected $groupFactory;
    private $categorySetupFactory;

    /**
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory, CategorySetupFactory $categorySetupFactory) {
        $this->groupFactory = $groupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }
    /**
     * (non-PHPdoc)
     *
     * @see \Apptha\Marketplace\Setup\InstallData::install()
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup ();
        $group = $this->groupFactory->create ();
        $group->setCode ( 'Marketplace Seller' )->save ();

        $categorySetup = $this->categorySetupFactory->create ( [
                'setup' => $setup
        ] );

        $categorySetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'seller_id', ['type' => 'int','backend' => '','frontend' => '','label' => 'Seller Id',
                'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,'user_defined' => false,'default' => '',
                'searchable' => false,'filterable' => false,'comparable' => false,'visible_on_front' => false,'used_in_product_listing' => true,'unique' => false,
                'apply_to' => ''] );

        $categorySetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'product_approval', ['type' => 'int','backend' => '','frontend' => '','label' => 'Product Auto Approval','input' => 'select','class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean','group' => 'Marketplace Details','visible' => true,
                'required' => false,'user_defined' => false,'default' => '','searchable' => false,'filterable' => false,
                'comparable' => false,'visible_on_front' => false,'used_in_product_listing' => true,'apply_to' => ''] );


            $attributeSetup = $this->categorySetupFactory->create ( [
                    'setup' => $setup
            ] );
            /**
             * Is Assign Product Attribute
             */
            $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'is_assign_product', ['type' => 'int','backend' => '','frontend' => '','label' => 'Is Assign Product',
                    'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,'user_defined' => false,'default' => '',
                    'searchable' => false,'filterable' => false,'comparable' => false,'visible_on_front' => false,
                    'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );
            /**
             * Assign Product Id Attribute
             */
            $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'assign_product_id', [
                    'type' => 'int','backend' => '','frontend' => '','label' => 'Assign Product Id','input' => 'text',
                    'user_defined' => false,'default' => '','searchable' => false,'class' => '',
                    'source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,
                    'filterable' => false,'comparable' => false,'visible_on_front' => false,
                    'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );
            /**
             * Config Assign Simple Id Attribute
             */
            $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'config_assign_simple_id', [ 'type' => 'int',
                    'backend' => '','frontend' => '','label' => 'Assign product id [Simple Product]','input' => 'text',
                    'class' => '','source' => '','group' => 'Marketplace Details','visible' => true,'required' => false,
                    'user_defined' => false,'default' => '','searchable' => false,'filterable' => false,'comparable' => false,
                    'visible_on_front' => false,'used_in_product_listing' => true,'unique' => false,'apply_to' => ''] );
            /**
             * Creating attribute for national shipping amount
             */
            $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'national_shipping_amount', [
                    'type' => 'int','backend' => '','frontend' => '','label' => 'National Shipping Amount',
                    'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details',
                    'visible' => true,'required' => false,'user_defined' => false,'default' => '',
                    'searchable' => false,'filterable' => false,'comparable' => false,'visible_on_front' => false,
                    'used_in_product_listing' => true,'unique' => false,'apply_to' => '' ] );

            /**
             * Creating attribute for international shipping amount
             */
            $attributeSetup->addAttribute ( \Magento\Catalog\Model\Product::ENTITY, 'international_shipping_amount', [
                    'type' => 'int','backend' => '','frontend' => '','label' => 'International Shipping Amount',
                    'input' => 'text','class' => '','source' => '','group' => 'Marketplace Details','visible' => true,
                    'required' => false,'user_defined' => false,'default' => '','searchable' => false,'filterable' => false,
                    'comparable' => false,'visible_on_front' => false,'used_in_product_listing' => true,'unique' => false,
                    'apply_to' => ''] );
            /**
             * Adding new attribute group name "Custom Attribute"
             */
            $attributeSetup->addAttributeGroup ( \Magento\Catalog\Model\Product::ENTITY, 'Default', 'Custom Attribute', 1000 );
        $setup->endSetup ();
    }
}