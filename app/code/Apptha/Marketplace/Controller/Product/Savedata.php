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
 * This class contains product save functions
 */
class Savedata extends \Magento\Framework\App\Action\Action {


    protected $resultPageFactory;
    protected $productRepository;
    protected $productFactory;
    protected $systemHelper;
    protected $dataHelper;
    protected $_file;

    /**
     * Constructor
     * \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * \Apptha\Marketplace\Helper\Marketplace $dataHelper
     * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Apptha\Marketplace\Helper\System $systemHelper, \Apptha\Marketplace\Helper\Marketplace $dataHelper, \Magento\Framework\Filesystem\Driver\File $file) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->systemHelper = $systemHelper;
        $this->dataHelper = $dataHelper;
        $this->_file = $file;
        parent::__construct ( $context );
    }

    /**
     * Execute the save product function
     *
     * @return object
     */
    public function execute() {
        $editSimpleProductFlag = 0;

        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        if (! $customerSession->isLoggedIn () && $status != 0) {
            $this->_redirect ( 'marketplace/seller/dashboard' );
            return;
        }
        $productId = $this->getRequest ()->getParam ( 'product_id' );
        $productAttributeSetId = $this->getRequest ()->getParam ( 'set' );
        $productTypeId = $this->getRequest ()->getParam ( 'type' );
        $productData = $this->getRequest ()->getParam ( 'product' );
        $categoryIds = $this->getRequest ()->getParam ( 'category_ids' );
        $nationalShippingAmount = $this->getRequest ()->getParam ( 'national_shipping_amount' );
        $internationalShippingAmount = $this->getRequest ()->getParam ( 'international_shipping_amount' );
        $postValue = $_POST;

        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        $simpleProductData = $simpleProductImagesPath = $simpleProdouctIds = array ();
        if ($productTypeId == 'configurable') {
            $simpleProductData ['configurable_image'] = $this->getRequest ()->getParam ( 'configurable_image' );
            $simpleProductData ['configurable_price'] = $this->getRequest ()->getParam ( 'configurable_price' );
            $simpleProductData ['configurable_qty'] = $this->getRequest ()->getParam ( 'configurable_qty' );
            $simpleProductData ['selected_attributes'] = $this->getRequest ()->getParam ( 'selected_attributes' );
            $simpleProductData ['selected_options'] = $this->getRequest ()->getParam ( 'selected_options' );
            $simpleProductData ['configurable_product'] = $this->getRequest ()->getParam ( 'configurable_product' );
            if ($this->getRequest ()->getParam ( 'configurable_product' ) != '') {
                $editSimpleProductFlag = 1;
            }
            $simpleProductInfo = $this->saveSimpleProductsForConfigurableProduct ( $simpleProductData, $productAttributeSetId );
            $simpleProdouctIds = $simpleProductInfo ['simple_product_ids'];
            $simpleProductImagesPath = $simpleProductInfo ['simple_product_images_path'];
        }

        if (! empty ( $productId )) {
            $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $productModel = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ();
            $productModel->addFieldToFilter ( 'sku', $productData ['sku'] );
            $productModel->addFieldToFilter ( 'entity_id', array (
                    'neq' => $productId
            ) );
            if (count ( $productModel ) >= 1) {
                $this->messageManager->addNotice ( __ ( 'Sku already exists' ) );
                $this->_redirect ( 'marketplace/product/add/product_id/' . $productId );
                return;
            }
        } else {
            $product = $this->productFactory->create ();
            $productModel = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ();
            $productModel->addFieldToFilter ( 'sku', $productData ['sku'] );
            if (count ( $productModel ) >= 1) {
                $this->messageManager->addNotice ( __ ( 'Sku already exists' ) );
                $this->_redirect ( 'marketplace/product/add' );
                return;
            }
        }
        if (empty ( $productId )) {
            $product->setTypeId ( $productTypeId );
            $product->setAttributeSetId ( $productAttributeSetId );
            $product->setCreatedAt ( strtotime ( 'now' ) );
            $product->setSellerId ( $sellerId );
        }
        $id = null;

        $manager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
        $store = $manager->getStore ( $id );
        $storeId = $store->getStoreId ();
        $websiteId = $store->getWebsiteId ();
        $product->setWebsiteIds ( array (
                $websiteId
        ) );
        $product = $this->assignProductData ( $productId, $product, $storeId, $productTypeId, $productData );
        $productData = $this->changeProductData ( $productData );
        $product->setSku ( $productData ['sku'] );
        $product->setName ( $productData ['name'] );
        if ($productTypeId == 'downloadable') {
            $product->setStockData ( array (
                    'use_config_manage_stock' => 0,
                    'is_in_stock' => 1,
                    'manage_stock' => 0,
                    'use_config_notify_stock_qty' => 0
            ) );
        } else {
            $product->setStockData ( array (
                    'qty' => $productData ['quantity_and_stock_status'] ['qty'],
                    'is_in_stock' => $productData ['quantity_and_stock_status'] ['is_in_stock']
            ) );
        }
        $productApproval = $this->systemHelper->getProductApproval ();
        $this->setProductApproval ( $productApproval, $product, $productData, $productId );
        $customAttributes = $this->getRequest ()->getParam ( 'custom_attributes' );
        $this->setProductData ( $product, $categoryIds, $productData, $nationalShippingAmount, $internationalShippingAmount, $customAttributes );
        if (! empty ( $productId )) {
            $product->save ();
            if(!empty($this->systemHelper->getYoutubeKey())) {
                $product = $this->dataHelper->addVideo($product,$postValue);
            }
            $this->messageManager->addSuccess ( __ ( 'You updated the product.' ) );
        } else {

            $productCollection = $this->_objectManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToFilter ( 'url_key', $productData ['name'] );
            $productCollectionData = $productCollection->getData ();
            $urlKeyCount = count ( $productCollectionData );
            if ($urlKeyCount >= 1) {
                $product->setUrlKey ( $productData ['name'] . rand ( 1, 10000 ) );
            }
            $product->save ();
            if(!empty($this->systemHelper->getYoutubeKey())) {
                $product = $this->dataHelper->addVideo($product,$postValue);
            }
            $this->messageManager->addSuccess ( __ ( 'You saved the product.' ) );
            if ($productApproval == 0) {
                $this->messageManager->addNotice ( __ ( 'Your Product is waiting for admin approval.' ) );
            }
        }
        if ($productTypeId == 'configurable') {
            $action = 'add';
            if (! empty ( $productId )) {
                $action = 'edit';
            }
            $this->associateSimpleProductsWithConfigurable ( $action, $product->getId (), $simpleProductData, $simpleProdouctIds, $productAttributeSetId, $editSimpleProductFlag );
        }
        $this->saveCustomOption ( $product->getId (), $productData );
        $imagesPaths = array ();
        $imagesPaths = $this->getRequest ()->getParam ( 'images_path' );
        if (is_array ( $imagesPaths ) && is_array ( $simpleProductImagesPath )) {
            $imagesPaths = array_diff ( $imagesPaths, $simpleProductImagesPath );
        }
        $this->saveImageForProduct ( $product->getId (), $imagesPaths );
        $removeImageIds = $this->getRequest ()->getParam ( 'remove_image' );
        $this->removeImageForProduct ( $product->getId (), $removeImageIds );
        $baseImage = $this->getRequest ()->getParam ( 'base_image' );
        $this->baseImageForProduct ( $product->getId (), $baseImage );
        $store = $storeId;
        $downloadProductId = $product->getId ();
        $downloadableData = $this->getRequest ()->getParam ( 'downloadable' );
        $marketplaceGeneral = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\General' );
        $marketplaceGeneral->assignDataForDownloadableProduct ( $downloadProductId, $store, $downloadableData );
        $this->updateStockDataForProduct ( $product->getId (), $productData );
        $this->_eventManager->dispatch ( 'controller_action_catalog_product_save_entity_after', [
                'controller' => $this,
                'product' => $product
        ] );
        $this->_redirect ( 'marketplace/product/manage' );
    }


