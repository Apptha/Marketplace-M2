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
 * */
namespace Apptha\Marketplace\Block\Adminhtml\Seller\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
/**
 * This class contains tab functions  in seller grid
 * @author user
 *
 */
class Tabs extends WidgetTabs {
    /**
     * Constructor for seller commission edit
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'seller_edit_tabs' );
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Commission Information' ) );
    }
    
    /**
     * To prepare before to html
     *
     * @return $this
     */
    protected function _beforeToHtml() {
        $this->addTab ( 'Commission', [ 
                'label' => __ ( 'General' ),
                'title' => __ ( 'General' ),
                'content' => $this->getLayout ()->createBlock ( 'Apptha\Marketplace\Block\Adminhtml\Seller\Edit\Tab\Info' )->toHtml (),
                'active' => true 
        ] );
        
        return parent::_beforeToHtml ();
    }
}