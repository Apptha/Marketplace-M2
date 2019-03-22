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
namespace Apptha\Marketplace\Block\Adminhtml\Subscriptionprofiles;

/**
 * Class For Manage Products Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     *
     * @var \Apptha\Grid\Model\Status
     */
    protected $_status;
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context            
     * @param \Magento\Backend\Helper\Data $backendHelper
     *            @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Apptha\Marketplace\Model\System\Config\Status $status, \Magento\Framework\Module\Manager $moduleManager, array $data = []) {
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct ( $context, $backendHelper, $data );
    }
    
    /**
     * Prepare Collection
     * 
     * @return $this
     */
    protected function _prepareCollection() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $collection = $objectManager->get ( 'Apptha\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        return $this;
    }
    
    /**
     * constructor function
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'subscriptionprofilesGrid' );
        $this->setDefaultSort ( 'id' );
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
        $this->setUseAjax ( true );
        $this->setVarNameFilter ( 'grid_record' );
    }
    
    /**
     * Prepare columns for subscription profiles grid
     * @return object
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'id', ['header' => __ ( 'ID' ),'type' => 'int','index' => 'id'] );
        $this->addColumn ( 'created_at', ['header' => __ ( 'Created At' ),'type' => 'datetime','index' => 'created_at'] );
        $this->addColumn ( 'email', array ('header' => __ ( 'Seller Email' ),'index' => 'seller_id','type' => 'email',
        'renderer' => '\Apptha\Marketplace\Block\Adminhtml\Subscriptionprofiles\Grid\Renderer\SellerEmail',
        'filter' => false,'sortable' => false) );
        $this->addColumn ( 'plan_name', ['header' => __ ( 'Plan Name' ),'type' => 'text','index' => 'plan_name'] );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $options = $objectManager->get ( 'Apptha\Marketplace\Model\Config\Source\Periodtype' )->toOptionArray ();
        $this->addColumn ( 'subscription_period_type', ['header' => __ ( 'Subscription Period Type' ),'type' => 'options',
        'index' => 'subscription_period_type','options' => $options] );        
        $this->addColumn ( 'period_frequency', ['header' => __ ( 'Period Frequency' ),'type' => 'int','index' => 'period_frequency'] );
        $this->addColumn ( 'max_product_count', ['header' => __ ( 'Max Product Count' ),'type' => 'text','index' => 'max_product_count'] );
        $this->addColumn ( 'fee', ['header' => __ ( 'Fee' ),'type' => 'int','index' => 'fee'] );
        $this->addColumn ( 'invoice', ['header' => __ ( 'Invoice Id' ),'type' => 'text','index' => 'invoice'] );
        $this->addColumn ( 'started_at', ['header' => __ ( 'Started At' ),'type' => 'datetime','index' => 'started_at'] );
        $this->addColumn ( 'ended_at', ['header' => __ ( 'End At' ),'type' => 'datetime','index' => 'ended_at'] );
        $options = $objectManager->get ( 'Apptha\Marketplace\Model\Config\Source\Profilestatus' )->toOptionArray ();
        $this->addColumn ( 'status', ['header' => __ ( 'Status' ),'type' => 'options','index' => 'status','options' => $options] );
        $block = $this->getLayout ()->getBlock ( 'grid.bottom.links' );
        if ($block) {
            $this->setChild ( 'grid.bottom.links', $block );
        }        
        return parent::_prepareColumns ();
    }
}