    /**
     * Change product data array
     *
     * @param array $productData
     *
     * @return array
     */
    public function changeProductData($productData) {
        if (! isset ( $productData ['price'] )) {
            $productData ['price'] = 0;
        }
        if (! isset ( $productData ['quantity_and_stock_status'] ['qty'] )) {
            $productData ['quantity_and_stock_status'] ['qty'] = 0;
        }
        return $productData;
    }

    /**
     * Assign product data
     *
     * @param int $productId
     * @param object $product
     * @param int $storeId
     * @param string $productTypeId
     * @param object $productData
     */
    public function assignProductData($productId, $product, $storeId, $productTypeId, $productData) {
        if (! empty ( $productId ) && $storeId != '1') {
            $product->setStoreId ( $storeId );
        } else {
            $product->setStoreId ( 0 );
        }

        if ($productTypeId != 'configurable') {
            $product->setPrice ( $productData ['price'] );
            $product->setSpecialPrice ( $productData ['specialprice'] );
            $product->setSpecialFromDate ( $productData ['specialpricefromdate'] );
            $product->setSpecialToDate ( $productData ['specialpricetodate'] );
        }
        return $product;
    }
    /**
     * To set product approval
     *
     * @param int $productApproval
     * @param object $product
     * @param array $productData
     * @param int $productId
     *
     * @return object
     */
    public function setProductApproval($productApproval, $product, $productData, $productId) {
        if ($productApproval == 1) {
            $product->setStatus ( $productData ['status'] );
            if (empty ( $productId )) {
                $product->setProductApproval ( 1 );
            }
        } else {
            if (empty ( $productId )) {
                $product->setStatus ( 2 );
            } else {
                if ($product->getProductApproval () == 1) {
                    $product->setStatus ( $productData ['status'] );
                }
            }
        }
        return $product;
    }

