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
namespace Apptha\Marketplace\Block\Adminhtml\Payments\Grid\Renderer;

/**
 * This class used to renderer payment method in payments grid
 */
class Payments extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        /**
         * Get customer id by row
         */
        $customerId = $this->_getValue ( $row );
        /**
         * Create object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get seller 
         */
        $sellerInfo = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
        /**
         * Prepare html content
         */
        $html = '';
        $html = $html . '<div>';
        $html = $html . '<div>' . __ ( 'PayPal Id : ' );
        /**
         * Checking for PayPal id
         */
        if ($sellerInfo->getPaypalId () != '') {
            /**
             * Assign PayPal id
             */
            $html = $html . $sellerInfo->getPaypalId ();
        } else {
            $html = $html . __ ( 'NA' );
        }
        $html = $html . '</div>';
        $html = $html . '<div>' . __ ( 'Bank Payment : ' );
        /**
         * Checking for bank payment
         */
        if ($sellerInfo->getBankPayment () != '') {
            /**
             * Assign bank payment
             */
            $html = $html . $sellerInfo->getBankPayment ();
        } else {
            $html = $html . __ ( 'NA' );
        }
        $html = $html . '</div>';
        /**
         * Return html
         */
        return $html . '</div>';
    }
}