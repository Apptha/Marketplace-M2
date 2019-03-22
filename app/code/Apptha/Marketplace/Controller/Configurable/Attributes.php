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
 * This class used to get configurable attribute block
 */
class Attributes extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    
    /**
     * To create configurable select attribute block
     *
     * @return void
     */
    public function execute() {
        /**
         * Get Attribute set id and current product id
         */
        $attributeSetId = $this->getRequest ()->getParam ( 'attribute_set_id' );
        $currentProductId = $this->getRequest ()->getParam ( 'current_product_id' );
        
        /**
         * Prepare current product attributes
         */
        $selectedAttributes = array ();
        if ($currentProductId) {
            $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $currentProductId );
            if ($product->getTypeId () == 'configurable') {
                $configurableAttributes = $product->getTypeInstance ( true )->getConfigurableAttributesAsArray ( $product );
                foreach ( $configurableAttributes as $configurableAttribute ) {
                    $selectedAttributes [] = $configurableAttribute ['attribute_id'];
                }
            }
        }
        
        /**
         * Product Types
         */
        $types = [ 
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE 
        ];
        
        $attributes = $this->_objectManager->get ( 'Magento\ConfigurableProduct\Model\ConfigurableAttributeHandler' )->getApplicableAttributes ();
        $attributes->addFieldToFilter ( 'entity_type_id', $attributeSetId );
        
        /**
         * Prepare select attribute content
         */
        echo '<ul>';
        foreach ( $attributes as $attribute ) {
            /**
             * Checking for configurable attribute or not
             */
            if (! $attribute->getApplyTo () || count ( array_diff ( $types, $attribute->getApplyTo () ) ) === 0) {
                $checked = '';
                if (count ( $selectedAttributes ) > 0 && in_array ( $attribute->getAttributeId (), $selectedAttributes )) {
                    $checked = 'checked onclick="return false;" onkeydown="return false;"';
                }
                echo '<li><input id="' . $attribute->getAttributeCode () . '" ' . $checked . ' name="attributes[' . $attribute->getAttributeCode () . ']" value="' . $attribute->getFrontendLabel () . '"
  title="' . $attribute->getFrontendLabel () . '" 
  class="attribute-checkbox validate-one-required-by-name" type="checkbox">';
                echo '<input type="hidden" name="attribute_ids[' . $attribute->getAttributeCode () . ']" value="' . $attribute->getAttributeId () . '" />';
                
                echo '<label for="' . $attribute->getAttributeCode () . '">' . $attribute->getFrontendLabel () . '</label></li>';
            }
        }
        echo '</ul>';
    }
}