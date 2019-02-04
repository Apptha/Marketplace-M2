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

use Apptha\Marketplace\Helper\Data;

/**
 * This class contains load seller store functions
 */
class Subscriptionplans extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
    protected $marketplaceHelperData;
    /**
     * Constructor
     *
     * \Apptha\Marketplace\Helper\Data $marketplaceHelperData
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, Data $marketplaceHelperData) {
        $this->marketplaceHelperData = $marketplaceHelperData;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        /**
         * Check whether module enabled or not
         */
        $moduleEnabled = $this->marketplaceHelperData->getModuleEnable ();
        if ($moduleEnabled) {
            $isSellerSubscriptionEnabled = $this->marketplaceHelperData->isSellerSubscriptionEnabled ();
            if($isSellerSubscriptionEnabled != 1){
            $this->_redirect ( 'customer/account' );
            }            
            /**
             * Getting logged in customer data
             */
            $currentUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $currentUser->getId ();
            /**
             * Creating seller data object
             */
            $sellerObject = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            /**
             * Getting seller status by seller id
             */
            $status = $sellerObject->load ( $customerId, 'customer_id' )->getStatus ();
            /**
             * Checking for logged in customer as a seller or not
             */
            if ($currentUser->isLoggedIn () && $status == 1) {
                /**
                 * Load layout
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($currentUser->isLoggedIn () && $status == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                /**
                 * Redirect to seller login page
                 */
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
