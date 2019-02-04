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
namespace Apptha\Vacationmode\Block\Adminhtml;

class Vacationmode extends \Magento\Backend\Block\Widget\Container {
    /**
     *
     * @var string
     */
    protected $_template = 'vacationmode/vacationmode.phtml';
    
    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context            
     * @param array $data            
     */
    public function __construct(\Magento\Backend\Block\Widget\Context $context, array $data = []) {
        parent::__construct ( $context, $data );
    }
    
    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout() {        
        $this->setChild ( 'grid', $this->getLayout ()->createBlock ( 'Apptha\Vacationmode\Block\Adminhtml\Vacationmode\Grid', 'apptha.vacationmode.grid' ) );
        return parent::_prepareLayout ();
    }
    
    
    /**
     *
     * @param string $type            
     * @return string
     */
    protected function _getCreateUrl() {
        return $this->getUrl ( 'vacationmode/*/new' );
    }
    
    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml() {
        return $this->getChildHtml ( 'grid' );
    }
}