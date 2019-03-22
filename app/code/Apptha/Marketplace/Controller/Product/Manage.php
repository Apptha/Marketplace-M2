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
namespace Apptha\Marketplace\Controller\Product;

class Manage extends \Magento\Framework\App\Action\Action {
    protected $dataHelper;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load recent orders in seller Dashboard
     *
     * @return $array
     */
    public function execute() {
        /**
         * Getting user session
         */
        $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Getting customer id
         */
        $customerId = $customer->getId ();
        /**
         * Getting seller data
         */
        $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerStatus = $seller->load ( $customerId, 'customer_id' )->getStatus ();
        
        /**
         * Checking whether module enable or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            /**
             * Checking whether seller or not
             */
            if ($customer->isLoggedIn () && $sellerStatus == 1) {
                /**
                 * Load layout for manage product
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customer->isLoggedIn () && $sellerStatus == 0) {
                /**
                 * Redirect to change buyer
                 */
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                /**
                 * Setting the notice session message
                 */
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                /**
                 * Redirect to login page
                 */
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            /**
             * Redirect to account page
             */
            $this->_redirect ( 'customer/account' );
        }
    }
}