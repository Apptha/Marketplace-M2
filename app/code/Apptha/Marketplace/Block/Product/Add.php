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
namespace Apptha\Marketplace\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource\Product\CollectionFactory;
use Zend\Form\Annotation\Instance;

/**
 * This class used to display product add/edit form
 */
class Add extends \Magento\Framework\View\Element\Template {
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection
     */
    protected $attributeSet;
    
    /**
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    
    /**
     *
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;
    
    /**
     *
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;
    
    protected $_scopeConfig;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSet
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSet, \Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState, CategoryRepositoryInterface $categoryRepository, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,array $data = []) {
        parent::__construct ( $context, $data );
        $this->storeManager = $context->getStoreManager();
        $this->attributeSet = $attributeSet;
        $this->product = $product;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->categoryRepository = $categoryRepository;
        $this->_scopeConfig = $scopeConfig;
    }
    
    /**
     * Prepare layout for add product
     *
     * @return object
     */
    public function _prepareLayout() {
        $productId = $this->getRequest ()->getParam ( 'product_id' );
        if (! empty ( $productId )) {
            $this->pageConfig->getTitle ()->set ( __ ( 'Edit Product' ) );
        } else {
            $this->pageConfig->getTitle ()->set ( __ ( 'Add Product' ) );
        }
        return parent::_prepareLayout ();
    }
    
    /**
     * Get base currency symbol
     *
     * @return string
     */
    public function getBaseCurrency() {
        return $this->storeManager->getStore ()->getBaseCurrencyCode ();
    }
    
    /**
     * Get save product action url
     *
     * @return string
     */
    public function getPostActionUrl() {
        return $this->getUrl ( 'marketplace/product/savedata' );
    }
    
    /**
     * Get Default Attribute Set Id
     *
     * @return int
     */
    public function getDefaultAttributeSetId() {
        return $this->product->getDefaultAttributeSetId ();
    }
    
    /**
     * Get Attribute set datas
     *
     * @return array
     */
    public function getAttributeSet() {
        return $this->attributeSet->toOptionArray ();
    }
    
    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted            
     * @param bool $asCollection            
     * @param bool $toLoad            
     *
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Helper\Category' )->getStoreCategories ( 'name', $asCollection, $toLoad );
    }
    
    /**
     * Sort the categories in alphabatical order
     *
     * @return Object
     */
    public function alphabaticalOrder($categories, $catChecked) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $categoryName = array ();
        foreach ( $categories as $category ) {
            if (! $category->getIsActive ()) {
                continue;
            }
            /**
             * Get category id
             */
            $catagoryId = $category->getId ();
            
            if ($this->categoryFlatConfig->isFlatEnabled () && $category->getUseFlatResource ()) {
                ( array ) $category->getChildrenNodes ();
            } else {
                $category->getChildren ();
            }
            
            /**
             * Checking for have children category or not
             */
            if ($category->hasChildren ()) {
                $catagoryId = $category->getId () . 'sub';
            }
            $categoryName [$catagoryId] = $category->getName ();
        }
        /**
         * Sort category name
         */
        asort ( $categoryName );
        return $objectManager->get ( 'Apptha\Marketplace\Helper\Data' )->showCategoriesTree ( $categoryName, $catChecked );
    }
    
    /**
     * Get ajax category tree action url
     *
     * @return string
     */
    public function getCategoryTreeAjaxUrl() {
        return $this->getUrl ( 'marketplace/product/category' );
    }
    
    /**
     * Get ajax image upload action url
     *
     * @return string
     */
    public function getImageUploadAjaxUrl() {
        return $this->getUrl ( 'marketplace/product/imageupload' );
    }
    
    /**
     * Getting product data
     *
     * @param int $productId            
     *
     * @return object $productData
     */
    public function getProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
    }
    
    /**
     * Getting stock state object
     *
     * @param int $productId            
     *
     * @return object $stockData
     */
    public function getProductStockDataQty($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $productId, 'product_id' );
    }
    
    /**
     * Get media image url
     *
     * @return string $mediaImageUrl
     */
    public function getMediaImageUrl() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'catalog/product';
    }
    /**
     * Get product approval or not
     *
     * @return int
     */
    public function getProductApproval() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Helper\System' )->getProductApproval ();
    }
    
    /**
     * Get custom attributes
     *
     * @return int
     */
    public function geCustomAttributes() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Helper\System' )->geCustomAttributes ();
    }
    
    /**
     * Get product types
     *
     * @return int
     */
    public function getProductTypes() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Helper\System' )->getProductTypes ();
    }
    
    /**
     * Get product custom options enabled or not
     *
     * @return int
     */
    public function getProductCustomOptions() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Helper\System' )->getProductCustomOptions ();
    }
    
    /**
     * Get ajax url for sku validation
     *
     * @return string
     */
    public function getSkuValidateAjaxUrl() {
        return $this->getUrl ( 'marketplace/product/skuvalidate' );
    }
    
    /**
     * Checking whether seller store shipping enabled or not
     *
     * @return boolean
     */
    public function isSellerProductShipping() {
        $isSellerProductShipping = 0;
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $isSellerShippingType = $objectModelManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        $isSellerShippingEnabled = $objectModelManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if ($isSellerShippingEnabled == 1 && $isSellerShippingType == 'product') {
            $isSellerProductShipping = 1;
        }
        return $isSellerProductShipping;
    }
    
    /**
     * Get custom attributes ajax url
     *
     * @return string
     */
    public function getCustomAttributesUrl() {
        return $this->getUrl ( 'marketplace/product/attributes' );
    }
}
