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

/**
 * This class contains sending email functions
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper {
    const XML_PATH_CONTACT_ADMIN_TEMPLATE = 'marketplace/seller/contact_admin_template';
    /**
     * Email Template for Product Approval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_PRODUCT_DISAPRROVAL_TEMPLATE = 'marketplace/product/disapproval_template';
    /**
     * Email Template for Product Disapproval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_PRODUCT_APPROVAL_TEMPLATE = 'marketplace/product/approval_template';
    /**
     * Email Template for Seller Approval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_SELLER_APPROVAL_TEMPLATE = 'marketplace/seller/admin_approval_template';
    /**
     * Email Template for Seller Disapproval
     * 
     * @return constant
     */
    const XML_PATH_SELLER_DISAPPROVAL_TEMPLATE = 'marketplace/seller/admin_disapproval_template';
    /**
     * Email Template for contact seller
     * 
     * @return constant
     */
    const XML_PATH_CONTACT_SELLER_TEMPLATE = 'marketplace/seller/contact_seller_template';
    
    /**
     * Email Template for order notification
     * 
     * @return constant
     */
    const XML_PATH_ORDER_NOTIFICATION_TEMPLATE = 'marketplace/order/notification_template';
    /**
     * Email Template for order item request
     * 
     * @return constant
     */
    const XML_PATH_ORDER_ITEM_REQUEST_TEMPLATE = 'marketplace/order/item_request_template';
    /**
     * Email Template for order item returns
     * 
     * @return constant
     */
    const XML_PATH_ORDER_ITEM_CANCEL_RETURN_TEMPLATE = 'marketplace/order/item_cancel_return_template';
    
    /**
     * Email Template for Seller Review Approval
     * 
     * @return constant
     */
    const XML_PATH_EMAIL_REVIEW_APPROVAL_TEMPLATE = 'marketplace/review/admin_approval_template';
    /**
     * Email Template for Seller Review Disapproval
     * 
     * @return constant
     */
    const XML_PATH_REVIEW_DISAPPROVAL_TEMPLATE = 'marketplace/review/admin_disapproval_template';
    
    /**
     * Email Template for Seller Review Notification
     * 
     * @return constant
     */
    const XML_PATH_REVIEW_NOTIFICATION_TEMPLATE = 'marketplace/review/admin_notification_template';
    
    /**
     * Email Template for Admin Subscription Notification
     * 
     * @return constant
     */
    const XML_PATH_SUBSCRIPTION_NOTIFICATION_TEMPLATE = 'marketplace/subscription/admin_notification_template';
    
    /**
     * Email Template for Seller Subscription Notification
     * 
     * @return constant
     */
    const XML_PATH_SELLER_SUBSCRIPTION_NOTIFICATION_TEMPLATE = 'marketplace/subscription/seller_notification_template';
    
    /**
     * Email Template for Seller Subscription Successful Notification
     * 
     * @return constant
     */
    const XML_PATH_SELLERSUCCESS_SUBSCRIPTION_NOTIFICATION_TEMPLATE = 'marketplace/subscription/sellersuccess_notification_template';
    /**
     * Email Template for low stock Notificatio
     *
     * @return constant
     */
    const XML_PATH_SELLER_PRODUCT_OUTOFSTOCK_NOTIFICATION = 'seller/product/outofstock/notification';
    /**
     * Email Template for Out Of Stock Notification
     *
     * @return constant
     */
    
    const XML_PATH_SELLER_PRODUCT_NOTIFICATION = 'seller/product/notification';
    
    protected $_scopeConfig;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     *
     * @var string
     */
    protected $temp_id;
    
    /**
     *
     * @param Magento\Framework\App\Helper\Context $context            
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig            
     * @param Magento\Store\Model\StoreManagerInterface $storeManager            
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation            
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder            
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder) {
        $this->_scopeConfig = $context;
        parent::__construct ( $context );
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }
    
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path            
     * @param int $storeId            
     * @return mixed
     */
    protected function getConfigValue($path, $storeId) {
        /**
         * To getting the store based config
         */
        return $this->scopeConfig->getValue ( $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId );
    }
    
    /**
     * Function to get store
     *
     * @return Store
     */
    public function getStore() {
        /**
         * Get store details from store manger object
         */
        return $this->_storeManager->getStore ();
    }
    
    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath) {
        /**
         * To get the configurable values
         */
        return $this->getConfigValue ( $xmlPath, $this->getStore ()->getStoreId () );
    }
    /**
     * [generateTemplate description] with template file and tempaltes variables values
     * 
     * @param Mixed $emailTemplateVariables            
     * @param Mixed $senderInfo            
     * @param Mixed $receiverInfo            
     * @return void
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo, $templateId, $area) {
        /**
         * To set template indentifiler
         */
        $this->_transportBuilder->setTemplateIdentifier ( $templateId )->setTemplateOptions ( [ 
                'area' => $area,
                'store' => $this->_storeManager->getStore ()->getId () 
        ] )->setTemplateVars ( $emailTemplateVariables )->setFrom ( $senderInfo )->addTo ( $receiverInfo ['email'], $receiverInfo ['name'] );
        /**
         * Return email template option
         */
        return $this;
    }
    
    /**
     * Send custom mail
     *
     * @param Mixed $emailTemplateVariables            
     * @param Mixed $senderInfo            
     * @param Mixed $receiverInfo            
     * @return void
     */
    public function yourCustomMailSendMethod($emailTemplateVariables, $senderInfo, $receiverInfo, $templateId) {
        
        /**
         * To declare all email template id
         */
        $productDisapprovalTemplate = 'marketplace_product_disapproval_template';
        $productApprovalTemplate = 'marketplace_product_approval_template';
        $sellerApprovalTemplate = 'marketplace_seller_admin_approval_template';
        $sellerDisApprovalTemplate = 'marketplace_seller_admin_disapproval_template';
        $contactSellerTemplate = 'marketplace_seller_contact_seller_template';
        $contactAdminTemplate = 'marketplace_seller_contact_admin_template';
        $orderNotificationTemplate = 'marketplace_order_notification_template';
        $orderItemRequestTemplate = 'marketplace_order_item_request_template';
        $orderItemCancelReturnTemplate = 'marketplace_order_item_cancel_return_template';
        $reviewApprovalTemplate = 'marketplace_review_admin_approval_template';
        $reviewDisApprovalTemplate = 'marketplace_review_admin_disapproval_template';
        $reviewNotificationTemplate = 'marketplace_review_admin_notification_template';
        $subscriptionNotificationTemplate = 'marketplace_subscription_admin_notification_template';
        $sellerSubscriptionNotificationTemplate = 'marketplace_subscription_seller_notification_template';
        $sellerSuccessSubscriptionNotificationTemplate = 'marketplace_subscription_sellersuccess_notification_template';
        $sellerProductOutofstockNotificationTemplate = 'seller_product_outofstock_notification';
        $sellerProductNotificationTemplate = 'seller_product_notification';
        
        /**
         * Checking for email template id
         */
        switch ($templateId) {
            case $productDisapprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_PRODUCT_DISAPRROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $productApprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_PRODUCT_APPROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $sellerApprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_SELLER_APPROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $sellerDisApprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_SELLER_DISAPPROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $contactSellerTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_CONTACT_SELLER_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $contactAdminTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_CONTACT_ADMIN_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $orderNotificationTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_ORDER_NOTIFICATION_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $orderItemRequestTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_ORDER_ITEM_REQUEST_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $orderItemCancelReturnTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_ORDER_ITEM_CANCEL_RETURN_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $reviewApprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_EMAIL_REVIEW_APPROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $reviewDisApprovalTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_REVIEW_DISAPPROVAL_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_ADMINHTML;
                break;
            case $reviewNotificationTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_REVIEW_NOTIFICATION_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $subscriptionNotificationTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_SUBSCRIPTION_NOTIFICATION_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $sellerSubscriptionNotificationTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_SELLER_SUBSCRIPTION_NOTIFICATION_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $sellerSuccessSubscriptionNotificationTemplate :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_SELLERSUCCESS_SUBSCRIPTION_NOTIFICATION_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $sellerProductOutofstockNotificationTemplate:
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_SELLER_PRODUCT_OUTOFSTOCK_NOTIFICATION);
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            case $sellerProductNotificationTemplate:
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_SELLER_PRODUCT_NOTIFICATION);
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            default :
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_ORDER_NOTIFICATION_TEMPLATE );
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        }
        /**
         * To generate template
         */
        $this->inlineTranslation->suspend ();
        $this->generateTemplate ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId, $area );
        /**
         * Create object for transportbuilder
         */
        $transport = $this->_transportBuilder->getTransport ();
        /**
         * Send message function
         */
        $transport->sendMessage ();
        /**
         * Resume the inline translation
         */
        $this->inlineTranslation->resume ();
    }
}