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

/**
 * Marketplace seller shipping
 *
 * This class contains seller shipping manipulations
 */
namespace Apptha\Marketplace\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

/**
 * This class contains the seller shipping functionality
 */
class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface {
    /**
     *
     * @var string $_code
     */
    protected $_code = 'apptha';
    protected $productRepository;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory, \Psr\Log\LoggerInterface $logger, \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory, \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,\Magento\Catalog\Api\ProductRepositoryInterface $productRepository, array $data = []) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->productRepository = $productRepository;

        parent::__construct ( $scopeConfig, $rateErrorFactory, $logger, $data );
    }

    /**
     * Get allowed methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        return [
                $this->getCarrierCode () => $this->getConfigData ( 'name' )
        ];
    }

    /**
     * To collect rates based on seller price
     *
     * @param RateRequest $request
     *
     * @return bool|Result
     */
    public function collectRates(RateRequest $request) {
        if (! $this->getConfigFlag ( 'active' )) {
            return false;
        }
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create ();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create ();

        $method->setCarrier ( $this->getCarrierCode () );
        $method->setCarrierTitle ( $this->getConfigData ( 'title' ) );

        $method->setMethod ( $this->getCarrierCode () );
        $method->setMethodTitle ( $this->getConfigData ( 'name' ) );

        $amount = $this->preparePriceForShipping ();

        $method->setPrice ( $amount );
        $method->setCost ( $amount );

        $result->append ( $method );

        return $result;
    }
    public function preparePriceForShipping() {
        /**
         * To getting quote session
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $session = $objectManager->create ( 'Magento\Checkout\Model\Session' );
        $shippingCountry = $session->getQuote ()->getShippingAddress ()->getCountry ();

        $totalShippingPrice = 0;
        $sellerPriceData = array ();
        foreach ( $session->getQuote ()->getAllItems () as $item ) {
            $itemShippingPrice = 0;
            if ($item->getIsVirtual () == 1 || $item->getParentItemId () != '') {
                continue;
            }
            $itemProductId = $item->getProductId ();
            $product = $this->productRepository->getById ( $itemProductId );
            $sellerId = $product->getSellerId ();
            if (! empty ( $sellerId )) {
                $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $isSellerShippingEnabled = $objectModelManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
                $isSellerShippingType = $objectModelManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
                if ($isSellerShippingEnabled == 1 && $isSellerShippingType == 'store') {
                    if (! array_key_exists ( $sellerId, $sellerPriceData )) {
                        $sellerData = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $sellerId );
                        $nationalShippingAmount = $sellerData->getNationalShippingAmount ();
                        $internationalShippingAmount = $sellerData->getInternationalShippingAmount ();
                        $country = $sellerData->getCountry ();
                        $sellerPriceData [$sellerId] ['national_shipping_amount'] = $nationalShippingAmount;
                        $sellerPriceData [$sellerId] ['international_shipping_amount'] = $internationalShippingAmount;
                        $sellerPriceData [$sellerId] ['country'] = $country;
                    } else {
                        $nationalShippingAmount = $sellerPriceData [$sellerId] ['national_shipping_amount'];
                        $internationalShippingAmount = $sellerPriceData [$sellerId] ['international_shipping_amount'];
                        $country = $sellerPriceData [$sellerId] ['country'];
                    }
                } else {

                    if (! array_key_exists ( $sellerId, $sellerPriceData )) {
                        $sellerData = $objectManager->get ( 'Apptha\Marketplace\Model\Seller' )->load ( $sellerId );
                        $country = $sellerData->getCountry ();
                        $sellerPriceData [$sellerId] ['country'] = $country;
                    } else {
                        $country = $sellerPriceData [$sellerId] ['country'];
                    }

                    $nationalShippingAmount = $product->getNationalShippingAmount ();
                    $internationalShippingAmount = $product->getInternationalShippingAmount ();
                }

                if ($shippingCountry == $country) {
                    $itemShippingPrice = $nationalShippingAmount;
                } else {
                    $itemShippingPrice = $internationalShippingAmount;
                }
            } else {
                $itemShippingPrice = $objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'carriers/apptha/price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
            }

            $totalShippingPrice = $totalShippingPrice + ($item->getQty () * $itemShippingPrice);
        }

        return $totalShippingPrice;
    }
}