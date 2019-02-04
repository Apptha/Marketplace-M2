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
namespace Apptha\Vacationmode\Model;

class VacationmodeFactory {
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager            
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_objectManager = $objectManager;
    }
    
    /**
     * Create new country model
     *
     * @param array $arguments            
     * @return \Magento\Directory\Model\Country
     */
    public function create(array $arguments = []) {
        return $this->_objectManager->create ( 'Apptha\Vacationmode\Model\Vacationmode', $arguments, false );
    }
}