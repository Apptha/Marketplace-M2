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
namespace Apptha\Marketplace\Controller\Configurable;

/**
 * This class used to get configurable attribute options
 */
class Options extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    public function __construct(\Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\App\Action\Context $context) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    
    /**
     * To create configurable select attribute options block
     *
     * @return void
     */
    public function execute() {
        /**
         * Get Attribute ids
         */
        $attributeCodes = $this->getRequest ()->getParam ( 'attributes' );
        $productId = $this->getRequest ()->getParam ( 'product_id' );
        $attributeIds = $this->getRequest ()->getParam ( 'attribute_ids' );
        
       
        $configurableData = array ();
        
        if (! empty ( $productId )) {
            /**
             * Get Product data by product id
             */
            $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            
            /**
             * Get configurable
             */
            $configurableData = $product->getTypeInstance ( true )->getConfigurableAttributesAsArray ( $product );
        }
        
        foreach ( $attributeCodes as $attributeCode => $label ) {
            
            $attributeId = '';
            if (isset ( $attributeIds [$attributeCode] )) {
                $attributeId = $attributeIds [$attributeCode];
            }
            
            /**
             * Getting selected product options values
             */
            $valueIndex = array ();
            if (! empty ( $attributeId ) && ! empty ( $configurableData [$attributeId] ['values'] )) {
                foreach ( $configurableData [$attributeId] ['values'] as $value ) {
                    $valueIndex [] = $value ['value_index'];
                }
            }
            
            /**
             * Getting product option values
             */
            $options = $this->_objectManager->get ( 'Magento\Catalog\Model\Product\Attribute\Repository' )->get ( $attributeCode )->getOptions ();
            $productOption = '<label class="label"><span>' . $label . '</span></label>';
            $productOption .= '<ul class="attribute-options-ul">';
            foreach ( $options as $option ) {
                $optionValue = $option->getValue ();
                
                /**
                 * Checking for product have option or not
                 */
                $checked = '';
                if (in_array ( $optionValue, $valueIndex )) {
                    $checked = 'checked';
                }
              
                if (! empty ( $optionValue )) {
                    $productOption .=  '<li><input ' . $checked . ' id="option_' . $option->getValue () . '" name="options[' . $option->getValue () . ']" value="' . $attributeCode . '"
  title="' . $attributeCode . '"
  class="attribute-options-checkbox validate-one-required-by-name" type="checkbox">';
                    $productOption .=  '<label for="option_' . $option->getValue () . '">' . $option->getLabel () . '</label></li>';
                }
            }
            $productOption .= '</ul>';
            $this->getResponse()->setBody($productOption);
            
        }
    }
}
