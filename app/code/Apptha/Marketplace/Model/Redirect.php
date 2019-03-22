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

use Apptha\Marketplace\Model\Url as CustomerUrl;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Url\DecoderInterface;

/**
 * This class contains redirect url functions
 */
class Redirect {
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
     * @var DecoderInterface
     */
    protected $urlDecoder;
    
    /**
     *
     * @var CustomerUrl
     */
    protected $customerUrl;
    
    /**
     *
     * @var UrlInterface
     */
    protected $url;
    
    /**
     *
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    
    /**
     *
     * @param RequestInterface $request            
     * @param Session $customerSession            
     * @param ScopeConfigInterface $scopeConfig            
     * @param UrlInterface $url            
     * @param DecoderInterface $urlDecoder            
     * @param CustomerUrl $customerUrl            
     * @param RedirectFactory $resultRedirectFactory            
     */
    public function __construct(RequestInterface $request, Session $customerSession, ScopeConfigInterface $scopeConfig, UrlInterface $url, DecoderInterface $urlDecoder, CustomerUrl $customerUrl, RedirectFactory $resultRedirectFactory) {
        $this->request = $request;
        $this->session = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->urlDecoder = $urlDecoder;
        $this->customerUrl = $customerUrl;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }
    
    /**
     * Retrieve redirect
     *
     * @return ResultRedirect
     */
    public function getRedirect() {
        $this->updateLastCustomerId ();
        $this->prepareRedirectUrl ();
        
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create ();
        $resultRedirect->setUrl ( $this->session->getBeforeAuthUrl ( true ) );
        return $resultRedirect;
    }
    /**
     * Function to get Seller Redirect
     * 
     * @return object
     */
    public function getSellerRedirect() {
        $this->prepareSellerRedirectUrl ();
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create ();
        $resultRedirect->setUrl ( $this->session->getBeforeAuthUrl ( true ) );
        return $resultRedirect;
    }
    
    /**
     * Update last customer id, if required
     *
     * @return void
     */
    protected function updateLastCustomerId() {
        $lastCustomerId = $this->session->getLastCustomerId ();
        if (isset ( $lastCustomerId ) && $this->session->isLoggedIn () && $lastCustomerId != $this->session->getId ()) {
            $this->session->unsBeforeAuthUrl ()->setLastCustomerId ( $this->session->getId () );
        }
    }
    
    /**
     * Prepare redirect URL
     *
     * @return void
     */
    protected function prepareRedirectUrl() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $baseUrl = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ();
        
        $url = $this->session->getBeforeAuthUrl ();
        if (! $url) {
            $url = $baseUrl;
        }
        
        switch ($url) {
            case $baseUrl :
                if ($this->session->isLoggedIn ()) {
                    $this->processLoggedCustomer ();
                } else {
                    $this->applyRedirect ( $this->customerUrl->getLoginUrl () );
                }
                break;
            
            case $this->customerUrl->getLogoutUrl () :
                $this->applyRedirect ( $this->customerUrl->getDashboardUrl () );
                break;
            
            default :
                if (! $this->session->getAfterAuthUrl ()) {
                    $this->session->setAfterAuthUrl ( $this->session->getBeforeAuthUrl () );
                }
                if ($this->session->isLoggedIn ()) {
                    $this->applyRedirect ( $this->session->getAfterAuthUrl ( true ) );
                }
                break;
        }
    }
    protected function prepareSellerRedirectUrl() {
        $this->applyRedirect ( $this->customerUrl->getLoginUrl () );
    }
    
    /**
     * Prepare redirect URL for logged in customer
     *
     * Redirect customer to the last page visited after logging in.
     *
     * @return void
     */
    protected function processLoggedCustomer() {
        // Set default redirect URL for logged in customer
        $this->applyRedirect ( $this->customerUrl->getAccountUrl () );
        
        if (! $this->scopeConfig->isSetFlag ( CustomerUrl::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD, ScopeInterface::SCOPE_STORE )) {
            $referer = $this->request->getParam ( CustomerUrl::REFERER_QUERY_PARAM_NAME );
            if ($referer) {
                $referer = $this->urlDecoder->decode ( $referer );
                if ($this->url->isOwnOriginUrl ()) {
                    $this->applyRedirect ( $referer );
                }
            }
        } elseif ($this->session->getAfterAuthUrl ()) {
            $this->applyRedirect ( $this->session->getAfterAuthUrl ( true ) );
        }
    }
    
    /**
     * Prepare redirect URL
     *
     * @param string $url            
     * @return void
     */
    private function applyRedirect($url) {
        $this->session->setBeforeAuthUrl ( $url );
    }
}