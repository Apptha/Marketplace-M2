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
namespace Apptha\Marketplace\Controller\Assignproduct;

/**
 * This class contains manage assign product functions
 */
class Manage extends \Magento\Framework\App\Action\Action {
    protected $dataHelper;
    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Apptha\Marketplace\Helper\Data $dataHelper            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load manage assign products layout
     *
     * @return $array
     */
    public function execute() {
        $this->checkSellerEnabledorNot ();
    }
    /**
     * Check Module Enabled or Not
     */
    public function checkSellerEnabledorNot() {
        $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customer->getId ();
        $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerStatus = $seller->load ( $customerId, 'customer_id' )->getStatus ();
        /**
         * Checking whether module enable or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        $enableAssignProduct = $this->dataHelper->getAssignProduct ();
        if ($moduleEnabledOrNot) {
            if ($customer->isLoggedIn () && $sellerStatus == 1 && $enableAssignProduct == 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customer->isLoggedIn () && $sellerStatus == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/product/manage' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
