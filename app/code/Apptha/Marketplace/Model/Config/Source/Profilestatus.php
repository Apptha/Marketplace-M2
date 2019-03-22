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
 * */
namespace Apptha\Marketplace\Model\Config\Source;

/**
 * This class contains seller subscription status functions
 */
class Profilestatus implements \Magento\Framework\Option\ArrayInterface {
    const PENDING = 0;
    const ACTIVE = 1;
    const COMPLETE = 2;
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::PENDING => __ ( 'Pending' ),
                static::ACTIVE => __ ( 'Active' ),
                static::COMPLETE => __ ( 'Complete' ) 
        ];
    }
}
