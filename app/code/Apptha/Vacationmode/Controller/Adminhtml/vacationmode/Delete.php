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
namespace Apptha\Vacationmode\Controller\Adminhtml\vacationmode;

class Delete extends \Magento\Backend\App\Action {
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute() {
        // check if we know what should be deleted
        $id = $this->getRequest ()->getParam ( 'vacation_id' );
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create ();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create ( 'Apptha\Vacationmode\Model\Vacationmode' );
                $model->load ( $id );
                $model->delete ();
                // display success message
                $this->messageManager->addSuccess ( __ ( 'The item has been deleted.' ) );
                return $resultRedirect->setPath ( '*/*/' );
            } catch ( \Exception $e ) {
                // display error message
                $this->messageManager->addError ( $e->getMessage () );
                // go back to edit form
                return $resultRedirect->setPath ( '*/*/edit', [ 
                        'vacation_id' => $id 
                ] );
            }
        }
        // display error message
        $this->messageManager->addError ( __ ( 'We can\'t find a item to delete.' ) );
        // go to grid
        return $resultRedirect->setPath ( '*/*/' );
    }
}