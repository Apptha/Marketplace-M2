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
namespace Apptha\Marketplace\Block\Adminhtml\Review\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
/**
 * This class contains tab functions for review edit feature
 * @author user
 *
 */
class Tabs extends WidgetTabs {
    /**
     * Construct class for seeting review edit tabs
     *  
     * @see \Magento\Framework\View\Element\Template::_construct()
     * @return void
     * 
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'review_edit_tabs' );
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Seller Review' ) );
    }
    /**
     * Get before html content
     * 
     * @see \Magento\Backend\Block\Widget\Tabs::_beforeToHtml()
     * @return object
     */
    protected function _beforeToHtml() {
        /**
         * Add Review button
         */
        $this->addTab ( 'Review', ['label' => __ ( 'Edit Review' ),'title' => __ ( 'Edit Review' ),
                'content' => $this->getLayout ()->createBlock ( 'Apptha\Marketplace\Block\Adminhtml\Review\Edit\Tab\Info' )->toHtml (),
                'active' => true] );
        return parent::_beforeToHtml ();
    }
}