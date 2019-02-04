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
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Object;

/**
 * This class used to display the assign products collection
 */
class Manage extends \Magento\Framework\View\Element\Template {
    
    /**
     * Initilize variable for product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $assignproductFactory;
    protected $systemHelper;
    protected $_currency;
    protected $stockRegistry;
    protected $messageManager;
    /**
     * Constructor
     * 
     * @param Template\Context $context            
     * @param ProductFactory $productFactory            
     * @param array $data            
     */
    public function __construct(Template\Context $context, Collection $assignproductFactory, StockRegistry $stockRegistry, \Apptha\Marketplace\Helper\System $systemHelper, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
        $this->assignproductFactory = $assignproductFactory;
        $this->stockRegistry = $stockRegistry;
        $this->systemHelper = $systemHelper;
        $this->messageManager = $messageManager;
        
        parent::__construct ( $context, $data );
    }
    
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $assignProductsCollection = $this->getFilterAssignProducts ();
        $this->setCollection ( $assignProductsCollection );
    }
    
    /**
     * Prepare layout for manage assign products
     *
     * @return object
     */
    protected function _prepareLayout() {
        
        /**
         * Set Layout for Assign Products
         */
        $this->pageConfig->getTitle ()->set ( __ ( "Assign Products" ) );
        parent::_prepareLayout ();
        /**
         * Set Pagination
         * 
         * @var unknown
         */
        $currentCollection = $this->getCollection ();
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.assignproduct.list.pager' );
        $pager->setLimit ( 10 )->setShowAmounts ( false )->setCollection ( $currentCollection );
        $this->setChild ( 'pager', $pager );
        return $this;
    }
    /**
     * Get Assign product approval or not
     *
     * @return int
     */
    public function getAssignProductDeleteApproval() {
        return $this->systemHelper->getDeleteProductApproval ();
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
     * Get product delete url
     *
     * @param int $productId            
     *
     * @return string
     */
    public function getAssignProductDeleteUrl($productId) {
        return $this->getUrl ( 'marketplace/product/delete' ) . 'product_id/' . $productId;
    }
    
    /**
     * Get new assign product url
     *
     * @return string
     */
    public function getNewAssignProductUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/search' );
    }
    /**
     * Get assign Product Edit URL
     */
    public function getAssignProductEditUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/edit/' );
    }
    
    /**
     * Function to load Assign Products Collection
     * 
     * @return void
     */
    public function getProducts() {
        $assignProductId = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $assignProductId );
        $productType = $product->getTypeId ();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $objectManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' );
        /**
         * Apply filters here
         */
        $productCollection->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'assign_product_id', $assignProductId )->addAttributeToFilter ( 'product_approval', 1 )->addAttributeToFilter ( 'type_id', $productType );
        return $productCollection;
    }
    /**
     * Function to get Seller Store Details
     * 
     * @return void
     */
    public function getSellerStoreDetails($sellerId) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = $objectModelManager->get ( 'Magento\UrlRewrite\Model\UrlRewrite' )->load ( $targetPath, 'target_path' );
        $getRequestPath = $mainUrlRewrite->getRequestPath ();
        $sellerModel = $objectModelManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerModelData = $sellerModel->load ( $sellerId, 'customer_id' );
        $storeName = $sellerModelData->getStoreName ();
        return array (
                'request_path' => $getRequestPath,
                'store_name' => $storeName 
        );
    }
    
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product            
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $url = $objectModelManager->get ( 'Magento\Checkout\Helper\Cart' )->getAddUrl ( $product );
        return [ 
                'action' => $url,
                'data' => [ 
                        'product' => $product->getEntityId (),
                        \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $objectModelManager->get ( 'Magento\Framework\Url\Helper\Data' )->getEncodedUrl ( $url ) 
                ]
                 
        ];
    }
    /**
     * Get compare price ajax function
     * 
     * @return array
     */
    public function getComparePriceAjaxUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/compare' );
    }
    /**
     * Filter Assign Products
     * 
     * @return object(collection)
     */
    public function getFilterAssignProducts() {
        $objectFrameworkManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectFrameworkManager->create ( 'Magento\Customer\Model\Session' );
        $marketplacesellerId = '';
        /**
         * Filter action
         */
        $productIds = array ();
        $delete = $this->getRequest ()->getPost ( 'multi' );
        $productIds = $this->getRequest ()->getParam ( 'id' );
        if (count ( $productIds ) > 0 && $delete == 'delete') {
            $deleteFlag = 0;
            foreach ( $productIds as $productId ) {
                $sellerSession = $objectFrameworkManager->get ( 'Magento\Customer\Model\Session' );
                $marketplacesellerId = $sellerSession->getId ();
                $deleteproduct = $objectFrameworkManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
                $productSellerId = $deleteproduct->getSellerId ();
                if ($marketplacesellerId == $productSellerId) {
                    $objectFrameworkManager->get ( 'Magento\Framework\Registry' )->register ( 'isSecureArea', true );
                    $deleteproduct->delete ();
                    $objectFrameworkManager->get ( 'Magento\Framework\Registry' )->unregister ( 'isSecureArea' );
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
        $assignproduct = $this->assignproductFactory->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $customerSession->getCustomerId () )->addAttributeToFilter ( 'is_assign_product', 1 )->addAttributeToFilter ( 'visibility', array (
                'eq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE 
        ) )->addAttributeToSort ( 'entity_id', 'desc' );
        $assignproductname = $this->getRequest ()->getPost ( 'name' );
        $assignproductprice = $this->getRequest ()->getPost ( 'price' );
        $assignproductstatus = $this->getRequest ()->getPost ( 'status' );
        $assignproductsku = $this->getRequest ()->getPost ( 'sku' );
        $assignproducttype = $this->getRequest ()->getPost ( 'type' );
        if ($assignproductstatus != "") {
            $assignproduct->addAttributeToFilter ( 'status', $assignproductstatus );
        }
        if ($assignproducttype != "") {
            $assignproduct->addAttributeToFilter ( 'type_id', $assignproducttype );
        }
        if ($assignproductname != "") {
            $assignproduct->addAttributeToFilter ( 'name', array (
                    array (
                            'like' => '%' . $assignproductname . '%' 
                    ) 
            ) );
        }
        if ($assignproductprice != "") {
            $assignproduct->addAttributeToFilter ( 'price', $assignproductprice );
        }
        if ($assignproductsku != "") {
            $assignproduct->addAttributeToFilter ( 'sku', array (
                    array (
                            'like' => '%' . $assignproductsku . '%' 
                    ) 
            ) );
        }
        
        /**
         * Filter by product attributes
         */
        $assignproductCollection = $objectFrameworkManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $customerSession->getCustomerId () )->addAttributeToFilter ( 'is_assign_product', 1 )->addAttributeToFilter ( 'product_approval', 1 )->addAttributeToFilter ( 'visibility', array (
                'eq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
        ) )->addAttributeToSort ( 'entity_id', 'desc' )->addFieldToFilter('config_assign_simple_id', array('notnull' => true));
        
        $allProductIds = $assignproductCollection->getAllIds();
        /**
         * Return product object
         */
        if(count($allProductIds) >= 1){
        $assignproduct->addFieldToFilter('entity_id', array('nin' => $allProductIds));
        }        
        return $assignproduct;
    }
    /**
     * Get Assign product stock using stock registry
     *
     * @param int $id            
     */
    public function getProductQty($id) {
        return $this->stockRegistry->getStockItem ( $id )->getQty ();
    }
}