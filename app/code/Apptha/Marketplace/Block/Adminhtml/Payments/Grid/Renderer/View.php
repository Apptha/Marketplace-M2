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
 * This class used to renderer all payments
 */
class View extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        /**
         * Getting customer id by row
         */
        $customerId = $this->_getValue ( $row );
        /**
         * Get edit url
         */
        $url = $this->getUrl ( '*/allpayments/index/id/' . $customerId );
        $html = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $collection = $objectManager->get ( 'Apptha\Marketplace\Model\Payments' )->getCollection ();
        $collection->addFieldToFilter ( 'seller_id', array (
                'eq' => $customerId 
        ) );
        if (count ( $collection ) >= 1) {
            /**
             * Prepare html content
             */
            $html = $html . '<a href="' . $url . '" >' . __ ( 'View All Payments' ) . '</a>';
        } else {
            $html = $html . 'NA';
        }
        /**
         * Return html content
         */
        return $html;
    }
}