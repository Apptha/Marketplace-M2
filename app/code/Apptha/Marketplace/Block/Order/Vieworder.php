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

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * This class contains seller order view page block functions
 */
class Vieworder extends \Magento\Framework\View\Element\Template {
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    
    /**
     *
     * @var \Magento\Sales\Model\Order\Address
     */
    protected $addressRenderer;
    
    /**
     *
     * @var \Apptha\Marketkplace\Helper\System
     */
    protected $systemHelper;
    /**
     *
     * @var $productFactory
     */
    protected $productFactory;
    
    /**
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    
    /**
     * Seller order view block construct
     *
     * @param TemplateContext $context            
     * @param Registry $registry            
     * @param PaymentHelper $paymentHelper            
     * @param AddressRenderer $addressRenderer            
     * @param array $data            
     */
    public function __construct(\Apptha\Marketplace\Helper\System $systemHelper, TemplateContext $context, Registry $registry, AddressRenderer $addressRenderer, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, array $data = []) {
        $this->systemHelper = $systemHelper;
        $this->addressRenderer = $addressRenderer;
        $this->coreRegistry = $registry;
        $this->productFactory = $productFactory;
        $this->localeCurrency = $localeCurrency;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Prepare layout for seller order view page
     *
     * @return void
     */
    protected function _prepareLayout() {
        /**
         * Get order id
         */
        $orderId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get order details
         */
        $orderDetails = $this->getOrderDetails ( $orderId );
        /**
         * Set order page title
         */
        $this->pageConfig->getTitle ()->set ( __ ("Order #") . ($orderDetails ['increment_id'] ) );
        return parent::_prepareLayout ();
    }
    
    /**
     * Payment info for seller order
     *
     * @return string
     */
    public function getPaymentInfoHtml() {
        /**
         * To call child html
         */
        return $this->getChildHtml ( 'payment_info' );
    }
    
    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder() {
        /**
         * To set registry
         */
        return $this->coreRegistry->registry ( 'current_order' );
    }
    
    /**
     * Returns string with formatted address
     *
     * @param Address $address            
     *
     * @return null|string
     */
    public function getFormattedAddress(Address $address) {
        /**
         * To render address
         */
        return $this->addressRenderer->format ( $address, 'html' );
    }
    
    /**
     * Get order details
     *
     * @param int $orderId            
     */
    public function getOrderDetails($orderId) {
        /**
         * Create instance object
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Getting session customer
         */
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Checking for logged in or not
         */
        if ($customerSession->isLoggedIn ()) {
            $sellerId = $customerSession->getId ();
        }
        /**
         * Get product details
         */
        $products = $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order id
         */
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
        $orderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $orderId );
        $incrementId = $orderDetails->getIncrementId ();
        $orderStatus = $orderDetails->getStatus ();
        $createdAt = $orderDetails->getCreatedAt ();
        $billingId = $orderDetails->getBillingAddress ()->getId ();
        $shippingId = '';
        if ($orderDetails->getShippingAddress ()) {
            $shippingId = $orderDetails->getShippingAddress ()->getId ();
        }
        /**
         * Getting address details
         */
        $orderDet = $objectManager->get ( 'Magento\Sales\Model\Order\Address' )->load ( $billingId );
        $billingAddress = $orderDet->getData ();
        $shippingAddress = array ();
        if (! empty ( $shippingId )) {
            $orderDets = $objectManager->get ( 'Magento\Sales\Model\Order\Address' )->load ( $shippingId );
            $shippingAddress = $orderDets->getData ();
        }
        /**
         * Get payment and shipping details from order
         */
        $shippingMethod = $orderDetails->getShippingDescription ();
        $paymentMethod = $orderDetails->getPayment ()->getMethodInstance ()->getTitle ();
        $sellerOrderCollection = $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
        $isInvoiced = $isShipped = $isCanceled = $isRefunded = $isReturned = $shippingAmount = 0;
        
        /**
         * Getting shipping amount
         */
        $refundedProductIds = array ();
        $sellerProductsCount = 0;
        $sellerShipFlag = $sellerInvoiceFlag = 1;
        /**
         * To iterate the product from the order
         */
        foreach ( $orderDetails->getAllItems () as $item ) {
            $itemProductId = $item->getProductId ();
            if (in_array ( $itemProductId, $productIds )) {
                $sellerProductsCount = $sellerProductsCount + 1;
                if ($item->getQtyRefunded () == $item->getQtyOrdered ()) {
                    $refundedProductIds [] = $itemProductId;
                }
                
                if ($item->getIsVirtual () != 1 && $item->getQtyOrdered () > $item->getQtyShipped () && $sellerShipFlag != 0) {
                    $sellerShipFlag = 0;
                }
                
                if ($item->getQtyOrdered () > $item->getQtyInvoiced () && $sellerInvoiceFlag != 0) {
                    $sellerInvoiceFlag = 0;
                }
            }
        }
        
        /**
         * Setting for order status flags
         */
        if (count ( $sellerOrderCollection )) {
            $isInvoiced = $sellerOrderCollection->getIsInvoiced ();
            $isShipped = $sellerOrderCollection->getIsShipped ();
            $isCanceled = $sellerOrderCollection->getIsCanceled ();
            $isRefunded = $sellerOrderCollection->getIsRefunded ();
            $isReturned = $sellerOrderCollection->getIsReturned ();
            if ($sellerOrderCollection->getShippingAmount () != '') {
                $shippingAmount = $sellerOrderCollection->getShippingAmount ();
            }
        }
        
        /**
         * Checking for is shipped
         */
        if (empty ( $isShipped )) {
            $isShipped = $sellerShipFlag;
        }
        /**
         * Checking for is invoiced
         */
        if (empty ( $isInvoiced )) {
            $isInvoiced = $sellerInvoiceFlag;
        }
        
        /**
         * Checking for refund flag
         */
        $refundFlag = 0;
        if ($sellerProductsCount == count ( $refundedProductIds )) {
            $refundFlag = 1;
        }
        
        $originalStatus = $orderDetails->getStatus ();
        
        $sellerOrderStatus = $sellerOrderCollection->getStatus ();
        /**
         * Return order details for seller order view
         */
        return array (
                'increment_id' => $incrementId,
                'created_at' => $createdAt,
                'status' => $orderStatus,
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'shipping_method' => $shippingMethod,
                'payment_method' => $paymentMethod,
                'is_invoiced' => $isInvoiced,
                'is_shipped' => $isShipped,
                'is_canceled' => $isCanceled,
                'is_refunded' => $isRefunded,
                'is_returned' => $isReturned,
                'seller_order_status' => $sellerOrderStatus,
                'shipping_amount' => $shippingAmount,
                'refunded_product_ids' => $refundedProductIds,
                'refund_flag' => $refundFlag,
                'original_Status' => $originalStatus 
        );
    }
    /**
     * Get product details from order
     *
     * @param int $getOrderId            
     *
     * @return array
     */
    public function getProductDetails($getOrderId) {
        /**
         * Getting seller product ids from order
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Getting logged in customer data
         */
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Getting seller id
         */
        if ($customerSession->isLoggedIn ()) {
            $sellerId = $customerSession->getId ();
        }
        /**
         * Create a object manager instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * To create a order item collection
         */
        $products = $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        /**
         * To seller all fields
         */
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order id
         */
        $products->addFieldToFilter ( 'order_id', $getOrderId );
        /**
         * filter by seller id
         */
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        /**
         * Return all product ids
         */
        return array_unique ( $products->getColumnValues ( 'product_id' ) );
    }
    /**
     * Get order product details
     *
     * @param object $product            
     * @param int $orderId            
     */
    public function getOrderProductDetails($product, $orderId) {
        /**
         * Getting seller product ids from order
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        if ($customerSession->isLoggedIn ()) {
            $sellerId = $customerSession->getId ();
        }
        /**
         * Seller products from order
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $products = $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        $products->addFieldToFilter ( 'product_id', $product );
        /**
         * Return seller product names in particular order
         */
        $attributeDatas = array ();
        
        /**
         * Get order data
         */
        $orderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' );
        /**
         * Load by order id
         */
        $orderData = $orderDetails->load ( $orderId );
        
        /**
         * To set all items
         */
        $orderItems = $orderData->getAllItems ();
        /**
         * To iterate order item
         */
        foreach ( $orderItems as $item ) {
            $productId = $item->getProductId ();
            /**
             * Load product by product id
             */
            $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $typeId = $product->getTypeId ();
            /**
             * Checking for product type equal to configurable
             */
            if ($typeId == "configurable") {
                /**
                 * Getting product option
                 */
                $customOptions = $item->getProductOptions ();
                /**
                 * Getting attribute info
                 */
                if(isset($customOptions ['attributes_info'])){
                $attributeDatas [$productId] = $customOptions ['attributes_info'];
                }
            }
        }
        
        /**
         * Return array with product details
         */
        return array (
                'products' => $products,
                'attributes' => $attributeDatas,
                'typeid' => $typeId 
        );
    }
    /**
     * To set order price details
     *
     * @param int $orderId            
     *
     * @return object $productss
     */
    public function getOrderPriceDetails($orderId) {
        /**
         * Getting seller product ids from order
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Get seller id
         */
        if ($customerSession->isLoggedIn ()) {
            $sellerId = $customerSession->getId ();
        }
        /**
         * Seller order detais
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $productss = $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ();
        $productss->addFieldToSelect ( '*' );
        /**
         * Filter by order id
         */
        $productss->addFieldToFilter ( 'order_id', $orderId );
        /**
         * Filter by seller id
         */
        $productss->addFieldToFilter ( 'seller_id', $sellerId );
        
        /**
         * Return seller product names in particular order
         */
        return $productss;
    }
    
