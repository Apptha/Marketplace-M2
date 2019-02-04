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
class Cataloginventorysave implements ObserverInterface {
    protected $action;
    public function __construct(Action $action, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->action = $action;
        $this->_productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * Execute the result
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $sellerNotification = \Magento\Framework\App\ObjectManager::getInstance ()->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'marketplace/seller/seller_lowstock' );
        $minimumQuantity = $this->scopeConfig->getValue ( 'cataloginventory/item_options/notify_stock_qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if ($sellerNotification == "1") {
            $orderId = $observer->getEvent ()->getOrder ()->getId ();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $order = $objectManager->create ( '\Magento\Sales\Model\Order' )->load ( $orderId );
            $orderItems = $order->getAllItems ();
            foreach ( $orderItems as $items ) {
                $productId = $items->getProductId ();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $repository = $objectManager->create ( 'Magento\Catalog\Model\ProductRepository' );
                $product = $objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
                // Checking based on the product type for configurable options
                if ($product->getTypeId() == 'configurable') {
                    $options = $this->getOptions($product);
                    foreach ( $options as $sku => $d ) {
                        $pr = $repository->get ( $sku );
                        $product = $this->_productRepository->getById ( $pr->getId () );
                        $inventory = $product->getQuantityAndStockStatus ();
                        $remainingQuantity = $inventory ['qty'];
                        $productSellerId = $product->getSellerId ();
                        $this->sendEmail($productSellerId, $remainingQuantity, $minimumQuantity, $product);
                    }
                } else {
                    $productStockObj = $objectManager->get ( 'Magento\CatalogInventory\Api\StockRegistryInterface' )->getStockItem ( $productId );
                    $remainingQuantity = $productStockObj->getQty ();
                    $productSellerId = $productStockObj->getSellerId ();
                    $this->sendEmail($productSellerId, $remainingQuantity, $minimumQuantity, $product);
                }
            }
        }
    }
    
    /**
     * Get configurable products
     *
     * @param array $product
     * @return array
     */
    public function getOptions($product) {
        $configData = $product->getTypeInstance ()->getConfigurableOptions ( $product );
        $options = array ();
        foreach ( $configData as $attr ) {
            foreach ( $attr as $p ) {
                $options [$p ['sku']] [$p ['attribute_code']] = $p ['option_title'];
            }
        }
        return $options;
    }
    /**
     * Send notification mail to seller
     *
     * @param int $productSellerId
     * @param int $remainingQuantity
     * @param int $minimumQuantity
     * @param int $product
     * @return void
     */
    
    public function sendEmail($productSellerId, $remainingQuantity, $minimumQuantity, $product) {
        $productSellerId = $product->getSellerId ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $seller = $objectManager->create ( 'Magento\Customer\Model\Customer' )->load ( $productSellerId );
        /**
         * Get admin details
         */
        $admin = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        /**
         * Assign admin details
         */
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        
        /**
         * Assing sender info
         */
        $senderInfo = [
                'name' => $adminName,
                'email' => $adminEmail
        ];
        
        /**
         * Assign receiver info
         */
        $receiverInfo = [
                'name' => $seller->getName (),
                'email' => $seller->getEmail ()
        ];
        /**
         * Assign values to email template variable
         */
        $emailTempVariables ['receivername'] = $seller->getName ();
        $emailTempVariables ['productname'] = $product->getName ();
        $emailTempVariables ['sku'] = $product->getSku ();
        $emailTempVariables ['qty'] = $remainingQuantity;
        $emailTempVariables ['minqty'] = $minimumQuantity;
        /**
         * Assign template id
         */
        if ($remainingQuantity < $minimumQuantity) {
            $templateId = 'seller_product_outofstock_notification';
        } else {
            exit;
        }
        /**
         * Send email notification
         */
        $objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
    }
}
