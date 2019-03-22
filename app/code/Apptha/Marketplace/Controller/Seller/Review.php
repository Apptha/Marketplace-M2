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
class Review extends \Magento\Framework\App\Action\Action {
    /**
     * Show all seller review
     */
    public function execute() {
        /**
         * Getting seller id for query param
         */
        $sellerId = $this->getRequest ()->getParam ( 'seller_id' );
        /**
         * Checking for seller id exist or not
         */
        if (empty ( $sellerId )) {
            /**
             * Creating object for logged in seller
             */
           
            $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            if (! $customerSession->isLoggedIn ()) {
                /**
                 * Redirect to customer login page
                 */
                $this->_redirect ( 'customer/account/login' );
                return;
            }
        }
        
        /**
         * Load seller review layout
         */
        $this->_view->loadLayout ();
        if ($this->getRequest ()->getParam ( 'seller_id' ) != '') {
            $this->_view->getLayout ()->unsetElement ( 'customer_account_navigation_block' );
        }
        $this->_view->renderLayout ();
    }
}