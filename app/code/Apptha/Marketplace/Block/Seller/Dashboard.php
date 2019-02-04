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
namespace Apptha\Marketplace\Block\Seller;
use Magento\Framework\View\Element\Template;
use Apptha\Marketplace\Model\ResourceModel\Order\Collection;
/**
 * This class used to display the products collection
 */
class Dashboard extends \Magento\Framework\View\Element\Template {
    
    /**
     * Initilize variable for product factory
     *
     * @var \Apptha\Marketplace\Model\ResourceModel\Order\Collection
     */
    protected $commissionObject;
    /**
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $loadCurrency;
    
    /**
     *
     * @param Template\Context $templateContext            
     * @param ProductFactory $productFactory            
     *
     * @param array $data            
     */
    public function __construct(Template\Context $templateContext, Collection $commissionObject, \Magento\Framework\Locale\CurrencyInterface $loadCurrency, array $data = []) {
        $this->commissionObject = $commissionObject;
        $this->loadCurrency = $loadCurrency;
        parent::__construct ( $templateContext, $data );
    }
    
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $objectManagerDashboard = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerObject = $objectManagerDashboard->get ( 'Magento\Customer\Model\Session' );
        $sellerId='';
        if ($customerObject->isLoggedIn ()) {
            $sellerId = $customerObject->getId ();
        }
        /**
         * Order collection filter by seller id
         */
        $sellerOrderCollection = $this->commissionObject->addFieldToSelect ( '*' );
        $sellerOrderCollection->addFieldToFilter ( 'seller_id', $sellerId);
         /**
         * Set order for manage order
         */
        $sellerOrderCollection->setOrder ( 'order_id', 'desc' );
        $this->setCollection ( $sellerOrderCollection );
    }
    
    /**
     * Prepare layout for seller order
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( "Seller Dashboard" ) );
        parent::_prepareLayout ();
        $pagerHtml = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.order.manage.pager' );
        $pagerHtml->setLimit ( 5 )->setShowAmounts ( false )->setCollection ( $this->getCollection () );
        $this->setChild ( 'pager', $pagerHtml );
        $this->getCollection ()->load ();
        return $this;
    }
    
    /**
     * Get product name for seller order
     *
     * @param int $orderId            
     * @param int $sellerId            
     *
     * @return array
     */
    public function getProductDetails($orderId, $getSellerId) {
        /**
         * Getting seller product ids from order
         */
        $objectManagerDashboard = \Magento\Framework\App\ObjectManager::getInstance ();
        $orderItemObject = $objectManagerDashboard->get ( 'Apptha\Marketplace\Model\Orderitems' )->getCollection ();
        $orderItemObject->addFieldToSelect ( '*' );
        $orderItemObject->addFieldToFilter ( 'order_id', $orderId );
        $orderItemObject->addFieldToFilter ( 'seller_id', $getSellerId );
        $productIds = array_unique ( $orderItemObject->getColumnValues ( 'product_id' ) );
        
        /**
         * Get seller order items
         */
        $sellerOrderObject = $objectManagerDashboard->get ( 'Magento\Sales\Model\Order' )->load ( $orderId );
        $orderProducts = $sellerOrderObject->getAllItems ();
        
        /**
         * Prepare product names
         */
        $productNames = array ();
        foreach ( $orderProducts as $product ) {
            if (in_array ( $product->getProductId (), $productIds )) {
                $productNames [] = $product->getName ();
            }
        }
        
        /**
         * Return seller product names in particualr order
         */
        return implode ( ',', $productNames );
    }
    /**
     * Get customer name and created at from sales order
     *
     * @param int $orderId            
     * @param int $sellerId            
     * @param int $customerId            
     *
     * @return array
     */
    public function getOrderDetails($orderId, $sellerId, $customerId, $flag = 0) {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection();
        $orderDatamodel->addFieldToFilter('entity_id', $orderId);
        if($flag == 1){
        $objDate = $objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        $date= $objDate->gmtDate('Y-m-d');
        $currentDate= $objDate->gmtDate('Y-m-d'. ' 00:00:00');
        $yesterDay = $objDate->gmtDate($date. ' 23:59:59');
        $orderDatamodel->addFieldToFilter('created_at', ['gteq' => $currentDate])
        ->addFieldToFilter('created_at', ['lteq' => $yesterDay]);
        $data = $orderDatamodel->getData(); 
        return isset($data[0]['grand_total']) ? $data[0]['grand_total'] : 0;
        } elseif ($flag == '2'){
            $to = date ( 'd-m-Y' );
            $toDay = date ( 'l', strtotime ( $to ) );
            /**
             * if today is monday, take last monday
             */
            if ($toDay == 'Monday') {
                $startDay = strtotime ( "-1 monday midnight" );
                $endDay = strtotime ( "yesterday" );
            } else {
                $startDay = strtotime ( "-2 monday midnight" );
                $endDay = strtotime ( "-1 sunday midnight" );
            }
            $from = date ( 'Y-m-d'. ' 00:00:00', $startDay );
            $to = date ( 'Y-m-d', $endDay );
            $fromDisplay = $from;
            $toDisplay = date ( 'Y-m-d'. ' 23:59:59', $endDay );
            $orderDatamodel->addFieldToFilter('created_at', ['gteq' => $fromDisplay])
            ->addFieldToFilter('created_at', ['lteq' => $toDisplay]);
            $data = $orderDatamodel->getData();
            return isset($data[0]['grand_total']) ? $data[0]['grand_total'] : 0;
        } elseif($flag == '3'){
            $from = date ( 'Y-m-01'. ' 00:00:00', strtotime ( 'last month' ) );
            $to = date ( 'Y-m-t', strtotime ( 'last month' ) );
            $to = date ( 'Y-m-d', strtotime ( $to . ' + 1 day' ) );
            $fromDisplay = $from;
            $toDisplay = date ( 'Y-m-t'. ' 23:59:59', strtotime ( 'last month' ) );
            $orderDatamodel->addFieldToFilter('created_at', ['gteq' => $fromDisplay])
            ->addFieldToFilter('created_at', ['lteq' => $fromDisplay]);
            $data = $orderDatamodel->getData();
            return isset($data[0]['grand_total']) ? $data[0]['grand_total'] : 0;
        } elseif($flag == '4'){
            $startDate = date ( 'Y-m-01 00:00:00', strtotime ( 'last year' ) );
            $endDate = date ( 'Y-m-t 12:59:59', strtotime ( 'last year' ) );
            $orderDatamodel->addFieldToFilter('created_at', ['gteq' => $startDate])
            ->addFieldToFilter('created_at', ['lteq' => $endDate]);
            $data = $orderDatamodel->getData();
            return isset($data[0]['grand_total']) ? $data[0]['grand_total'] : 0;
        }
    }
    
    /**
     * Get pager for seller orders
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $currencysymbol = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        return $currencysymbol->getStore()->getCurrentCurrencyCode();
    }
    
    /**
     * Get currency symbol by code
     *
     * @param string $currencyCode            
     *
     * @return string
     */
    public function getCurrencySymbol($currencyCode) {
        return $this->loadCurrency->getCurrency ( $currencyCode )->getSymbol ();
    }
    
    /**
     * Function to get seller product info
     *
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     */
    public function getTodayOrder($orderId, $sellerId, $customerId , $flag) {
        return $this->getOrderDetails($orderId, $sellerId, $customerId, $flag);
    }
    
    /**
     * Get Last week income
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     *
     */
    function getLastWeekIncome($orderId, $sellerId, $customerId, $flag) {
        return $this->getOrderDetails($orderId, $sellerId, $customerId, $flag);
    }
    
    /**
     * Get Last month income
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     *
     */
    function getLastMonthIncome($orderId, $sellerId, $customerId, $flag) {
        return $this->getOrderDetails($orderId, $sellerId, $customerId, $flag);
        
    }
    
    /**
     * Get Last year income
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     */
    function getLastYearIncome($orderId, $sellerId, $customerId, $flag) {
        return $this->getOrderDetails($orderId, $sellerId, $customerId, $flag);
    }
}