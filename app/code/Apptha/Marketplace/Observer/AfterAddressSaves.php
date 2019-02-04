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
namespace Apptha\Marketplace\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\ObserverInterface;

/**
 * Customer Observer Model
 */
class AfterAddressSaves implements ObserverInterface
{

    /**
     * @var CustomerSession
     */
    private $customerSession;
protected $_customerRepositoryInterface;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession,\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }
    /**
     * Address after save event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
        $customerEmail = $customerSession->getCustomer()->getEmail();
        $customerId = $customerSession->getCustomer()->getId();
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        $customerEmail = $customer->getEmail();
        $sellerModels = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $load = $sellerModels->load ($customerId, 'customer_id' );
        $sellerEmail = $load->getEmail ();
        if($customerEmail != $sellerEmail){
        $load->setEmail ( $customerEmail)->save ();
        }
      }
}
}