    /**
     * To set product data
     *
     * @param object $product
     * @param array $categoryIds
     * @param array $productData
     * @param float $nationalShippingAmount
     * @param float $internationalShippingAmount
     * @param array $customAttributes
     *
     * @return object
     */
    public function setProductData($product, $categoryIds, $productData, $nationalShippingAmount, $internationalShippingAmount, $customAttributes) {

        $product->setCategoryIds ( $categoryIds );
        $product->setDescription ( $productData ['description'] );
        if (! empty ( $productData ['weight'] )) {
            $product->setWeight ( $productData ['weight'] );
        }
        $ConfigEdit = $this->getRequest ()->getParam ( 'config' );
        if(!empty($ConfigEdit)){
            $product->setVisibility ( '1' );
        } else{
        $product->setVisibility ( \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH );
        }

        if (! empty ( $nationalShippingAmount )) {
            $product->setNationalShippingAmount ( $nationalShippingAmount );
        }
        if (! empty ( $internationalShippingAmount )) {
            $product->setInternationalShippingAmount ( $internationalShippingAmount );
        }
        if (! empty ( $customAttributes )) {
            $product = $this->_objectManager->get ( 'Apptha\Marketplace\Block\Product\Configurable' )->addCustomAttributes ( $product, $customAttributes, $productData );
        }
        $product->setTaxClassId ( 0 );
        $product->setMetaKeyword ( $productData ['meta_keyword'] );
        $product->setMetaDescription ( $productData ['meta_description'] );
        return $product;
    }

