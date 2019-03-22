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
namespace Apptha\Marketplace\Block\Adminhtml\Review\Edit\Tab;

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
class Info extends Generic implements TabInterface {
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
     * @return object        
     */
    public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $newsStatus, \Apptha\Marketplace\Model\System\Config\Status $status, array $data = []) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_newsStatus = $newsStatus;
        $this->status = $status;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }
    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __ ( 'Edit Review' );
    }
    
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __ ( 'Edit Review' );
    }
    
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm() {
        /** @var $model \Apptha\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'marketplace_review' );
        $form = $this->_formFactory->create ();
        
        $fieldset = $form->addFieldset ( 'base_fieldset', [ 
                'legend' => __ ( 'Edit Seller Review' ) 
        ] );
        if ($model->getId ()) {
            $fieldset->addField ( 'id', 'hidden', [ 
                    'name' => 'id' 
            ] );
        }
        $field = $fieldset->addField ( 'review', 'textarea', [ 
                'name' => 'review',
                'label' => __ ( 'Review' ),
                'required' => true 
        ] );
        $data = $model->getData ();
        $rating = '';
        if (isset ( $data ['rating'] )) {
            $rating = $data ['rating'];
        }
        $ratingValue = '';
        for($inc = 1; $inc <= 5; $inc ++) {
            $class = '';
            if (! empty ( $rating ) && $rating == $inc) {
                $class = 'checked="checked"';
            }
            $ratingValue = $ratingValue . '<td style="text-align:center"><input ' . $class . ' type="radio" name="rating" value="' . $inc . '" /></td>';
        }
        $s_table = '<table><tr><th><?php echo __("1 Star") ?></th><th><?php echo __("2 Stars") ?></th><th><?php echo __("3 Stars") ?></th><th><?php echo __("4 Stars") ?></th><th><?php echo __("5 Stars") ?></th></tr><tr>'; 
        $end_table = '</tr></table>';
            $ratingHtml = '<div style="margin-left: -120px;" class="admin__field field field-ratings  required _required" data-ui-id="adminhtml-review-edit-tab-info-0-fieldset-element-form-field-ratings">
                       <label class="label admin__field-label" for="ratings" data-ui-id="adminhtml-review-edit-tab-info-0-fieldset-element-radio-ratings-label"><span>Ratings</span></label>
                       <div class="admin__field-control control">' .$s_table. $ratingValue .$end_table. '</div></div>';
        
        $field->setAfterElementHtml ( $ratingHtml );
        
        $fieldset->addField ( 'status', 'select', [ 
                'name' => 'status',
                'label' => __ ( 'Status' ),
                'required' => true,
                'values' => $this->status->toOptionArray () 
        ] );
        
        $form->setValues ( $data );
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
    /**
     *
     * Function to show tab
     * @return boolean
     *
     */
    public function canShowTab() {
        return true;
    }
    
    /**
     *
     * Function to check hidden
     * @return boolean
     *
     */
    public function isHidden() {
        return false;
    }
}