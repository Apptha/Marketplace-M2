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
namespace Apptha\Marketplace\Block\Adminhtml\Subscriptionplans\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Apptha\Marketplace\Model\System\Config\Status;

/**
 * Class Contains Seller Commission Functions
 */
class Form extends Generic implements TabInterface {
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     * @var \Apptha\Marketplace\Model\System\Config\Status
     */
    protected $editorConfig;
    protected $_systemStatus;
    protected $status;
    /**
     *
     * To call construct for set object for form
     *
     * @param Context $context            
     * @param Registry $registry            
     * @param FormFactory $formFactory            
     * @param Config $wysiwygConfig            
     * @param Status $systemStatus            
     * @param array $data       
     * 
     * @return void
     */
    public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $systemStatus, \Apptha\Marketplace\Model\Config\Source\Status $status, array $data = []) {
        $this->editorConfig = $wysiwygConfig;
        $this->_systemStatus = $systemStatus;
        $this->status = $status;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }
    /**
     * Prepare form fields
     * @return object
     */
    protected function _prepareForm() {
        /** @var $model \Apptha\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'marketplace_subscriptionplans' );
        $form = $this->_formFactory->create ();
        $data = $model->getData ();
        
        if (count ( $data ) >= 1) {
            $fieldset = $form->addFieldset ( 'base_fieldset', [ 
                    'legend' => __ ( 'Edit Subscription Plans' ) 
            ] );
        } else {
            $fieldset = $form->addFieldset ( 'base_fieldset', [ 
                    'legend' => __ ( 'Add Subscription Plans' ) 
            ] );
        }
        if ($model->getId ()) {
            $fieldset->addField ( 'id', 'hidden', [ 
                    'name' => 'id' 
            ] );
        }
        $fieldset->addField ( 'plan_name', 'text', [ 
                'name' => 'plan_name',
                'label' => __ ( 'Plan Name' ),
                'required' => true 
        ] );
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $periodtype = $objectManager->get ( 'Apptha\Marketplace\Model\Config\Source\Periodtype' );
        
        $fieldset->addField ( 'subscription_period_type', 'select', [ 
                'name' => 'subscription_period_type',
                'label' => __ ( 'Subscription Period Type' ),
                'required' => true,
                'values' => $periodtype->toOptionArray () 
        ] );
        
        $fieldset->addField ( 'period_frequency', 'text', [ 
                'name' => 'period_frequency',
                'label' => __ ( 'Period Frequency' ),
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero' 
        ] );
        
        $unlimited = $maxProductCountValue = 0;
        $data ['is_unlimited'] = 0;
        if (isset ( $data ['max_product_count'] )) {
            if ($data ['max_product_count'] == 'unlimited') {
                $unlimited = 1;
                $data ['is_unlimited'] = 1;
                $data ['max_product_count'] = 0;
            } else {
                $maxProductCountValue = $data ['max_product_count'];
            }
        }
        
        $isUnlimited = $fieldset->addField ( 'is_unlimited', 'checkbox', [ 
                'name' => 'is_unlimited',
                'label' => __ ( 'Is Unlimited Product' ),
                'onclick' => 'this.value = this.checked ? 1 : 0;',
                'checked' => $unlimited 
        ] );
        
        $maxProductCount = $fieldset->addField ( 'max_product_count', 'text', [ 
                'name' => 'max_product_count',
                'label' => __ ( 'Max Product Count' ),
                'required' => true,
                'value' => $maxProductCountValue,
                'class' => 'validate-digits validate-greater-than-zero' 
        ] );
        
        $fieldset->addField ( 'fee', 'text', [ 
                'name' => 'fee',
                'label' => __ ( 'Fee' ),
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero' 
        ] );
        
        $fieldset->addField ( 'status', 'select', [ 
                'name' => 'status',
                'label' => __ ( 'Status' ),
                'required' => true,
                'values' => $this->status->toOptionArray () 
        ] );
        
        $form->setValues ( $data );
        $this->setForm ( $form );
        
        $this->setChild ( 'form_after', $this->getLayout ()->createBlock ( 'Magento\Backend\Block\Widget\Form\Element\Dependence' )->addFieldMap ( $isUnlimited->getHtmlId (), $isUnlimited->getName () )->addFieldMap ( $maxProductCount->getHtmlId (), $maxProductCount->getName () )->addFieldDependence ( $maxProductCount->getName (), $isUnlimited->getName (), 0 ) );
        
        return parent::_prepareForm ();
    }
    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __ ( 'Edit Plans' );
    }
    
    /**
     *Function for showing tab
     *@return booelan
     *
     */
    public function canShowTab() {
        return true;
    }
    
    /**
     *
     * Function for checking tab hidden
     * @return boolean
     *
     */
    public function isHidden() {
        return false;
    }
    
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __ ( 'Edit Plans' );
    }
}