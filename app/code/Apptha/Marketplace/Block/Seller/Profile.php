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
 * This class used to display the products collection
 */
class Profile extends \Magento\Directory\Block\Data {
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Directory\Helper\Data $directoryHelperObject, \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionFactory, \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryFactory, \Magento\Framework\Json\EncoderInterface $encoder, \Magento\Framework\App\Cache\Type\Config $configCacheType, array $data = []) {
        parent::__construct ( $context, $directoryHelperObject, $encoder, $configCacheType, $regionFactory, $countryFactory, $data );
    }
    /**
     * Prepare layout for seller profile
     *
     * @see \Magento\Framework\View\Element\AbstractBlock::_prepareLayout()
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( "My Store" ) );
        return parent::_prepareLayout ();
    }
    /**
     * Seller profile
     *
     * @return array
     */
    public function getSellerProfile() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $countryData = $objectModelManager->get ( 'Magento\Directory\Model\Country' );
        $countryDatasCollection = $countryData->getCollection ();
        $countryData = $countryDatasCollection->getData ();
        $customerId='';
        $customerSession = $objectModelManager->get ( 'Magento\Customer\Model\Session' );
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
        }
        /**
         * Get seller details
         */
        $sellerDatas = $objectModelManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $sellerDetails = $sellerDatas->load ( $customerId, 'customer_id' );
        $logoImage = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Marketplace/Seller/Resized';
        $bannerImage = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Marketplace/Sellerbanner/Resized';
        return array (
                'seller_details' => $sellerDetails,
                'country_list' => $countryData,
                'logo_image' => $logoImage,
                'banner_image' => $bannerImage 
        );
    }
    
    /**
     * Checking whether seller store shipping enabled or not
     *
     * @return boolean
     */
    public function isSellerStoreShipping() {
        $isSellerStoreShipping = 0;
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $isSellerShippingEnabled = $objectModelManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        $isSellerShippingType = $objectModelManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if ($isSellerShippingEnabled == 1 && $isSellerShippingType == 'store') {
            $isSellerStoreShipping = 1;
        }
        return $isSellerStoreShipping;
    }
    
    /**
     * Get base currency code value
     *
     * @return string
     */
    public function getBaseCurrencyCode() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Apptha\Marketplace\Block\Product\Add' )->getBaseCurrency ();
    }
}