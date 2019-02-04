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
namespace Apptha\Vacationmode\Controller\Seller;

class Vacationmode extends \Magento\Framework\App\Action\Action {
    
    /**
     * Marketplace helper data object
     *
     * @var \Apptha\Marketplace\Helper\Data
     */
    protected $dataHelper;
    
    /**
     * Constructor
     *
     * @param \Apptha\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    
    public function execute() {
        $customerObject = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerObject->getId ();
        $sellerObject = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        if(!empty($customerId)){
        $this->_view->loadLayout ();
        $this->_view->getLayout ()->initMessages ();
        $this->_view->renderLayout ();
        }else{
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
}