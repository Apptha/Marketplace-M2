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
class Adminseller implements ObserverInterface {
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
        $customer = $observer->getRequest ()->getPost ( 'customer' );
        $groupId = $customer ['group_id'];
        $customerEmail = $customer ['email'];
        /**
         * Checking for is seller group or not
         */

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $registeredCustomers = $objectManager->create ( 'Magento\Customer\Model\Customer' )->getCollection ();
        foreach ( $registeredCustomers as $customers ) {
            if ($customers->getEmail () == $customerEmail) {
                $customerId = $customers->getId ();
            }
        }
        if ($groupId == 4) {
            /**
             * Load seller object
             */
            $sellerModel = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
            /**
             * To set seller data
             */
            $sellerModel->setEmail ( $customerEmail )->setStatus ( 1 )->setCustomerId ( $customerId )->save ();
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $productCollection = $objectManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' );
            foreach ( $productCollection as $productCollection ) {
                $id = $productCollection->getEntityId ();
                $productCollection->load ( $id );
                $sellerId = $productCollection->getSellerId ();
                if ($customerId == $sellerId) {
                    $productCollection->setStatus ( '2' )->save ();
                }
            }
            $sellerModel = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
            $sellerModel->setEmail ( $customerEmail )->setStatus ( 0 )->setCustomerId ( $customerId )->save ();
        }
    }
}