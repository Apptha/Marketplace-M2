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
class Mostviewed extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productsFactory;
    protected $productRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
            \Magento\Catalog\Model\ProductRepository $productRepository,
            array $data = []
            ) {
                $this->_productsFactory = $productsFactory;
                $this->productRepository = $productRepository;
                parent::__construct($context, $data);
    }

    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $currentStoreId = $this->_storeManager->getStore()->getId();

        $collection = $this->_productsFactory->create()
        ->addAttributeToSelect(
                '*'
                )->addViewsCount()->setStoreId(
                        $currentStoreId
                        )->addStoreFilter(
                                $currentStoreId
                                );
                        $items = $collection->getItems();
                        $this->setCollection ( $items);
    }
    /**
     * Getting most viewed products
     */
    public function getMostViewedCollection()
    {
        $objectManagerDashboard = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerObject = $objectManagerDashboard->get ( 'Magento\Customer\Model\Session' );
        $sellerId='';
        if ($customerObject->isLoggedIn ()) {
            $sellerId = $customerObject->getId ();
        }
        $currentStoreId = $this->_storeManager->getStore()->getId();
        $collection = $this->_productsFactory->create()->addAttributeToSelect('*')->addViewsCount()->setStoreId($currentStoreId)->addStoreFilter($currentStoreId);
        $entityId= array();

        foreach($collection as $coreCollection){
            $product = $this->productRepository->getById ( $coreCollection->getEntityId());
            $sellerId = $product->getSellerId ();
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $om->get('Magento\Customer\Model\Session');
            $customerId = $customerSession->getCustomer()->getId();
            if($sellerId == $customerId){
                $entityId[] = $product->getEntityId ();
            }
        }
      return $collection->addAttributeToFilter('entity_id',array('in'=>array($entityId)));
    }

}