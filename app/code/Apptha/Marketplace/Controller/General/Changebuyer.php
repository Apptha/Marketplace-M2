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
namespace Apptha\Marketplace\Controller\General;

/**
 * This class contains customer to buyer funcationality
 */
class Changebuyer extends \Magento\Framework\App\Action\Action {
    /**
     * Funtion to change customer to seller layout
     *
     * @return layout
     */
    public function execute() {
        /**
         * Getting Customer Session
         * 
         * @param
         *            s customer Id(int)
         */
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        if ($customerSession->isLoggedIn () && $status == 0) {
            $this->_view->loadLayout ();
            $this->_view->renderLayout ();
        } elseif ($customerSession->isLoggedIn () && $status == 1) {
            $this->_redirect ( 'marketplace/seller/dashboard' );
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
}
