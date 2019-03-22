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
namespace Apptha\Vacationmode\Block\Adminhtml\Vacationmode\Edit\Tab;

/**
 * Vacationmode edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    
    /**
     *
     * @var \Apptha\Vacationmode\Model\Status
     */
    protected $_status;
    
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context            
     * @param \Magento\Framework\Registry $registry            
     * @param \Magento\Framework\Data\FormFactory $formFactory            
     * @param \Magento\Store\Model\System\Store $systemStore            
     * @param array $data            
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, \Apptha\Vacationmode\Model\Status $status, array $data = []) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }
    
    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm() {
        /* @var $model \Apptha\Vacationmode\Model\BlogPosts */
        $model = $this->_coreRegistry->registry ( 'vacationmode' );
        
        $isElementDisabled = false;
        $dateFormat = $this->_localeDate->getDateFormat ( \IntlDateFormatter::MEDIUM );
        $timeFormat = $this->_localeDate->getTimeFormat ( \IntlDateFormatter::MEDIUM );
        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create ();
        
        $form->setHtmlIdPrefix ( 'page_' );
        
        $fieldset = $form->addFieldset ( 'base_fieldset', [ 
                'legend' => __ ( 'Item Information' ) 
        ] );
        
        if ($model->getId ()) {
            $fieldset->addField ( 'vacation_id', 'hidden', [ 
                    'name' => 'vacation_id' 
            ] );
        }
        
        $fieldset->addField ( 'vacation_id', 'text', [ 
                'name' => 'vacation_id',
                'label' => __ ( 'Id' ),
                'title' => __ ( 'Id' ),
                'required' => true,
                'disabled' => $isElementDisabled 
        ] );
        
        
        $fieldset->addField ( 'from_date', 'date', [ 
                'name' => 'from_date',
                'label' => __ ( 'From Date' ),
                'title' => __ ( 'From Date' ),
                'date_format' => $dateFormat,
                'required' => true,
                'disabled' => $isElementDisabled 
        ] );
        $fieldset->addField ( 'to_date', 'date', [ 
                'name' => 'to_date',
                'label' => __ ( 'To Date' ),
                'title' => __ ( 'To Date' ),
                'date_format' => $dateFormat,
                'required' => true,
                'disabled' => $isElementDisabled 
        ] );
        
        $fieldset->addField ( 'vacation_message', 'textarea', [ 
                'name' => 'vacation_message',
                'label' => __ ( 'Vacation Message' ),
                'title' => __ ( 'Vacation Message' ),
                'required' => true,
                'disabled' => $isElementDisabled 
        ] );
        
        $fieldset->addField ( 'seller_id', 'text', [ 
                'name' => 'seller_id',
                'label' => __ ( 'Seller Id' ),
                'title' => __ ( 'Seller Id' ),
                'required' => true,
                'disabled' => $isElementDisabled 
        ] );
        
        if (! $model->getId ()) {
            $model->setData ( 'is_active', $isElementDisabled ? '0' : '1' );
        }
        
        $form->setValues ( $model->getData () );
        $this->setForm ( $form );
        
        return parent::_prepareForm ();
    }
    
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel() {
        return __ ( 'Item Information' );
    }
    
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle() {
        return __ ( 'Item Information' );
    }
    
    /**
     *
     * {@inheritdoc}
     *
     */
    public function canShowTab() {
        return true;
    }
    
    /**
     *
     * {@inheritdoc}
     *
     */
    public function isHidden() {
        return false;
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId            
     * @return bool
     */
    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed ( $resourceId );
    }
    public function getTargetOptionArray() {
        return array (
                '_self' => "Self",
                '_blank' => "New Page" 
        );
    }
}
