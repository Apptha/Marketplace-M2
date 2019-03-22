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
namespace Apptha\Marketplace\Controller\Order;

/**
 * This class contains manage seller order page
 */
class Manage extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
    protected $dataHelper;
    
    /**
     * Manage seller order construct
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Apptha\Marketplace\Helper\Data $dataHelper            
     */
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
         * Getting logged in user data
         */
        $customerSessionData = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSessionData->getId ();
        /**
         * Getting seller information
         */
        $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        /**
         * Checking for seller or not
         */
        $this->checkSellerOrNot ( $status, $customerSessionData );
    }
    /**
     * Function to check whether seller or not
     *
     * @return layout
     */
    public function checkSellerOrNot($status, $customerSessionData) {
        
        /**
         * Checking whether module enabled or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            /**
             * Checking for seller status
             */
            if ($customerSessionData->isLoggedIn () && $status == 1) {
                /**
                 * Load layout
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customerSessionData->isLoggedIn () && $status == 0) {
                /**
                 * Redirect to change buyer controller
                 */
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                /**
                 * Setting a session notice message
                 */
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                /**
                 * Redirect to seller login page
                 */
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
