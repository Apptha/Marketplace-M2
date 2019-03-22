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
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Zend\Form\Annotation\Object;

/**
 * This class used to display the products collection
 */
class Displayseller extends \Magento\Directory\Block\Data {
    /**
     * Prepare display seller layout
     *
     * @return Object
     */
    public function _prepareLayout() {
        $customerId = $this->getRequest ()->getParam ( 'id' );
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerDatas = $objectModelManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerDetails = $sellerDatas->load ( $customerId, 'customer_id' );
        $sellerStoreName = $sellerDetails->getStoreName ();
        $this->pageConfig->getTitle ()->set ( __ ( ucfirst ( $sellerStoreName ) ) );
        return parent::_prepareLayout ();
    }
    
    /**
     * Function to Get Seller State and Address
     *
     * @return array
     */
    public function getAddress() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerId = $this->getRequest ()->getParam ( 'id' );
        $sellerDatas = $objectModelManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerDetails = $sellerDatas->load ( $customerId, 'customer_id' );
        $sellerState = $sellerDetails->getState ();
        $sellerCountry = trim ( $sellerDetails->getCountry () );
        return array (
                'state' => $sellerState,
                'country' => $sellerCountry 
        );
    }
    /**
     * Function to get Contact Details of seller
     *
     * @return array
     */
    public function getSellerDetails() {
        $customerName = $customerEmail = '';
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerModel = $objectManager->get ( 'Magento\Customer\Model\Customer' );
        $sellerDetails = $sellerModel->load ( $sellerId );
        $sellerName = $sellerDetails->getFirstname ();
        $sellerEmail = $sellerDetails->getEmail ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        if ($customerSession->isLoggedIn ()) {
            $customerName = $customerSession->getCustomer ()->getName ();
            $customerEmail = $customerSession->getCustomer ()->getEmail ();
        }
        return array (
                'seller_name' => $sellerName,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'seller_id' => $sellerId,
                'seller_email' => $sellerEmail 
        );
    }
    
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product            
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $url = $this->getAddToCartUrl ( $product );
        return [ 
                'action' => $url,
                'data' => [ 
                        'product' => $product->getEntityId (),
                        \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $objectModelManager->get ( 'Magento\Framework\Url\Helper\Data' )->getEncodedUrl ( $url ) 
                ] 
        ];
    }
    
    /**
     * Retrieve add to wishlist params
     *
     * @param \Magento\Catalog\Model\Product $product            
     *
     * @return string
     */
    public function getAddToWishlistParams($product) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectModelManager->get ( 'Magento\Wishlist\Helper\Data' )->getAddParams ( $product );
    }
    
    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     *
     * @param \Magento\Catalog\Model\Product $product            
     * @param array $additional            
     *
     * @return string
     */
    public function getAddToCartUrl($product, $additional = []) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        if ($product->getTypeInstance ()->hasRequiredOptions ( $product )) {
            if (! isset ( $additional ['_escape'] )) {
                $additional ['_escape'] = true;
            }
            if (! isset ( $additional ['_query'] )) {
                $additional ['_query'] = [ ];
            }
            $additional ['_query'] ['options'] = 'cart';
            
            return $this->getProductUrl ( $product, $additional );
        }
        return $objectModelManager->get ( 'Magento\Checkout\Helper\Cart' )->getAddUrl ( $product, $additional );
    }
    
    /**
     * Get product url
     *
     * @param Object $product            
     * @param array $additional            
     *
     * @return string
     */
    public function getProductUrl($product, $additional = []) {
        if ($this->hasProductUrl ( $product )) {
            if (! isset ( $additional ['_escape'] )) {
                $additional ['_escape'] = true;
            }
            return $product->getUrlModel ()->getUrl ( $product, $additional );
        }
        
        return '#';
    }
    
    /**
     * Check whether product url or not
     *
     * @param Object $product            
     *
     * @return bool
     */
    public function hasProductUrl($product) {
        if ($product->getVisibleInSiteVisibilities ()) {
            return true;
        }
        if ($product->hasUrlDataObject () && in_array ( $product->hasUrlDataObject ()->getVisibility (), $product->getVisibleInSiteVisibilities () )) {
            return true;
        }
        return false;
    }
    
    /**
     * Get rating data
     *
     * @param int $sellerId            
     *
     * @return array
     */
    public function getRatingsData($sellerId) {
        $ratings = $count = array ();
        
        $oneRating = $twoRating = $threeRating = $fourRating = $fiveRating = $overall = 0;
        
        /**
         * Get review collection
         */
        $reviews = $this->getReviewcount ( $sellerId );
        
        foreach ( $reviews as $review ) {
            $overall = $overall + 1;
            switch ($review->getRating ()) {
                case 1 :
                    $oneRating = $oneRating + 1;
                    break;
                case 2 :
                    $twoRating = $twoRating + 1;
                    break;
                case 3 :
                    $threeRating = $threeRating + 1;
                    break;
                case 4 :
                    $fourRating = $fourRating + 1;
                    break;
                default :
                    $fiveRating = $fiveRating + 1;
            }
        }
        
        /**
         * Check advanced total is greater than or equal to 1
         */
        if ($overall != 0) {
            if ($oneRating > 0) {
                $ratings ['one'] = ($oneRating / $overall) * 100;
            }
            if ($twoRating > 0) {
                $ratings ['two'] = ($twoRating / $overall) * 100;
            }
            if ($threeRating > 0) {
                $ratings ['three'] = ($threeRating / $overall) * 100;
            }
            if ($fourRating > 0) {
                $ratings ['four'] = ($fourRating / $overall) * 100;
            }
            if ($fiveRating > 0) {
                $ratings ['five'] = ($fiveRating / $overall) * 100;
            }
            
            $count ['one'] = $oneRating;
            $count ['two'] = $twoRating;
            $count ['three'] = $threeRating;
            $count ['four'] = $fourRating;
            $count ['five'] = $fiveRating;
        }
        return array (
                'percent' => $ratings,
                'count' => $count 
        );
    }
    
    /**
     * Get review data
     *
     * @param int $sellerId            
     *
     * @return object
     */
    public function getReviewcount($sellerId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $reviews = $objectManager->get ( 'Apptha\Marketplace\Model\Review' )->getCollection ();
        $reviews->addFieldToSelect ( '*' );
        $reviews->addFieldToFilter ( 'seller_id', $sellerId );
        $reviews->addFieldToFilter ( 'status', 1 );
        return $reviews;
    }
    
    /**
     * Get review url
     */
    public function getReviewUrl($sellerId) {
        return $this->getUrl ( 'marketplace/seller/review/seller_id/' . $sellerId );
    }
    
    /**
     * Check whether customer logged in or not
     */
    public function getLoggedInCustomerId() {
        $customerId = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
        }
        return $customerId;
    }
    
    /**
     * Get login url
     *
     * @return string
     */
    public function getLoginUrl() {
        return $this->getUrl ( 'customer/account/login' );
    }
    
    /**
     * Get write review url
     *
     * @return string
     */
    public function getWriteReviewUrl($sellerId) {
        return $this->getUrl ( 'marketplace/seller/review/seller_id/' . $sellerId . '/write/1' );
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
        return $objectManager->get ( 'Apptha\Marketplace\Block\Seller\Review' )->canReview ( $customerId, $sellerId );
    }
    /**
     * Check whether is seller review enable or not
     *
     * @return bool
     */
    public function isSellerReviewEnabled() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerReview = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        return $sellerReview->isSellerReviewEnabled ();
    }
    /**
     * 
     * Function to get product price
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product) {
        $priceRender = $this->getPriceRender ();
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render ( \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                    $product,
                    [
                            'include_container' => true,
                            'display_minimal_price' => true,
                            'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                    ]
                    );
        }
    
        return $price;
    }
    
    /**
     * 
     * Function for price render
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender() {
        return $this->getLayout()->getBlock('product.price.render.default');
    }
}