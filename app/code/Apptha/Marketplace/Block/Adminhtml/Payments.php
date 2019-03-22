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
namespace Apptha\Marketplace\Block\Adminhtml;
use Magento\Backend\Block\Widget\Grid\Container;
/**
 * This class contains seller payments grid
 */
class Payments extends Container {
    /**
     * Constructor Function 
     *
     * @return void
     */
    protected function _construct() {
        /**
         * Setup seller payments grid info
         */
        $this->_controller = 'adminhtml_payments';
        /**
         * Set block group
         */
        $this->_blockGroup = 'Apptha_Marketplace';
        /**
         * Set header text
         */
        $this->_headerText = __ ( 'Manage Seller Payments' );
        /**
         * Call parent class construct
         */
        parent::_construct ();
        /**
         * Removing add seller payment button
         */
        $this->removeButton ( 'add' );
    }
}