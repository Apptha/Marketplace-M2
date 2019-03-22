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
namespace Apptha\Marketplace\Block\Order\Item\Renderer;

/**
 * Order item render block
 */
class DefaultRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer {
    
    /**
     * Get seller order details
     *
     * @param object $_item            
     * @param object $order            
     * @param object
     * @return object
     */
    public function getSellerOrderDetails($_item, $orderId, $sellerId) {
        /**
         * To get order collection by order id and seller id
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get seller order details
     *
     * @param object $_item            
     * @param object $order            
     * @param object
     * @return object
     */
    public function getSellerOrderItemDetails($_item, $orderId, $sellerId) {
        /**
         * Get order items collection
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get order item action
     *
     * @return string
     */
    public function orderItemAction() {
        /**
         * Get order item url
         */
        return $this->getUrl ( 'marketplace/order/item' );
    }
}