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

class Edit extends \Magento\Backend\App\Action {
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @param Action\Context $context            
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory            
     * @param \Magento\Framework\Registry $registry            
     */
    public function __construct(Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct ( $context );
    }
    
    /**
     *
     * {@inheritdoc}
     *
     */
    protected function _isAllowed() {
        return true;
    }
    
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction() {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Apptha_Vacationmode::Vacationmode' )->addBreadcrumb ( __ ( 'Apptha Vacationmode' ), __ ( 'Apptha Vacationmode' ) )->addBreadcrumb ( __ ( 'Manage Item' ), __ ( 'Manage Item' ) );
        return $resultPage;
    }
    
    /**
     * Edit Item
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        // 1. Get ID and create model
        $id = $this->getRequest ()->getParam ( 'vacation_id' );
        $model = $this->_objectManager->create ( 'Apptha\Vacationmode\Model\Vacationmode' );
        
        // 2. Initial checking
        if (! empty ( $id )) {
            $model->load ( $id );
            if (! $model->getId ()) {
                $this->messageManager->addError ( __ ( 'This item no longer exists.' ) );
                /**
                 * \Magento\Backend\Model\View\Result\Redirect $resultRedirect
                 */
                $resultRedirect = $this->resultRedirectFactory->create ();
                
                return $resultRedirect->setPath ( '*/*/' );
            }
        }
        
        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get ( 'Magento\Backend\Model\Session' )->getFormData ( true );
        if (! empty ( $data )) {
            $model->setData ( $data );
        }
        
        // 4. Register model to use later in blocks
        $this->_coreRegistry->register ( 'vacationmode', $model );
        
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction ();
        $resultPage->addBreadcrumb ( __ ( 'Apptha' ), __ ( 'Apptha' ) );
        $resultPage->addBreadcrumb ( $id ? __ ( 'Edit Item' ) : __ ( 'New Item' ), $id ? __ ( 'Edit Item' ) : __ ( 'New Item' ) );
        $resultPage->getConfig ()->getTitle ()->prepend ( $id ? __ ( 'Edit Item' ) : __ ( 'New Item' ) );
        return $resultPage;
    }
}