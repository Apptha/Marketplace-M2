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
namespace Apptha\Marketplace\Controller\Product;

/**
 * This class contains product sku validation functions
 */
class Skuvalidate extends \Magento\Framework\App\Action\Action {
  
    /**
     *
     * @var $storeManager
     */
    protected $storeManager;
    /**
     * Constructor
     *
     * \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
    }
    /**
     * Function to validate product sku
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting sku from query string
         */
        $sku = trim ( $this->getRequest ()->getParam ( 'sku' ) );
       
        /**
         * Getting product collection
         */
        $productData = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'sku', $sku );
        /**
         * Getting product count
         */
        $skuCount = count ( $productData );
        /**
         * To print product count
         */
        echo $skuCount;
    }
}