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
namespace Apptha\Marketplace\Controller\Order;

/**
 * This function contains seller order cancel, return and refund functionality
 */
class Order extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager ();
        parent::__construct ( $context );
    }
    /**
     * Seller cancel, return and refund function
     *
     * @return $resultPage
     */
    public function execute() {
        $id = $this->getRequest ()->getParam ( 'id' );
        $action = $this->getRequest ()->getParam ( 'action' );
     
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        $sellerOrderItems = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->load ( $id, 'order_item_id' );
        
        if ($sellerOrderItems->getSellerId () == $sellerId) {
            
            if ($action == 'canceled' || $action == 'returned') {
                
                $productSku = $sellerOrderItems->getProductSku ();
                
                /**
                 * Change seller order status
                 */
                if ($action == 'canceled') {
                    $sellerOrderItems->setIsCanceled ( 1 );
                    $sellerOrderItems->setStatus ( 'canceled' );
                } else {
                    $sellerOrderItems->setIsReturned ( 1 );
                    $sellerOrderItems->setStatus ( 'Returned' );
                }
                $sellerOrderItems->save ();
                
                $orderItems = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
                $orderItems->addFieldToFilter ( 'seller_id', $sellerId );
                $orderItems->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
                
                $orderItemCount = count ( $orderItems );
                if ($action == 'canceled') {
                    $orderItems->addFieldToFilter ( 'is_canceled', 1 );
                } else {
                    $orderItems->addFieldToFilter ( 'is_returned', 1 );
                }
                $canceledOrderItemCount = count ( $orderItems );
                
                /**
                 * Update seller order status
                 */
                if ($orderItemCount == $canceledOrderItemCount) {
                    $sellerOrder = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ();
                    $sellerOrder->addFieldToFilter ( 'seller_id', $sellerId );
                    $sellerOrder->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
                    $sellerOrderId = $sellerOrder->getFirstItem ()->getId ();
                    $updateOrderStatus = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->load ( $sellerOrderId );
                    if ($action == 'canceled') {
                        $updateOrderStatus->setIsCanceled ( 1 );
                    } else {
                        $updateOrderStatus->setIsReturned ( 1 );
                    }
                    $updateOrderStatus->setStatus ( 'canceled' );
                    $updateOrderStatus->save ();
                }
                
                /**
                 * Change parent order status
                 */
                $order = $this->_objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $sellerOrderItems->getOrderId () );
                
                $allItems = $order->getAllItems ();
                $allItemsCount = count ( $allItems );
                
                $allSellerOrderItem = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
                $allSellerOrderItem->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
                if ($action == 'canceled') {
                    $allSellerOrderItem->addFieldToFilter ( 'is_canceled', 1 );
                } else {
                    $allSellerOrderItem->addFieldToFilter ( 'is_returned', 1 );
                }
                $allSellerOrderItemCount = count ( $allSellerOrderItem );
                
                $status = $order->getStatus ();
                
                /**
                 * Update order comment for cancellation of item
                 */
                $message = __ ( 'The product' ) . ' ' . '[sku : ' . $productSku . '] has been ' . $action . ' ' . 'by' . ' ' . $customerSession->getCustomer ()->getName ();
                
                $order->addStatusToHistory ( $status, $message, false );
                
                if ($allItemsCount == $allSellerOrderItemCount && $order->canCancel ()) {
                    $order->cancel ();
                }
                $order->save ();
                
                /**
                 * Send order status email
                 */
                $this->sendCancelOrRefundEmail ( $customerSession, $sellerOrderItems, $action );
                
                $msg = __ ( 'The item has been' ) . " $action " . __ ( 'successfully' );
                $this->messageManager->addSuccess ( $msg );
                $this->_redirect ( 'marketplace/order/vieworder/id/' . $sellerOrderItems->getOrderId () );
            }
            
            if ($action == 'refunded') {
                $this->refundProcess ( $sellerOrderItems );
            }
        } else {
            $this->messageManager->addError ( __ ( 'You dont have permission to proceed this operation.' ) );
            $this->_redirect ( 'customer/account' );
        }
    }
    
    /**
     * Seller refund producess
     *
     * @param
     *            array
     *            
     * @return void
     */
    public function refundProcess($sellerOrderItems) {
       
        $sellerOrderItems->setIsRefunded ( 1 );
        $sellerOrderItems->save ();
        
        /**
         * Get order items
         */
        $orderItems = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        $orderItems->addFieldToFilter ( 'seller_id', $sellerOrderItems->getSellerId () );
        $orderItems->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
        
        $orderItemCount = count ( $orderItems );
        $orderItems->addFieldToFilter ( 'is_refunded', 1 );
        $canceledOrderItemCount = count ( $orderItems );
        
        /**
         * Update seller order status
         */
        if ($orderItemCount == $canceledOrderItemCount) {
            $sellerOrder = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ();
            $sellerOrder->addFieldToFilter ( 'seller_id', $sellerOrderItems->getSellerId () );
            $sellerOrder->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
            $sellerOrderId = $sellerOrder->getFirstItem ()->getId ();
            
            $updateOrderStatus = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->load ( $sellerOrderId );
            $updateOrderStatus->setIsRefunded ( 1 );
            $updateOrderStatus->setStatus ( 'refunded' );
            $updateOrderStatus->save ();
        }
        
        /**
         * Get seller order details
         */
        $sellerOrder = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ();
        $sellerOrder->addFieldToFilter ( 'seller_id', $sellerOrderItems->getSellerId () );
        $sellerOrder->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
        $incrementId = $sellerOrder->getFirstItem ()->getIncrementId ();
        
        /**
         * Email receiver and sender details
         */
        
        $admin = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $receiverInfo = [ 
                'name' => $adminName,
                'email' => $adminEmail 
        ];
        
        $senderInfo = [ 
                'name' => $customerSession->getCustomer ()->getName (),
                'email' => $customerSession->getCustomer ()->getEmail () 
        ];
        
        $templateId = 'marketplace_order_item_request_template';
        
        /**
         * Prepare email template
         */
        $emailTemplateVariables = array ();
        $emailTemplateVariables ['receivername'] = $adminName;
        $emailTemplateVariables ['requesttype'] = 'Refund';
        $emailTemplateVariables ['requestperson'] = 'Seller';
        $emailTemplateVariables ['requestperson_name'] = $customerSession->getCustomer ()->getName ();
        $emailTemplateVariables ['requestperson_email'] = $customerSession->getCustomer ()->getEmail ();
        $emailTemplateVariables ['increment_id'] = $incrementId;
        $emailTemplateVariables ['reason'] = '';
        $emailTemplateVariables ['product_id'] = $sellerOrderItems->getProductId ();
        $emailTemplateVariables ['seller_id'] = $sellerOrderItems->getSellerId ();
        $emailTemplateVariables ['order_id'] = $sellerOrderItems->getOrderId ();
        $emailTemplateVariables ['requesturl'] = '';
        
        $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId );
        
        $this->messageManager->addSuccess ( __ ( 'The refund request sent successfully.' ) );
        $this->_redirect ( 'marketplace/order/vieworder/id/' . $sellerOrderItems->getOrderId () );
    }
    
    /**
     * Send cancel/return email to buyer
     *
     * @param object $customerSession            
     * @param array $sellerOrderItems            
     * @param string $action            
     *
     * @return void
     */
    public function sendCancelOrRefundEmail($customerSession, $sellerOrderItems, $action) {
        
        /**
         * Get seller order data
         */
      
        $sellerOrder = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ();
        $sellerOrder->addFieldToFilter ( 'seller_id', $sellerOrderItems->getSellerId () );
        $sellerOrder->addFieldToFilter ( 'order_id', $sellerOrderItems->getOrderId () );
        $sellerOrderData = $sellerOrder->getFirstItem ();
        
        /**
         * Get customer data
         */
        $buyerData = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerOrderData->getCustomerId () );
        
        $receiverInfo = [ 
                'name' => $buyerData->getName (),
                'email' => $buyerData->getEmail () 
        ];
        
        $senderInfo = [ 
                'name' => $customerSession->getCustomer ()->getName (),
                'email' => $customerSession->getCustomer ()->getEmail () 
        ];
        
        $templateId = 'marketplace_order_item_cancel_return_template';
        
        /**
         * Prepare email template
         */
        $emailTemplateVariables = array ();
        $emailTemplateVariables ['receivername'] = $buyerData->getName ();
        $emailTemplateVariables ['actiontype'] = ucfirst ( $action );
        $emailTemplateVariables ['sellername'] = $customerSession->getCustomer ()->getName ();
        $emailTemplateVariables ['requestperson'] = 'Buyer';
        $emailTemplateVariables ['requestperson_name'] = $buyerData->getName ();
        $emailTemplateVariables ['requestperson_email'] = $buyerData->getEmail ();
        $emailTemplateVariables ['increment_id'] = $sellerOrderData->getIncrementId ();
        $emailTemplateVariables ['order_id'] = $sellerOrderItems->getOrderId ();
        $emailTemplateVariables ['product_id'] = $sellerOrderItems->getProductId ();
        $emailTemplateVariables ['seller_id'] = $sellerOrderItems->getSellerId ();
        
        $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId );
    }
}
