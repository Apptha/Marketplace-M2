<?php

/**
 * Copyright �� 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apptha\Marketplace\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Customer\Controller\Adminhtml\Index\Delete {
    /**
     * Delete customer action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute() {
      
        $resultRedirect = $this->resultRedirectFactory->create ();
        $formKeyIsValid = $this->_formKeyValidator->validate ( $this->getRequest () );
        $isPost = $this->getRequest ()->isPost ();
        if (! $formKeyIsValid || ! $isPost) {
            $this->messageManager->addError ( __ ( 'Customer could not be deleted.' ) );
            return $resultRedirect->setPath ( 'customer/index' );
        }
        
        $customerId = $this->initCurrentCustomer ();
        
        if (! empty ( $customerId )) {
            try {
                $this->_customerRepository->deleteById ( $customerId );
                $this->messageManager->addSuccess ( __ ( 'You deleted the customer.' ) );
                
                $customerFactory = $this->_objectManager->get ( '\Magento\Customer\Model\CustomerFactory' );
                $customer = $customerFactory->create ();
                $customer->load ( $customerId );
                $customerGroupId = $customer->getGroupId ();
                
                if ($customerGroupId == 4) {
                    $sellerProductCollection = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customerId );
                    $sellerProductDatas = $sellerProductCollection->getData ();
                    foreach ( $sellerProductDatas as $sellerProducts ) {
                        $productId = $sellerProducts ['entity_id'];
                        $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->reset()->load ( $productId )->setStatus ( 2 )->setProductApproval ( 0 )->save ();
                    }
                    $seller = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
                    $seller->delete ();
                    
                    $this->messageManager->addSuccess ( __ ( 'Seller Products were disabled and disapproved' ) );
                }
            } catch ( \Exception $exception ) {
                $this->messageManager->addError ( $exception->getMessage () );
            }
        }
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create ( ResultFactory::TYPE_REDIRECT );
        return $resultRedirect->setPath ( 'customer/index' );
    }
}
