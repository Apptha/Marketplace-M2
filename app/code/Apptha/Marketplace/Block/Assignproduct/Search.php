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
namespace Apptha\Marketplace\Block\Assignproduct;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Instance;

/**
 * This class used to display the assign products collection
 */
class Search extends \Magento\Framework\View\Element\Template {
    
    /**
     * Initilize variable for product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    protected $systemHelper;
    protected $_storecurrency;
    /**
     * Initilize variable for stock registry
     *
     * @var Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;
    protected $messageManager;
    
    /**
     *
     * @param Template\Context $context            
     * @param ProductFactory $productFactory            
     * @param array $data            
     */
    public function __construct(Template\Context $context, Collection $productFactory, \Magento\Directory\Model\Currency $storecurrency, StockRegistry $stockRegistry, \Apptha\Marketplace\Helper\System $systemHelper, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
        $this->systemHelper = $systemHelper;
        $this->messageManager = $messageManager;
        $this->_storecurrency = $storecurrency;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $assignedProductscollection = $this->getAssignedProducts ();
        $this->setCollection ( $assignedProductscollection );
    }
    /**
     * Prepare layout for manage product
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( "Assign Products" ) );
        parent::_prepareLayout ();
        return $this;
    }
    /**
     * Get new product url
     *
     * @return string
     */
    public function getNewProductUrl() {
        return $this->getUrl ( 'marketplace/product/add' );
    }
    /**
     * Get Filter Result Url
     */
    public function getSearchResultUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/search' );
    }
    
    /**
     * Get Manage Assign product pager html
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Get Products from search word
     * 
     * @return object
     */
    public function getAssignedProducts() {
        $searchedProductName = $sellerId = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        $searchedProductName = $this->getRequest ()->getParam ( 'filter_name' );
        $products = $this->productFactory->addAttributeToSelect ( '*' );
        if (! empty ( $searchedProductName )) {
            /**
             * Filter by name
             */
            $products->addAttributeToFilter ( 'name', array (
                    array (
                            'like' => '%' . $searchedProductName . '%' 
                    ) 
            ) );
            /**
             * Filter by status
             */
            $products->addAttributeToFilter ( 'status', array (
                    'neq' => 2,
                    '' 
            ) );
            
            /**
             * Filter by status
             */
            $products->addAttributeToFilter ( 'visibility', array (
                    'neq' => 1,
                    '' 
            ) );
            /**
             * Filter by Product Approval
             */
            $products->addAttributeToFilter ( 'product_approval', array (
                    'neq' => 0 
            ) );
            /**
             * Filter by seller Id
             */
            $products->addAttributeToFilter ( 'seller_id', array (
                    'neq' => $sellerId 
            ) );
        } else {
            $products = '';
        }
        
        return $products;
    }
    
    /**
     * Get Assign Product Url
     * 
     * @return string
     */
    public function getAddAssignProductUrl() {
        return $this->getUrl ( 'marketplace/assignproduct/add' );
    }
    /**
     * Get store symbol
     * @retun void
     */
    public function getStoreCurrencySymbol() {
        return $this->_storecurrency->getCurrencySymbol ();
    }
    
    /**
     * Function to check already assigned or not
     * 
     * @return object
     */
    public function getAlreadyAssignedProducts($proId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customerSession->getId ();
        $productCollection = $objectManager->create ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' );
        /**
         * Apply filters here
         */
        $productCollection->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $sellerId )->addAttributeToFilter ( 'assign_product_id', $proId );
        return $productCollection;
    }
}
