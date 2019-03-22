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

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     *
     * @var \Apptha\Vacationmode\Model\vacationmodeFactory
     */
    protected $_vacationmodeFactory;

    /**
     *
     * @var \Apptha\Vacationmode\Model\Status
     */
    protected $_status;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Apptha\Vacationmode\Model\vacationmodeFactory $vacationmodeFactory
     * @param \Apptha\Vacationmode\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *            @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Apptha\Vacationmode\Model\VacationmodeFactory $VacationmodeFactory, \Apptha\Vacationmode\Model\Status $status, \Magento\Framework\Module\Manager $moduleManager, array $data = []) {
        $this->_vacationmodeFactory = $VacationmodeFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct ( $context, $backendHelper, $data );
    }

    /**
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'postGrid' );
        $this->setDefaultSort ( 'vacation_id' );
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
        $this->setUseAjax ( false );
        $this->setVarNameFilter ( 'post_filter' );
    }

    /**
     *
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_vacationmodeFactory->create ()->getCollection ();
        $this->setCollection ( $collection );

        parent::_prepareCollection ();

        return $this;
    }

    /**
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'vacation_id', [
                'header' => __ ( 'ID' ),
                'type' => 'text',
                'index' => 'vacation_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
        ] );

        $this->addColumn ( 'from_date', [
                'header' => __ ( 'From Date' ),
                'index' => 'from_date',
                'type' => 'datetime'
        ] );

        $this->addColumn ( 'to_date', [
                'header' => __ ( 'To Date' ),
                'index' => 'to_date',
                'type' => 'datetime'
        ] );

        $this->addColumn ( 'seller_id', [
                'header' => __ ( 'Seller Id' ),
                'index' => 'seller_id'
        ] );

        $this->addColumn ( 'seller_name', [
                'header' => __ ( 'Seller Name' ),
                'renderer' => '\Apptha\Vacationmode\Block\Adminhtml\Vacationmode\Renderer\Seller',
                'index' => 'seller_id',
                'filter' => false
        ] );

        $this->addColumn ( 'store_name', [
                'header' => __ ( 'Store Name' ),
                'renderer' => '\Apptha\Vacationmode\Block\Adminhtml\Vacationmode\Renderer\Store',
                'index' => 'seller_id',
                'filter' => false
        ] );

        $options =
                array(
                        "0"    => "Select Option",
                        "1"    => "Enabled",
                        "2"    => "Disabled"
                );

        $this->addColumn ( 'vacation_status', [
                'header' => __ ( 'Status' ),
                'index' => 'vacation_status',
                'renderer' => '\Apptha\Vacationmode\Block\Adminhtml\Vacationmode\Renderer\Status',
                'type' => 'options',
                'options' => $options
        ] );

        $this->addExportType ( $this->getUrl ( 'vacationmode/*/exportCsv', [
                '_current' => true
        ] ), __ ( 'CSV' ) );
        $this->addExportType ( $this->getUrl ( 'vacationmode/*/exportExcel', [
                '_current' => true
        ] ), __ ( 'Excel XML' ) );

        $block = $this->getLayout ()->getBlock ( 'grid.bottom.links' );
        if ($block) {
            $this->setChild ( 'grid.bottom.links', $block );
        }

        return parent::_prepareColumns ();
    }

    /**
     *
     * @return $this
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'vacation_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'vacationmode' );
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl ( 'vacationmode/*/index', [
                '_current' => true
        ] );
    }
}