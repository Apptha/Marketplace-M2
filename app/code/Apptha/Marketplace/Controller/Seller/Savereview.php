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
 * This class contains the seller review section
 */
class Savereview extends \Magento\Framework\App\Action\Action {
    /**
     * Save review for seller
     */
    public function execute() {
      
        /**
         * Creating current user object
         */
        $customerSession = $this->_objectManager->create ( 'Magento\Customer\Model\Session' );
        /**
         * Getting seller id form query param
         */
        $sellerId = $this->getRequest ()->getPost ( 'seller_id' );
        /**
         * Checking user logged in or not
         */
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
            /**
             * Preparing review data
             */
            if ($sellerId != $customerId) {
                $id = null;
                /**
                 * Creating a store object
                 */
                $manager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
                $store = $manager->getStore ( $id );
                $storeId = $store->getId ();
                /**
                 * Getting query variables
                 */
                $feedback = $this->getRequest ()->getPost ( 'feedback' );
                $rating = $this->getRequest ()->getPost ( 'star' );
                
                /**
                 * Getting date data using datetime object
                 */
                $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
                /**
                 * Saving customer review for seller
                 */
                $reveiwModel = $this->_objectManager->create ( 'Apptha\Marketplace\Model\Review' );
                $reveiwModel->setSellerId ( $sellerId );
                $reveiwModel->setCustomerId ( $customerId );
                $reveiwModel->setRating ( $rating );
                $reveiwModel->setReview ( $feedback );
                $reveiwModel->setStoreId ( $storeId );
                
                /**
                 * Checking for auto approval option
                 */
               
                $autoApproval = $this->_objectManager->create ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'marketplace/review/approval' );
                
                /**
                 * Manipulate based on auto approval option setting
                 */
                if ($autoApproval == 1) {
                    $reveiwModel->setStatus ( 1 );
                    $this->messageManager->addSuccess ( __ ( 'Your review has been added successfully' ) );
                } else {
                    $reveiwModel->setStatus ( 0 );
                    $this->messageManager->addSuccess ( __ ( 'Your review is awaiting for moderation' ) );
                }
                
                /**
                 * Save customer review module for seller
                 */
                $reveiwModel->setCreatedAt ( $date );
                $reveiwModel->save ();
                
                /**
                 * Checking for notification enabled or not
                 */
                $notification = $this->_objectManager->create ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'marketplace/review/notification' );
                if ($notification == 1) {
                    
                    $customer = $this->_objectManager->create ( 'Magento\Customer\Model\Customer' );
                    $customer->load ( $customerId );
                    $customerName = $customer->getFirstname ();
                    $customerEmail = $customer->getEmail ();
                    
                    /**
                     * Getting sender details
                     */
                    $senderInfo = [
                        'name' => $customerName,
                        'email' => $customerEmail
                    ];
                    
                    /**
                     * Getting receiver details
                     */
                    $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
                    $adminEmail = $seller->getAdminEmail ();
                    $admin = $seller->getAdminName ();
                    
                    $receiverInfo = [
                        'name' => $admin,
                        'email' => $adminEmail
                    ];
                    
                    /**
                     * Get customer data
                     */
                    $StoreData = $this->_objectManager->create ( 'Apptha\Marketplace\Model\Seller' )->load ( $sellerId );
                    $storeName = $StoreData->getStoreName ();
                    if (empty ( $storeName )) {
                        $customerData = $this->_objectManager->create ( 'Magento\Customer\Model\Customer' );
                        $customerData->load ( $sellerId );
                        $storeName = $customerData->getFirstname ();
                    }
                    
                    /**
                     * Assign values for your template variables
                     */
                    $emailTempVariables = array ();
                    $emailTempVariables ['ownername'] = $admin;
                    $emailTempVariables ['cname'] = $storeName;
                    $emailTempVariables ['cemail'] = $customerEmail;
                    
                    /**
                     * Notify review by mail
                     */
                    $templateIdValue = 'marketplace_review_admin_notification_template';
                    $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateIdValue );
                }
            }
        }
        $this->_redirect ( $this->_redirect->getRefererUrl());
    }
}