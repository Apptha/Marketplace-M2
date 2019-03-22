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
namespace Apptha\Marketplace\Controller\Adminhtml\Review;

use Apptha\Marketplace\Controller\Adminhtml\Review;

/**
 * This class contains the functionality of edit seller review
 */
class Save extends Review {
    /**
     * Function to save Seller Review Data
     *
     * @return id(int)
     */
    public function execute() {
        $isPost = $this->getRequest ()->getPost ();
        if ($isPost) {
            /**
             * To create review instance
             */
            $reviewModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Review' );
            $reviewId = $this->getRequest ()->getPost ( 'id' );
            /**
             * Checking review id exist or not
             */
            if ($reviewId) {
                $reviewModel->load ( $reviewId );
            }
            /**
             * Getting review data
             */
            $review = $this->getRequest ()->getParam ( 'review' );
            $rating = $this->getRequest ()->getParam ( 'rating' );
            $status = $this->getRequest ()->getParam ( 'status' );
            /**
             * Setting review data
             */
            $reviewModel->setReview ( $review );
            $reviewModel->setRating ( $rating );
            $reviewModel->setStatus ( $status );
            try {
                /**
                 * Save review data
                 */
                $reviewModel->save ();
                
                /**
                 * Checking review approval status
                 */
                if ($status == 1) {
                    $templateIdValue = 'marketplace_review_admin_approval_template';
                    $this->sendReviewNotification ( $reviewModel, $templateIdValue );
                } else {
                    $templateIdValue = 'marketplace_review_admin_disapproval_template';
                    $this->sendReviewNotification ( $reviewModel, $templateIdValue );
                }
                
                /**
                 * Display success message
                 */
                $this->messageManager->addSuccess ( __ ( 'Data has been saved.' ) );
                /**
                 * Check if 'Save and Continue'
                 */
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', [ 
                            'id' => $reviewModel->getId (),
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
                    'id' => $reviewId 
            ] );
        }
    }
    
    /**
     * Send review notification
     *
     * @param object $reviewDetails            
     * @param string $templateIdValue            
     *
     * @return void
     */
    public function sendReviewNotification($reviewDetails, $templateIdValue) {
        
        /**
         * Getting store scope
         */
        $notification = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'marketplace/review/notification' );
        /**
         * Checking for review notification
         */
        if ($notification == 1) {
            $review = $reviewDetails->getReview ();
            /**
             * Gettin customer and seller details
             */
            $customerId = $reviewDetails->getCustomerId ();
            $sellerId = $reviewDetails->getSellerId ();
            
            /**
             * Create object for customer
             */
            $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
            $customer->load ( $customerId );
            /**
             * Get customer details
             */
            $customerName = $customer->getFirstname ();
            $customerEmail = $customer->getEmail ();
            
            /**
             * Create object for seller
             */
            $StoreData = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            $StoreData->load ( $sellerId );
            
            /**
             * Get seller details
             */
            $storeName = $StoreData->getStoreName ();
            
            if (empty ( $storeName )) {
                $customerData = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $customerData->load ( $sellerId );
                $storeName = $customerData->getFirstname ();
            }
            
            $senderIds = array ();
            $senderIds [] = $customerId;
            $senderIds [] = $sellerId;
            
            /**
             * Send mail to seller
             */
            foreach ( $senderIds as $senderId ) {
                $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $customerSession->load ( $senderId );
                $sellerName = $customerSession->getFirstname ();
                $sellerEmail = $customerSession->getEmail ();
                
                /**
                 * Assign values for your template variables
                 */
                $emailTempVariables = array ();
                $emailTempVariables ['name'] = $sellerName;
                $emailTempVariables ['customer_name'] = $customerName;
                $emailTempVariables ['customer_email'] = $customerEmail;
                $emailTempVariables ['store_name'] = $storeName;
                $emailTempVariables ['review'] = $review;
                
                /**
                 * Assign receiver info
                 */
                $receiverInfo = [ 
                        'name' => $sellerName,
                        'email' => $sellerEmail 
                ];
                
                $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
                $adminEmail = $seller->getAdminEmail ();
                $admin = $seller->getAdminName ();
                /**
                 * Assign sender info
                 */
                $senderInfo = [ 
                        'name' => $admin,
                        'email' => $adminEmail 
                ];
                /**
                 * Send email notification
                 */
                $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateIdValue );
            }
        }
    }
}