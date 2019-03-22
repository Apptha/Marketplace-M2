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
namespace Apptha\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains manipulation functions
 */
class Data extends AbstractHelper {
    const XML_PATH_TITLE = 'marketplace/general/title';
    const XML_SELLER_APPROVAL = 'marketplace/seller/seller_approval';
    const XML_CONTACT_ADMIN = 'marketplace/seller/contact_admin';
    const XML_SELLER_PROFILE = 'marketplace/seller/seller_link';
    const XML_ADMIN_EMAILS = 'trans_email/ident_general/email';
    const XML_ADMIN_NAME = 'trans_email/ident_general/name';
    const XML_MODULE_ENABLE = 'marketplace/general/enable_in_frontend';
    const XML_ASSIGN_PRODUCT = 'marketplace/product/assign_product';
    const XML_SELLER_REVIEW = 'marketplace/review/active';
    const XML_SUBSCRIPTION_REVIEW = 'marketplace/subscription/active';
    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $productFactory;
    protected $conficAttributeData;
    
    /**
     *
     * @param Context $context            
     * @param ScopeConfigInterface $scopeConfig            
     */
    public function __construct(Context $context,\Magento\Store\Model\StoreManagerInterface $storeManager, CategoryRepositoryInterface $categoryRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\ConfigurableProduct\Helper\Data $conficAttributeData) {
        parent::__construct ( $context );
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->productFactory = $productFactory;
        $this->conficAttributeData = $conficAttributeData;
    }
    /**
     * Get head title for Marketplace
     *
     * @return string
     */
    public function getHeadTitle() {
        return $this->scopeConfig->getValue ( static::XML_PATH_TITLE, ScopeInterface::SCOPE_STORE );
    }
    /**
     * Get Enable/disable Marketplace
     *
     * @return string
     */
    public function getModuleEnable() {
        return $this->scopeConfig->getValue ( static::XML_MODULE_ENABLE, ScopeInterface::SCOPE_STORE );
    }
    /**
     * Get Seller Approval from backend
     *
     * @return string
     */
    public function getSellerApproval() {
        return $this->scopeConfig->getValue ( static::XML_SELLER_APPROVAL, ScopeInterface::SCOPE_STORE );
    }
    /**
     * Function to enable Assign Product
     *
     * @return string
     */
    public function getAssignProduct() {
        return $this->scopeConfig->getValue ( static::XML_ASSIGN_PRODUCT, ScopeInterface::SCOPE_STORE );
    }
    /**
     * Function to enable Contact Admin
     *
     * @param unknown $categoryName            
     * @param unknown $catChecked            
     * @return string
     */
    public function getContactAdmin() {
        return $this->scopeConfig->getValue ( static::XML_CONTACT_ADMIN, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Function to enable seller store in produt page
     *
     * @param unknown $categoryName            
     * @param unknown $catChecked            
     * @return string
     */
    public function getSellerProfile() {
        return $this->scopeConfig->getValue ( static::XML_SELLER_PROFILE, ScopeInterface::SCOPE_STORE );
    }
    /**
     * Function to get admin general email
     *
     * @param unknown $categoryName            
     * @param unknown $catChecked            
     * @return string
     */
    public function getAdminEmail() {
        return $this->scopeConfig->getValue ( static::XML_ADMIN_EMAILS, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Function to get admin general name
     *
     * @param unknown $categoryName            
     * @param unknown $catChecked            
     * @return string
     */
    public function getAdminName() {
        return $this->scopeConfig->getValue ( static::XML_ADMIN_NAME, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Getting store categories list
     * Passed category information as array
     * @param array $categories
     * @return array
     */
    public function showCategoriesTree($categoryName, $catIds) {
        $array = '<ul class="category_ul">';
        foreach ( $categoryName as $key => $catname ) {
            $catagory = $this->categoryRepository->get ( $key, $this->storeManager->getStore ()->getId () );
            $count = $catagory->getProductCount ();
            $catChecked = $this->checkSelectedCategory ( str_replace ( 'sub', '', $key ), $catIds );
            if (strstr ( $key, 'sub' )) {
                $key = str_replace ( 'sub', '', $key );
                $array .= '<li class="level-top  parent" id="' . $key . '"><a href="javascript:void(0);"><span class="end-plus" id="' . $key . '_span"></span></a><span class="last-collapse"><input id="cat' . $key . '" type="checkbox" name="category_ids[]" ' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $key . '" type="checkbox" name="category_ids[]"  ' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label>';
            }
        }
        $array .= '</li>';
        return $array . '</ul>';
    }
    
    /**
     * Function to get the selected category
     *
     * @param array $key            
     * @param array $categoryId            
     * @return array
     */
    public function checkSelectedCategory($key, $categoryid) {
        $catChecked = '';
        if (in_array ( $key, $categoryid )) {
            $catChecked = 'checked';
        }
        return $catChecked;
    }
    
    /**
     * Check is seller or not
     *
     * @return boolean
     */
    public function isSeller() {
        $response = array (
                'is_seller' => 1,
                'msg' => '',
                'redirect' => '' 
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        if (! $customerSession->isLoggedIn ()) {
            $response = array (
                    'is_seller' => 0,
                    'msg' => 'You must have a Seller Account to access this page',
                    'redirect' => 'marketplace/seller/login' 
            );
        } else {
            $customerGroupSession = $objectManager->get ( 'Magento\Customer\Model\Group' );
            $customerGroupData = $customerGroupSession->load ( 'Marketplace Seller', 'customer_group_code' );
            $sellerGroupId = $customerGroupData->getId ();
            
            $currentCustomerGroupId = $customerSession->getCustomerGroupId ();
            
            if ($currentCustomerGroupId != $sellerGroupId) {
                $response = array (
                        'is_seller' => 0,
                        'msg' => 'Admin Approval is required. Please wait until admin confirms your Seller Account',
                        'redirect' => 'marketplace/seller/changebuyer' 
                );
            }
        }
        return $response;
    }
    /**
     * Function to get seller store path
     *
     * @param unknown $productId            
     * @return array
     */
    public function getSellerRequestpath($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $sellerId = $product->getSellerId ();
        $sellerDatas = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerDetails = $sellerDatas->load ( $sellerId, 'customer_id' );
        $sellerStoreName = $sellerDetails->getStoreName ();
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = $objectManager->get ( 'Magento\UrlRewrite\Model\UrlRewrite' )->load ( $targetPath, 'target_path' );
        $requestPath = $mainUrlRewrite->getRequestPath ();
        return array (
                'request_path' => $requestPath,
                'store_name' => $sellerStoreName 
        );
    }
    /**
     * Get Formatted Price
     * 
     * @return price( (number)
     */
    public function getFormattedPrice($price) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $priceHelper = $objectManager->get ( 'Magento\Framework\Pricing\Helper\Data' );
        return $priceHelper->currency ( $price, true, false );
    }
    
    /**
     * Get seller review enabled or not
     *
     * @return bool
     */
    public function isSellerReviewEnabled() {
        return $this->scopeConfig->getValue ( static::XML_SELLER_REVIEW, ScopeInterface::SCOPE_STORE );
    }
    /**
     * Get seller subscription enabled or not
     *
     * @return bool
     */
    public function isSellerSubscriptionEnabled() {
        return $this->scopeConfig->getValue ( static::XML_SUBSCRIPTION_REVIEW, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Seller profile
     *
     * @return array
     */
    public function getSellerDetails() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerId='';
        $customerSession = $objectModelManager->get ( 'Magento\Customer\Model\Session' );
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
        }
        /**
         * Get seller details
         */
        $sellerDatas = $objectModelManager->get ( 'Apptha\Marketplace\Model\Seller' );
        return $sellerDatas->load ( $customerId, 'customer_id' );
    }
}