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
 * This class contains the functionality of edit subscription plan
 */
class Save extends Subscriptionplans {
    /**
     * Function to save Seller Review Data
     *
     * @return id(int)
     */
    public function execute() {
        /**
         * Checking data exist or not
         */
        $isPost = $this->getRequest ()->getPost ();
        if ($isPost) {
            $subscriptionPlansModel = $this->_objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionplans' );
            /**
             * Checking for subscription plan exist or not
             */
            $subscriptionPlanId = $this->getRequest ()->getPost ( 'id' );
            if ($subscriptionPlanId) {
                $subscriptionPlansModel->load ( $subscriptionPlanId );
            }
            
            /**
             * Getting subscription details
             */
            $planName = $this->getRequest ()->getParam ( 'plan_name' );
            $subscriptionPeriodType = $this->getRequest ()->getParam ( 'subscription_period_type' );
            $periodFrequency = $this->getRequest ()->getParam ( 'period_frequency' );
            $maxProductCount = $this->getRequest ()->getParam ( 'max_product_count' );
            $fee = $this->getRequest ()->getParam ( 'fee' );
            $status = $this->getRequest ()->getParam ( 'status' );
            $isProductUnlimited = $this->getRequest ()->getParam ( 'is_unlimited' );
            
            /**
             * Getting date
             */
            $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
            
            $subscriptionPlansModel->setPlanName ( $planName );
            $subscriptionPlansModel->setSubscriptionPeriodType ( $subscriptionPeriodType );
            $subscriptionPlansModel->setPeriodFrequency ( $periodFrequency );
            
            if ($isProductUnlimited == 1) {
                $subscriptionPlansModel->setMaxProductCount ( 'unlimited' );
            } else {
                $subscriptionPlansModel->setMaxProductCount ( $maxProductCount );
            }
            /**
             * Setting subscription option
             */
            $subscriptionPlansModel->setFee ( $fee );
            $subscriptionPlansModel->setStatus ( $status );
            
            $subscriptionPlansModel->setUpdatedAt ( $date );
            if (empty ( $subscriptionPlanId )) {
                $subscriptionPlansModel->setCreatedAt ( $date );
            }
            
            /**
             * Saving subscroption plan
             */
            try {
                $subscriptionPlansModel->save ();
                /**
                 * Display success message
                 */
                $this->messageManager->addSuccess ( __ ( 'Data has been saved.' ) );
                /**
                 * Check if 'Save and Continue'
                 */
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', [ 
                            'id' => $subscriptionPlansModel->getId (),
                            '_current' => true 
                    ] );
                    return;
                }
                /**
                 * Go to grid page
                 */
                $this->_redirect ( '*/*/' );
                return;
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
            $this->_getSession ()->setFormData ( $formData );
            $this->_redirect ( '*/*/edit', [ 
                    'id' => $subscriptionPlanId 
            ] );
        }
    }
}