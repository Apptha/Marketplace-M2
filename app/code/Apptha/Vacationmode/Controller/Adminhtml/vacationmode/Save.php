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

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action {
    
    /**
     *
     * @param Action\Context $context            
     */
    public function __construct(Action\Context $context) {
        parent::__construct ( $context );
    }
    
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        $data = $this->getRequest ()->getPostValue ();
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create ();
        if (! empty ( $data )) {
            $model = $this->_objectManager->create ( 'Apptha\Vacationmode\Model\Vacationmode' );
            
            $id = $this->getRequest ()->getParam ( 'vacation_id' );
            if (! empty ( $id )) {
                $model->load ( $id );
                $model->setCreatedAt ( date ( 'Y-m-d H:i:s' ) );
            }
            try {
                $uploader = $this->_objectManager->create ( 'Magento\MediaStorage\Model\File\Uploader', [ 
                        'fileId' => 'image' 
                ] );
                $uploader->setAllowedExtensions ( [ 
                        'jpg',
                        'jpeg',
                        'gif',
                        'png' 
                ] );
                /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                $imageAdapter = $this->_objectManager->get ( 'Magento\Framework\Image\AdapterFactory' )->create ();
                $uploader->setAllowRenameFiles ( true );
                $uploader->setFilesDispersion ( true );
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( DirectoryList::MEDIA );
                $result = $uploader->save ( $mediaDirectory->getAbsolutePath ( 'emizen_banner' ) );
                if ($result ['error'] == 0) {
                    $data ['image'] = 'emizen_banner' . $result ['file'];
                }
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
            if (isset ( $data ['image'] ['delete'] ) && $data ['image'] ['delete'] == '1')
                $data ['image'] = '';
            unset ( $data ['vacation_id'] );
            $data ['from_date'] = date ( 'Y-m-d', strtotime ( $data ['from_date'] ) );
            $data ['to_date'] = date ( 'Y-m-d', strtotime ( $data ['to_date'] ) );
            $model->setData ( $data );
            
            try {
                $model->save ();
                $this->messageManager->addSuccess ( __ ( 'The Vacationmode has been saved.' ) );
                $this->_objectManager->get ( 'Magento\Backend\Model\Session' )->setFormData ( false );
                if ($this->getRequest ()->getParam ( 'back' )) {
                    return $resultRedirect->setPath ( '*/*/edit', [ 
                            'vacation_id' => $model->getId (),
                            '_current' => true 
                    ] );
                }
                return $resultRedirect->setPath ( '*/*/' );
            } catch ( \Magento\Framework\Exception\LocalizedException $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            } catch ( \RuntimeException $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            } catch ( \Exception $e ) {
                $this->messageManager->addException ( $e, __ ( 'Something went wrong while saving the Vacationmode.' ) );
            }
            
            $this->_getSession ()->setFormData ( $data );
            return $resultRedirect->setPath ( '*/*/edit', [ 
                    'vacation_id' => $this->getRequest ()->getParam ( 'vacation_id' ) 
            ] );
        }
        return $resultRedirect->setPath('*/*/');
    }
}