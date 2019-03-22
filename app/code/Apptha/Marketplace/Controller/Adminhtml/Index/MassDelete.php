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
namespace Apptha\Marketplace\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete for customers
 */
class MassDelete extends \Magento\Customer\Controller\Adminhtml\Index\MassDelete {
    
    /**
     *
     * @param AbstractCollection $collection            
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection) {
        
        $customerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
        $customerModelCollection = $customerModel->getCollection ();
        $customerDatas = $customerModelCollection->getData ();
        $customerIds = array ();
        $sellerFlag = 0;
        foreach ( $customerDatas as $customerData ) {
            $customerIds [] = $customerData ['customer_id'];
        }
        $customersDeleted = 0;
        foreach ( $collection->getAllIds () as $customerId ) {
            $this->customerRepository->deleteById ( $customerId );
            
            /**
             * Disable the seller products.
             */
            if (in_array ( $customerId, $customerIds )) {
                $sellerFlag = 4;
                $sellerProductCollection = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customerId );
                $sellerProductDatas = $sellerProductCollection->getData ();
                foreach ( $sellerProductDatas as $sellerProducts ) {
                    $productId = $sellerProducts ['entity_id'];
                    $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->reset()->load ( $productId )->setStatus ( 2 )->setProductApproval ( 0 )->save ();
                }
                $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
                $seller->delete ();
            }
            $customersDeleted ++;
        }
        if ($customersDeleted) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', $customersDeleted ) );
            if ($sellerFlag == 4) {
                $this->messageManager->addSuccess ( __ ( 'Seller Products were disabled and disapproved' ) );
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create ( ResultFactory::TYPE_REDIRECT );
        $resultRedirect->setPath ( $this->getComponentRefererUrl () );
        return $resultRedirect;
    }
}
