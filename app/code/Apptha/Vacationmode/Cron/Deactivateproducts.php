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
namespace Apptha\Vacationmode\Cron;

use Magento\Catalog\Model\ResourceModel\Product\Action;

/**
 * This class contains functionality of deactive subscription profiles
 */
class Deactivateproducts {
    protected $logger;
    protected $date;
    protected $action;
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;
    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $_indexerCollectionFactory;
    /**
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param Action $action
     */
    
    public function __construct(\Psr\Log\LoggerInterface $logger, \Magento\Framework\Stdlib\DateTime\DateTime $date, Action $action, \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory) {
            $this->logger = $logger;
            $this->date = $date;
            $this->action = $action;
            $this->_indexerFactory = $indexerFactory;
            $this->_indexerCollectionFactory = $indexerCollectionFactory;
    }
    
    /**
     * Deactive subscription profiles
     */
    public function execute() {
        
        $date = (new \DateTime ())->format ( 'Y-m-d' );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $vacationCollection = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->getCollection ();
        $vacationCollection = $vacationCollection->addFieldToFilter ( 'vacation_status', '0' )->addFieldToFilter ( 'from_date', [
            'lteq' => $date
        ] )->addFieldToFilter ( 'to_date', [
            'gteq' => $date
        ] );
        
        $storeManager = $objectManager->get ( '\Magento\Store\Model\StoreManagerInterface' );
        $storeId = $storeManager->getStore ()->getStoreId ();
        foreach ( $vacationCollection as $vacationCollection ) {
            $productCollectionFactory = $objectManager->get ( '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory' );
            $productCollectionFactory = $productCollectionFactory->create ();
            $productCollectionFactory->addAttributeToSelect ( '*' )->addFieldToFilter ( 'seller_id', $vacationCollection ['seller_id'] );
            foreach ( $productCollectionFactory as $products ) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $storeManager = $objectManager->get ( '\Magento\Store\Model\StoreManagerInterface' );
                $storeId = $storeManager->getStore ()->getStoreId ();
                $this->action->updateAttributes ( [
                    $products->getEntityId ()
                ], [
                    'status' => 2
                ], $storeId );
            }
            if($vacationCollection ['mail_status'] == 0){
                $flag = '1';
                $vacation = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->load ( $vacationCollection ['vacation_id'] );
                $vacation->setMailStatus ( '1' );
                $vacation->save ();
                $this->sendNotificationMailToSeller ( $vacationCollection,$flag );
            }
        }
        $vacationCollection = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->getCollection ();
        $vacationCollection = $vacationCollection->addFieldToFilter ( 'vacation_status', '0' )->addFieldToFilter ( 'mail_status', '1' )->addFieldToFilter ( 'to_date', [
            'lt' => $date
        ] );
        
        foreach ( $vacationCollection as $vacationCollection ) {
            $productCollectionFactory = $objectManager->get ( '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory' );
            $productCollectionFactory = $productCollectionFactory->create ();
            $productCollectionFactory->addAttributeToSelect ( '*' )->addFieldToFilter ( 'seller_id', $vacationCollection ['seller_id'] );
            foreach ( $productCollectionFactory as $products ) {
                $this->action->updateAttributes ( [
                    $products->getEntityId ()
                ], [
                    'status' => 1
                ], $storeId );
            }
            if($vacationCollection ['mail_status'] == '1'){
                $flag = '2';
                $vacation = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->load ( $vacationCollection ['vacation_id'] );
                $vacation->setVacationStatus ( '2' );
                $vacation->setMailStatus ( '' );
                $vacation->save ();
                $this->sendNotificationMailToSeller ( $vacationCollection,$flag );
            }
        }
        
        $vacationCollections = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->getCollection ();
        $vacationCollections = $vacationCollections->addFieldToFilter ( 'vacation_status', '2' )->addFieldToFilter ( 'mail_status', '1' )->addFieldToFilter ( 'from_date', [
            'lteq' => $date
        ] )->addFieldToFilter ( 'to_date', [
            'gteq' => $date
        ] );
        foreach ( $vacationCollections as $vacationCollections ) {
            $productCollectionFactory = $objectManager->get ( '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory' );
            $productCollectionFactory = $productCollectionFactory->create ();
            $productCollectionFactory->addAttributeToSelect ( '*' )->addFieldToFilter ( 'seller_id', $vacationCollections ['seller_id'] );
            foreach ( $productCollectionFactory as $products ) {
                $this->action->updateAttributes ( [
                    $products->getEntityId ()
                ], [
                    'status' => 1
                ], $storeId );
            }
            if($vacationCollections ['mail_status'] == '1'){
                $flag = '3';
                $vacation = $objectManager->get ( 'Apptha\Vacationmode\Model\Vacationmode' )->load ( $vacationCollections ['vacation_id'] );
                $vacation->setMailStatus ( '' );
                $vacation->save ();
                $this->sendNotificationMailToSeller ( $vacationCollections,$flag );
            }
        }
        
        $indexerCollection = $this->_indexerCollectionFactory->create();
        $ids = $indexerCollection->getAllIds();
        foreach ($ids as $id) {
            $idx = $this->_indexerFactory->create()->load($id);
            $idx->reindexAll($id);
        }
    }
    /**
     * Send notification mail to seller
     *
     * @param object $subscriptionProfile
     * @return void
     */
    public function sendNotificationMailToSeller($vacationCollection,$flag) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Assign seller id
         */
        $sellerId = $vacationCollection->getSellerId ();
        
        /**
         * Get admin details
         */
        $admin = $objectManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        
        /**
         * Get seller details
         */
        $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerId );
        
        /**
         * Assign receiver info
         */
        $receiverInfo = [
            'name' => $customer->getName (),
            'email' => $customer->getEmail ()
        ];
        
        /**
         * Assign sender info
         */
        $senderInfo = [
            'name' => $adminName,
            'email' => $adminEmail
        ];
        
        /**
         * Declare email template
         */
        $emailTempVariables = array ();
        /**
         * Assign email template
         */
        if($flag == '1'){
            $emailTempVariables ['displaymessage'] =  ( __ ( 'Vacation mode is enabled and products are disabled.'));
        } else if($flag == '2'){
            $emailTempVariables ['displaymessage'] =  ( __ ( 'Vacation mode is disabled and products are enabled.'));
        }else if($flag == '3'){
            $emailTempVariables ['displaymessage'] =  ( __ ( 'Vacation mode is disabled and products are enabled.'));
        }
        $emailTempVariables ['adminname'] = $adminName;
        $emailTempVariables ['sellername'] = $customer->getName ();
        $emailTempVariables ['message'] = $vacationCollection->getVacationMessage ();
        $emailTempVariables ['fromdate'] = $vacationCollection->getFromDate ();
        $emailTempVariables ['todate'] = $vacationCollection->getToDate ();
        
        /**
         * Assign template id
         */
        $templateId = 'vacationstatus_seller_vacation_mode_template';
        /**
         * Send subsciption expired notification to seller
         */
        $objectManager->get ( 'Apptha\Vacationmode\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
    }
}