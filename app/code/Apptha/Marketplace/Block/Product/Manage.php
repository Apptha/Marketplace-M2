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
use Zend\Form\Annotation\Object;

/**
 * This class used to display the products collection
 */
class Manage extends \Magento\Framework\View\Element\Template {
    
    /**
     * Initilize variable for product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    protected $systemHelper;
    protected $_currency;
    /**
     * Initilize variable for stock registry
     *
     * @var Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;
    protected $messageManager;
    
    /**
     *
     * @param Template\Context $context            
     * @param ProductFactory $productFactory            
     * @param array $data            
     */
    public function __construct(Template\Context $context, Collection $productFactory, \Magento\Directory\Model\Currency $currency, StockRegistry $stockRegistry, \Apptha\Marketplace\Helper\System $systemHelper, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
        $this->systemHelper = $systemHelper;
        $this->messageManager = $messageManager;
        $this->_currency = $currency;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $collection = $this->getFilterProducts ();
        $this->setCollection ( $collection );
    }
    
    /**
     * Get product approval or not
     *
     * @return int
     */
    public function getProductDeleteApproval() {
        return $this->systemHelper->getDeleteProductApproval ();
    }
    /**
     * Get product stock using stock registry
     *
     * @param int $id            
     */
    public function getProductQty($id) {
        return $this->stockRegistry->getStockItem ( $id )->getQty ();
    }
    
    /**
     * Prepare layout for manage product
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( "Manage Products" ) );
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.product.list.pager' );
        $pager->setLimit ( 10 )->setShowAmounts ( false )->setCollection ( $this->getCollection () );
        $this->setChild ( 'pager', $pager );
        
        return $this;
    }
    
    /**
     * Get Manage product pager html
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Get add new product url
     *
     * @return string
     */
    public function getAddProductUrl() {
        return $this->getUrl ( 'marketplace/product/add' );
    }
    
    /**
     * Get bulk product enable or not
     *
     * @return boolean
     */
    public function getProductBulkApproval() {
        return $this->systemHelper->getBulkProductApproval ();
    }
    /**
     * Get edit existing product url
     *
     * @param
     *            int product id
     *            
     * @return string url
     */
    public function getPrductEditUrl($productId) {
        return $this->getUrl ( 'marketplace/product/add' ) . 'product_id/' . $productId;
    }
    
    /**
     * Get product delete url
     *
     * @param int $productId            
     *
     * @return string
     */
    public function getProductDeleteUrl($productId) {
        return $this->getUrl ( 'marketplace/product/delete' ) . 'product_id/' . $productId;
    }
    
    /**
     * Get bulk upload url
     *
     * @return string
     */
    public function getBulkUploadUrl() {
        return $this->getUrl ( 'marketplace/product/bulkupload' );
    }
    /**
     * Product filter
     *
     * @return Object
     */
    public function getFilterProducts() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        
        /**
         * Filter action
         */
        $productIds = array ();
        $delete = $this->getRequest ()->getPost ( 'multi' );
        $productIds = $this->getRequest ()->getParam ( 'id' );
        if (count ( $productIds ) > 0 && $delete == 'delete') {
            $deleteFlag = 0;
            foreach ( $productIds as $productId ) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
                $sellerId = $customerSession->getId ();
                $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
                $productSellerId = $product->getSellerId ();
                if ($sellerId == $productSellerId) {
                    $objectManager->get ( 'Magento\Framework\Registry' )->register ( 'isSecureArea', true );
                    $product->delete ();
                    $objectManager->get ( 'Magento\Framework\Registry' )->unregister ( 'isSecureArea' );
                    $deleteFlag = 1;
                }
            }
            if ($deleteFlag == 1) {
                $this->messageManager->addSuccess ( __ ( 'The product has been deleted successfully.' ) );
            }
        }
        /**
         * Filter by product attributes
         */
        $product = $this->productFactory->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $customerSession->getCustomerId () );
        $name = $this->getRequest ()->getPost ( 'name' );
        $price = $this->getRequest ()->getPost ( 'price' );
        $status = $this->getRequest ()->getPost ( 'status' );
        $sku = $this->getRequest ()->getPost ( 'sku' );
        $type = $this->getRequest ()->getPost ( 'type' );
        if ($status != "") {
            $product->addAttributeToFilter ( 'status', $status );
        }
        if ($type != "") {
            $product->addAttributeToFilter ( 'type_id', $type );
        }
        if ($name != "") {
            $product->addAttributeToFilter ( 'name', array (
                    array (
                            'like' => '%' . $name . '%' 
                    ) 
            ) );
        }
        if ($price != "") {
            $product->addAttributeToFilter ( 'price', $price );
        }
        if ($sku != "") {
            $product->addAttributeToFilter ( 'sku', array (
                    array (
                            'like' => '%' . $sku . '%' 
                    ) 
            ) );
        }
        
        $product->addAttributeToFilter ( 'visibility', array (
                'eq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH 
        ) );
        $product->addAttributeToSort ( 'entity_id', 'DESC' );
        
        /**
         * Return product object
         */
        return $product;
    }
    
    /**
     * Get Current symbol
     * @retun void
     */
    public function getCurrencySymbol() {
        return $this->_currency->getCurrencySymbol ();
    }
    
    /**
     * Get Stock Item
     * @retun void
     */
    public function getStockItem() {
        return $this->_scopeConfig->getValue('cataloginventory/item_options/notify_stock_qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Get enable/disable
     *
     * @return string
     */
    public function isOutOfStockEnabled() {
        return $this->_scopeConfig->getValue('marketplace/seller/seller_lowstock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Get product collection for seller grid
     *
     * @return array
     */
    public function getProductCollection() {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get('\Magento\Catalog\Model\ProductRepository'); 
    }
}