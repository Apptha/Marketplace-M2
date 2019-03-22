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
class Compare extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var $resultRawFactory
     * @var $storeManager
     */
    protected $resultRawFactory;
    protected $storeManager;
    /**
     * Constructor
     *
     * \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
     * \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Store\Model\StoreManagerInterface $storeManager) {
        parent::__construct ( $context );
        $this->resultRawFactory = $resultRawFactory;
        $this->storeManager = $storeManager;
    }
    /**
     * Function to validate product sku
     *
     * @return void
     */
    public function execute() {
        $attributeData = $this->getRequest ()->getParam ( 'attributes' );
        $currentProductId = $this->getRequest ()->getParam ( 'id' );
        $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $currentProductId );
        $productCollection = $this->_objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $product )->addAttributeToSelect ( '*' );
        foreach ( $attributeData as $opt => $key ) {
            $productCollection->addAttributeToFilter ( $opt, $key );
        }
        $productCollectionData = $productCollection->getData ();
        foreach ( $productCollectionData as $productData ) {
            $proId [] = $productData ['entity_id'];
        }
        $proId = json_encode ( $proId );
        echo $proId;
    }
}