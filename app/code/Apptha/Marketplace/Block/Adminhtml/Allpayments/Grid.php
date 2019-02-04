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
namespace Apptha\Marketplace\Block\Adminhtml\Allpayments;
/**
 * Class For Seller payments grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    /**
     * @var \Magento\Framework\Module\Manager
     * @var \Apptha\Grid\Model\Status
     */
    protected $moduleObj;
    protected $status;
   /**
    * Construct function 
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Backend\Helper\Data $backendHelper
    * @param \Apptha\Marketplace\Model\System\Config\Status $status
    * @param \Magento\Framework\Module\Manager $moduleObj
    * @param array $data
    * @return void
    */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Apptha\Marketplace\Model\System\Config\Status $status, \Magento\Framework\Module\Manager $moduleObj, array $data = []) {
        $this->status = $status;
        $this->moduleObj = $moduleObj;
        parent::__construct ( $context, $backendHelper, $data );
    }
    /**
     * Constructor function for all payments
     * @return void
     */
    protected function _construct() {
        /**
         * Call parent construct function
         */
        parent::_construct ();
        $this->setId ( 'allpaymentsGrid' );
        /**
         * Set default order
         */
        $this->setDefaultDir ( 'DESC' );
        /**
         * Set default sort by id
         */
        $this->setDefaultSort ( 'id' );
        $this->setSaveParametersInSession ( true );
        /**
         * Set use ajax equal to true
         */
        $this->setUseAjax ( true );
        $this->setVarNameFilter ( 'grid_record' );
    }
    
    /**
     * Prepare Collection
     *
     * @return object 
     */
    protected function _prepareCollection() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        $collection = $objectManager->get ( 'Apptha\Marketplace\Model\Payments' )->getCollection ();
        $collection->addFieldToFilter ( 'seller_id', array ('eq' => $sellerId) );
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        return $this;
    }    
    /**
     * Prepare columns for subscription profiles grid
     * @return object
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'id', ['header' => __ ( 'ID' ),'type' => 'int','index' => 'id'] );
        $this->addColumn ( 'invoice', ['header' => __ ( 'Invoice Id' ),'type' => 'text','index' => 'invoice'] );
        $this->addColumn ( 'created_at', ['header' => __ ( 'Created At' ),'type' => 'datetime','index' => 'created_at'] );
        $this->addColumn ( 'paid_amount', ['header' => __ ( 'Paid Amount' ),'type' => 'int','index' => 'paid_amount'] );
        $this->addColumn ( 'payment_type', ['header' => __ ( 'Payment Type' ),'type' => 'text','index' => 'payment_type'] );
        $this->addColumn ( 'comment', ['header' => __ ( 'Comment' ),'type' => 'text','index' => 'comment'] );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $options = $objectManager->get ( 'Apptha\Marketplace\Model\Config\Source\Isack' )->toOptionArray ();
        $this->addColumn ( 'is_ack', ['header' => __ ( 'Is Acknowledged' ),'type' => 'options','index' => 'is_ack','options' => $options] );
        $this->addColumn ( 'ack_at', ['header' => __ ( 'Acknowledged At' ),'type' => 'datetime','index' => 'ack_at'] );
        $block = $this->getLayout ()->getBlock ( 'grid.bottom.links' );
        if ($block) {
            $this->setChild ( 'grid.bottom.links', $block );
        }        
        return parent::_prepareColumns ();
    }
}