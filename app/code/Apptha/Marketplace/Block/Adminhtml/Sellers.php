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

class Sellers extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        $this->_controller = 'adminhtml_sellers';
        $this->_blockGroup = 'Apptha_Marketplace';
        $this->_headerText = __ ( 'Manage Seller' );
        $this->_addButtonLabel = __ ( 'Add Seller' );
        $this->_addNewButton ();
        parent::_construct ();
    }
    
    /**
     * Function for New button
     * 
     * @return void
     */
    protected function _addNewButton() {
        $this->addButton ( 'add', [ 
                'label' => $this->getAddButtonLabel (),
                'onclick' => 'setLocation(\'' . $this->getCreateUrl () . '\')',
                'class' => 'add primary' 
        ] );
    }
    /**
     * Function for get Url
     * 
     * @return string
     */
    public function getCreateUrl() {
        return $this->getUrl ( 'customer/index/new' );
    }
}