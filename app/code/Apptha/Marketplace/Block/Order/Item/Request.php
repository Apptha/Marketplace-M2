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
namespace Apptha\Marketplace\Block\Order\Item;

/**
 * Order item render block
 */
class Request extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer {
    
    /**
     * Get order details
     *
     * @param int $orderId            
     * @param int $sellerId            
     *
     * @return object $sellerOrder
     */
    public function getOrderDetails($orderId, $sellerId) {
        /**
         * To create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * To get order details
         */
        return $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get order item details
     *
     * @param int $orderId            
     * @param int $sellerId            
     * @param int $productId            
     *
     * @return object $sellerOrderItems
     */
    public function getOrderItemDetails($orderId, $sellerId, $productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * To get order item details
         */
        return $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'product_id', $productId )->getFirstItem ();
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
         * Create object
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get get order currency symbol
         */
        return $objectManager->get ( 'Apptha\Marketplace\Block\Order\Vieworder' )->getCurrencySymbol ( $currencyCode );
    }
}