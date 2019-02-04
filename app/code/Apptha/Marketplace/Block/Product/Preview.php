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

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource\Product\CollectionFactory;
use Zend\Form\Annotation\Object;
use Magento\CatalogInventory\Model\StockRegistry;

/**
 * This class used to display product preview page
 */
class Preview extends \Magento\Framework\View\Element\Template {
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection
     */
    protected $stockRegistry;
    
    /**
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, StockRegistry $stockRegistry, \Magento\Catalog\Model\Product $product, array $data = []) {
        parent::__construct ( $context, $data );
        $this->storeManager = $context->getStoreManager();
        $this->stockRegistry = $stockRegistry;
        $this->product = $product;
    }
    
    /**
     * Prepare layout for add product
     *
     * @return object
     */
    public function _prepareLayout() {
        $productId = $this->getRequest ()->getParam ( 'id' );
        $productDetails = $this->getProductData ( $productId );
        $productName = $productDetails->getName ();
        $this->pageConfig->getTitle ()->set ( $productName );
        
        return parent::_prepareLayout ();
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
     * Get Assign product stock using stock registry
     *
     * @param int $id            
     */
    public function getProductQty($id) {
        return $this->stockRegistry->getStockItem ( $id )->getIsInStock ();
    }
    
    /**
     * Get Product Details
     *
     * @return array
     */
    public function getPreviewProductDetatils($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $sellerId = $product->getSellerId ();
        $productName = $product->getName ();
        $productSku = $product->getSku ();
        $productPrice = $product->getPrice ();
        $imagehelper = $objectManager->get ( 'Magento\Catalog\Helper\Image' );
        $productImage = $imagehelper->init ( $product, 'category_page_list' )->constrainOnly ( FALSE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 700 )->getUrl ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $dataHelper = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $productPrice = $dataHelper->getFormattedPrice ( $productPrice );
        $isInStock = $this->getProductQty ( $productId );
        return array (
                'sellerid' => $sellerId,
                'product_name' => $productName,
                'product_sku' => $productSku,
                'product_image' => $productImage,
                'product_price' => $productPrice,
                'isinstock' => $isInStock 
        );
    }
}