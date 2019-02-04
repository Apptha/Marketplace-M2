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

use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains loading category functions
 */
class Category extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    protected $dataHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager            
     * @param CategoryRepositoryInterface $categoryRepository            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, CategoryRepositoryInterface $categoryRepository, \Apptha\Marketplace\Helper\Data $dataHelper) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->dataHelper = $dataHelper;
    }
    
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
        
        /**
         * Get values from post data from ajax request for showing the sub level categories
         */
        $categoryId = trim ( $this->getRequest ()->getParam ( 'category_id' ) );
        $category = $this->categoryRepository->get ( $categoryId, $this->storeManager->getStore ()->getId () );
        $subcategories = $category->getChildrenCategories ();
        foreach ( $subcategories as $category ) {
            $catId = $category->getId ();
            /**
             * Condition to check for sub category
             */
            if ($category->hasChildren ()) {
                $catId = $category->getId () . 'sub';
            }
            $customerName [$catId] = $category->getName ();
        }
        /**
         * Sort in alphabatical order.
         */
        asort ( $customerName );
        
        /**
         * The decode selected category ids
         */
        $catChecked = json_decode ( trim ( $this->getRequest ()->getPost ( 'selectedCatIds' ) ) );
        
        /**
         * Show categories tree
         */
        echo $this->dataHelper->showCategoriesTree ( $customerName, $catChecked );
    }
}
