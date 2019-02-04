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

use Magento\Framework\View\Element\Template;

/**
 * This class used to display the seller review collection / form
 */
class Review extends \Magento\Framework\View\Element\Template {
    
    /**
     *
     * @param Template\Context $context            
     * @param ProductFactory $productFactory            
     * @param array $data            
     */
    public function __construct(Template\Context $context, \Magento\Framework\Message\ManagerInterface $messageManager, array $data = []) {
        $this->messageManager = $messageManager;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Prepare layout for seller review
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( "Seller Review" ) );
        parent::_prepareLayout ();
        $pagerContent = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.seller.review.pager' );
        $pagerContent->setLimit ( 10 )->setShowAmounts ( false )->setCollection ( $this->getCollection () );
        $this->setChild ( 'pager', $pagerContent );
        $this->getCollection ()->load ();
        return $this;
    }
    
    /**
     * Set seller review collection
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $sellerId = $this->getSellerId ();
        $collection = $this->getSellerReview ( $sellerId );
        $this->setCollection ( $collection );
    }
    
    /**
     * Get seller review pager html
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Get seller details
     *
     * @return object $sellerData
     */
    public function getStoreDetails() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerId = $this->getSellerId ();
        return $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $sellerId, 'customer_id' );
    }
    
    /**
     * Get store name
     *
     * @return string $storeName
     */
    public function getStoreName() {
        $storeName = $this->getStoreDetails ()->getStoreName ();
        if (empty ( $storeName )) {
            $sellerId = $this->getSellerId ();
            $storeName = $this->getCustomerName ( $sellerId );
        }
        return $storeName;
    }
    
    /**
     * Get customer name by customer id
     *
     * @return string
     */
    public function getCustomerName($customerId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $customerId )->getFirstname ();
    }
    
    /**
     * Get seller id
     *
     * @return int $sellerId
     */
    public function getSellerId() {
        $sellerId = $this->getRequest ()->getParam ( 'seller_id' );
        if (empty ( $sellerId )) {
            $sellerId = $this->getLoggedInCustomerId ();
        }
        return $sellerId;
    }
    
    /**
     * Get logged in customer id
     *
     * @return int
     */
    public function getLoggedInCustomerId() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        return $customerSession->getId ();
    }
    
    /**
     * Get seller review data
     *
     * @return Object
     */
    public function getSellerReview() {
        $sellerId = $this->getSellerId ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $reviews = $objectManager->get ( 'Apptha\Marketplace\Model\Review' )->getCollection ();
        $reviews->addFieldToSelect ( '*' );
        $reviews->addFieldToFilter ( 'seller_id', $sellerId );
        $reviews->addFieldToFilter ( 'status', 1 );
        return $reviews;
    }
    
    /**
     * Check whether customer can review or not
     *
     * @param int $customerId            
     * @param int $sellerId            
     *
     * @return bool
     */
    public function canReview($customerId, $sellerId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $purchaseSellerOrderCount = $this->getCustomerPurchasedOrderCount ( $customerId, $sellerId );
        
        if ($purchaseSellerOrderCount >= 1) {
            $lastMonth = date ( 'Y-m-d', strtotime ( "-4 week" ) );
            $sellerReviewCollection = $objectManager->get ( 'Apptha\Marketplace\Model\Review' )->getCollection ();
            $sellerReviewCollection->addFieldToFilter ( 'customer_id', array (
                    'eq' => $customerId 
            ) );
            $sellerReviewCollection->addFieldToFilter ( 'created_at', array (
                    'from' => $lastMonth 
            ) );
            $sellerReviewCollection->addFieldToFilter ( 'seller_id', array (
                    'eq' => $sellerId 
            ) );
            $reviewedCount = count ( $sellerReviewCollection );
            if ($purchaseSellerOrderCount > $reviewedCount) {
                return 1;
            }
        }
        return 0;
    }
    
    /**
     * Get seller product ids
     *
     * @return array
     */
    public function getSellerProductIds($sellerId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $products = $objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ();
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        return array_unique ( $products->getColumnValues ( 'entity_id' ) );
    }
    
    /**
     * Get customer purchased product ids
     *
     * @param int $customerId            
     *
     * @return array $productIds
     */
    public function getCustomerPurchasedOrderCount($customerId, $sellerId) {
        $purchaseSellerOrderCount = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $lastMonth = date ( 'Y-m-d', strtotime ( "-4 week" ) );
        $orderCollection = $objectManager->get ( 'Magento\Sales\Model\Order' )->getCollection ();
        $orderCollection->addFieldToFilter ( 'customer_id', array (
                'eq' => $customerId 
        ) );
        $orderCollection->addFieldToFilter ( 'status', array (
                'eq' => \Magento\Sales\Model\Order::STATE_COMPLETE 
        ) );
        $orderCollection->addFieldToFilter ( 'created_at', array (
                'from' => $lastMonth 
        ) );
        $sellerProductIds = $this->getSellerProductIds ( $sellerId );
        foreach ( $orderCollection as $order ) {
            foreach ( $order->getAllItems () as $item ) {
                if (in_array ( $item->getProductId (), $sellerProductIds )) {
                    $purchaseSellerOrderCount = $purchaseSellerOrderCount + 1;
                    break;
                }
            }
        }
        return $purchaseSellerOrderCount;
    }
    /**
     * Get Product data
     *
     * @param int $productId            
     *
     * @return object
     */
    public function getProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
    }
    
    /**
     * Get url for seller review save
     *
     * @return string
     */
    public function saveReviewUrl() {
        return $this->getUrl ( 'marketplace/seller/savereview' );
    }
}