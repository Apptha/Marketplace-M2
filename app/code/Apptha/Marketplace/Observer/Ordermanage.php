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
namespace Apptha\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Apptha\Marketplace\Helper\Data;

/**
 * This class contains saving order details functions
 */
class Ordermanage implements ObserverInterface {
    protected $marketplaceData;
    protected $systemHelper;
    /**
     *
     * @param Data $marketplaceData            
     */
    public function __construct(Data $marketplaceData, \Apptha\Marketplace\Helper\System $systemHelper) {
        $this->marketplaceData = $marketplaceData;
        $this->systemHelper = $systemHelper;
    }
    /**
     * Execute the result
     *
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Getting order ids
         */
        $order = $observer->getOrderIds ();
        /**
         * Assign first order id from order array
         */
        $orderId = $order [0];
        /**
         * Create instance for object manage
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get order details by order id
         */
        $orderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' );
        $orderData = $orderDetails->load ( $orderId );
        /**
         * Get order currency code
         */
        $currencyCode = $orderData->getOrderCurrencyCode ();
        /**
         * Get order shipping amount
         */
        $orderShippingAmount = $orderDetails->getShippingAmount ();
        /**
         * Get order increment id
         */
        $incrementId = $orderDetails->getIncrementId ();
        /**
         * Get ordered customer id
         */
        $customerId = $orderDetails->getCustomerId ();
        /**
         * Get order items
         */
        $orderItems = $orderData->getAllItems ();
        $sellerData = array ();
        $customOptions = array ();
        /**
         * saving each order items
         */
        foreach ( $orderItems as $item ) {
            $productId = $item->getProductId ();
            $itemId = $item->getItemId ();
            $customOptions = $item->getProductOptions ();
            $customOptionArray = json_encode ( $customOptions );
            $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $sellerId = $product->getSellerId ();
            if (! empty ( $sellerId ) && $item->getParentItemId () == '') {
                $sellerDatas = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $sellerId, 'customer_id' );
                $productCommission = $sellerDatas->getCommission ();
                $productCommission = $this->getGlobalCommissionValue($productCommission);
                $productSku = $item->getSku ();
                $productName = $item->getName ();
                $productPrice = $item->getPrice ();
                $baseProductPrice = $item->getBasePrice ();
                $sellerOrderItemsModel = $objectManager->create( 'Apptha\Marketplace\Model\Orderitems' );
                $productQty = $item->getQtyOrdered ();
                $sellerOrderItemsModel->setProductId ( $productId )->setProductSku ( $productSku )->setProductName ( $productName )->setSellerId ( $sellerId )->setOrderId ( $orderId )->setProductPrice ( $productPrice )->setBaseProductPrice ( $baseProductPrice )->setOrderItemId ( $itemId )->setProductQty ( $productQty )->setProductSku ( $productSku )->setProductName ( $productName )->setCommission ( $productCommission )->setOptions ( $customOptionArray )->setStatus ( 'pending' )->save ();
                $commission = $this->getCommssionValue($productCommission,$productPrice,$productQty);
                
                $isSellerShippingEnabled = $objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
                $isSellerShippingType = $objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
                $sellerShippingAmount = 0;
                if ($orderData->getShippingMethod () == 'apptha_apptha' && $isSellerShippingEnabled == 1 && $item->getIsVirtual () != 1) {
                    $country = $sellerDatas->getCountry ();
                    if ($isSellerShippingType == 'store') {
                        $nationalShippingAmount = $sellerDatas->getNationalShippingAmount ();
                        $internationalShippingAmount = $sellerDatas->getInternationalShippingAmount ();
                    } else {
                        $nationalShippingAmount = $product->getNationalShippingAmount ();
                        $internationalShippingAmount = $product->getInternationalShippingAmount ();
                    }
                    $sellerShippingAmount = 0;
                    if ($orderData->getBillingAddress ()->getCountryId () == $country) {
                        $sellerShippingAmount = $nationalShippingAmount * $productQty;
                    } else {
                        $sellerShippingAmount = $internationalShippingAmount * $productQty;
                    }
                }
                if (array_key_exists ( $sellerId, $sellerData )) {
                    $sellerData [$sellerId] ['price'] += $productPrice * $productQty;
                    $sellerData [$sellerId] ['commission'] += $commission;
                    $sellerData [$sellerId] ['seller_id'] = $sellerId;
                    $sellerData [$sellerId] ['shipping'] += $sellerShippingAmount;
                } else {
                    $sellerData [$sellerId] ['price'] = $productPrice * $productQty;
                    $sellerData [$sellerId] ['commission'] = $commission;
                    $sellerData [$sellerId] ['seller_id'] = $sellerId;
                    $sellerData [$sellerId] ['shipping'] = $sellerShippingAmount;
                }
            }
        }
        $customerOrderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' );
        $customerOrderData = $customerOrderDetails->load ( $orderId );
        /**
         * save the order items based on seller
         */
        $savedIds = array();
        foreach ( $sellerData as $sellerIds ) {
            $sellerId = $sellerIds ['seller_id'];
            $customerSession = $objectManager->create ( 'Magento\Customer\Model\Session' );
            if ($customerSession->isLoggedIn ()) {
                $customerId = $customerSession->getId ();
            }
            $products = $objectManager->create ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
            $products->addFieldToSelect ( '*' );
            $products->addFieldToFilter ( 'order_id', $orderId );
            $products->addFieldToFilter ( 'seller_id', $sellerId );
            $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
            if ($orderData->getShippingMethod () != 'apptha_apptha') {
                $orderShippingAmount = $customerOrderData->getShippingAmount ();
                $totalSellerShippingQty = $totalShippingQty = $shippingAmount = 0;
                foreach ( $orderDetails->getAllItems () as $item ) {
                    $itemProductId = $item->getProductId ();
                    if (in_array ( $itemProductId, $productIds ) && $item->getIsVirtual () != 1) {
                        $totalSellerShippingQty = $totalSellerShippingQty + $item->getQtyOrdered ();
                    }
                    if ($item->getIsVirtual () != 1) {
                        $totalShippingQty = $totalShippingQty + $item->getQtyOrdered ();
                    }
                }
                if (! empty ( $orderShippingAmount ) && ! empty ( $totalSellerShippingQty ) && ! empty ( $totalShippingQty )) {
                    $shippingAmount = round ( $orderShippingAmount * ($totalSellerShippingQty / $totalShippingQty), 2 );
                }
            } else {
                $shippingAmount = $sellerIds ['shipping'];
            }
            $sellerAmount = $sellerIds ['price'] - $sellerIds ['commission'];
            $sellerOrderModel = $objectManager->create( 'Apptha\Marketplace\Model\Order' );
            $sellerOrderModel->setSellerId ( $sellerIds ['seller_id'] )->setOrderId ( $orderId )->setSellerProductTotal ( $sellerIds ['price'] )->setCommission ( $sellerIds ['commission'] )->setSellerAmount ( $sellerAmount )->setIncrementId ( $incrementId )->setOrderCurrencyCode ( $currencyCode )->setCustomerId ( $customerId )->setShippingAmount ( $shippingAmount )->setStatus ( 'pending' )->save ();
            /**
             * Send order details to seller
             */
            
            $savedIds[] = $sellerId;
        }
        $this->sendOrderEmail ( $savedIds, $orderId );
    }
    
    public function sendOrderEmail($savedIds, $orderId){
        
        foreach ($savedIds as $savedId){
            $this->sendOrderEmailToSeller ( $savedId, $orderId );
        }
    }
    /**
     * Get commission value
     * 
     * @param float $productCommission
     * @param float $productPrice
     * @param int $productQty
     * 
     * @return float
     */
    public function getCommssionValue($productCommission,$productPrice,$productQty){
        if ($productCommission != 0) {
            $commissionPerProduct = $productPrice * ($productCommission / 100);
            $commission = $commissionPerProduct * $productQty;
        } else {
            $commission = 0;
        }
        return $commission;
    }
    
    /**
     * Get global commission value
     * 
     * @param float $productCommission
     * 
     * @return float $productCommission
     */
    public function getGlobalCommissionValue($productCommission){
        if (empty ( $productCommission )) {
            $productCommission = $this->systemHelper->getGlobalCommission ();
        }
        return $productCommission;
    }
    
    /**
     * To send order notification email to seller
     *
     * @param int $sellerId            
     * @param int $orderId            
     *
     * @return void
     */
    public function sendOrderEmailToSeller($sellerId, $orderId) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load seller details by seller id
         */
        $seller = $objectManager->create( 'Magento\Customer\Model\Customer' )->load ( $sellerId );
        
        /**
         * Get admin details
         */
        $admin = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        
        /**
         * Assing sender info
         */
        $senderInfo = [ 
                'name' => $adminName,
                'email' => $adminEmail 
        ];
        
        /**
         * Assign receiver info
         */
        $receiverInfo = [ 
                'name' => $seller->getName (),
                'email' => $seller->getEmail () 
        ];
        
        /**
         * Load order details by order id
         */
        $orderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' );
        $orderData = $orderDetails->load ( $orderId );
        
        /**
         * To declare email template variable
         */
        $emailTempVariables = array ();
        /**
         * Assign values to email template variable
         */
        $emailTempVariables ['seller_name'] = $seller->getName ();
        $emailTempVariables ['buyer_name'] = $orderData->getCustomerFirstname () . ' ' . $orderData->getCustomerLastname ();
        $emailTempVariables ['buyer_email'] = $orderData->getCustomerEmail ();
        $emailTempVariables ['increment_id'] = $orderData->getIncrementId ();
        $emailTempVariables ['order_id'] = $orderId;
        $emailTempVariables ['seller_id'] = $sellerId;
        /**
         * Assign template id
         */
        $templateId = 'marketplace_order_notification_template';
        /**
         * Send email notification
         */
        $objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
    }
}