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
/**
 * This class contains seller review edit functionality
 */
namespace Apptha\Marketplace\Controller\Adminhtml\Payments;

use Apptha\Marketplace\Controller\Adminhtml\Payments;

class Edit extends Payments {
    /**
     * Seller review edit action
     */
    public function execute() {
        /**
         * Gettin plan id from query string
         */
        $profileId = $this->getRequest ()->getParam ( 'id' );
       
        /**
         * Create object for subscriptionplans
         */
        $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        /**
         * Checking for subscription plan id exist or not
         */
        if ($profileId) {
            $sellerModel->load ( $profileId );
            if (! $sellerModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Subscription Plan no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $paymentsData = $this->_session->getNewsData ( true );
        if (! empty ( $paymentsData )) {
            $sellerModel->setData ( $paymentsData );
        }
        /**
         * Creaging register for subscription plan model
         */
        $this->_coreRegistry->register ( 'marketplace_payments', $sellerModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate markeptlace menu
         */
        $resultHtml->setActiveMenu ( 'Apptha_Marketplace::main_menu' );
        /**
         * Setting title for subscrption plan
         */
        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Pay' ) );
        return $resultHtml;
    }
}
