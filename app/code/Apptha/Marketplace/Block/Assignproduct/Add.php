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
namespace Apptha\Marketplace\Block\Assignproduct;

use Magento\Framework\View\Element\Template;

/**
 * This class contains Add Assign Product Functions
 */
class Add extends \Magento\Framework\View\Element\Template {
    protected $request;
    protected $storeManager;
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\App\Request\Http $request, array $data = []) {
        parent::__construct ( $context, $data );
        $this->_request = $request;
        $this->storeManager =  $context->getStoreManager();
    }
    
    /**
     * Prepare layout for manage product
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        $handle = $this->_request->getFullActionName ();
        if ($handle == "marketplace_assignproduct_edit") {
            $this->pageConfig->getTitle ()->set ( __ ( 'Edit Product' ) );
        } else {
            $this->pageConfig->getTitle ()->set ( __ ( 'Add Product' ) );
        }
        return parent::_prepareLayout ();
    }
    
    /**
     * Save Action
     * 
     * @return string
     */
    public function getSaveAssignProductUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/save' );
    }
    
    /**
     * Save Action
     * 
     * @return string
     */
    public function getEditAssignProductUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/savedata' );
    }
    /**
     * Get ajax url for sku validation
     *
     * @return string
     */
    public function getSkuValidateAjaxUrl() {
        return $this->getUrl ( 'marketplace/product/skuvalidate' );
    }
    
    /**
     * Getting stock state object
     *
     * @param int $productId            
     *
     * @return object $stockData
     */
    public function getProductStockDataQty($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $productId, 'product_id' );
    }
    /**
     * Function to load Assign Product Details
     */
    public function getAssignProductDetails($proId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $proId );
        $prodSku = $product->getSku ();
        $prodPrice = $product->getPrice ();
        $prodDesc = $product->getDescription ();
        $stockDetails = $this->getProductStockDataQty ( $proId );
        $prodQty = $stockDetails->getQty ();
        $prodIsInStock = $stockDetails->getIsInStock ();
        if (empty ( $prodIsInStock )) {
            $prodIsInStock = 0;
        }
        return array (
                'sku' => $prodSku,
                'price' => $prodPrice,
                'desc' => $prodDesc,
                'qty' => $prodQty,
                'is_in_stock' => $prodIsInStock 
        );
    }
    
    /**
     * Getting product data
     *
     * @param int $productId            
     *
     * @return object $productData
     */
    public function getProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
    }
    /**
     * Getting Assign Product Configurable Attributes
     * 
     * @return array
     */
    public function getAssignProductAttributes($product) {
        return $product->getTypeInstance ( true )->getConfigurableAttributes ( $product );
    }
    /**
     * Get Product Used Attributes
     */
    public function getProductUsedAttributes($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductAttributeIds ( $product );
    }
    /**
     * Get Associated Products
     * 
     * @return collection'
     */
    public function getAssociatedProducts($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $product );
    }
    
    /**
     * Is seller product shipping
     */
    public function isSellerProductShipping(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Block\Product\Add' )->isSellerProductShipping();
    }
}