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

class Save extends Sellers {
    /**
     * Function to save Seller Data
     *
     * @return id(int)
     */
    public function execute() {
        $isPost = $this->getRequest ()->getPost ();
        if ($isPost) {
            $sellerModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Seller' );
            $sellerId = $this->getRequest ()->getPost ( 'id' );
            if ($sellerId) {
                $sellerModel->load ( $sellerId );
            }
            $formData = $this->getRequest ()->getParam ( 'commission' );
            $sellerModel->setCommission ( $formData );
            try {
                $sellerModel->save ();
                // Display success message
                $this->messageManager->addSuccess ( __ ( 'Data has been saved.' ) );
                // Check if 'Save and Continue'
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', [ 
                            'id' => $sellerModel->getId (),
                            '_current' => true 
                    ] );
                    return;
                }
                // Go to grid page
                $this->_redirect ( '*/*/' );
                return;
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
            $this->_getSession ()->setFormData ( $formData );
            $this->_redirect ( '*/*/edit', [ 
                    'id' => $sellerId 
            ] );
        }
    }
}