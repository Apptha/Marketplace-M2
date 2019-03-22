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

/**
 * This class contains order refund functions
 */
class Invoice implements ObserverInterface {
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Get Order Details
         * 
         * @var unknown
         */
        $invoice = $observer->getEvent ()->getInvoice ();
        $order = $invoice->getOrder ();
        $sellerNotInvoice = $allSellerId = array ();
        foreach ( $invoice->getAllItems () as $item ) {
            
            if ($item->getOrderItem ()->getParentItem ()) {
                continue;
            }
            
            /**
             * Get Product Data
             * 
             * @var int(Product Id)
             */
            $productId = $item->getProductId ();
            /**
             * Create object instance
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            /**
             * Load product data by product id
             */
            $product = $objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            /**
             * Assign seller id
             */
            $sellerId = $product->getSellerId ();
            
            /**
             * Checking for seller id exist or not
             */
            if (! empty ( $sellerId )) {
                if ($item->getOrderItem ()->getQtyOrdered () != $item->getQty ()) {
                    $sellerNotInvoice [] = $sellerId;
                }
                $allSellerId [] = $sellerId;
            }
        }
        
        /**
         * Get all seller id for invoice
         */
        if (count ( $allSellerId ) >= 1) {
            $allSellerId = array_unique ( $allSellerId );
        }
        
        if (count ( $sellerNotInvoice ) >= 1) {
            $sellerNotInvoice = array_unique ( $sellerNotInvoice );
            $allSellerId = array_diff ( $allSellerId, $sellerNotInvoice );
        }
        
        foreach ( $allSellerId as $allSeller ) {
            /**
             * Update seller order status
             */
            $sellerOrderCollection = $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () )->addFieldToFilter ( 'seller_id', $allSeller )->getFirstItem ();
            
            $totalAmount = $sellerOrderCollection->getSellerAmount () + $sellerOrderCollection->getShippingAmount ();
            $this->updateSellerAmount ( $allSeller, $totalAmount );
        }
        $invoice=$order->canShip();
        if($invoice){
            $orderStatus='processing';
        }
        else{
            $orderStatus='completed';
        }
        $sellerOrderCollection = $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () );
        $sellerOrderCollectionDatas =$sellerOrderCollection->getData();
        foreach($sellerOrderCollectionDatas as $sellerOrderCollectionData){
            $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->load($sellerOrderCollectionData['id'])->setStatus($orderStatus)->save();
        }
        
    }
    
    /**
     * Update seller amount
     *
     * @param int $updateSellerId            
     * @param double $totalAmount            
     *
     * @return void
     */
    public function updateSellerAmount($updateSellerId, $totalAmount) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load seller by seller id
         */
        $sellerModel = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerDetails = $sellerModel->load ( $updateSellerId, 'customer_id' );
        /**
         * Get remaining amount
         */
        $remainingAmount = $sellerDetails->getRemainingAmount ();
        /**
         * Total remaining amount
         */
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        /**
         * Set total remaining amount
         */
        $sellerDetails->setRemainingAmount ( $totalRemainingAmount );
        /**
         * Save remaining amount
         */
        $sellerDetails->save ();
    }
}