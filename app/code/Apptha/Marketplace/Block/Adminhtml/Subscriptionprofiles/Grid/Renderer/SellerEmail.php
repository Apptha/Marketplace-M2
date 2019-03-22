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
namespace Apptha\Marketplace\Block\Adminhtml\Subscriptionprofiles\Grid\Renderer;

/**
 * Class for seller subscription profile email functions
 */
class SellerEmail extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $email = '';
        $id = $this->_getValue ( $row );
        /**
         * Checking for id avaiable or not
         */
        if ($id != '') {
            /**
             * Creating customer instance
             */
            $customerObj = \Magento\Framework\App\ObjectManager::getInstance ();
            /**
             * Getting seller email id
             */
            $sellerObj = $customerObj->get ( 'Magento\Customer\Model\Customer' )->load ( $id );
            $email = $sellerObj->getEmail ();
        }
        /**
         * Return seller email value
         */
        return $email;
    }
}