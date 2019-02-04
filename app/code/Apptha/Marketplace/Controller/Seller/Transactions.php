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
namespace Apptha\Marketplace\Controller\Seller;

/**
 * This class contains the seller tr
 */
class Transactions extends \Magento\Framework\App\Action\Action {
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        /**
         * Check whether module enabled or not
         */
        $checkingForModule = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' )->getModuleEnable ();
        if ($checkingForModule) {
            $logedInUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $logedInUser->getId ();
            $loggedUserObject = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            $status = $loggedUserObject->load ( $customerId, 'customer_id' )->getStatus ();
            if ($logedInUser->isLoggedIn () && $status == 1) {
                $transactionId = $this->getRequest ()->getParam ( 'id' );
                if (! empty ( $transactionId )) {
                    $this->updateAcknowledgeForTransaction ( $customerId, $transactionId );
                }
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($logedInUser->isLoggedIn () && $status == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
    
    /**
     * To update acknowledge for seller transaction
     *
     * @param int $customerId            
     * @param int $transactionId            
     *
     * @return void
     */
    public function updateAcknowledgeForTransaction($customerId, $transactionId) {
        /**
         * Getting seller payment by id
         */
        $sellerPayments = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Payments' )->load ( $transactionId );
        /**
         * Checking for seller payment count
         */
        if (count ( $sellerPayments ) >= 1) {
            /**
             * Get seller id form seller payment model
             */
            $sellerId = $sellerPayments->getSellerId ();
            /**
             * Checking for seller payments
             */
            if ($customerId == $sellerId) {
                /**
                 * Getting date
                 */
                $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
                /**
                 * Setting data to seller payments
                 */
                $sellerPayments->setIsAck ( 1 );
                $sellerPayments->setAckAt ( $date );
                $sellerPayments->save ();
                /**
                 * Seting session message for seller
                 */
                $this->messageManager->addSuccess ( __ ( 'The payment has been updated successfully.' ) );
            }
        }
    }
}
