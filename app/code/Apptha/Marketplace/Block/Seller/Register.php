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

use Magento\Customer\Model\AccountManagement;

/**
 * Seller register form block
 */
class Register extends \Magento\Directory\Block\Data {


/**
 * Configuration path to customer password required character classes number
 */
const XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER = 'customer/password/required_character_classes_number';

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param array $data
     *            @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Directory\Helper\Data $directoryHelper, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\App\Cache\Type\Config $configCacheType, \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory, \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory, array $data = []) {
        parent::__construct ( $context, $directoryHelper, $jsonEncoder, $configCacheType, $regionCollectionFactory, $countryCollectionFactory, $data );
        $this->_isScopePrivate = false;
    }

    /**
     * Get config
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path) {
        return $this->_scopeConfig->getValue ( $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }

    /**
     * Prepare layout for register seller
     *
     * @return $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( 'Create New Seller Account' ) );
        return parent::_prepareLayout ();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectModelManager->get ( 'Apptha\Marketplace\Model\Url' )->getRegisterPostUrl ();
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $url = $this->getData ( 'back_url' );
        if ($url === null) {
            $url = $objectModelManager->get ( 'Apptha\Marketplace\Model\Url' )->getLoginUrl ();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $data = $this->getData ( 'form_data' );
        if ($data === null) {
            $formData = $objectModelManager->get ( 'Magento\Customer\Model\Session' )->getCustomerFormData ( true );
            $data = new \Magento\Framework\DataObject ();
            if ($formData) {
                $data->addData ( $formData );
                $data->setCustomerData ( 1 );
            }
            if (isset ( $data ['region_id'] )) {
                $data ['region_id'] = ( int ) $data ['region_id'];
            }
            $this->setData ( 'form_data', $data );
        }
        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId() {
        $countryId = $this->getFormData ()->getCountryId ();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId ();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return mixed
     */
    public function getRegion() {
        if (null !== ($region = $this->getFormData ()->getRegion ())) {
            return $region;
        } else{
        $region = $this->getFormData ()->getRegionId ();
        if (null !== ($region)){
        return $region;
        }
        }
        return null;
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectModelManager->get ( 'Magento\Framework\Module\Manager' )->isOutputEnabled ( 'Magento_Newsletter' );
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param \Magento\Customer\Model\Metadata\Form $form
     * @param string|null $scope
     *
     * @return $this
     */
    public function restoreSessionData(\Magento\Customer\Model\Metadata\Form $form, $scope = null) {
        if ($this->getFormData ()->getCustomerData ()) {
            $request = $form->prepareRequest ( $this->getFormData ()->getData () );
            $data = $form->extractData ( $request, $scope, false );
            $form->restoreData ( $data );
        }

        return $this;
    }
    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
    return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }
}