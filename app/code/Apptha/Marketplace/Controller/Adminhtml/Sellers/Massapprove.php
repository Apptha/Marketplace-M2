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
namespace Apptha\Marketplace\Controller\Adminhtml\Sellers;

use Apptha\Marketplace\Controller\Adminhtml\Sellers;

class Massapprove extends Sellers {
    /**
     *
     * @return void
     */
    public function execute() {
        $approvalIds = $this->getRequest ()->getParam ( 'approve' );
        foreach ( $approvalIds as $approvalId ) {
            try {
                $customer = $this->_objectManager->get ( '\Apptha\Marketplace\Model\Seller' );
                $customer->load ( $approvalId )->setStatus ( 1 )->save ();
                $sellerDetails = $customer->load ( $approvalId );
                $customerId = $sellerDetails->getCustomerId ();
               
                $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $customerSession->load ( $customerId );
                $sellerName = $customerSession->getFirstname ();
                $sellerEmail = $customerSession->getEmail ();
                /**
                 * Assign values for your template variables
                 */
                $emailTempVariables = array ();
                $emailTempVariables ['name'] = $sellerName;

                $receiverInfo = [
                        'name' => $sellerName,
                        'email' => $sellerEmail
                ];
                $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
                $adminEmail = $seller->getAdminEmail ();
                $admin = $seller->getAdminName ();
                $senderInfo = [
                        'name' => $admin,
                        'email' => $adminEmail
                ];
                $templateIdValue = 'marketplace_seller_admin_approval_template';
                $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateIdValue );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        if (count ( $approvalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were approved.', count ( $approvalIds ) ) );
            $this->messageManager->addSuccess ( __ ( 'Please Enable The Seller Products' ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
