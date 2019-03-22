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
class Refund implements ObserverInterface {
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
        $creditmemo = $observer->getEvent ()->getCreditmemo ();
        $order = $creditmemo->getOrder ();
        foreach ( $creditmemo->getAllItems () as $item ) {
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
                /**
                 * Send email notification to buyer
                 */
                $this->sendRefundEmailNotification ( $order, $productId, $sellerId, 'buyer' );
                /**
                 * Set email notification to seller
                 */
                $this->sendRefundEmailNotification ( $order, $productId, $sellerId, 'Seller' );
            }
        }
    }
    
    /**
     * Send refund notification to seller and buyer
     *
     * @param object $order            
     * @param int $productId            
     * @param int $sellerId            
     * @param string $person            
     *
     * @return void
     */
    public function sendRefundEmailNotification($order, $productId, $sellerId, $person) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get admin details
         */
        $admin = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        
        /**
         * Set sender info
         */
        $senderInfo = [ 
                'name' => $adminName,
                'email' => $adminEmail 
        ];
        
        /**
         * Checking for buyer
         */
        
        if ($person == 'buyer') {
            /**
             * Setting receiver info
             */
            $receiverInfo = [ 
                    'name' => $order->getCustomerFirstname () . ' ' . $order->getCustomerLastname (),
                    'email' => $order->getCustomerEmail () 
            ];
        } else {
            /**
             * Setting seller details
             */
            $seller = $objectManager->create ( 'Magento\Customer\Model\Customer' )->load ( $sellerId );
            
            /**
             * Setting receiver info
             */
            $receiverInfo = [ 
                    'name' => $seller->getName (),
                    'email' => $seller->getEmail () 
            ];
        }
        
        /**
         * Assign template id
         */
        $templateId = 'marketplace_order_item_cancel_return_template';
        
        /**
         * Decleare email template variable
         */
        $emailTemplateVariables = array ();
        /**
         * Checkinf for email person
         */
        if ($person == 'buyer') {
            $emailTemplateVariables ['receivername'] = $order->getCustomerFirstname () . ' ' . $order->getCustomerLastname ();
        } else {
            $emailTemplateVariables ['receivername'] = $seller->getName ();
        }
        /**
         * Setting email template variables
         */
        $emailTemplateVariables ['actiontype'] = 'Refund';
        $emailTemplateVariables ['sellername'] = $adminName;
        $emailTemplateVariables ['requestperson'] = 'Buyer';
        $emailTemplateVariables ['requestperson_name'] = $order->getCustomerFirstname () . ' ' . $order->getCustomerLastname ();
        $emailTemplateVariables ['requestperson_email'] = $order->getCustomerEmail ();
        $emailTemplateVariables ['increment_id'] = $order->getIncrementId ();
        $emailTemplateVariables ['order_id'] = $order->getId ();
        $emailTemplateVariables ['product_id'] = $productId;
        $emailTemplateVariables ['seller_id'] = $sellerId;
        
        /**
         * Send email notification
         */
        $objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId );
    }
}