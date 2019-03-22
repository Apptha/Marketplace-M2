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

class Massapprove extends Review {

    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Get list of ids
         */
        $approvalIds = $this->getRequest ()->getParam ( 'approve' );
        /**
         * Iterate ids
         */
        foreach ( $approvalIds as $approvalId ) {
            try {
                /**
                 * Update review status
                 */
                $review = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Review' );
                $review->load ( $approvalId )->setStatus ( 1 )->save ();
                
                /**
                 * Send notification
                 */
                $reviewDetails = $review->load ( $approvalId );
                $templateIdValue = 'marketplace_review_admin_approval_template';
                $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Adminhtml\Review\Save' )->sendReviewNotification ( $reviewDetails, $templateIdValue );
            } catch ( \Exception $e ) {
                /**
                 * Adding session error message
                 */
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Checking for updated review count
         */
        if (count ( $approvalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were approved.', count ( $approvalIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
