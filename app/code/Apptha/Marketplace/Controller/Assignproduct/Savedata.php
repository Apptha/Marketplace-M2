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
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
namespace Apptha\Marketplace\Controller\Assignproduct;

class Savedata extends \Magento\Framework\App\Action\Action {
    /**
     * Function to create new assign products
     *
     * @return $array
     */
    public function execute() {
        $childArray = $attributeValues = $baseSimpleIds = $childrenProductIds = $newProducts = array ();
        $variants = $this->getRequest ()->getParam ( 'variant' );
        $assignProd = $this->getRequest ()->getParam ( 'assignprod' );
        $assignProductDataPrice = $this->getRequest ()->getParam ( 'price' );
        $assignProductDatasqty = $this->getRequest ()->getParam ( 'qty' );
        $baseConfigProdId = $this->getRequest ()->getParam ( 'prod-id' );
        $parentId = $baseConfigProdId;
        $baseConfigproduct = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $baseConfigProdId );
        $baseConfigAttributeSetId = $baseConfigproduct->getAttributeSetId ();
        $baseTypeId = $baseConfigproduct->getTypeId ();
        /**
         * Configurable product
         */
        $nationalShippingAmount = $this->getRequest ()->getParam ( 'national_shipping_amount' );
        $internationalShippingAmount = $this->getRequest ()->getParam ( 'international_shipping_amount' );
        $this->saveBaseProductInfo ( $parentId, $assignProd,$nationalShippingAmount,$internationalShippingAmount);
        if ($baseTypeId == "configurable") {
            
            $attributes = $baseConfigproduct->getTypeInstance ( true )->getConfigurableAttributes ( $baseConfigproduct );
            $baseConfigProducts = $this->_objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $baseConfigproduct );
            $childProducts = $baseConfigProducts->getData ();
            $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $sellerId = $customerSession->getId ();
            foreach ( $childProducts as $childProduct ) {
                $childrenProductIds [] = $childProduct ['entity_id'];
            }
            $result = array_diff ( $childrenProductIds, $variants );
            if (count ( $result ) > 1) {
                foreach ( $result as $productValue ) {
                    if (($key = array_search ( $productValue, $childrenProductIds )) !== false) {
                        unset ( $childrenProductIds [$key] );
                    }
                }
            }
            
            foreach ( $variants as $variant ) {
                $childArray ['id'] = $variant;
                $childArray [$variant] ['price'] = $assignProductDataPrice [$variant];
                $childArray [$variant] ['qty'] = $assignProductDatasqty [$variant];
                /**
                 * edit and save already created variants
                 */
                
                if (in_array ( $variant, $childrenProductIds )) {
                    $this->update ( $variant, $childArray );
                }
                
                /**
                 * New Variant Simple Product Creation
                 */
                if (! in_array ( $variant, $childrenProductIds )) {
                    $newProducts [] = $variant;
                    $productData = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $variant );
                    foreach ( $attributes as $attributeData ) {
                        $attributeId = $attributeData->getProductAttribute ()->getAttributeCode ();
                        $optionValue = $productData->getData ( $attributeData->getProductAttribute ()->getAttributeCode () );
                        $attributeValues [$attributeId] = $optionValue;
                    }
                    /**
                     * Associate Simple Product to Configurable Product
                     */
                    $baseSimpleIds [] = $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Assignproduct\Save' )->saveAssignproduct ( $childArray, $sellerId, $productData, $attributeValues, $baseConfigproduct, $parentId );
                }
                $configurableProduct = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $parentId );
                $baseSimpleIds = array_merge ( $baseSimpleIds, $childrenProductIds );
                $configurableProduct->setAffectConfigurableProductAttributes ($baseConfigAttributeSetId );
                $configurableProduct->setNewVariationsAttributeSetId ( $baseConfigAttributeSetId );
                 if(empty($parentId)){
                    $configurableProduct->setAssociatedProductIds ( $baseSimpleIds );
                }
                $configurableProduct->setCanSaveConfigurableAttributes ( true );
                $configurableProduct->save ();
            }
        }
        $this->messageManager->addSuccess ( __ ( 'You saved the product.' ) );
        $this->_redirect ( 'marketplace/assignproduct/manage' );
    }
    
    /**
     * Function to update Existing Products
     * 
     * @return array
     */
    public function update($variant, $childArray) {
        $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $variant );
        $product->setPrice ( $childArray [$variant] ['price'] );
        $product->save ();
        $assignproductStockData ['quantity_and_stock_status'] ['qty'] = $childArray [$variant] ['qty'];
        $stockData = $this->_objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $variant, 'product_id' );
        $stockData->setQty ( $assignproductStockData ['quantity_and_stock_status'] ['qty'] );
        $stockData->save ();
    }
    /**
     *
     * Save Base Assign Product
     * 
     * @return array
     */
    public function saveBaseProductInfo ( $parentId, $assignProd,$nationalShippingAmount,$internationalShippingAmount) {
        $baseProduct = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $parentId );
        $currentTypeId = $baseProduct->getTypeId ();
        if ($currentTypeId != "configurable") {
            $baseProduct->setPrice ( $assignProd ['price'] );
        }
        $baseProduct->setSku ( $assignProd ['sku'] );
        $baseProduct->setDescription ( $assignProd ['description'] );
        if (! empty ( $nationalShippingAmount )) {
            $baseProduct->setNationalShippingAmount ( $nationalShippingAmount );
        }
        if (! empty ( $internationalShippingAmount )) {
            $baseProduct->setInternationalShippingAmount ( $internationalShippingAmount );
        }
        $baseProduct->save ();
        if ($currentTypeId != "configurable") {
            
            $assignproductStockData ['quantity_and_stock_status'] ['qty'] = $assignProd ['qty'];
            $assignproductStockData ['quantity_and_stock_status'] ['is_in_stock'] = $assignProd ['is-in-stock'];
            $stockData = $this->_objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $parentId, 'product_id' );
            $stockData->setQty ( $assignproductStockData ['quantity_and_stock_status'] ['qty'] );
            $stockData->setIsInStock ( $assignproductStockData ['quantity_and_stock_status'] ['is_in_stock'] );
            $stockData->save ();
        }
    }
}