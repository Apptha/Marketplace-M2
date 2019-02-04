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
namespace Apptha\Marketplace\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Apptha\Marketplace\Controller\Adminhtml\Sellers;

class Massapprove extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @param Context $context            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(Context $context, PageFactory $resultPageFactory) {
        parent::__construct ( $context );
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Index action
     *
     * @return void
     */
    public function execute() {
        $result = $this->getRequest ()->getParam ( 'id' );
        foreach ( $result as $approvalProductId ) {
            try {
                $customerObject = $this->_objectManager->get ( '\Magento\Catalog\Model\Product' );
                $customerObject->load ( $approvalProductId )->setStatus ( 1 )->setProductApproval ( 1 )->save ();
                $sellerDetails = $customerObject->load ( $approvalProductId );
                $customerId = $sellerDetails->getSellerId ();
                $productName = $sellerDetails->getName ();
                $sellerDetails = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $sellerDetails->load ( $customerId );
                $sellerName = $sellerDetails->getFirstname ();
                $sellerEmail = $sellerDetails->getEmail ();
                $receiverInfo = [ 
                        'name' => $sellerName,
                        'email' => $sellerEmail 
                ];
                $templateId = 'marketplace_product_approval_template';
                $emailTempVariables = array ();
                $emailTempVariables ['name'] = $sellerName;
                $emailTempVariables ['productname'] = $productName;
                $adminInfo = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
                $adminEmail = $adminInfo->getAdminEmail ();
                $admin = $adminInfo->getAdminName ();
                $senderInfo = [ 
                        'name' => $admin,
                        'email' => $adminEmail 
                ];
                $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/products/index' );
    }
}