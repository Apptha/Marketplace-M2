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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains manipulation functions
 */
class Data extends AbstractHelper {
    const XML_VACATION_MODE = 'vacationstatus/seller/vacation_mode';
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
    public function __construct(Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, CategoryRepositoryInterface $categoryRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\ConfigurableProduct\Helper\Data $conficAttributeData) {
        parent::__construct ( $context );
        $this->scopeConfig = $context->getScopeConfig ();
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->productFactory = $productFactory;
        $this->conficAttributeData = $conficAttributeData;
    }
    /**
     * Get Enable/disable Vacationmode
     *
     * @return string
     */
    public function getVacationMode() {
        return $this->scopeConfig->getValue ( static::XML_VACATION_MODE, ScopeInterface::SCOPE_STORE );
    }
}