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
/**
 * This class used to display the products collection
 */
class Managesellers extends \Magento\Framework\View\Element\Html\Link {
    /**
     * Function to Get Href for Top Link
     *
     * @return string
     */
    public function getHref() {
        /**
         * Checking whether customer logged in or not
         */
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $helper = $objectGroupManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $moduleEnabledOrNot = $helper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            return $this->getUrl ( 'marketplace/seller/allseller' );
        }
    }
    
    /**
     * Function to Get Label on Top Link
     *
     * @return string
     */
    public function getLabel() {
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $helper = $objectGroupManager->get ( 'Apptha\Marketplace\Helper\Data' );
        $moduleEnabledOrNot = $helper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            return __ ( 'Our Sellers' );
        }
    }
}