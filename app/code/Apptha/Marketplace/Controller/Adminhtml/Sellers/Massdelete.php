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

class MassDelete extends Sellers {
    /**
     *
     * @return voids
     */
    public function execute() {
        $sellerIds = $this->getRequest ()->getParam ( 'approve' );
        $customersDeleted = 0;
        foreach ( $sellerIds as $customerId ) {
            $customerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            $customerModelCollection = $customerModel->getCollection ()->addFieldToFilter( 'id',$customerId );
            $customerDatas = $customerModelCollection->getData ();
            $customerModel->load($customerId)->delete ();
            $customerId = $customerDatas['0']['customer_id'];
            /**
             * Disable the seller products.
             */
                $sellerFlag = 4;
                $sellerProductCollection = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customerId );
                $sellerProductDatas = $sellerProductCollection->getData ();
                foreach ( $sellerProductDatas as $sellerProducts ) {
                    $productId = $sellerProducts ['entity_id'];
                    $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId )->setStatus ( 2 )->setProductApproval ( 0 )->save ();
                }
            $customersDeleted ++;
        }
        if ($customersDeleted) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', $customersDeleted ) );
            if ($sellerFlag == 4) {
                $this->messageManager->addSuccess ( __ ( 'Seller Products were disabled and disapproved' ) );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}
