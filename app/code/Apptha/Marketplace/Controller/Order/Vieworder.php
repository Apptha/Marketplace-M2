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
namespace Apptha\Marketplace\Controller\Order;

use Zend\Form\Annotation\Instance;

class Vieworder extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * Seller over view
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory            
     *
     * @return Object
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
       
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        if ($customerSession->isLoggedIn () && $status == 1) {
            
            /**
             * Get seller order collection
             */
            $orderId = $this->getRequest ()->getParam ( 'id' );
            $sellerOrderCollection = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
            $sellerOrderCollection->addFieldToFilter ( 'seller_id', $customerId );
            $sellerOrderCollection->addFieldToFilter ( 'order_id', $orderId );
            if (count ( $sellerOrderCollection ) >= 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } else {
                $this->messageManager->addNotice ( __ ( 'You dont have permission to access this page.' ) );
                $this->_redirect ( 'marketplace/order/manage' );
            }
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access.' ) );
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
}
