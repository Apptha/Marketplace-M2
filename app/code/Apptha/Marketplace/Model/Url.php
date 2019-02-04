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
namespace Apptha\Marketplace\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Customer url model
 */
class Url {
    /**
     * Route for customer account login page
     */
    const ROUTE_ACCOUNT_LOGIN = 'marketplace/seller/login';
    
    /**
     * Config name for Redirect Customer to Account Dashboard after Logging in setting
     */
    const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'customer/startup/redirect_dashboard';
    
    /**
     * Query param name for last url visited
     */
    const REFERER_QUERY_PARAM_NAME = 'referer';
    
    /**
     *
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     *
     * @var RequestInterface
     */
    protected $request;
    
    /**
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     *
     * @var Session
     */
    protected $customerSession;
    
    /**
     *
     * @var EncoderInterface
     */
    protected $urlEncoder;
    
    /**
     *
     * @param Session $customerSession            
     * @param ScopeConfigInterface $scopeConfig            
     * @param RequestInterface $request            
     * @param UrlInterface $urlBuilder            
     * @param EncoderInterface $urlEncoder            
     */
    public function __construct(Session $customerSession, ScopeConfigInterface $scopeConfig, RequestInterface $request, UrlInterface $urlBuilder, EncoderInterface $urlEncoder) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->urlEncoder = $urlEncoder;
    }
    
    /**
     * Retrieve customer login url
     *
     * @return string
     */
    public function getLoginUrl() {
        /**
         * Return login url
         */
        return $this->urlBuilder->getUrl ( static::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams () );
    }
    
    /**
     * Retrieve parameters of customer login url
     *
     * @return array
     */
    public function getLoginUrlParams() {
        $params = [ ];
        $referer = $this->request->getParam ( static::REFERER_QUERY_PARAM_NAME );
        if (! $referer && ! $this->scopeConfig->isSetFlag ( static::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD, ScopeInterface::SCOPE_STORE ) && ! $this->customerSession->getNoReferer ()) {
            $referer = $this->urlBuilder->getUrl ( '*/*/*', [ 
                    '_current' => true,
                    '_use_rewrite' => true 
            ] );
            $referer = $this->urlEncoder->encode ( $referer );
        }
        
        if ($referer) {
            $params = [ 
                    static::REFERER_QUERY_PARAM_NAME => $referer 
            ];
        }
        
        return $params;
    }
    
    /**
     * Retrieve customer login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl() {
        /**
         * Declare params
         */
        $params = [ ];
        /**
         * Assing params
         */
        if ($this->request->getParam ( static::REFERER_QUERY_PARAM_NAME )) {
            $params = [ 
                    static::REFERER_QUERY_PARAM_NAME => $this->request->getParam ( static::REFERER_QUERY_PARAM_NAME ) 
            ];
        }
        /**
         * Return seller login post url
         */
        return $this->urlBuilder->getUrl ( 'marketplace/seller/loginPost', $params );
    }
    
    /**
     * Retrieve customer logout url
     *
     * @return string
     */
    public function getLogoutUrl() {
        /**
         * Return account logout url
         */
        return $this->urlBuilder->getUrl ( 'customer/account/logout' );
    }
    
    /**
     * Retrieve customer dashboard url
     *
     * @return string
     */
    public function getDashboardUrl() {
        /**
         * Return seller dashboard url
         */
        return $this->urlBuilder->getUrl ( 'marketplace/seller/dashboard' );
    }
    
    /**
     * Retrieve customer account page url
     *
     * @return string
     */
    public function getAccountUrl() {
        /**
         * Return seller dashboard url
         */
        return $this->urlBuilder->getUrl ( 'marketplace/seller/dashboard' );
    }
    
    /**
     * Retrieve customer register form url
     *
     * @return string
     */
    public function getRegisterUrl() {
        /**
         * Return seller create url
         */
        return $this->urlBuilder->getUrl ( 'marketplace/seller/create' );
    }
    
    /**
     * Retrieve customer register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl() {
        /**
         * Return seller create post url
         */
        return $this->urlBuilder->getUrl ( 'marketplace/seller/createpost' );
    }
    
    /**
     * Retrieve customer account edit form url
     *
     * @return string
     */
    public function getEditUrl() {
        /**
         * Return account edit url
         */
        return $this->urlBuilder->getUrl ( 'customer/account/edit' );
    }
    
    /**
     * Retrieve customer edit POST URL
     *
     * @return string
     */
    public function getEditPostUrl() {
        /**
         * To get edit post url
         */
        return $this->urlBuilder->getUrl ( 'customer/account/editpost' );
    }
    
    /**
     * Retrieve url of forgot password page
     *
     * @return string
     */
    public function getForgotPasswordUrl() {
        /**
         * Get forgot password url
         */
        return $this->urlBuilder->getUrl ( 'customer/account/forgotpassword' );
    }
    
    /**
     * Retrieve confirmation URL for Email
     *
     * @param string $email            
     * @return string
     */
    public function getEmailConfirmationUrl($email = null) {
        /**
         * Get account confirmation notification url
         */
        return $this->urlBuilder->getUrl ( 'customer/account/confirmation', [ 
                'email' => $email 
        ] );
    }
}