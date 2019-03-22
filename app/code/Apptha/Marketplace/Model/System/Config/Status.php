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
namespace Apptha\Marketplace\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * This class contains seller Status functions
 */
class Status implements ArrayInterface {
    const ENABLED = 1;
    const DISABLED = 0;
    
    /**
     * Function to get Options
     * 
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::ENABLED => __ ( 'Approved' ),
                static::DISABLED => __ ( 'Disapproved' ) 
        ];
    }
}
