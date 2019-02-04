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
 * This class contains the subscription plan bass disable functionality
 */
class Massdisable extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Plan ids for disable
         */
        $disableIds = $this->getRequest ()->getParam ( 'selected' );
        /**
         * Id iteration for diable
         */
        foreach ( $disableIds as $disableId ) {
            try {
                /**
                 * Object for subascription plans
                 */
                $subscriptionPlansObj = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionplans' );
                $subscriptionPlansObj->load ( $disableId )->setStatus ( 0 )->save ();
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Count for session message
         */
        if (count ( $disableIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were disabled.', count ( $disableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
