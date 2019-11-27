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
namespace Apptha\Marketplace\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Instance;

/**
 * This class used to configurable product image
 */
class Summary extends \Magento\Framework\View\Element\Template {
    
    /**
     * To generate combinations
     *
     * @param array $data
     * @param array $all
     * @param array $group
     * @param mixed $val
     * @param int $i
     * @return array $all
     */
    public function generateCombinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0) {
        $keys = array_keys ( $data );
        if (isset ( $value ) === true) {
            array_push ( $group, $value );
        }
        if ($i >= count ( $data )) {
            array_push ( $all, $group );
        } else {
            $currentKey = $keys [$i];
            $currentElement = $data [$currentKey];
            foreach ( $currentElement as $val ) {
                $this->generateCombinations ( $data, $all, $group, $val, $i + 1 );
            }
        }
        return $all;
    }
    
    /**
     * Get media image url
     *
     * @return string
     */
    public function getMediaImageUrl() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'tmp/catalog/product';
    }
    
    /**
     * Get configurable product data
     *
     * @param int $productId            
     *
     * @return object
     */
    public function getConfigurableProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
    }
    
    /**
     * Get existing product variants array
     */
    public function getexistingProductVariants($productId, $product, $allAttributesIds, $allAttributes) {
        /**
         * Set variant flag 0
         */
        $variantsFlag = 0;
        $existProductVariants = $associatedProductsIds = array ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $productAttributes = $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductAttributeIds ( $product );
        
        /**
         * Checking product attributes equals to current attribute change
         */
        if (count ( $allAttributesIds ) == count ( $productAttributes )) {
            $diffAttributes = array_diff ( $allAttributesIds, $productAttributes );
            if (count ( $diffAttributes ) <= 0) {
                $variantsFlag = 1;
            }
        }
        
        /**
         * Get existing associted product ids
         */
        if ($variantsFlag == 1) {
            $existProducts = $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $product )->getData ();
            foreach ( $existProducts as $existProduct ) {
                $associatedProductsIds [] = $existProduct ['entity_id'];
            }
        }
        
        /**
         * Prepare exist product variants array
         */
        foreach ( $associatedProductsIds as $associatedProductsId ) {
            $associatedProductData = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $associatedProductsId );
            foreach ( $allAttributes as $allAttribute ) {
                $existProductVariants [$associatedProductsId] [] = $associatedProductData->getData ( $allAttribute );
            }
        }
        return $existProductVariants;
    }
    
    /**
     * Get qty for product
     *
     * @param int $simpleProductIdValue            
     *
     * @return int
     */
    public function getQtyForProduct($simpleProductIdValue) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $simpleProductIdValue, 'product_id' )->getQty ();
    }
    
    /**
     * Get price for product
     *
     * @param int $simpleProductIdValue            
     *
     * @return float
     */
    public function getPriceForProduct($simpleProductIdValue) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $simpleProductIdValue )->getPrice ();
    }
    
    /**
     * Get Base Currency For Configurable Product Summary
     *
     * @return string
     */
    public function getBaseCurrencyForConfigurableProductSummary() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Block\Product\Add' )->getBaseCurrency ();
    }
}