    /**
     * Save images for product
     *
     * @param int $productId
     * @param array $imagesPaths
     * @return void
     */
    public function saveImageForProduct($productId, $imagesPaths) {
        if (count ( $imagesPaths ) >= 1) {
            array_unique ( $imagesPaths );

            $productImage = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $images = [ ];
            $inc = 1;
            foreach ( $imagesPaths as $path ) {
                $length = 10;
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen ( $characters );
                $randomString = '';
                for($i = 0; $i < $length; $i ++) {
                    $randomString .= $characters [rand ( 0, $charactersLength - 1 )];
                }
                $randomStringArr = array (
                        "position" => $inc,
                        "media_type" => "image",
                        "video_provider" => "",
                        "file" => $path,
                        "value_id" => "",
                        "label" => "",
                        "disabled" => 0,
                        "removed" => "",
                        "video_url" => "",
                        "video_title" => "",
                        "video_description" => "",
                        "video_metadata" => "",
                        "role" => ""
                );
                $images [$randomString] = $randomStringArr;
                $inc = $inc + 1;
            }
            $productImage->setData ( 'media_gallery', [
                    'images' => $images
            ] );
            $productImage->save ();
        }
    }

    /**
     * Removing existing images from product
     *
     * @param
     *            int product id
     * @param array $removeImageIds
     * @return void
     */
    public function removeImageForProduct($productId, $imagesIds) {
        if (count ( $imagesIds ) < 1) {
            return;
        }
        array_unique ( $imagesIds );

        $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $images = $product->getMediaGalleryImages ();
        $objectGallery = $this->_objectManager->get ( 'Magento\Catalog\Model\ResourceModel\Product\Gallery' );
        $objectGallery->deleteGallery ( $imagesIds );
        $mediaDirectory = $this->_objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( \Magento\Framework\App\Filesystem\DirectoryList::MEDIA );
        $mediaRootDir = $mediaDirectory->getAbsolutePath ();
        foreach ( $imagesIds as $image ) {
            foreach ( $images as $productImage ) {
                if ($productImage ['id'] == $image) {
                    $imageFilePath = $productImage ['file'];
                    $mediaRootDirectory = $mediaRootDir . 'catalog/product';
                    if ($this->_file->isExists ( $mediaRootDirectory . $imageFilePath )) {
                        $this->_file->deleteFile ( $mediaRootDirectory . $imageFilePath );
                    }
                }
            }
        }
    }

    /**
     * Set base image
     *
     * @param int $productId
     * @param string $baseImage
     * @return void
     */
    public function baseImageForProduct($productId, $baseImage) {
        if (! empty ( $baseImage )) {

            $productBaseImage = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            $productBaseImage->setImage ( $baseImage );
            $productBaseImage->setSmallImage ( $baseImage );
            $productBaseImage->setThumbnail ( $baseImage );
            $productBaseImage->save ();
        }
    }

