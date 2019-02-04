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
namespace Apptha\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Apptha\Marketplace\Helper\Data;

/**
 * This class contains seller approval/disapproval functions
 */
class Seller implements ObserverInterface {
    /**
     *
     * @var $marketplaceData
     */
    protected $marketplaceData;
    
    /**
     * Constructor
     * 
     * @param Data $marketplaceData            
     */
    public function __construct(Data $marketplaceData) {
        $this->marketplaceData = $marketplaceData;
    }
    /**
     * Execute the result
     * 
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Get is seller param
         */
        $isSeller = $observer->getRequest ()->getPost ( 'is_seller' );
        /**
         * Checking for is seller or not
         */
        if ($isSeller) {
            /**
             * Creating instance for object manager
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            /**
             * Get customer session
             */
            $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
            /**
             * Get customer id
             */
            if ($customerSession->isLoggedIn ()) {
                /**
                 * Get customer details
                 */
                $customerId = $customerSession->getId ();
                $customerDetails = $customerSession->getCustomer ();
                $customerEmail = $customerDetails->getEmail ();
                $sellerApproval = $this->marketplaceData->getSellerApproval ();
                /**
                 * Load custome group data
                 */
                $customerGroupSession = $objectManager->get ( 'Magento\Customer\Model\Group' );
                $customerGroupData = $customerGroupSession->load ( 'Marketplace Seller', 'customer_group_code' );
                /**
                 * Get customer group id
                 */
                $sellerGroupId = $customerGroupData->getId ();
                /**
                 * Checking seller approval or not
                 */
                if ($sellerApproval) {
                    /**
                     * Set customer group id
                     */
                    $customerDetails->setGroupId ( $sellerGroupId )->save ();
                    $sellerModel = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
                    /**
                     * Set customer details
                     */
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 0 )->setCustomerId ( $customerId )->save ();
                } else {
                    /**
                     * Set group id to seller
                     */
                    $customerDetails->setGroupId ( $sellerGroupId )->save ();
                    /**
                     * Load seller object
                     */
                    $sellerModel = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
                    /**
                     * To set seller data
                     */
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 1 )->setCustomerId ( $customerId )->save ();
                }
            }
        }
    }
}