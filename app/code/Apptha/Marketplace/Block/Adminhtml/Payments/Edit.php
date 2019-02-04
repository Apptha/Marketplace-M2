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
namespace Apptha\Marketplace\Block\Adminhtml\Payments;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
/**
 * Class for seller payment grid
 */
class Edit extends Container {
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;
    
    /**
     * Constructor function 
     * @param Context $context            
     * @param Registry $frameworkRegistry            
     * @param array $data
     * @return void            
     */
    public function __construct(Context $context, Registry $frameworkRegistry, array $data = []) {
        $this->_registry = $frameworkRegistry;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Prepare layout file for edit seller payments
     *
     * @return object  layout
     */
    protected function _prepareLayout() {
        $this->_formScripts [] = "function toggleEditor() { if (tinyMCE.getInstanceById('post_content') == null) {  tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else { tinyMCE.execCommand('mceRemoveControl', false, 'post_content'); }  }; ";
        return parent::_prepareLayout ();
    }
    
    /**
     * Construct function for payments
     * @return void
     */
    protected function _construct() {
        /**
         * Declare object id
         */
        $this->_objectId = 'id';
        /**
         * Declare controller
         */
        $this->_controller = 'adminhtml_payments';
        /**
         * Declare block group
         */
        $this->_blockGroup = 'Apptha_Marketplace';
        /**
         * Call the perant construct function
         */
        parent::_construct ();
        /**
         * Update save button label as 'Pay'
         */
        $this->buttonList->update ( 'save', 'label', __ ( 'Pay' ) );
        /**
         * Remove save and continue button
         */
        $this->buttonList->remove ( 'saveandcontinue', 'label', __ ( 'Save and Continue Edit' ) );
        /**
         * Remove delete button
         */
        $this->buttonList->remove ( 'delete', 'label', __ ( 'Delete' ) );
        /**
         * Remove reset button
         */
        $this->buttonList->remove ( 'reset', 'label', __ ( 'Reset' ) );
    }
}