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
namespace Apptha\Marketplace\Controller\Configurable;

/**
 * This class used to configurable product image
 */
class Image extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactoryObject;
    
    /**
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    public function __construct(\Magento\Framework\View\Result\PageFactory $resultPageFactoryObject, \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\LayoutFactory $layoutFactory) {
        $this->messageManager = $context->getMessageManager();
        $this->resultPageFactoryObject = $resultPageFactoryObject;
        $this->layoutFactory = $layoutFactory;
        parent::__construct ( $context );
    }
    
    /**
     * To create configurable image, price and quantity block
     *
     * @return void
     */
    public function execute() {
        /**
         * To resolving Cannot modify header information issue
         */
        ob_start ();
        echo $this->layoutFactory->create ()->createBlock ( 'Apptha\Marketplace\Block\Product\Image' )->setTemplate ( 'product/image.phtml' )->toHtml ();
    }
}
