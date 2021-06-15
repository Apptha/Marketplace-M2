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

use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\ShipmentFactory;
use Zend\Form\Annotation\Instance;

/**
 * This class contains seller order item invoice and shippment funcationaity
 */
class Update extends \Magento\Framework\App\Action\Action {
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
    
    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    
    /**
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;
    /**
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    protected $shipmentSender;
    
    /**
     *
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;
    
    /**
     * Invoice and shipment construct
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory            
     * @param \Magento\Catalog\Model\ProductFactory $productFactory            
     * @param InvoiceSender $invoiceSender            
     * @param ShipmentSender $shipmentSender            
     * @param ShipmentFactory $shipmentFactory            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Model\ProductFactory $productFactory, InvoiceSender $invoiceSender, ShipmentSender $shipmentSender, ShipmentFactory $shipmentFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        $this->productFactory = $productFactory;
        $this->invoiceSender = $invoiceSender;
        $this->shipmentSender = $shipmentSender;
        $this->shipmentFactory = $shipmentFactory;
        parent::__construct ( $context );
    }
    /**
     * Seller invoice and shipment
     *
     * @return string
     */
    public function execute() {
        $itemsArray = $productSkus = array ();
        $orderId = $this->getRequest ()->getParam ( 'order_id' );
        $shipVirtualFlag = $this->getRequest ()->getParam ( 'ship_flag' );
        $action = $this->getRequest ()->getParam ( 'action' );
       
        $order = $this->_objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $orderId );
        
