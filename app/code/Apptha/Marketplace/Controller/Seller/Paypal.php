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
 * This class contains load seller store functions
 */
class Paypal extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
    protected $marketplaceData;
    /**
     * Constructor
     *
     * \Apptha\Marketplace\Helper\Data $marketplaceData
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $marketplaceData) {
        $this->marketplaceData = $marketplaceData;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        /**
         * Check whether module enabled or not
         */
       
        $marketplaceEnabled = $this->marketplaceData->getModuleEnable ();
        if ($marketplaceEnabled) {
            /**
             * Getting logged in user details
             */
            $loggedCustomerData = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $loggedCustomerData->getId ();
            /**
             * Getting seller data
             */
            $sellerObject = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            $status = $sellerObject->load ( $customerId, 'customer_id' )->getStatus ();
            /**
             * Checking for seller logged in or not
             */
            if ($loggedCustomerData->isLoggedIn () && $status == 1) {
                $planId = $this->getRequest ()->getParam ( 'plan_id' );
                if (! empty ( $planId )) {
                    
                    /**
                     * Get product collection filter by seller id
                     */
                    $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addFieldToFilter ( 'seller_id', $customerId );
                    $sellerProductIds = $product->getAllIds ();
                    $sellerProductCount = count($sellerProductIds);
                    
                    $maxProductCount = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionplans' )->load($planId)->getMaxProductCount ();
                    
                    
                    $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
                    $sellerSubscribedPlan = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
                    $sellerSubscribedPlan->addFieldToFilter ( 'seller_id', $customerId );
                    $sellerSubscribedPlan->addFieldToFilter ( 'status', 1 );
                    $sellerSubscribedPlan->addFieldtoFilter ( 'ended_at', array (array ('gteq' => $date), array ('ended_at','null' => '')) );
                    
                    if(count ( $sellerSubscribedPlan ) >= 1 && $maxProductCount != 'unlimited' && $sellerProductCount > $maxProductCount){
                    $this->messageManager->addNotice ( __ ( 'Product(s) count exceed ' ).$maxProductCount.__ ( '. Kindly delete exceed product before degrade subscription plan' ) );
                    $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                    }else{
                    /**
                    * Load layout for PayPal redirection page
                    */
                    $this->_view->loadLayout ();
                    $this->_view->renderLayout ();
                    }
                } else {
                    /**
                     * Throw the error message
                     */
                    $this->messageManager->addNotice ( __ ( 'Kindly select of the subscription plan.' ) );
                    $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                }
            } elseif ($loggedCustomerData->isLoggedIn () && $status == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            /**
             * Redirect to customer login page
             */
            $this->_redirect ( 'customer/account' );
        }
    }
}
