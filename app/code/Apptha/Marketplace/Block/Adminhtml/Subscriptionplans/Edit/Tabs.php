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
namespace Apptha\Marketplace\Block\Adminhtml\Subscriptionplans\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class for seller subscription plans tab functions
 */
class Tabs extends WidgetTabs {
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'subscriptionplans_edit_tabs' );
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Seller Subscription Plans' ) );
    }
    
    /**
     *Function to get before html
     * @return $this
     */
    protected function _beforeToHtml() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $model = $objectManager->get ( 'Magento\Framework\Registry' )->registry ( 'marketplace_subscriptionplans' );
        $data = $model->getData ();
        if (count ( $data ) >= 1) {
            $this->addTab ( 'Subscriptionplans', [ 
                    'label' => __ ( 'Edit Subscription Plans' ),
                    'title' => __ ( 'Edit Subscription Plans' ),
                    'content' => $this->getLayout ()->createBlock ( 'Apptha\Marketplace\Block\Adminhtml\Subscriptionplans\Edit\Tab\Form' )->toHtml (),
                    'active' => true 
            ] );
        } else {
            $this->addTab ( 'Subscriptionplans', [ 
                    'label' => __ ( 'Add Subscription Plans' ),
                    'title' => __ ( 'Add Subscription Plans' ),
                    'content' => $this->getLayout ()->createBlock ( 'Apptha\Marketplace\Block\Adminhtml\Subscriptionplans\Edit\Tab\Form' )->toHtml (),
                    'active' => true 
            ] );
        }
        return parent::_beforeToHtml ();
    }
}