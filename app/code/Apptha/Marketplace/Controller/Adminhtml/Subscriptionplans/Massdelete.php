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
namespace Apptha\Marketplace\Controller\Adminhtml\Subscriptionplans;

use Apptha\Marketplace\Controller\Adminhtml\Subscriptionplans;

/**
 * This class contains the subscription plan mass delete functionality.
 */
class Massdelete extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting plan id for mass delete
         */
        $enableIds = $this->getRequest ()->getParam ( 'selected' );
        /**
         * Iterate plan id
         */
        foreach ( $enableIds as $enableId ) {
            try {
                /**
                 * Create object for subscription plans
                 */
                $subscriptionPlanObj = $this->_objectManager->get ( '\Apptha\Marketplace\Model\Subscriptionplans' );
                /**
                 * Delete selected plan by plan id
                 */
                $subscriptionPlanObj->load ( $enableId )->delete ();
            } catch (\Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Checking for plan count
         */
        if (count ( $enableIds )) {
            /**
             * Setting sessio message for subscription plan delete
             */
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', count ( $enableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
