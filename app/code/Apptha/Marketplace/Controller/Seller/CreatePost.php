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

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;

/**
 * This class contains seller save functions
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount {
    /** @var AccountManagementInterface */
    protected $accountManagement;
   
    /** @var Escaper */
    protected $escaper;
    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;
    /** @var DataObjectHelper  */
    protected $dataObjectHelper;
    /**
     *
     * @var Session
     */
    protected $session;
    /**
     *
     * @var AccountRedirect
     */
    private $accountRedirect;
   
    /**
    * 
    * @param Context $context
    * @param Session $customerSession
    * @param AccountManagementInterface $accountManagement
    * @param UrlFactory $urlFactory
    * @param Escaper $escaper
    * @param DataObjectHelper $dataObjectHelper
    * @param AccountRedirect $accountRedirect
    */
    public function __construct(Context $context, Session $customerSession, AccountManagementInterface $accountManagement, UrlFactory $urlFactory, Escaper $escaper, DataObjectHelper $dataObjectHelper, AccountRedirect $accountRedirect) {
        $this->session = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->escaper = $escaper;
        $this->urlModel = $urlFactory->create ();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;
        parent::__construct ( $context );
    }
    
    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress() {
       
        
        if (! $this->getRequest ()->getPost ( 'create_address' )) {
            return null;
        }
        
        $addressForm = $this->_objectManager->get ( 'Magento\Customer\Model\Metadata\FormFactory' )->create ( 'customer_address', 'customer_register_address' );
        $allowedAttributes = $addressForm->getAllowedAttributes ();
        
        $addressData = [ ];
        
        $regionDataObject = $this->_objectManager->get ( 'Magento\Customer\Api\Data\RegionInterfaceFactory' )->create ();
        foreach ( $allowedAttributes as $attribute ) {
            $attributeCode = $attribute->getAttributeCode ();
            $value = $this->getRequest ()->getParam ( $attributeCode );
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id' :
                    $regionDataObject->setRegionId ( $value );
                    break;
                case 'region' :
                    $regionDataObject->setRegion ( $value );
                    break;
                default :
                    $addressData [$attributeCode] = $value;
            }
        }
        $addressDataObject = $this->_objectManager->get ( 'Magento\Customer\Api\Data\AddressInterfaceFactory' )->create ();
        $this->dataObjectHelper->populateWithArray ( $addressDataObject, $addressData, '\Magento\Customer\Api\Data\AddressInterface' );
        $addressDataObject->setRegion ( $regionDataObject );
        
        $addressDataObject->setIsDefaultBilling ( $this->getRequest ()->getParam ( 'default_billing', false ) )->setIsDefaultShipping ( $this->getRequest ()->getParam ( 'default_shipping', false ) );
        return $addressDataObject;
    }
    
    /**
     * Create customer account action
     *
     * @return void @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute() {
        $resultRedirectFlag = 0;
      
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create ();
        
        if ($this->session->isLoggedIn () || ! $this->_objectManager->get ( 'Magento\Customer\Model\Registration' )->isAllowed ()) {
            $resultRedirect->setPath ( '*/*/' );
            return $resultRedirect;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            $url = $this->urlModel->getUrl ( '*/*/create', [ 
                    '_secure' => true 
            ] );
            $resultRedirect->setUrl ( $this->_redirect->error ( $url ) );
            return $resultRedirect;
        }
        
        $this->session->regenerateId ();
        
        try {
            $address = $this->extractAddress ();
            $addresses = $address === null ? [ ] : [ 
                    $address 
            ];
            
            $customer = $this->_objectManager->get ( 'Magento\Customer\Model\CustomerExtractor' )->extract ( 'customer_account_create', $this->_request );
            $customer->setAddresses ( $addresses );
            
            $password = $this->getRequest ()->getParam ( 'password' );
            $confirmation = $this->getRequest ()->getParam ( 'password_confirmation' );
            $redirectUrl = $this->session->getBeforeAuthUrl ();
            
            $this->checkPasswordConfirmation ( $password, $confirmation );
            
            $customer = $this->accountManagement->createAccount ( $customer, $password, $redirectUrl );
            
            if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
                $this->_objectManager->get ( 'Magento\Newsletter\Model\SubscriberFactory' )->create ()->subscribeCustomerById ( $customer->getId () );
            }
            
            $this->_eventManager->dispatch ( 'customer_register_success', [ 
                    'account_controller' => $this,
                    'customer' => $customer 
            ] );
            
            $confirmationStatus = $this->accountManagement->getConfirmationStatus ( $customer->getId () );
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $email = $this->_objectManager->get ( 'Magento\Customer\Model\Url' )->getEmailConfirmationUrl ( $customer->getEmail () );
                
                $this->messageManager->addSuccess ( __ ( 'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.', $email ) );
                
                $url = $this->urlModel->getUrl ( $this->_redirect->getRefererUrl());
                $resultRedirect->setUrl ( $this->_redirect->success ( $url ) );
            } else {
                $this->session->setCustomerDataAsLoggedIn ( $customer );
                $this->messageManager->addSuccess ( $this->getSuccessMessage () );
                $resultRedirect = $this->accountRedirect->getRedirect ();
                $url = $this->urlModel->getUrl ( 'marketplace/seller/dashboard', [ 
                        '_secure' => true 
                ] );
                $resultRedirect->setUrl ( $this->_redirect->success ( $url ) );
            }
            $resultRedirectFlag = 1;          
        } catch ( StateException $e ) {
            $url = $this->urlModel->getUrl ( 'customer/account/forgotpassword' );
            
            $message = __ ( 'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.', $url );
            
            $this->messageManager->addError ( $message );
        } catch ( InputException $e ) {
            $this->messageManager->addError ( $this->escaper->escapeHtml ( $e->getMessage () ) );
            foreach ( $e->getErrors () as $error ) {
                $this->messageManager->addError ( $this->escaper->escapeHtml ( $error->getMessage () ) );
            }
        } catch ( \Exception $e ) {
            $this->messageManager->addException ( $e, __ ( 'We can\'t save the customer.' ) );
        }
        if($resultRedirectFlag == 0){
        $this->session->setCustomerFormData ( $this->getRequest ()->getPostValue () );
        $defaultUrl = $this->urlModel->getUrl ( '*/*/create', [ 
                '_secure' => true 
        ] );        
        $resultRedirect->setUrl ( $this->_redirect->error ( $defaultUrl ) );
        }
        return $resultRedirect;
    }
    
    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password            
     * @param string $confirmation            
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation) {
        if ($password != $confirmation) {
            throw new InputException ( __ ( 'Please make sure your passwords match.' ) );
        }
    }
    
    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage() {
        
        if ($this->_objectManager->get ( 'Magento\Customer\Helper\Address' )->isVatValidationEnabled ()) {
            if ($this->_objectManager->get ( 'Magento\Customer\Helper\Address' )->getTaxCalculationAddressType () == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING) {
                $message = __ ( 'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.', $this->urlModel->getUrl ( 'customer/address/edit' ) );
            } else {
                $message = __ ( 'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.', $this->urlModel->getUrl ( 'customer/address/edit' ) );
            }
        } else {
            $message = __ ( 'Thank you for registering with %1.', $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getFrontendName () );
        }
        return $message;
    }
}
