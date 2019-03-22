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
namespace Apptha\Vacationmode\Block\Adminhtml\Vacationmode;

class Edit extends \Magento\Backend\Block\Widget\Form\Container {
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context            
     * @param \Magento\Framework\Registry $registry            
     * @param array $data            
     */
    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, array $data = []) {
        $this->_coreRegistry = $registry;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Initialize vacationmode edit block
     *
     * @return void
     */
    protected function _construct() {
        $this->_objectId = 'vacation_id';
        $this->_blockGroup = 'Apptha_Vacationmode';
        $this->_controller = 'adminhtml_vacationmode';
        
        parent::_construct ();
        
        $this->buttonList->update ( 'save', 'label', __ ( 'Save Vacationmode' ) );
        $this->buttonList->add ( 'saveandcontinue', [ 
                'label' => __ ( 'Save and Continue Edit' ),
                'class' => 'save',
                'data_attribute' => [ 
                        'mage-init' => [ 
                                'button' => [ 
                                        'event' => 'saveAndContinueEdit',
                                        'target' => '#edit_form' 
                                ] 
                        ] 
                ] 
        ], - 100 );
        
        $this->buttonList->update ( 'delete', 'label', __ ( 'Delete Vacationmode' ) );
    }
    
    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText() {
        if ($this->_coreRegistry->registry ( 'vacationmode' )->getId ()) {
            return __ ( "Edit Vacationmode '%1'", $this->escapeHtml ( $this->_coreRegistry->registry ( 'vacationmode' )->getTitle () ) );
        } else {
            return __ ( 'New Vacationmode' );
        }
    }
    
    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl() {
        return $this->getUrl ( 'vacationmode/*/save', [ 
                '_current' => true,
                'back' => 'edit',
                'active_tab' => '{{tab_id}}' 
        ] );
    }
    
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout() {
        $this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout ();
    }
}