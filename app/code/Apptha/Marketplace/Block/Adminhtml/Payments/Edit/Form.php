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
 * @package    Apptha_Marketplace
 * @version     1.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
namespace Apptha\Marketplace\Block\Adminhtml\Payments\Edit;
use Magento\Backend\Block\Widget\Form\Generic;
/**
 * This class used for form container for payments
 */
class Form extends Generic {
    /**
     * Function to prepare form
     * @return object
     */
    protected function _prepareForm() {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create ( [ 
                'data' => [ 
                        'id' => 'edit_form',
                        'action' => $this->getData ( 'action' ),
                        'method' => 'post' 
                ] 
        ] );
        /**
         * Set form container
         */
        $form->setUseContainer ( true );
        /**
         * Set form
         */
        $this->setForm ( $form );
        /**
         * Return prepare form function
         */
        return parent::_prepareForm ();
    }
}