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

class Status {
    /**
     * #@+
     * Status values
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray() {
        return [ 
                self::STATUS_ENABLED => __ ( 'Enabled' ),
                self::STATUS_DISABLED => __ ( 'Disabled' ) 
        ];
    }
    
    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions() {
        $result = [ ];
        
        foreach ( self::getOptionArray () as $index => $value ) {
            $result [] = [ 
                    'value' => $index,
                    'label' => $value 
            ];
        }
        
        return $result;
    }
    
    /**
     * Retrieve option text by option value
     *
     * @param string $optionId            
     * @return string
     */
    public function getOptionText($optionId) {
        $options = self::getOptionArray ();
        
        return isset ( $options [$optionId] ) ? $options [$optionId] : null;
    }
}