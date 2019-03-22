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
namespace Apptha\Vacationmode\Controller\Seller;

class Save extends \Magento\Framework\App\Action\Action {
    
    /**
     * Function to save vacation details
     *
     * @return object
     */
    
    public function execute() {
        $data = $this->getRequest ()->getPostValue ();
        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $session = $ObjectManager->get ( 'Magento\Customer\Model\Session' );
        
        unset ( $data ['save_profile'] );
        $data ['from_date'] = date ( 'Y-m-d', strtotime ( $data ['from_date'] ) );
        $data ['to_date'] = date ( 'Y-m-d', strtotime ( $data ['to_date'] ) );
        $data ['seller_id'] = $session->getId ();
        $resultRedirect = $this->resultRedirectFactory->create ();
        $vacationCollection = $ObjectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->getCollection ();
        $count = count ( $vacationCollection );
        if ($data ['vacation_id']) {
            foreach ( $vacationCollection as $vacationCollection ) {
                
                if ($data ['seller_id'] == $vacationCollection->getSellerId ()) {
                    $vacationCollection = $ObjectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->load ( $vacationCollection->getId () );
                    $vacationCollection = $vacationCollection->setData ( $data );
                    $vacationCollection->save ();
                    $this->messageManager->addSuccess ( __ ( 'The Vacation mode has been updated.' ) );
                    return $resultRedirect->setPath ( 'vacationmode/seller/vacationmode/' );
                }
            }
        } else {
            unset ( $data ['vacation_id'] );
            $vacationCollection = $ObjectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' );
            $vacationCollection->setData ( $data );
            $vacationCollection->save ();
            $this->messageManager->addSuccess ( __ ( 'The Vacationmode has been saved.' ) );
            return $resultRedirect->setPath ( 'vacationmode/seller/vacationmode/' );
        }
    }
}