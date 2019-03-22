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

class MassDelete extends Review {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting review ids
         */
        $reviewIds = $this->getRequest ()->getParam ( 'approve' );
        /**
         * Iterate review ids
         */
        foreach ( $reviewIds as $reviewId ) {
            try {
                /**
                 * Create object for review
                 */
                $sellerFactory = $this->_objectManager->get ( '\Apptha\Marketplace\Model\Review' );
                /**
                 * Delete review by review id
                 */
                $sellerFactory->load ( $reviewId )->delete ();
            } catch (\Exception $e ) {
                /**
                 * Set session message
                 */
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * To show deleted review count in session message
         */
        if (count ( $reviewIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', count ( $reviewIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
