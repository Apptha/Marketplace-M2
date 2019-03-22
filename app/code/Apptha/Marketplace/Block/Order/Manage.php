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
namespace Apptha\Marketplace\Block\Order;

use Magento\Framework\View\Element\Template;
use Apptha\Marketplace\Model\ResourceModel\Order\Collection;

/**
 * This class used to display the products collection
 */
class Manage extends \Magento\Framework\View\Element\Template {
    
    /**
     * Initilize variable for product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $commissionFactory;
    
    /**
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    
    /**
     *
     * Manage order block construct
     *
     * @param Template\Context $context            
     * @param ProductFactory $productFactory            
     * @param array $data            
     *
     * @return void
     */
    public function __construct(Template\Context $context, Collection $commissionFactory, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, array $data = []) {
        $this->commissionFactory = $commissionFactory;
        $this->localeCurrency = $localeCurrency;
        
        parent::__construct ( $context, $data );
    }
    
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        /**
         * Creating object for customer session
         */
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectModelManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Declare customer id
         */
        $customerId='';
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
        }
        
        /**
         * Filter by selle id
         */
        $collection = $this->commissionFactory->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'seller_id', $customerId );
        
        /**
         * Set order for manage order
         */
        $collection->setOrder ( 'order_id', 'desc' );
        $this->setCollection ( $collection );
    }
   
    
    /**
     * Prepare Page Html
     *
     * @return string
     */
    public function getPagerHtml() {
        /**
         * To get child html
         */
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Prepare layout for view seller order
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        /**
         * Setting title for manage order
         */
        $this->pageConfig->getTitle ()->set ( __ ( "Orders" ) );
        /**
         * Call perant prepare layout
         */
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pageContent = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.order.manage.pager' );
        $pageContent->setLimit ( 10 )->setShowAmounts ( false )->setCollection ( $this->getCollection () );
        $this->setChild ( 'pager', $pageContent );
        $this->getCollection ()->load ();
        /**
         * Return layout
         */
        return $this;
    }
    
    /**
     * Get product details
     *
     * @param int $getOrderId            
     * @param int $getSellerId            
     */
    public function getProductDetails($getOrderId, $getSellerId) {
        /**
         * Getting seller product ids from order
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $products = $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order id
         */
        $products->addFieldToFilter ( 'order_id', $getOrderId );
        /**
         * Filter by seller id
         */
        $products->addFieldToFilter ( 'seller_id', $getSellerId );
        /**
         * Get product ids
         */
        $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
        
        /**
         * Get order data
         * 
         * @var object $orderDet
         */
        $orderDet = $objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $getOrderId );
        
        /**
         * Get order all items
         * 
         * @var unknown
         */
        $orderItems = $orderDet->getAllItems ();
        
        /**
         * Prepare product names for order
         * 
         * @var array $productNames
         */
        $productNames = array ();
        foreach ( $orderItems as $item ) {
            /**
             * Checking for seller product or not
             */
            if (in_array ( $item->getProductId (), $productIds )) {
                /**
                 * Assign product name
                 */
                $productNames [] = $item->getName ();
            }
        }
        
        /**
         * Return seller product names in particualr order
         */
        return implode ( ',', $productNames );
    }
    
    /**
     * Get seller order details
     *
     * @param int $orderId            
     * @param int $sellerId            
     * @param int $customerId            
     *
     * @return array
     */
    public function getOrderDetails($orderId, $sellerId, $customerId) {
        /**
         * To prepare object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Initilize seller model
         *
         * @var object $sellerModel
         */
        $sellerModel = $objectManager->get ( 'Magento\Customer\Model\Customer' );
        /**
         * Load customer by customer id
         */
        $sellerDetails = $sellerModel->load ( $customerId );
        /**
         * Get first name
         */
        $customerName = $sellerDetails->getFirstname ();
        /**
         * Load order by order id
         */
        $orderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $orderId );
        /**
         * Get created at
         */
        $createdAt = $orderDetails->getCreatedAt ();
        
        /**
         * Get product details
         */
        $products = $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        $products->addFieldToSelect ( '*' )->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId );
        
        /**
         * Get product ids
         */
        $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
        
        /**
         * Get refund flag
         */
        $sellerRefundedProductIds = array ();
        $sellerOrderProductsCount = 0;
        /**
         * Iterate the products from order
         */
        foreach ( $orderDetails->getAllItems () as $item ) {
            $itemProductId = $item->getProductId ();
            /**
             * Checking seller product or not
             */
            if (in_array ( $itemProductId, $productIds )) {
                /**
                 * To increase the product count
                 */
                $sellerOrderProductsCount = $sellerOrderProductsCount + 1;
                /**
                 * Checking for product refund
                 */
                if ($item->getQtyRefunded () == $item->getQtyOrdered ()) {
                    /**
                     * To set refunded product ids
                     */
                    $sellerRefundedProductIds [] = $itemProductId;
                }
            }
        }
        
        /**
         * Setting for refund product count
         */
        $refundFlag = 0;
        if ($sellerOrderProductsCount == count ( $sellerRefundedProductIds )) {
            $refundFlag = 1;
        }
        
        /**
         * Return void
         */
        return array (
                'customer_name' => $customerName,
                'created_at' => $createdAt,
                'refund_flag' => $refundFlag 
        );
    }
    
    /**
     * Get currency symbol by code
     *
     * @param string $currencyCode            
     *
     * @return string
     */
    public function getCurrencySymbol($currencyCode) {
        /**
         * To get currency symbol
         */
        return $this->localeCurrency->getCurrency ( $currencyCode )->getSymbol ();
    }
}