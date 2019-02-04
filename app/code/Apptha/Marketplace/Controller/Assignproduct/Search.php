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
 * This class contains search assign product functions
 */
class Search extends \Magento\Framework\App\Action\Action {
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
     * Function to load assign products layout
     *
     * @return $array
     */
    public function execute() {
        $marketplaceSeller = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $marketplaceSellerId = $marketplaceSeller->getId ();
        $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $marketplacesellerStatus = $seller->load ( $marketplaceSellerId, 'customer_id' )->getStatus ();
        /**
         * Checking whether module enable or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            if ($marketplaceSeller->isLoggedIn () && $marketplacesellerStatus == 1) {
                $storeName = $seller->getStoreName ();
                if (empty ( $storeName )) {
                    $this->messageManager->addNotice ( __ ( 'You must have a seller store to assign products' ) );
                    $this->_redirect ( 'marketplace/seller/profile' );
                }
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($marketplaceSeller->isLoggedIn () && $marketplacesellerStatus == 0) {
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