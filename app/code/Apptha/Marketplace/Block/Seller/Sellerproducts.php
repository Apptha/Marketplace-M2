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
namespace Apptha\Marketplace\Block\Seller;

/**
 * This class used to display the products collection
 */
class Sellerproducts extends \Magento\Directory\Block\Data {
    /**
     * Prepare display seller layout
     *
     * @return Object
     */
    public function _prepareLayout() {
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.allproducts.pager' );
        $pager->setLimit ( 12 )->setShowAmounts ( false )->setCollection ( $this->getAllProducts () );
        $this->setChild ( 'pager', $pager );
        return parent::_prepareLayout ();
    }
    /**
     * display seller construct
     *
     * @return void
     */
    public function getAllProducts() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerId = $this->getRequest ()->getParam ( 'id' );
        
        // get values of current page
        $page = ($this->getRequest ()->getParam ( 'p' )) ? $this->getRequest ()->getParam ( 'p' ) : 1;
        // get values of current limit
        $pageSize = ($this->getRequest ()->getParam ( 'limit' )) ? $this->getRequest ()->getParam ( 'limit' ) : 12;
        
        return $objectModelManager->get ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $customerId )->addAttributeToFilter ( 'product_approval', 1 )->addAttributeToFilter ( 'status', 1 )->addAttributeToFilter ( 'visibility', array (
                'eq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH 
        ) )->setPageSize ( $pageSize )->setCurPage ( $page );
    }
    
    /**
     * Function for add pagination
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product            
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $url = $objectModelManager->get ( 'Magento\Checkout\Helper\Cart' )->getAddUrl ( $product );
        return [ 
                'action' => $url,
                'data' => [ 
                        'product' => $product->getEntityId (),
                        \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $objectModelManager->get ( 'Magento\Framework\Url\Helper\Data' )->getEncodedUrl ( $url ) 
                ]
                 
        ];
    }
    
    /**
     * Function for get vacation details
     */
    public function getVacationDetails() {
        $customerId = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $date =  (new \DateTime())->format('Y-m-d');
        $vacationCollection = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->getCollection();
        return $vacationCollection
        ->addFieldToFilter('seller_id', ['eq' => $customerId])
        ->addFieldToFilter('vacation_status','0')
        ->addFieldToFilter('from_date', ['lteq' => $date])
        ->addFieldToFilter('to_date', ['gteq' => $date])
        ->getFirstItem();
    }
}