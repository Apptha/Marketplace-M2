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
namespace Apptha\Marketplace\Controller\Adminhtml\Review;

use Apptha\Marketplace\Controller\Adminhtml\Review;

class Index extends Review {
    /**
     *
     * @return void
     */
    public function execute() {
        if ($this->getRequest ()->getQuery ( 'ajax' )) {
            $this->_forward ( 'grid' );
            return;
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        /**
         * Enable menu
         */
        $resultPage->setActiveMenu ( 'Apptha_Marketplace::main_menu' );
        /**
         * Set title
         */
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Seller Review' ) );
        /**
         * Rertun view page
         */
        return $resultPage;
    }
}
