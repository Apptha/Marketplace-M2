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
namespace Apptha\Marketplace\Controller\Product;

/**
 * This class contains Product Delete Functions
 */
class Delete extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var object $dataHelper
     * @var object $messageManager
     */
    protected $dataHelper;
    protected $messageManager;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Apptha\Marketplace\Helper\Data $dataHelper            
     * @param \Magento\Framework\Message\ManagerInterface $messageManager            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Apptha\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    /**
     * Load product delete product page
     *
     * @return void
     */
    public function execute() {
        
        /**
         * Setting delete flag
         */
        $deleteFlag = 0;
        /**
         * Getting product id from query string
         */
        $productId = $this->getRequest ()->getParam ( 'product_id' );
        /**
         * Getting delete product flag
         */
        $deleteFlag = $this->sellerProductDelete ( $productId );
        /**
         * Checking whether delete flag value equal to one or not
         */
        if ($deleteFlag == 1) {
            /**
             * Setting success session message
             */
            $this->messageManager->addSuccess ( __ ( 'The product has been deleted successfully.' ) );
        } else {
            /**
             * Setting error session message
             */
            $this->messageManager->addError ( __ ( 'You dont have access to delete this product.' ) );
        }
        /**
         * Redirect to manage page
         */
        $this->_redirect ( '*/*/manage' );
    }
    /**
     * Function to delete seller products
     *
     * @return boolean
     */
    public function sellerProductDelete($productId) {
        /**
         * Setting delete flag
         */
        $deleteFlag = 0;
       
        /**
         * Getting logged in customer object
         */
        $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customer->getId ();
        /**
         * Get product object
         */
        $productObject = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $productObjectSellerId = $productObject->getSellerId ();
        /**
         * Checking whether seller id is equal to product seller id
         */
        if ($sellerId == $productObjectSellerId) {
            /**
             * To set register for secure area
             */
            $this->_objectManager->get ( 'Magento\Framework\Registry' )->register ( 'isSecureArea', true );
            /**
             * To delete product
             */
            $productObject->delete ();
            /**
             * To unregister for secure area
             */
            $this->_objectManager->get ( 'Magento\Framework\Registry' )->unregister ( 'isSecureArea' );
            /**
             * Set delete flag
             */
            $deleteFlag = 1;
        }
        /**
         * To return delete flag
         */
        return $deleteFlag;
    }
}