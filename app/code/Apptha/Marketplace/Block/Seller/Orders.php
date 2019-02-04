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
class Orders extends \Magento\Framework\View\Element\Template {
    
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
    public function getOrderDetails(){
        $objectManagerDashboard = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerObject = $objectManagerDashboard->get ( 'Magento\Customer\Model\Session' );
        $sellerId='';
        if ($customerObject->isLoggedIn ()) {
            $sellerId = $customerObject->getId ();
        }
        /**
         * Order collection filter by seller id
         */
        $sellerOrderCollection = $objectManagerDashboard->create ( 'Apptha\Marketplace\Model\ResourceModel\Order\Collection' );
        $sellerOrderCollection->addFieldToFilter ( 'seller_id', $sellerId);
        return $sellerOrderCollection;
    }
}