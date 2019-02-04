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
/**
 * This class contains seller review edit functionality
 */
namespace Apptha\Marketplace\Controller\Adminhtml\Review;

use Apptha\Marketplace\Controller\Adminhtml\Review;

class Edit extends Review {
    /**
     * Seller review edit action
     */
    public function execute() {
        $reviewId = $this->getRequest ()->getParam ( 'id' );
     
        $reviewModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Review' );
        /**
         * Checking for review exist or not
         */
        if ($reviewId) {
            $reviewModel->load ( $reviewId );
            if (! $reviewModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Review no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $reviewData = $this->_session->getNewsData ( true );
        /**
         * Setting review data
         */
        if (! empty ( $reviewData )) {
            $reviewModel->setData ( $reviewData );
        }
        $this->_coreRegistry->register ( 'marketplace_review', $reviewModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate menu
         */
        $resultHtml->setActiveMenu ( 'Apptha_Marketplace::main_menu' );
        /**
         * To set title
         */
        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Edit Seller Review' ) );
        /**
         * Return result html page
         */
        return $resultHtml;
    }
}