    /**
     * Update stock data for product
     *
     * @param int $productId
     * @param array $productData
     * @return void
     */
    public function updateStockDataForProduct($productId, $productData) {

        $stockData = $this->_objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $productId, 'product_id' );
        $stockData->setQty ( $productData ['quantity_and_stock_status'] ['qty'] );
        $stockData->setIsInStock ( $productData ['quantity_and_stock_status'] ['is_in_stock'] );
        $stockData->save ();
    }

    /**
     * Save product custom option
     *
     * @param int $productId
     * @param array $productData
     * @return void
     */
    public function saveCustomOption($productId, $productData) {

        $product = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        /**
         * Delete existing product custom option
         */
        if ($product->getOptions ()) {
            foreach ( $product->getOptions () as $opt ) {
                $opt->delete ();
            }
            $product->setCanSaveCustomOptions ( 1 );
            $product->save ();
        }

        /**
         * Initialize product options
         */
        if (isset ( $productData ['options'] )) {
            $productOptions = $productData ['options'];
            /**
             * Initialize product options
             */
            $customOptions = [ ];
            foreach ( $productOptions as $customOptionData ) {
                if (empty ( $customOptionData ['is_delete'] )) {
                    if (isset ( $customOptionData ['values'] )) {
                        $customOptionData ['values'] = array_filter ( $customOptionData ['values'], function ($valueData) {
                            return empty ( $valueData ['is_delete'] );
                        } );
                    }

                    $customOption = $this->_objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory')->create ( [
                            'data' => $customOptionData
                    ] );
                    $customOption->setProductSku ( $product->getSku () );
                    $customOptions [] = $customOption;
                }
            }
            $product->setOptions ( $customOptions );
            $product->setCanSaveCustomOptions ( true );

            $product->save ();
        }
    }
    /**
     * Save configurable associated products
     *
     * @param array $simpleProductData
     *
     * @return array $simpleProductIds
     */
    public function saveSimpleProductsForConfigurableProduct($simpleProductData, $productAttributeSetId) {
        $configurableImage = $simpleProductData ['configurable_image'];
        $configurablePrice = $simpleProductData ['configurable_price'];
        $configurableQty = $simpleProductData ['configurable_qty'];
        $selectedOptions = $simpleProductData ['selected_options'];
        $configurableProducts = $simpleProductData ['configurable_product'];
        $simpleProductIds = array ();
        /**
         * Save simple products data
         */
        $imageFlag = $productCount = 0;
        $usedPath = $simpleProductImagesPath = array ();
        if (is_array ( $configurableProducts )) {
            foreach ( $configurableProducts as $configurableProduct ) {
                /**
                 * Get combination value
                 */

                if (isset ( $configurableProduct ['attribute_combination'] )) {
                    $attributeCombination = $configurableProduct ['attribute_combination'];

                    $attributeCombinationArray = explode ( "-", $attributeCombination );
                    $attributeCombinationArray = array_filter ( $attributeCombinationArray );

                    $existingProductId = '';
                    if (isset ( $configurableProduct ['simple_product_id'] )) {
                        $simpleProduct = '';
                        $existingProductId = $configurableProduct ['simple_product_id'];
                        $simpleProductSku = $configurableProduct ['sku'];
                    } else {

                        $customerSessionObj = $this->_objectManager->create ( 'Magento\Customer\Model\Session' );
                        $sellerId = $customerSessionObj->getId ();
                        $simpleProduct = $this->_objectManager->create ( '\Magento\Catalog\Model\Product' );
                        $simpleProduct->setSku ( $configurableProduct ['sku'] );
                        $simpleProduct->setName ( $configurableProduct ['sku'] );
                        $simpleProduct->setAttributeSetId ( $productAttributeSetId );
                        $simpleProduct->setStatus ( 1 );
                        $ConfigEdit = $this->getRequest ()->getParam ( 'config' );
                        $ConfigEdit =='1'?$simpleProduct->setVisibility ( '1' ):$simpleProduct->setVisibility ( \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE );
                        $simpleProduct->setTaxClassId ( 0 );
                        $simpleProduct->setTypeId ( 'simple' );
                        $simpleProduct->setSellerId ( $sellerId );
                        $id = null;
                        $manager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
                        $store = $manager->getStore ( $id );
                        $websiteId = $store->getWebsiteId ();
                        $simpleProduct->setStoreId ( 0 );
                        $simpleProduct->setWebsiteIds ( array (
                                $websiteId
                        ) );
                        $simpleProduct->setProductApproval ( 1 );
                        /**
                         * Prepare configurable attributes
                         */
                        $configurableAttributes = $this->getConfigurableAttributes ( $selectedOptions, $attributeCombinationArray );
                        /**
                         * Storing configurable attributes
                         */
                        $simpleProduct->addData ( $configurableAttributes );
                    }
                    $qtyData = $this->setQtyValue ( $existingProductId, $simpleProduct, $configurableQty, $configurableProduct, $attributeCombinationArray );
                    $qty = $qtyData ['qty'];
                    $priceData = $this->setPriceValue ( $existingProductId, $simpleProduct, $configurablePrice, $configurableProduct, $attributeCombinationArray );
                    $price = $priceData ['price'];
                    if (empty ( $existingProductId )) {
                        $simpleProduct = $qtyData ['simple_product'];
                        $simpleProduct = $priceData ['simple_product'];
                        $simpleProduct->save ();
                    } else {
                        $simpleProduct = $this->updateSimpleProductInfo ( $existingProductId, $simpleProductSku, $qty, $price );
                        $this->deleteExistingProductImages ( $simpleProduct->getId () );
                    }
                    $simpleProductIds [] = $simpleProduct->getId ();
                    /**
                     * Set product image
                     */
                    $baseImage = '';
                    $imagesPaths = array ();
                    if ($configurableImage == 'image_all' && isset ( $configurableProduct ['image_path'] ['all'] )) {
                        $imageFlag = 1;

                        $imagesPaths = $simpleProductImagesPath = $this->_objectManager->create ( '\Apptha\Marketplace\Model\Seller' )->getImagesForConfigurable($configurableProduct ['image_path'] ['all']);
                        $baseImage = isset ( $configurableProduct ['base_path'] ['all'] )? $configurableProduct ['base_path'] ['all'] : '';
                    }
                    /**
                     * Set variant based product image
                     */
                    $productIdForImage = '';
                    if ($configurableImage == 'image_unique') {
                        $imageReturn = $this->getImageInfo ( $attributeCombinationArray, $configurableProduct, $imagesPaths, $simpleProductImagesPath, $usedPath, $simpleProduct );
                        $usedPath = $imageReturn ['used_path'];
                        $productIdForImage = $imageReturn ['product_id_for_image'];
                        $imagesPaths = $imageReturn ['images_paths'];
                        $simpleProductImagesPath = $imageReturn ['simple_product_images_path'];
                        $baseImage = $imageReturn ['base_image'];
                    }
                    if ($productCount == 0 && $imageFlag == 1) {
                        $this->saveImageForProduct ( $simpleProduct->getId (), $imagesPaths );
                        $productCount = 1;
                    } elseif ($productCount != 0 && $imageFlag == 1) {
                        $this->updateImageValueByProduct ( $simpleProduct->getId (), $simpleProductIds [0] );
                    } else {
                        (!empty ( $productIdForImage ))? $this->updateImageValueByProduct ( $simpleProduct->getId (), $productIdForImage ) : $this->saveImageForProduct ( $simpleProduct->getId (), $imagesPaths );
                    }
                    if (! empty ( $baseImage )) {
                        $this->baseImageForProduct ( $simpleProduct->getId (), $baseImage );
                    }
                }
            }
        }
        $simpleProductInfo ['simple_product_images_path'] = $simpleProductImagesPath;
        $simpleProductInfo ['simple_product_ids'] = $simpleProductIds;
        return $simpleProductInfo;
    }

    /**
     * To get image info
     *
     * @param array $attributeCombinationArray
     * @param array $configurableProduct
     * @param array $imagesPaths
     * @param array $simpleProductImagesPath
     * @param array $usedPath
     * @param array $simpleProduct
     *
     * @return array
     */
    public function getImageInfo($attributeCombinationArray, $configurableProduct, $imagesPaths, $simpleProductImagesPath, $usedPath, $simpleProduct) {
        $baseImage = $productIdForImage = '';
        foreach ( $attributeCombinationArray as $value ) {
            if (isset ( $configurableProduct ['image_path'] [$value] )) {
                foreach ( $configurableProduct ['image_path'] [$value] as $imagePath ) {
                    $imagesPaths [] = $imagePath;
                    $simpleProductImagesPath [] = $imagePath;
                    if (in_array ( $imagePath, $usedPath )) {
                        $productIdForImage = array_search ( $imagePath, $usedPath );
                    } else {
                        $usedPath [$simpleProduct->getId ()] = $imagePath;
                    }
                }
            }
            if (isset ( $configurableProduct ['base_path'] [$value] )) {
                $baseImage = $configurableProduct ['base_path'] [$value];
            }
        }
        return array (
                'used_path' => $usedPath,
                'product_id_for_image' => $productIdForImage,
                'images_paths' => $imagesPaths,
                'simple_product_images_path' => $simpleProductImagesPath,
                'base_image' => $baseImage
        );
    }

    /**
     * Update existing simple product info
     *
     * @param int $existingProductId
     * @param string $simpleProductSku
     * @param int $qty
     * @param float $price
     *
     * @return void
     */
    public function updateSimpleProductInfo($existingProductId, $simpleProductSku, $qty, $price) {
        $updateFlag = 0;

        $existingProduct = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $existingProductId );

        if ($existingProduct->getSku () != $simpleProductSku) {
            $existingProduct->setSku ( $simpleProductSku );
            $existingProduct->setName ( $simpleProductSku );
            $updateFlag = 1;
        }
        if (! empty ( $price )) {
            $existingProduct->setPrice ( $price );
            $updateFlag = 1;
        }
        /**
         * Checking for updated or not
         */
        if ($updateFlag == 1) {
            $existingProduct->save ();
        }

        if (! empty ( $qty )) {
            $existingProductData ['quantity_and_stock_status'] ['qty'] = $qty;
            $existingProductData ['quantity_and_stock_status'] ['is_in_stock'] = 1;
            $this->updateStockDataForProduct ( $existingProductId, $existingProductData );
        }
        return $existingProduct;
    }
    /**
     * Set product Qty value
     *
     * @param object $simpleProduct
     * @param object $configurableQty
     * @param object $configurableProduct
     * @param
     *            $attributeCombinationArray
     * @param object $simpleProduct
     */
    public function setQtyValue($existingProductId, $simpleProduct, $configurableQty, $configurableProduct, $attributeCombinationArray) {
        $qty = '';
        /**
         * Set product qty
         */
        if ($configurableQty == 'qty_all' && isset ( $configurableProduct ['qty'] ['all'] )) {
            $qty = $configurableProduct ['qty'] ['all'];
        }

        /**
         * Set variant based product qty
         */
        if ($configurableQty == 'qty_unique') {
            foreach ( $attributeCombinationArray as $value ) {
                if (isset ( $configurableProduct ['qty'] [$value] )) {
                    $qty = $configurableProduct ['qty'] [$value];
                }
            }
        }

        $qtyData = array ();
        $qtyData ['qty'] = $qty;

        if (empty ( $existingProductId )) {
            if (empty ( $qty )) {
                $qty = 0;
            }
            $simpleProduct->setStockData ( array (
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => $qty
            ) );
            $qtyData ['simple_product'] = $simpleProduct;
        }

        return $qtyData;
    }

    /**
     * Set product price value
     *
     * @param object $simpleProduct
     * @param object $configurablePrice
     * @param object $configurableProduct
     * @param
     *            $attributeCombinationArray
     * @param object $simpleProduct
     */
    public function setPriceValue($existingProductId, $simpleProduct, $configurablePrice, $configurableProduct, $attributeCombinationArray) {
        /**
         * Set product price
         */
        $price = '';
        if ($configurablePrice == 'price_all' && isset ( $configurableProduct ['price'] ['all'] ) || $configurablePrice == 'price_skip' && isset ( $configurableProduct ['price'] ['all'] )) {
            $price = $configurableProduct ['price'] ['all'];
        }

        /**
         * Set variant based product price
         */
        if ($configurablePrice == 'price_unique') {
            foreach ( $attributeCombinationArray as $value ) {
                if (isset ( $configurableProduct ['price'] [$value] )) {
                    $price = $configurableProduct ['price'] [$value];
                }
            }
        }
        $priceData = array ();
        $priceData ['price'] = $price;

        if (empty ( $existingProductId )) {
            if (empty ( $price )) {
                $price = 0;
            }
            $simpleProduct->setPrice ( $price );
            $priceData ['simple_product'] = $simpleProduct;
        }
        return $priceData;
    }

    /**
     * Update image for products
     *
     * @param int $currentProductId
     * @param int $createdProductId
     *
     * @return void
     */
    public function updateImageValueByProduct($currentProductId, $createdProductId) {

        $productImages = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $createdProductId )->getMediaGalleryImages ();
        $currentProduct = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $currentProductId );
        if (count ( $productImages ) >= 1) {
            foreach ( $productImages as $productImage ) {
                $currentProduct->addImageToMediaGallery ( $productImage ['path'], array (
                        'image',
                        'small_image',
                        'thumbnail'
                ), false, false );
            }
            $currentProduct->save ();
        }
    }

    /**
     * Delete existing product images
     *
     * @param int $deleteImageProductId
     *
     * @return void
     */
    public function deleteExistingProductImages($deleteImageProductId) {

        $productImages = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $deleteImageProductId )->getMediaGalleryImages ();
        $deleteimagesIds = array ();
        foreach ( $productImages as $productImage ) {
            $deleteimagesIds [] = $productImage ['value_id'];
        }
        if (count ( $deleteimagesIds ) >= 1) {
            $this->removeImageForProduct ( $deleteImageProductId, $deleteimagesIds );
        }
    }

    /**
     * Get Configurable Attributes
     *
     * @param array $selectedOptions
     * @param array $attributeCombinationArray
     *
     * @return array $configurableAttributes
     */
    public function getConfigurableAttributes($selectedOptions, $attributeCombinationArray) {
        $configurableAttributes = array ();
        /**
         * Prepare configurable attribute data
         */
        foreach ( $selectedOptions as $key => $selectedOption ) {
            if (in_array ( $key, $attributeCombinationArray )) {
                $configurableAttributes [$selectedOption] = $key;
            }
        }
        return $configurableAttributes;
    }

    /**
     * To set associated simple product with configurable product
     *
     * @param int $productId
     * @param array $simpleProductData
     * @param array $simpleProdouctIds
     * @param int $productAttributeSetId
     *
     * @return void
     */
    public function associateSimpleProductsWithConfigurable($action, $productId, $simpleProductData, $simpleProdouctIds, $productAttributeSetId, $editSimpleProductFlag) {
        if ($editSimpleProductFlag == 1) {

            $configurableProduct = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );

            if ($action == 'add') {
                $attributeModel = $this->_objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute' );
                $position = 0;
                /**
                 * Get configurable attributes
                 */
                $attributes = array ();
                if (isset ( $simpleProductData ['selected_attributes'] )) {
                    $selectedAttributes = $simpleProductData ['selected_attributes'];
                    foreach ( $selectedAttributes as $selectedAttribute ) {
                        $attributes [] = $this->_objectManager->create ( 'Magento\Catalog\Model\Product\Attribute\Repository' )->get ( $selectedAttribute )->getAttributeId ();
                    }
                }
                /**
                 * Set attributes for configurable product
                 */
                foreach ( $attributes as $attributeId ) {
                    $data = array (
                            'attribute_id' => $attributeId,
                            'product_id' => $productId,
                            'position' => $position
                    );
                    $position ++;
                    $attributeModel->setData ( $data )->save ();
                }
            }

            /**
             * Checking add or edit action for configurable product
             */
            if ($action == 'add') {
                /**
                 * Set configurable product attributes
                 */
                $configurableProduct->setTypeId ( "configurable" );
                $this->_objectManager->create ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->setUsedProductAttributeIds ( $attributes, $configurableProduct );
                $configurableProduct->setNewVariationsAttributeSetId ( $productAttributeSetId );
                $attributesData = $this->_objectManager->create ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getConfigurableAttributesAsArray ( $configurableProduct );
                $configurableProduct->setConfigurableAttributesData ( $attributesData );
            }

            $configurableProduct->setAssociatedProductIds ( $simpleProdouctIds );
            $configurableProduct->setCanSaveConfigurableAttributes ( true);
            $configurableProduct->save ();
        }
    }
}