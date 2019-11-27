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
 * This class used to manipulate configurable product section
 */
class Configurable extends \Magento\Framework\View\Element\Template {
    /**
     * Get product approval or not
     *
     * @return int
     */
    public function getProductTypes() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return explode ( ',', $objectManager->get ( 'Apptha\Marketplace\Helper\System' )->getProductTypes () );
    }
    
    /**
     * Get configurable attributes ajax url
     *
     * @return string
     */
    public function getConfigurableAttributesUrl() {
        return $this->getUrl ( 'marketplace/configurable/attributes' );
    }
    
    /**
     * Get configurable attribute options ajax url
     *
     * @return string
     */
    public function getConfigurableOptionsUrl() {
        return $this->getUrl ( 'marketplace/configurable/options' );
    }
    
    /**
     * Get configurable bulk images & price ajax url
     *
     * @return string
     */
    public function getConfigurableBulkUrl() {
        return $this->getUrl ( 'marketplace/configurable/image' );
    }
    
    /**
     * Get Attribute Set Id
     *
     * @return int
     */
    public function getAttributeSetId() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->getDefaultAttributeSetId ();
    }
    
    /**
     * Get product data
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
     * Get configurable product attributes
     *
     * @param object $product            
     *
     * @return array
     */
    public function getConfigurableProductAttributes($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductAttributeIds ( $product );
    }
    
    /**
     * Get configurable product attribute label by attribute code
     *
     * @return string
     */
    public function getConfigurableProductAttributeLabel($attributeCode) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product\Attribute\Repository' )->get ( $attributeCode )->getFrontendLabel ();
    }
    
    /**
     * Get used associated product data
     *
     * @param object $productData            
     *
     * @return array $associatedProductsIds
     */
    public function getUsedAssociatedProductData($productData) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $usedProducts = $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $productData )->getData ();
        /**
         * Preparing associated product entity ids
         */
        $associatedProductsIds = array ();
        foreach ( $usedProducts as $usedProduct ) {
            $associatedProductsIds [] = $usedProduct ['entity_id'];
        }
        return $associatedProductsIds;
    }
    
    /**
     * Get qty for configurable associated product
     *
     * @param int $usedProductData            
     *
     * @return int
     */
    public function getQtyForConfigurableAssoicatedProduct($usedProductId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $usedProductId, 'product_id' )->getQty ();
    }
    
    /**
     * Get simple product media image url
     *
     * @return string
     */
    public function getSimpleProductMediaImageUrl() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'catalog/product';
    }
    
    /**
     * Get base currency code for configurale product variants
     *
     * @return string
     */
    public function getAssociatedVariantsBaseCurrency() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Block\Product\Add' )->getBaseCurrency ();
    }
    
    /**
     * Get associated product ids
     *
     * @param string $simpleProducts
     *
     * @return array $simpleProdouctIds
     */
    public function getAssociatedProductIds($simpleProducts) {
        /**
         * Declare simple product skus
         * Declare simple product ids
         */
        $simpleProductSkus = $simpleProdouctIds = array ();
        foreach ( $simpleProducts as $simpleProduct ) {
            $splitSimpleProductsAttributes = explode ( ",", $simpleProduct );
            foreach ( $splitSimpleProductsAttributes as $splitSimpleProductsAttribute ) {
                $splitSimpleProductsSku = explode ( "=", $splitSimpleProductsAttribute );
                if ($splitSimpleProductsSku [0] == 'sku') {
                    $simpleProductSkus [] = $splitSimpleProductsSku [1];
                    continue;
                }
            }
        }
    
        if (count ( $simpleProductSkus ) >= 1) {
            /**
             * Create instance for object manager
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $productModel = $objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ();
            $productModel->addFieldToFilter ( 'sku', array (
                    'in' => $simpleProductSkus
            ) );
            $simpleProdouctIds = $productModel->getColumnValues ( 'entity_id' );
        }
    
        return $simpleProdouctIds;
    }
    /**
     * Add product custom attributes
     *
     * @param object $product
     * @param array $customAttributes
     * @param array $productData
     *
     * @return object $product
     */
    public function addCustomAttributes($product, $customAttributes, $productData) {
        $customAttributeArray = array ();
        foreach ( $customAttributes as $customAttribute ) {
            if (isset ( $productData [$customAttribute] )) {
    
                /**
                 * Save multi values
                 */
                if (is_array ( $productData [$customAttribute] )) {
                    $customAttributeArray [$customAttribute] = implode ( ',', $productData [$customAttribute] );
                } else {
                    $customAttributeArray [$customAttribute] = $productData [$customAttribute];
                }
            }
        }
        if (count ( $customAttributeArray ) >= 1) {
            $product->addData ( $customAttributeArray );
        }
        return $product;
    }
    
    /**
     * Get edit existing product url
     *
     * @param int product id
     *
     * @return string url
     */
    public function getPrductEditUrl($productId) {
        return $this->getUrl ( 'marketplace/product/add',['config' => '1']) . 'product_id/' . $productId;
    }
}
