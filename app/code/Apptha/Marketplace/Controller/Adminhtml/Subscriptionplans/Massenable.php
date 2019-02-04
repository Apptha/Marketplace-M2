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
 * This class contains mass subsacription plans enabled functionality
 */
class Massenable extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Select plan ids
         */
        $enableIds = $this->getRequest ()->getParam ( 'selected' );
        foreach ( $enableIds as $enableId ) {
            try {
                /**
                 * Create subscription plan object
                 */
                $subscriptionPlans = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionplans' );
                /**
                 * To enable subscription plans
                 */
                $subscriptionPlans->load ( $enableId )->setStatus ( 1 )->save ();
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Enabled subscription plans count
         */
        if (count ( $enableIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were enabled.', count ( $enableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
