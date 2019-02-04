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
namespace Apptha\Marketplace\Controller\Seller;

use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * This class contains create seller functions
 */
class Create extends \Magento\Customer\Controller\AbstractAccount {
    /** @var Registration */
    protected $registration;
    /**
     *
     * @var Session
     */
    protected $session;
    
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * Constructor
     *
     * @param Context $context            
     * @param Session $customerSession            
     * @param PageFactory $resultPageFactory            
     * @param Registration $registration            
     */
    public function __construct(Context $context, Session $customerSession, PageFactory $resultPageFactory, Registration $registration) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->registration = $registration;
        parent::__construct ( $context );
    }
    
    /**
     * Customer register form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
        if ($this->session->isLoggedIn () || ! $this->registration->isAllowed ()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath ( '*/*' );
            return $resultRedirect;
        }
        
        /** @var \Magento\Framework\View\Result\Page $resultPage */       
        return $this->resultPageFactory->create ();
    }
}
