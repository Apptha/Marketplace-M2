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
namespace Apptha\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Apptha\Marketplace\Helper\Data;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * This class blocks seller to add product to wishlist
 */
class Wishlist implements ObserverInterface {
    /**
     *
     * @var $request
     * @var $_redirect
     * @var $_response
     * @var $urlinterface
     *
     */
    protected $request;
    protected $_redirect;
    protected $_response;
    protected $urlinterface;
    
    /**
     * Constructor
     * 
     * @param Data $marketplaceData       
     * @return void     
     */
    public function __construct(\Magento\Framework\Message\ManagerInterface $messagemanager, \Magento\Framework\App\Request\Http $request, RedirectInterface $redirect, ActionFlag $actionFlag, \Magento\Framework\UrlInterface $urlinterface, ResponseInterface $response) {
        $this->messagemanager = $messagemanager;
        $this->_request = $request;
        $this->_actionFlag = $actionFlag;
        $this->_redirect = $redirect;
        $this->_response = $response;
        $this->urlinterface = $urlinterface;
    }
    /**
     * Function to check seller product or not
     *
     * @see \Apptha\Marketplace\Event\ObserverInterface::execute()
     * @return void
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * To getting full action name by http request
         */
        $handle = $this->_request->getFullActionName ();
        /**
         * To create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get Current Url
         * 
         * @var unknown
         */
        $url = $this->urlinterface->getCurrentUrl ();
        /**
         * Checking handle for wishlist_index_add or not
         */
        if ($handle == "wishlist_index_add") {
            /**
             * Get product id from query string
             */
            $productId = $observer->getRequest ()->getParam ( 'product' );
            /**
             * Get product data
             */
            $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            /**
             * Set seller id from product
             */
            $productSellerId = $product->getSellerId ();
            /**
             * Get logged in customer details
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = '';
            /**
             * Get customer id
             */
            if ($customerSession->isLoggedIn ()) {
                $customerId = $customerSession->getId ();
            }
            /**
             * Checking product id and logged in id are same
             */
            if ($productSellerId == $customerId) {
                /**
                 * Set action flag
                 */
                $this->_actionFlag->set ( '', Action::FLAG_NO_DISPATCH, true );
                /**
                 * Set session error message
                 */
                $this->messagemanager->addError ( __("Seller can't add their own product") );
                $this->_redirect->redirect ( $this->_response, $url );
            }
        }
    }
}