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
namespace Apptha\Vacationmode\Controller\Adminhtml\vacationmode;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class ExportExcel extends \Magento\Backend\App\Action {
    protected $_fileFactory;
    
    /**
     * Function to export vacation details as excel file
     *
     * @return object
     */
    
    public function execute() {
        $this->_view->loadLayout ( false );
        
        $fileName = 'vacationmode.xml';
        
        $exportBlock = $this->_view->getLayout ()->createBlock ( 'Apptha\Vacationmode\Block\Adminhtml\Vacationmode\Grid' );
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        
        $this->_fileFactory = $objectManager->create ( 'Magento\Framework\App\Response\Http\FileFactory' );
        
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile(),
            DirectoryList::VAR_DIR
        );
    }
}