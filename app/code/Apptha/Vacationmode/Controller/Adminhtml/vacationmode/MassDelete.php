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

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action {
    /**
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute() {
        $itemIds = $this->getRequest ()->getParam ( 'vacationmode' );
        if (! is_array ( $itemIds ) || empty ( $itemIds )) {
            $this->messageManager->addError ( __ ( 'Please select item(s).' ) );
        } else {
            try {
                foreach ( $itemIds as $itemId ) {
                    $post = $this->_objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->load ( $itemId );
                    $post->delete ();
                }
                $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) have been deleted.', count ( $itemIds ) ) );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        return $this->resultRedirectFactory->create ()->setPath ( 'vacationmode/*/index' );
    }
}