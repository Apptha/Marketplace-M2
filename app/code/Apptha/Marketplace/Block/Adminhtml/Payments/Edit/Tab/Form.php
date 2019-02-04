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
namespace Apptha\Marketplace\Block\Adminhtml\Payments\Edit\Tab;

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
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    protected $_newsStatus;
    
    /**
     *
     * @var \Apptha\Marketplace\Model\System\Config\Status
     */
    protected $status;
    
    /**
     *
     * @param Context $context            
     * @param Registry $registry            
     * @param FormFactory $formFactory            
     * @param Config $wysiwygConfig            
     * @param Status $newsStatus            
     * @param array $data            
     * @return void
     */
    public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $newsStatus, \Apptha\Marketplace\Model\Config\Source\Status $status, array $data = []) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_newsStatus = $newsStatus;
        $this->status = $status;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }
    
    /**
     * Prepare form Functon 
     *
     * @return object \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm() {
        /** @var $model \Apptha\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'marketplace_payments' );
        /**
         * Create form object
         */
        $form = $this->_formFactory->create ();
        /**
         * Convert object to data
         */
        $data = $model->getData ();
        /**
         * Create field set for form
         */
        $fieldset = $form->addFieldset ( 'base_fieldset', [ 
                'legend' => __ ( 'Pay' ) 
        ] );
        /**
         * Crete hidden id field
         */
        $field = $fieldset->addField ( 'id', 'hidden', [ 
                'name' => 'id' 
        ] );
        /**
         * Create pay amount field
         */
        $paymentField = $fieldset->addField ( 'pay_amount', 'text', [ 
                'name' => 'pay_amount',
                'label' => __ ( 'Pay Amount' ),
                'required' => true,
                'class' => 'validate-greater-than-zero' 
        ] );
        /**
         * Create invoice field
         */
        $fieldset->addField ( 'invoice', 'text', [ 
                'name' => 'invoice',
                'label' => __ ( 'Invoice Id' ),
                'required' => true 
        ] );
        
        /**
         * Create textarea field for comment
         */
        $fieldset->addField ( 'comment', 'textarea', [ 
                'name' => 'comment',
                'label' => __ ( 'Comment' ),
                'required' => true 
        ] );
        
        /**
         * Create remaining amount html
         */
        $html = '<div style="color: #303030;font-size: 14px;font-weight: 600;margin-left: 80px;">' . __ ( 'Pending' ) . '<br/>' . __ ( 'Payment' );
        $html = $html . '<span style="margin-left: 40px;">' . round ( $data ['remaining_amount'], 2 ) . '</span></div><br/>';
        
        /**
         * Set html element
         */
        $field->setAfterElementHtml ( $html );
        
        /**
         * Creating payment field
         */
        $paymentsValues = '&nbsp;&nbsp;&nbsp;&nbsp;<input checked="checked" type="radio" name="payment_type" value="Paypal Id : ' . $data ['paypal_id'] . '" />' . __ ( 'PayPal' );
        $paymentsValues = $paymentsValues . '&nbsp;&nbsp;<input type="radio" name="payment_type" value="Bank Payment : ' . $data ['bank_payment'] . '" />' . __ ( 'Bank Payment' );
        /**
         * Creating payment html
         */
        $paymentsHtml = '<div style="color: #303030;font-size: 14px;font-weight: 600;;margin-left: -87px;margin-top: 20px;" class="admin__field field field-invoice  required _required" data-ui-id="adminhtml-payments-edit-tab-form-0-fieldset-element-form-field-invoice">
                    <label class="label admin__field-label" for="payment_type" data-ui-id="adminhtml-payments-edit-tab-form-0-fieldset-element-text-invoice-label"><span><b>' . __ ( 'Payment Mode' ) . '</b></span></label>
                    &nbsp;<span>' . $paymentsValues . '</span></div>';
        
        $paymentField->setAfterElementHtml ( $paymentsHtml );
        /**
         * Set values
         */
        $form->setValues ( $data );
        /**
         * Set form
         */
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
    
    /**
     * Function for Prepare label for tabs
     *
     * @return string
     */
    public function getTabLabel() {
        /**
         * Return seller payments
         */
        return __ ( 'Seller Payments' );
    }
    
    /**
     *Function to  show Tab
     *
     * @return boolean
     */
    public function canShowTab() {
        /**
         * Return true
         */
        return true;
    }
    
    /**
     * Function to check whether hidden
     * @return boolan
     */
    public function isHidden() {
        /**
         * Return false
         */
        return false;
    }
    
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        /**
         * Return seller payments
         */
        return __ ( 'Seller Payments' );
    }
}