        /**
         * Getting shipping amount
         */
        $shippingAmount = 0;
        $totalSellerShippingQty = 0;
        
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        if (! empty ( $sellerId )) {
            $shipmentFlag = $invoiceFlag = 0;
            $subTotal = $baseSubtotal = 0;
            $product = $this->productFactory->create ()->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
            $sellerProductIds = $product->getAllIds ();
            
            /**
             * Getting seller canceled products
             */
            $sellerCanceledProducts = array ();
            $sellerOrderCollection = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
            $sellerOrderCollection->addFieldToFilter ( 'seller_id', $sellerId );
            $sellerOrderCollection->addFieldToFilter ( 'order_id', $orderId );
            $sellerOrderCollection->addFieldToFilter ( 'is_canceled', 1 );
            
            foreach ( $sellerOrderCollection as $sellerOrderItems ) {
                $sellerCanceledProducts [] = $sellerOrderItems->getProductId ();
            }
            
            /**
             * Prepare invoice and shipment items
             */
            foreach ( $order->getAllItems () as $item ) {
                
                $itemProductId = $item->getProductId ();
                $qty = 0;
                if (in_array ( $itemProductId, $sellerProductIds ) && ! in_array ( $itemProductId, $sellerCanceledProducts )) {
                    
                    $invoiceData = $this->getInvoiceData ( $action, $item, $qty, $subTotal, $baseSubtotal, $invoiceFlag, $totalSellerShippingQty );
                    
                    $qty = $invoiceData ['qty'];
                    $subTotal = $invoiceData ['sub_total'];
                    $baseSubtotal = $invoiceData ['base_subtotal'];
                    $invoiceFlag = $invoiceData ['invoice_flag'];
                    $totalSellerShippingQty = $invoiceData ['total_seller_shipping_qty'];
                    
                    if ($action == 'shipment') {
                        $qty = $item->getQtyOrdered () - $item->getQtyShipped ();
                        $shipmentFlag = 1;
                    }
                    if ($qty > 0) {
                        $productSkus [] = $item->getSku ();
                    }
                }
                $itemsArray [$item->getId ()] = $qty;
            }
            
            if ($action == 'invoice' && $invoiceFlag == 1) {
                $sellerOrderForShippings = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ();
                $sellerOrderForShippings->addFieldToFilter ( 'seller_id', $sellerId );
                $sellerOrderForShippings->addFieldToFilter ( 'order_id', $orderId );
                
                foreach ( $sellerOrderForShippings as $sellerOrderForShipping ) {
                    $shippingAmount = $sellerOrderForShipping ['shipping_amount'];
                    break;
                }
                
                /**
                 * Create invoice function
                 */
                $this->createInvoice ( $order, $itemsArray, $productSkus, $shippingAmount, $subTotal, $baseSubtotal, $shipVirtualFlag );
                $this->messageManager->addSuccess ( __ ( 'The invoice has been created successfully.' ) );
            }
            
            if ($action == 'shipment' && $shipmentFlag == 1) {
                /**
                 * Shipment function
                 */
                $this->shipment ( $order, $itemsArray, $productSkus );
                $this->messageManager->addSuccess ( __ ( 'The shipment has been created successfully.' ) );
            }
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath ( 'marketplace/order/vieworder/id/' . $orderId );
            return $resultRedirect;
        } else {
            $this->messageManager->addSuccess ( __ ( 'You dont have permission to proceed this action' ) );
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath ( 'marketplace/seller/login' );
            return $resultRedirect;
        }
    }
    
    /**
     *
     * Create invoice for seller product
     *
     * @param object $order            
     * @param array $itemsArray            
     * @param array $productSkus            
     * @param float $shippingAmount            
     * @param float $subTotal            
     * @param float $baseSubtotal            
     * @param int $shipVirtualFlag            
     *
     * @return void
     */
    public function createInvoice($order, $itemsArray, $productSkus, $shippingAmount, $subTotal, $baseSubtotal, $shipVirtualFlag) {
        if ($order->canInvoice ()) {
         
            $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            /**
             * Prepare invoice object
             */
            $invoice = $this->_objectManager->get ( 'Magento\Sales\Model\Service\InvoiceService' )->prepareInvoice ( $order, $itemsArray );
            $invoice->setShippingAmount ( $shippingAmount );
            $invoice->setSubtotal ( $subTotal );
            $invoice->setBaseSubtotal ( $baseSubtotal );
            $invoice->setGrandTotal ( $subTotal + $shippingAmount );
            $invoice->setBaseGrandTotal ( $subTotal + $shippingAmount );
            $invoice->setRequestedCaptureCase ( \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE );
            $invoice->register ();
            $invoice->getOrder ()->setIsInProcess ( true );
            /**
             * Create invoice
             */
            $transactionSave = $this->_objectManager->get ( 'Magento\Framework\DB\Transaction' )->addObject ( $invoice )->addObject ( $invoice->getOrder () );
            $transactionSave->save ();
            $this->invoiceSender->send ( $invoice );
            
            $successMsg = '';
            if (count ( $productSkus ) > 0) {
                $successMsg = __ ( 'The invoice has been created for ' ) . '[sku : ' . implode ( $productSkus ) . ']' . __ ( ' by ' ) . $customerSession->getCustomer ()->getName ();
            }
            $order->addStatusHistoryComment ( ' | ' . _ ( 'Invoice' ) . ' #' . $invoice->getIncrementId () . ' | ' . $successMsg )->setIsCustomerNotified ( true )->save ();
            /**
             * Change seller order status
             */
            $sellerOrderCollection = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () )->addFieldToFilter ( 'seller_id', $customerSession->getId () )->getFirstItem ();
            if (count ( $sellerOrderCollection )) {
                /**
                 * To prepare total amount
                 */
                $totalAmount = $sellerOrderCollection->getSellerAmount () + $sellerOrderCollection->getShippingAmount ();
                /**
                 * To update seller amount
                 */
                $this->_objectManager->get ( 'Apptha\Marketplace\Observer\Invoice' )->updateSellerAmount ( $customerSession->getId (), $totalAmount );
                
                $sellerOrder = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->load ( $sellerOrderCollection->getId () );
                $sellerOrder->setIsInvoiced ( 1 );
                $sellerOrder->setShippingAmount ( $shippingAmount );
                if ($sellerOrder->getIsShipped () == 1 || $shipVirtualFlag == 0) {
                    $sellerOrder->setStatus ( 'completed' );
                } else {
                    $sellerOrder->setStatus ( 'processing' );
                }
                $sellerOrder->save ();
            }
        }
    }
    
    /**
     * Update shipment for seller based order
     *
     * @param object $order            
     * @param array $itemsArray            
     * @param array $productSkus            
     */
    public function shipment($order, $itemsArray, $productSkus) {
        
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $shipment = $this->shipmentFactory->create ( $order, $itemsArray, $this->getRequest ()->getPost ( 'tracking' ) );
        
        /**
         * Prepare shipment
         */
        $shipment->register ();
        $transactionSave = $this->_objectManager->get ( 'Magento\Framework\DB\Transaction' );
        $transactionSave->addObject ( $shipment )->addObject ( $shipment->getOrder () );
        $transactionSave->save ();
        /**
         * Create Shipment
         */
        $this->shipmentSender->send ( $shipment );
        $successMsg = '';
        if (count ( $productSkus ) > 0) {
            $successMsg = __ ( 'The shipment has been created for ' ) . '[sku : ' . implode ( $productSkus ) . ']' . __ ( ' by ' ) . $customerSession->getCustomer ()->getName ();
        }
        $order->setIsInProcess ( true );
        $order->addStatusHistoryComment ( ' | ' . __ ( 'Shipment' ) . ' #' . $shipment->getIncrementId () . ' | ' . $successMsg )->setStatus ( 'processing' )->setIsCustomerNotified ( true )->save ();
        /**
         * Update seller order status
         */
        $sellerOrderCollection = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () )->addFieldToFilter ( 'seller_id', $customerSession->getId () )->getFirstItem ();
        if (count ( $sellerOrderCollection )) {
            $sellerOrder = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Order' )->load ( $sellerOrderCollection->getId () );
            $sellerOrder->setIsShipped ( 1 );
            if ($sellerOrder->getIsInvoiced () == 1) {
                $sellerOrder->setStatus ( 'completed' );
            } else {
                $sellerOrder->setStatus ( 'processing' );
            }
            $sellerOrder->save ();
        }
    }
    
    /**
     * Prepare invoice data for create invoice
     *
     * @param string $action            
     * @param Object $item            
     * @param int $qty            
     * @param float $subTotal            
     * @param float $baseSubtotal            
     * @param int $invoiceFlag            
     * @param int $totalSellerShippingQty            
     *
     * @return array
     */
    public function getInvoiceData($action, $item, $qty, $subTotal, $baseSubtotal, $invoiceFlag, $totalSellerShippingQty) {
        if ($action == 'invoice') {
            if ($item->getQtyOrdered () > $item->getQtyRefunded ()) {
                $qty = $item->getQtyOrdered () - $item->getQtyInvoiced ();
            }
            $subTotal = $subTotal + $qty * $item->getPrice ();
            $baseSubtotal = $baseSubtotal + $qty * $item->getBasePrice ();
            $invoiceFlag = 1;
            if ($item->getIsVirtual () != 1) {
                $totalSellerShippingQty = $totalSellerShippingQty + $item->getQtyOrdered () - $item->getQtyShipped ();
            }
        }
        /**
         * Prepare invoice data
         */
        $invoiceData = array ();
        $invoiceData ['qty'] = $qty;
        $invoiceData ['sub_total'] = $subTotal;
        $invoiceData ['base_subtotal'] = $baseSubtotal;
        $invoiceData ['invoice_flag'] = $invoiceFlag;
        $invoiceData ['total_seller_shipping_qty'] = $totalSellerShippingQty;
        return $invoiceData;
    }
}
