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
class Cataloginventoryupdate implements ObserverInterface {
    protected $action;
    protected $_productRepository;
    protected $_stockItemRepository;
    public function __construct(Action $action, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemRepository, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->action = $action;
        $this->_productRepository = $productRepository;
        $this->_stockItemRepository = $stockItemRepository;
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
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $repository = $objectManager->create ( 'Magento\Catalog\Model\ProductRepository' );
            $product = $observer->getProduct ();
            $productType = $product->getTypeId();
            if ($productType == 'configurable') {
            $data = $product->getTypeInstance ()->getConfigurableOptions ( $product );
            $options = array ();
                foreach ( $data as $attr ) {
                    foreach ( $attr as $p ) {
                        $options [$p ['sku']] [$p ['attribute_code']] = $p ['option_title'];
                    }
                }
                foreach ( $options as $sku => $d ) {
                    $pr = $repository->get ( $sku );
                    $product = $this->_productRepository->getById ( $pr->getId () );
                    $inventory = $this->_stockItemRepository->getStockItem($productId);
                    $inStock = $inventory ['is_in_stock'];
                    $quantity = $inventory ['qty'];
                    $productSellerId = $product->getSellerId ();                    
                    if ($pr->getId () && $quantity < $minimumQuantity) {
                        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
                        $logger = new \Zend\Log\Logger();
                        $logger->addWriter($writer);
                        $logger->info(print_r($pr->getId () , true));
                        
                        $seller = $objectManager->create ( 'Magento\Customer\Model\Customer' )->load ( $pr->getId () );
                        $admin = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
                        $adminName = $admin->getAdminName ();
                        $adminEmail = $admin->getAdminEmail ();
                        $senderInfo = [
                                'name' => $adminName,
                                'email' => $adminEmail
                        ];
                        $receiverInfo = [ 
                                'name' => $seller->getName (),
                                'email' => $seller->getEmail () 
                        ];
                        
                        $emailTempVariables ['receivername'] = $seller->getName ();
                        $emailTempVariables ['sku'] = $product->getSku (); 
                        $emailTempVariables ['productname'] = $product->getName ();
                        $sellerTemplateId = $this->getTemplate ( $quantity, $inStock, $minimumQuantity );
                        $objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $sellerTemplateId );
                    }
                }
            } else {
            $productId = $product->getId ();
            $product = $this->_productRepository->getById ( $productId );
            $inventory = $this->_stockItemRepository->getStockItem($productId);
            $inStock = $inventory ['is_in_stock'];
            $quantity = $inventory ['qty'];
            $productSellerId = $product->getSellerId ();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            if ($productSellerId && $quantity < $minimumQuantity) {
                
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
                 * Assign receiver info
                 */
                $receiverInfo = [ 
                        'name' => $seller->getName (),
                        'email' => $seller->getEmail () 
                ];
                
                /**
                 * Assing sender info
                 */
                $senderInfo = [ 
                        'name' => $adminName,
                        'email' => $adminEmail 
                ];
                /**
                 * Assign values to email template variable
                 */
                $emailTempVariables ['sku'] = $product->getSku ();
                $emailTempVariables ['receivername'] = $seller->getName ();
                $emailTempVariables ['productname'] = $product->getName ();
                $emailTempVariables ['qty'] = $quantity;
                $emailTempVariables ['minqty'] = $minimumQuantity;
                /**
                 * Assign template id
                 */
                $sellerTemplateId = $this->getTemplate ( $quantity, $inStock, $minimumQuantity );
                /**
                 * Send email notification
                 */
                if ($sellerTemplateId != "") {
                    $objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $sellerTemplateId );
                    return;
                }
            }
          }
        }
    }
    
    /*
     * Get select seller email template
     * return array
     */
    public function getTemplate($quantity, $inStock, $minimumQuantity) {
        if ($quantity == '0' || $inStock == "") {
            return 'seller_product_notification';
        } else if ($quantity < $minimumQuantity && $quantity != '0') {
            return 'seller_product_outofstock_notification';
        } else{
            exit;
        }
    }
}
