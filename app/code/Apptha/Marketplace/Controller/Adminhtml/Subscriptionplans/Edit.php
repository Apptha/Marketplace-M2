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
namespace Apptha\Marketplace\Controller\Adminhtml\Subscriptionplans;

use Apptha\Marketplace\Controller\Adminhtml\Subscriptionplans;

class Edit extends Subscriptionplans {
    /**
     * Seller review edit action
     */
    public function execute() {
        /**
         * Gettin plan id from query string
         */
       
        $planId = $this->getRequest ()->getParam ( 'id' );
         /**
         * Create object for subscriptionplans
         */
        $subscriptionModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionplans' );
        /**
         * Checking for subscription plan id exist or not
         */
        if ($planId) {
            $subscriptionModel->load ( $planId );
            if (! $subscriptionModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Subscription Plan no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $subscriptionPlanData = $this->_session->getNewsData ( true );
        if (! empty ( $subscriptionPlanData )) {
            $subscriptionModel->setData ( $subscriptionPlanData );
        }
        /**
         * Creaging register for subscription plan model
         */
        $this->_coreRegistry->register ( 'marketplace_subscriptionplans', $subscriptionModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate markeptlace menu
         */
        $resultHtml->setActiveMenu ( 'Apptha_Marketplace::main_menu' );
        /**
         * Setting title for subscrption plan
         */
        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Edit Subscription Plans' ) );
        return $resultHtml;
    }
}
