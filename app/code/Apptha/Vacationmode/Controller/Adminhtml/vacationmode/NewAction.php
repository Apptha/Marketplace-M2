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

class NewAction extends \Magento\Backend\App\Action {
    /**
     *
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;
    
    /**
     *
     * @param \Magento\Backend\App\Action\Context $context            
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory            
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct ( $context );
    }
    
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute() {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create ();
        return $resultForward->forward ( 'edit' );
    }
    
    /**
     *
     * {@inheritdoc}
     *
     */
    protected function _isAllowed() {
        return true;
    }
}