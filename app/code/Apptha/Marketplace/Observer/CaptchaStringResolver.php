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
namespace Apptha\Marketplace\Observer;
/**
 * This class contains functions for captcha validation
 * @author user
 *
 */
class CaptchaStringResolver {
    /**
     * Get Captcha String
     *
     * @param \Magento\Framework\App\RequestInterface $request            
     * @param string $formId            
     * @return string
     */
    public function resolve(\Magento\Framework\App\RequestInterface $request, $formId) {
        /**
         * Assign captcha params
         */
        $captchaParams = $request->getPost ( \Magento\Captcha\Helper\Data::INPUT_NAME_FIELD_VALUE );
        
        /**
         * Return captcha string
         */
        return isset ( $captchaParams [$formId] ) ? $captchaParams [$formId] : '';
    }
}