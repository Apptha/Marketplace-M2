<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apptha\Marketplace\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

abstract class Configuration extends \Magento\Framework\App\Action\Action
{
  
   /**
    * @var customerSession
    */
    
    protected $customerSession;
    
    /**
     * @var StoreManagerInterface
     */
    
    protected $storeManager;
    
    /**
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager
        ) {
            $this->customerSession = $customerSession;
            $this->resultPageFactory = $resultPageFactory;
            $this->storeManager = $storeManager;
            parent::__construct($context);
    }
}
