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
 * This class contains assign product add functions
 */
class Add extends \Magento\Framework\App\Action\Action {
    /**
     * Data Helper
     *
     * @var unknown
     */
    protected $dataHelper;
    /**
     * Constructo Function
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Apptha\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    /**
     * Function to load add assign product layout
     *
     * @return $array
     */
    public function execute() {
        $this->checkSubscriptionPlan ();
        $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Assignproduct\Manage' )->checkSellerEnabledorNot ();
    }
    /**
     * Checking for product limit based on subscription
     */
    public function checkSubscriptionPlan() {
        /**
         * Checking for subscription enabled or not
         */
        
        $loggedInUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $loggedInUser->getId ();
        $checkSubscriptionPlans = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' )->isSellerSubscriptionEnabled ();
        if ($checkSubscriptionPlans) {
            $currentDate = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
            /**
             * To count subscription profiles
             */
            $subscribedData = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
            $subscribedData->addFieldToFilter ( 'seller_id', $sellerId );
            $subscribedData->addFieldToFilter ( 'status', 1 );
            $subscribedData->addFieldtoFilter ( 'ended_at', array (array ('gteq' => $currentDate),
                    array ('ended_at','null' => '')));
            /**
             * Prepare maximum product limt for seller
             */
            if (count ( $subscribedData )) {
                $productLimit = '';
                foreach ( $subscribedData as $subscribeInfo ) {
                    $productLimit = $subscribeInfo->getMaxProductCount ();
                    break;
                }
                $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
                $sellerProductIds = $product->getAllIds ();
                /**
                 * Checking maximum product limit
                 */
                if ($productLimit <= count ( $sellerProductIds ) && $productLimit != 'unlimited') {
                    $this->messageManager->addNotice ( __ ( 'You have reached your product limit. If you want to add more product(s) kindly upgrade your subscription plan.' ) );
                    $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                    return;
                }
            } else {
                $this->messageManager->addNotice ( __ ( 'You have not subscribed any plan yet. Kindly subscribe for adding product(s).' ) );
                $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                return;
            }
        }
    }

}