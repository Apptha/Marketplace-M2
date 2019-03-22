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
 * */
/**
 * Recipient email config path
 */
namespace Apptha\Marketplace\Controller\Contact;

use \Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Send contact email to seller
 */
class Savedata extends \Magento\Framework\App\Action\Action {
    /**
     * Post user question
     *
     * @return void
     */
    public function execute() {
        $subject = $this->getRequest ()->getPost ( 'subject' );
        /**
         * get message
         */
        $message = $this->getRequest ()->getPost ( 'message' );
        
        $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $adminEmail = $seller->getAdminEmail ();
        $admin = $seller->getAdminName ();
        $receiverInfo = [ 
                'name' => $admin,
                'email' => $adminEmail 
        ];
        
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getCustomerId ();
            $customerEmail = $customerSession->getCustomer ()->getEmail ();
            $customerName = $customerSession->getCustomer ()->getName ();
            $sellerDatas = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            $sellerDetails = $sellerDatas->load ( $customerId, 'customer_id' );
            $sellerContact = $sellerDetails->getContact ();
        } else {
            $sellerContact = '';
            $customerName = '';
            $customerEmail = '';
        }
        $senderInfo = [ 
                'name' => $customerName,
                'email' => $customerEmail 
        ];
        $emailTempVariables ['subject'] = $subject;
        $emailTempVariables ['message'] = $message;
        $emailTempVariables ['contact'] = $sellerContact;
        $emailTempVariables ['name'] = $customerName;
        $emailTempVariables ['email'] = $customerEmail;
        
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        /* call send mail method from helper or where you define it */
        
        $templateId = 'marketplace_seller_contact_admin_template';
        $this->_objectManager->get ( 'Apptha\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
        $this->messageManager->addSuccess ( __ ( 'Thanks for contacting us with your comments and questions. We\'ll respond to you very soon' ) );
        $this->_redirect ( '*/contact/form' );
    }
}
