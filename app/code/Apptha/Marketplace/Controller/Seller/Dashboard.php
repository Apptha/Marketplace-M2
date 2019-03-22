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
 * This class contains seller dashboard functions
 */
class Dashboard extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
    protected $dataHelper;
    
    /**
     * Constructor
     *
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
        $customerObject = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerObject->getId ();
        $sellerObject = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        if(!empty($customerId)){
        $status = $sellerObject->load ( $customerId, 'customer_id' )->getStatus ();
        $this->checkingForModuleEnabledOrNOt ( $status, $customerObject );
        }else{
        $this->_redirect ( 'marketplace/seller/login' );
        }
    }
    /**
     * Function to check whether seller or not
     *
     * @return layout
     */
    public function checkingForModuleEnabledOrNOt($status, $customerObject) {
        /**
         * Check whether module enabled or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            if ($customerObject->isLoggedIn () && $status == 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customerObject->isLoggedIn () && $status == 0) {
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
