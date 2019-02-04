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
namespace Apptha\Marketplace\Controller\Adminhtml\Payments;

use Apptha\Marketplace\Controller\Adminhtml\Payments;

/**
 * This class contains the functionality of edit subscription plan
 */
class Save extends Payments {
    /**
     * Function to pay seller amount
     *
     * @return id(int)
     */
    public function execute() {
        /**
         * Checking data exist or not
         */
        $isPost = $this->getRequest ()->getPost ();
        if ($isPost) {
         
            $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            /**
             * Checking for seller profile exist or not
             */
            $profileId = $this->getRequest ()->getPost ( 'id' );
            if ($profileId) {
                $sellerModel->load ( $profileId );
            }
            
            /**
             * Getting subscription details
             */
            $payAmount = $this->getRequest ()->getParam ( 'pay_amount' );
            $comment = $this->getRequest ()->getParam ( 'comment' );
            $invoice = $this->getRequest ()->getParam ( 'invoice' );
            $paymentType = $this->getRequest ()->getParam ( 'payment_type' );
            
            /**
             * Getting date
             */
            $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
            
            $remainingAmount = $sellerModel->getRemainingAmount ();
            $receivedAmount = $sellerModel->getReceivedAmount ();
            
            /**
             * Checking for pay amount greater than or not
             */
            if ($payAmount > $remainingAmount) {
                $this->messageManager->addNotice ( __ ( 'Kindly check your pay amount.' ) );
                $this->_redirect ( '*/*/edit', [ 
                        'id' => $profileId 
                ] );
                return;
            }
            /**
             * Setting subscription option
             */
            $sellerModel->setRemainingAmount ( $remainingAmount - $payAmount );
            $sellerModel->setReceivedAmount ( $receivedAmount + $payAmount );
            
            /**
             * Saving seller paymetn details
             */
            try {
                $sellerModel->save ();
                /**
                 * Create object for seller payment
                 */
                $paymentModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Payments' );
                $paymentModel->setPaidAmount ( $payAmount );
                $paymentModel->setSellerId ( $sellerModel->getCustomerId () );
                $paymentModel->setInvoice ( $invoice );
                $paymentModel->setCreatedAt ( $date );
                $paymentModel->setComment ( $comment );
                $paymentModel->setPaymentType ( $paymentType );
                $paymentModel->setIsAck ( 0 );
                
                /**
                 * Save payment model
                 */
                $paymentModel->save ();
                /**
                 * Display success message
                 */
                $this->messageManager->addSuccess ( __ ( 'The payment has been updated successfully.' ) );
                /**
                 * Check if 'Save and Continue'
                 */
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', [ 
                            'id' => $paymentModel->getId (),
                            '_current' => true 
                    ] );
                    return;
                }
                /**
                 * Go to grid page
                 */
                $this->_redirect ( '*/*/' );
                return;
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
            $this->_redirect ( '*/*/edit', [ 
                    'id' => $profileId 
            ] );
        }
    }
}