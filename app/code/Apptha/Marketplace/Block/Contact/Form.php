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
namespace Apptha\Marketplace\Block\Contact;

use Magento\Framework\View\Element\Template;

/**
 * This class used to display the contact admin form for seller
 */
class Form extends \Magento\Directory\Block\Data {
    
    /**
     * Prepare layout for contact form
     *
     * @return \Magento\Framework\View\Element\AbstractBlock::_prepareLayout()
     */
    public function _prepareLayout() {
        /**
         * Set page title
         */
        $this->pageConfig->getTitle ()->set ( __ ( "Contact Admin" ) );
        /**
         * Call prepare layout
         */
        return parent::_prepareLayout ();
    }
}