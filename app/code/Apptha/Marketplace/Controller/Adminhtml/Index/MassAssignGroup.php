<?php

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apptha\Marketplace\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassAssignGroup
 */
class MassAssignGroup extends AbstractMassAction {
    /**
     *
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     *
     * @param Context $context            
     * @param Filter $filter            
     * @param CollectionFactory $collectionFactory            
     * @param CustomerRepositoryInterface $customerRepository            
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, CustomerRepositoryInterface $customerRepository) {
        parent::__construct ( $context, $filter, $collectionFactory );
        $this->customerRepository = $customerRepository;
    }
    
    /**
     * Customer mass assign group action
     *
     * @param AbstractCollection $collection            
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection) {
        $customersUpdated = 0;
        foreach ( $collection as $customerIds ) {
            // Verify customer exists
            $customerId = $customerIds->getEntityId ();
            $customer = $this->customerRepository->getById ( $customerId );
            $customer->setGroupId ( $this->getRequest ()->getParam ( 'group' ) );
            $this->customerRepository->save ( $customer );
            
            $groupId = $customerIds->getGroupId ();
            $customerEmail = $customerIds->getEmail ();
            /**
             * Checking for is seller group or not
             */
           
            $registeredCustomers = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' )->getCollection ();
            foreach ( $registeredCustomers as $customers ) {
                if ($customers->getEmail () == $customerEmail) {
                    $customerId = $customers->getId ();
                    $groupId = $customers->getGroupId ();
                }
            }
            if ($groupId == 4) {
                /**
                 * Load seller object
                 */
                $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
                
                $collection = $sellerModel->load ( $customerId, 'customer_id' );
                if (count ( $collection ) >= '1') {
                    /**
                     * To set seller data
                     */
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 1 )->setCustomerId ( $customerId )->save ();
                } else {
                    
                    $collection->setEmail ( $customerEmail )->setStatus ( 0 )->setCustomerId ( $customerId )->save ();
                }
            } else {
                
                /**
                 * Load seller object
                 */
                $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
                /**
                 * To set seller data
                 */
                $collection = $sellerModel->load ( $customerId, 'customer_id' );
                $collection->setStatus ( 0 )->save ();
            }
            $customersUpdated ++;
        }
        if ($customersUpdated) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were updated.', $customersUpdated ) );
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create ( ResultFactory::TYPE_REDIRECT );
        $resultRedirect->setPath ( $this->getComponentRefererUrl () );
        
        return $resultRedirect;
    }
}
