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
use Magento\Catalog\Model\ResourceModel\Product\Action;

/**
 * This class contains seller approval/disapproval functions
 */
class Sellerstatus implements ObserverInterface {
    protected $action;
    public function __construct(Action $action) {
        $this->action = $action;
    }
    
    /**
     * Execute the result
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $product = $observer->getProduct ();
        $productSellerId = $product->getSellerId ();
        if ($productSellerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $sellerModel = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $productSellerId, 'customer_id' );
            $sellerStatus = $sellerModel->getStatus ();
            if ($sellerStatus == '0') {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $storeManager = $objectManager->get ( '\Magento\Store\Model\StoreManagerInterface' );
                $storeId = $storeManager->getStore ()->getStoreId ();
                $this->action->updateAttributes ( [ 
                        $product->getEntityId () 
                ], [ 
                        'status' => 2 
                ], $storeId );
            }
        }
    }
}
