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

class Save extends \Magento\Framework\App\Action\Action {
    protected $resultPageFactory;
    protected $productRepository;
    protected $productFactory;
    protected $systemHelper;
    protected $dataHelper;
    /**
     * Constructor
     * \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * \Apptha\Marketplace\Helper\Data $dataHelper
     * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Apptha\Marketplace\Helper\System $systemHelper, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->systemHelper = $systemHelper;
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load assign products layout
     *
     * @return $array
     */
    public function execute() {
        /**
         * Get variant combination
         *
         * @var ids(product Id)
         */
        $assignProductData = $this->getRequest ()->getParam ( 'assignproduct' );
        $parentId = $assignProductData ['assign_product_id'];
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        $childArray = $attributeValues = $simpleIds = array ();
        $attributes = '';
        $configProductData = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $parentId );
        $configTypeId = $configProductData->getTypeId ();
        if ($configTypeId == "configurable") {
            $attributes = $configProductData->getTypeInstance ( true )->getConfigurableAttributes ( $configProductData );
            $variants = $this->getRequest ()->getParam ( 'variant' );
            $assignProductDataPrice = $this->getRequest ()->getParam ( 'price' );
            $assignProductDatasqty = $this->getRequest ()->getParam ( 'qty' );
            foreach ( $variants as $variantProduct ) {
                $productData = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $variantProduct );
                foreach ( $attributes as $attributeData ) {
                    $attributeId = $attributeData->getProductAttribute ()->getAttributeCode ();
                    $optionValue = $productData->getData ( $attributeData->getProductAttribute ()->getAttributeCode () );
                    $attributeValues [$attributeId] = $optionValue;
                }
                $childArray ['id'] = $variantProduct;
                $childArray [$variantProduct] ['price'] = $assignProductDataPrice [$variantProduct];
                $childArray [$variantProduct] ['qty'] = $assignProductDatasqty [$variantProduct];
                $simpleIds [] = $this->saveAssignproduct ( $childArray, $sellerId, $productData, $attributeValues, $configProductData, $parentId );
            }
        }
        
        $nationalShippingAmount = $this->getRequest ()->getParam ( 'national_shipping_amount' );
        $internationalShippingAmount = $this->getRequest ()->getParam ( 'international_shipping_amount' );
        $configProductData = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $parentId );
        $this->saveConfigurableProductInfo ( $assignProductData, $configProductData, $sellerId, $simpleIds, $attributes,$nationalShippingAmount,$internationalShippingAmount );
        
        $this->messageManager->addSuccess ( __ ( 'You saved the product.' ) );
    }
    /**
     * Function to save Assign Product
     */
    public function saveAssignproduct($childArray, $sellerId, $productData, $attributeValues, $assignProductData, $parentId) {
        $baseSimpleProduct = $childArray ['id'];
        $assignproductStockData = array ();
        $assignProduct = $this->productFactory->create ();
        $id = null;
        
        $manager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
        $store = $manager->getStore ( $id );
        $storeId = $store->getStoreId ();
        $websiteId = $store->getWebsiteId ();
        $assignProduct->setStoreId ( $storeId );
        $assignProduct->setWebsiteIds ( array (
                $websiteId 
        ) );
        $assignProduct->setSku ( $assignProductData ['sku'] . rand ( 1, 1000 ) );
        $assignProduct->setName ( $productData ['name'] );
        $assignProduct->setTaxClassId ( 0 );
        $assignProduct->setPrice ( $childArray [$baseSimpleProduct] ['price'] );
        $assignProduct->setStockData ( array (
                'qty' => $childArray [$baseSimpleProduct] ['qty'],
                'is_in_stock' => $productData ['quantity_and_stock_status'] ['is_in_stock'] 
        ) );
        $assignProduct->setMetaKeyword ( $productData ['meta_keyword'] );
        $assignProduct->setMetaDescription ( $productData ['meta_description'] );
        $productApproval = $this->systemHelper->getProductApproval ();
        $assignProduct->setCategoryIds ( $productData ['category_ids'] );
        $assignProduct->setDescription ( $productData ['description'] );
        $assignProduct->setWeight ( $productData ['weight'] );
        $assignProduct->setVisibility ( \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE );
        $assignProduct->setAssignProductId ( $parentId );
        $assignProduct->setIsAssignProduct ( 1 );
        $assignProduct->setTypeId ( $productData ['type_id'] );
        $assignProduct->setAttributeSetId ( $productData ['attribute_set_id'] );
        $assignProduct->setCreatedAt ( strtotime ( 'now' ) );
        $assignProduct->setSellerId ( $sellerId );
        $assignProduct->setConfigAssignSimpleId ( $childArray ['id'] );
        foreach ( $attributeValues as $key => $attributeValue ) {
            $assignProduct->setData ( $key, $attributeValue );
        }
        $productApproval = $this->systemHelper->getProductApproval ();
        if ($productApproval == 1) {
            $assignProduct->setStatus ( 1 );
            $assignProduct->setProductApproval ( 1 );
        } else {
            $assignProduct->setStatus ( 2 );
            $assignProduct->setProductApproval ( 0 );
        }
        $productCollection = $this->_objectManager->get ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToFilter ( 'url_key', $productData ['name'] );
        $productCollectionData = $productCollection->getData ();
        $urlKeyCount = count ( $productCollectionData );
        if ($urlKeyCount >= 1) {
            $assignProduct->setUrlKey ( $productData ['name'] . rand ( 1, 10000 ) );
        }
        $assignproductStockData ['quantity_and_stock_status'] ['qty'] = $childArray [$baseSimpleProduct] ['qty'];
        $assignproductStockData ['quantity_and_stock_status'] ['is_in_stock'] = $productData ['quantity_and_stock_status'] ['is_in_stock'];
        $assignProduct = $this->productRepository->save ( $assignProduct );
        /**
         * Save stock and image for product
         */
        $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Product\Savedata' )->baseImageForProduct ( $assignProduct->getId (), $productData->getImage () );
        $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Product\Savedata' )->updateStockDataForProduct ( $assignProduct->getId (), $assignproductStockData );
        return $assignProduct->getId ();
    }
    
    /**
     * save Configurable Product
     */
    public function saveConfigurableProductInfo($assignProductData, $configProductData, $sellerId, $simpleIds, $configAttributes,$nationalShippingAmount,$internationalShippingAmount) {
        $configProduct = $this->productFactory->create ();
        $id = null;
        $manager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
        $store = $manager->getStore ( $id );
        $websiteId = $store->getWebsiteId ();
        $configProduct->setStoreId ( 0 );
        $configProduct->setWebsiteIds ( array (
                $websiteId 
        ) );
        $configProduct->setSku ( $assignProductData ['sku'] . rand ( 1, 1000 ) );
        $configProduct->setName ( $configProductData ['name'] );
        $configProduct->setTaxClassId ( 0 );
        $configProduct->setMetaKeyword ( $configProductData ['meta_keyword'] );
        $configProduct->setMetaDescription ( $configProductData ['meta_description'] );
        $productApproval = $this->systemHelper->getProductApproval ();
        $configProduct->setCategoryIds ( $configProductData ['category_ids'] );
        $configProduct->setDescription ( $assignProductData ['description'] );
        $configProduct->setWeight ( $configProductData ['weight'] );
        $configProduct->setVisibility ( \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE );
        $configProduct->setAssignProductId ( $configProductData ['entity_id'] );
        $configProduct->setIsAssignProduct ( 1 );
        $configProduct->setTypeId ( $configProductData ['type_id'] );
        $configProduct->setAttributeSetId ( $configProductData ['attribute_set_id'] );
        $configProduct->setCreatedAt ( strtotime ( 'now' ) );
        $configProduct->setSellerId ( $sellerId );
        $productCollection = $this->_objectManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToFilter ( 'url_key', $configProductData ['name'] );
        $productCollectionData = $productCollection->getData ();
        $urlKeyCount = count ( $productCollectionData );
        if ($urlKeyCount >= 1) {
            $configProduct->setUrlKey ( $configProductData ['name'] . rand ( 1, 10000 ) );
        }
        if ($configProductData ['type_id'] != "configurable") {
            $configProduct->setPrice ( $assignProductData ['price'] );
        }
        
        if (! empty ( $nationalShippingAmount )) {
            $configProduct->setNationalShippingAmount ( $nationalShippingAmount );
        }
        if (! empty ( $internationalShippingAmount )) {
            $configProduct->setInternationalShippingAmount ( $internationalShippingAmount );
        }
        
        $productApproval = $this->systemHelper->getProductApproval ();
        if ($productApproval == 1) {
            $configProduct->setStatus ( 1 );
            $configProduct->setProductApproval ( 1 );
        } else {
            $configProduct->setStatus ( 2 );
            $configProduct->setProductApproval ( 0 );
        }
        $configProduct = $this->productRepository->save ( $configProduct );
        /**
         * Save stock and image for product
         */
        $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Product\Savedata' )->baseImageForProduct ( $configProduct->getId (), $configProductData->getImage () );
        $configurableProduct = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $configProduct->getId () );
        $typeId = $configurableProduct->getTypeId ();
        if ($typeId != "configurable") {
            $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Product\Savedata' )->updateStockDataForProduct ( $configProduct->getId (), $assignProductData );
        }
        if ($typeId == "configurable") {
            $configurableProductId = $configProduct->getId ();
            $configurableProduct = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $configurableProductId );
            $configModel = $this->_objectManager->create ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute' );
            $position = 0;
            $attributeIds = array ();
            foreach ( $configAttributes as $attri ) {
                $attributeIds [] = $attributeId = $attri->getProductAttribute ()->getAttributeId ();
                $configdata = array (
                        'attribute_id' => $attributeId,
                        'product_id' => $configurableProductId,
                        'position' => $position 
                );
                    $position ++;
                    $configModel->setData ( $configdata )->save();
                }
                $configurableProduct->setTypeId("configurable");
                $this->_objectManager->create ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->setUsedProductAttributeIds ( $attributeIds, $configurableProduct );
                $configurableProduct->setNewVariationsAttributeSetId($configProductData ['attribute_set_id'] );
                $attributesData=$this->_objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getConfigurableAttributesAsArray($configurableProduct);
                $configurableProduct->setConfigurableAttributesData($attributesData);
                $configurableProduct->setAssociatedProductIds($simpleIds);
                $configurableProduct->setCanSaveConfigurableAttributes(true);
                $configurableProduct->save();
                $this->_objectManager->get('Apptha\Marketplace\Controller\Product\Savedata')->updateStockDataForProduct ( $configProduct->getId (), $assignProductData );
                }
        $this->_redirect ( 'marketplace/assignproduct/manage' );
    }
}