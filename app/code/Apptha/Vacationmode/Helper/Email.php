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
namespace Apptha\Vacationmode\Helper;

/**
 * This class contains sending email functions
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper {
    
    /**
     * Email template for vacation mode
     */
    
    const XML_PATH_VACATION_MODE_TEMPLATE = 'vacationstatus/seller/vacation_mode_template';
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
        ] )->setTemplateVars ( $emailTemplateVariables )->setFrom ( $senderInfo )->addTo ( $receiverInfo ['email'], $receiverInfo ['name'] )->addBcc($senderInfo['email']);
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
        $vacationModeTemplate = 'vacationstatus_seller_vacation_mode_template';
        
        /**
         * Checking for email template id
         */
        switch ($templateId) {
            case $vacationModeTemplate:
                $this->temp_id = $this->getTemplateId ( static::XML_PATH_VACATION_MODE_TEMPLATE);
                $area = \Magento\Framework\App\Area::AREA_FRONTEND;
                break;
            default :
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