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

class Massdisapprove extends Review {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting disapproval id for seller review
         */
        $disApprovalIds = $this->getRequest ()->getParam ( 'approve' );
        foreach ( $disApprovalIds as $disApprovalId ) {
            try {
                /**
                 * Creating seller review object
                 */
                $review = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Review' );
                $review->load ( $disApprovalId )->setStatus ( 0 )->save ();
                $reviewDetails = $review->load ( $disApprovalId );
                /**
                 * Assign template value
                 */
                $templateIdValue = 'marketplace_review_admin_disapproval_template';
                /**
                 * Sending mail for notification
                 */
                $this->_objectManager->get ( 'Apptha\Marketplace\Controller\Adminhtml\Review\Save' )->sendReviewNotification ( $reviewDetails, $templateIdValue );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * To display disapproved review count in session message
         */
        if (count ( $disApprovalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were disapproved.', count ( $disApprovalIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
