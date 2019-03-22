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
 * Customer login form block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Login extends \Magento\Framework\View\Element\Template {
    /**
     *
     * @var int
     */
    private $_username = - 1;
    
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context            
     * @param \Magento\Customer\Model\Session $customerSession            
     * @param \Magento\Customer\Model\Url $customerUrl            
     * @param array $data            
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Apptha\Marketplace\Model\Url $customerUrl, array $data = []) {
        parent::__construct ( $context, $data );
        $this->_isScopePrivate = false;
        $this->_customerUrl = $customerUrl;
        $this->_customerSession = $customerSession;
    }
    
    /**
     *
     * @return $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( 'Seller Login' ) );
        return parent::_prepareLayout ();
    }
    
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl() {
        return $this->_customerUrl->getLoginPostUrl ();
    }
    
    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl() {
        return $this->_customerUrl->getForgotPasswordUrl ();
    }
    
    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername() {
        if (- 1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername ( true );
        }
        return $this->_username;
    }
    
    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled() {
        return ( bool ) ! $this->_scopeConfig->getValue ( \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }
}