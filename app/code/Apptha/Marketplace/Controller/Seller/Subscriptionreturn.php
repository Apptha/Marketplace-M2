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

use Magento\Store\Model\ScopeInterface;

/**
 * This class contains load seller store functions
 */
class Subscriptionreturn extends \Magento\Framework\App\Action\Action {
    const XML_MARKETPLACE_SUBSCRIPTION_NOTIFICATION = 'marketplace/subscription/notification';
    /**
     * Marketplace helper data object
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
    protected $dataHelper;
    /**
     * Constructor
     *
     * \Apptha\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        
        /**
         * Getting return response from PayPal
         */
        $invoiceId = $this->getRequest ()->getParam ( 'invoice' );
        $planAmount = $this->getRequest ()->getParam ( 'planamount' );
        $sellerId = $this->getRequest ()->getParam ( 'customerid' );
        
        $currentUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $currentUser->getId ();
        
        /**
         * Checking for customer logged in or not using seller id
         */
        if ($sellerId != $customerId) {
            /**
             * Redirect to seller subscriptionplans page
             */
            $this->messageManager->addNotice ( __ ( 'Some error occured while processing your request.' ) );
            $this->_redirect ( 'marketplace/seller/subscriptionplans' );
            return false;
        }        
        $subscriptionProfilesModels = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
        $subscriptionProfilesModels->addFieldtoFilter ( 'status', array ('eq' => 1) );    
        $subscriptionProfilesModels->addFieldtoFilter ( 'seller_id', array ('eq' => $sellerId) );
        $subscriptionProfileIds = $subscriptionProfilesModels->getAllIds ();
        foreach ( $subscriptionProfileIds as $subscriptionProfileId ) {
            $subscriptionProfile = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionprofiles' )->load ( $subscriptionProfileId );
            $subscriptionProfile->setStatus ( 2 )->save ();
        }        
        /**
         * Get subscription profiles object
         */
        $subscriptionProfilesModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionprofiles' )->load ( $invoiceId, 'invoice' );
        $subscriptionProfilesModel->setStatus ( 1 );
        $subscriptionProfilesModel->setFee ( $planAmount );
        $subscriptionProfilesModel->save();
        
        /**
         * To update seller subscription details in subscription profile table
         */
        if (count ( $subscriptionProfilesModel )) {
            /**
             * Checking for email notification
             */
            $emailNotificationEnabled = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( static::XML_MARKETPLACE_SUBSCRIPTION_NOTIFICATION, ScopeInterface::SCOPE_STORE );
            
            if ($emailNotificationEnabled == 1) {
                /**
                 * Get admin details
                 */
                $admin = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
                $adminName = $admin->getAdminName ();
                $adminEmail = $admin->getAdminEmail ();
                
                /**
                 * Getting seller details
                 */
                $customerData = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
                $customer = $customerData->getCustomer ();
                
                $senderInfo = [ 
                        'name' => $customer->getName (),
                        'email' => $customer->getEmail () 
                ];
                
                $receiverInfo = [ 
                        'name' => $adminName,
                        'email' => $adminEmail 
                ];
                
                /**
                 * To prepare email notification template
                 */
                $emailTempVariables = array ();
                $emailTempVariables ['ownername'] = $adminName;
                $emailTempVariables ['invoice'] = $invoiceId;
                $emailTempVariables ['selleremail'] = $customer->getName ();
                $emailTempVariables ['planname'] = $subscriptionProfilesModel->getPlanName ();
                $emailTempVariables ['planamount'] = round ( $planAmount, 2 );
                
                $currency = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseCurrencyCode ();
                $emailTempVariables ['currency'] = $currency;
                
                $templateId = 'marketplace_subscription_admin_notification_template';
                
                /**
                 * Send subscription notification mail to admin
                 */
                $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
                
                $templateId = 'marketplace_subscription_sellersuccess_notification_template';
                $emailTempVariables ['enddate'] = $subscriptionProfilesModel->getEndedAt ();
                $emailTempVariables ['sellername'] = $customer->getName ();
                /**
                 * Send subscription notification mail to seller
                 * Changed sender and receiver values
                 */
                $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $receiverInfo, $senderInfo, $templateId );
            }
            
            $this->messageManager->addNotice ( __ ( 'You have subscribed successfully. It will be activated shortly.' ) );
            $this->_redirect ( 'marketplace/seller/subscriptionplans' );
        } else {
            $this->messageManager->addNotice ( __ ( 'Some error occured while processing your request.' ) );
            $this->_redirect ( 'marketplace/seller/subscriptionplans' );
        }
    }
}
