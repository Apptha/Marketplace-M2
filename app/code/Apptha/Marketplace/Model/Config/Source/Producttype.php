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
namespace Apptha\Marketplace\Model\Config\Source;

/**
 * This class contains product type functions
 */
class Producttype implements \Magento\Framework\Option\ArrayInterface {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return [
                [
                        'value' => 'simple',
                        'label' => __ ( 'Simple' )
                ],
                [
                        'value' => 'virtual',
                        'label' => __ ( 'Virtual' )
                ],
                [
                        'value' => 'configurable',
                        'label' => __ ( 'Configurable' )
                ] ,
                [
                        'value' => 'downloadable',
                        'label' => __ ( 'Downloadable' )
                ]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return [
                0 => __ ( 'Simple' ),
                1 => __ ( 'Virtual' ),
                2 => __ ( 'Configurable' ),
3=>__('Downloadable')
        ];
    }
}