    /**
     * Order update url
     *
     * @return string
     */
    public function getOrderUpdateUrl() {
        /**
         * Get order update url
         */
        return $this->getUrl ( 'marketplace/order/update' );
    }
    
    /**
     * Seller order management
     *
     * @return bool
     */
    public function getSellerOrderManagement() {
        /**
         * To check whether seller order management enabled or not
         */
        return $this->systemHelper->getSellerOrderManagement ();
    }
    
    /**
     * Seller order item cancel url
     *
     * @return string
     */
    public function getOrderItemUrl() {
        /**
         * Get order item url
         */
        return $this->getUrl ( 'marketplace/order/Order' );
    }
    
    /**
     * Getting shipment flag
     *
     * @param int $orderId            
     */
    public function shipmentFlag($orderId) {
        /**
         * To set shipment flag equal to zero
         */
        $shipmentFlag = 0;
        /**
         * To create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load order details by order id
         */
        $order = $objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $orderId );
        /**
         * Get seller id
         */
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        
        /**
         * Get product by seller id
         */
        $product = $this->productFactory->create ()->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
        /**
         * Get all product ids
         */
        $sellerProductIds = $product->getAllIds ();
        /**
         * Setting shipment flag based on order seller items
         */
        foreach ( $order->getAllItems () as $item ) {
            $itemProductId = $item->getProductId ();
            $qty = 0;
            /**
             * Checking for seller product
             * and checking for virtural product or not
             */
            if (in_array ( $itemProductId, $sellerProductIds ) && $item->getIsVirtual () != 1) {
                /**
                 * Assign qty
                 */
                $qty = $item->getQtyOrdered () - $item->getQtyShipped ();
                /**
                 * Assign shipped qty
                 */
                $shippedQty = $item->getQtyShipped ();
                /**
                 * Checking for shipped qty
                 */
                if ($qty > 0 || $shippedQty > 0) {
                    return 1;
                }
            }
        }
        /**
         * Return shipment flag
         */
        return $shipmentFlag;
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
         * Get currency symbol
         */
        return $this->localeCurrency->getCurrency ( $currencyCode )->getSymbol ();
    }
    /**
     * Filter Products by type
     * 
     * @return array
     */
    public function getFilterProductType($getProductDatas) {
        /**
         * Iterate product ids
         */
        foreach ( $getProductDatas as $productIds ) {
            /**
             * Create instance for object manager
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();            
            /**
             * Get parent array
             */
            $parentArray = $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getParentIdsByChild ( $productIds );
            /**
             * Checking for parent array and product ids
             */
            if (! empty ( $parentArray ) && ($key = array_search ( $productIds, $getProductDatas )) !== false) {
                /**
                 * Unset the product
                 */
                unset ( $getProductDatas [$key] );
            }
        }
        /**
         * Return the product data
         */
        return $getProductDatas;
    }
    
    /**
     * Get Product Type
     * 
     * @return string
     */
    public function getProductType($productId) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get product details
         */
        $baseConfigproduct = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        /**
         * Return product type id
         */
        return $baseConfigproduct->getTypeId ();
    }
    
    /**
     * Filter Products by type
     *
     * @return array
     */
    public function getProductTypeId($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId )->getTypeId ();
    }
}