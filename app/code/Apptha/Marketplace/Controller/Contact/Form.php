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
namespace Apptha\Marketplace\Controller\Contact;

/**
 * This class contains contact seller form
 */
class Form extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
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
         * Get logged in customer details
         */
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        $this->checkSellerOrNot ( $status, $customerSession );
    }
    /**
     * Function to check whether seller or not
     *
     * @param int $status            
     * @param object $customerSession            
     *
     * @return layout
     */
    public function checkSellerOrNot($status, $customerSession) {
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        /**
         * Check whether module enabled or not
         */
        if ($moduleEnabledOrNot) {
            if ($customerSession->isLoggedIn () && $status == 1) {
                /**
                 * Load layout
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customerSession->isLoggedIn () && $status == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
