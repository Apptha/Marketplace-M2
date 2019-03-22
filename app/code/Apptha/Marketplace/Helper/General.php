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
namespace Apptha\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains manipulation functions
 */
class General extends AbstractHelper {
    /**
     * To validate image file from downloadable data
     * @param object $uploader, int $imgSize, int $validateFlag
     * @return void
     */
    public function getImageValidation($uploader, $imgSize, $validateFlag) {
        $uploaderArray = array ();
        if (! $imgSize) {
            $uploader->setFilesDispersion ( true );
            $validateFlag = 1;
        }
        $uploaderArray ['uploader'] = $uploader;
        $uploaderArray ['validate_flag'] = $validateFlag;
        return $uploaderArray;
    }

    /**
     * Assign bulk uploaded downloadable date to product
     * @param int $downloadProductId, int $store
     * @return void
     */
    public function assignDataForDownloadableProduct($downloadProductId, $store, $downloadableData) {

        if (isset ( $downloadProductId ) && isset ( $store )) {
            $this->addDownloadableProductData ( $downloadProductId, $store, $downloadableData);
        }

    }
    /**
     * Adding bulk uploaded downloadable data to product
     * @param int $downloadProductId, int $store
     * @return void
     */
    public function addDownloadableProductData($downloadProductId, $store, $downloadableData) {

        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $downloadModel = $objectGroupManager->get ( 'Apptha\Marketplace\Model\Download');
        $downloadSample = $objectGroupManager->get ( 'Magento\Downloadable\Model\Sample');
        $downloadLink = $objectGroupManager->get ( 'Magento\Downloadable\Model\Link');

        /**
         * Initilize downloadable product sample and link files
         */
        $sampleTpath = $linkTpath = $slinkTpath = array ();


        foreach ( $_FILES as $key => $result ) {
            $downloadData = $downloadModel->prepareDownloadProductData ( $_FILES, $key, $result );
            if (! empty ( $downloadData ['sample_tpath'] )) {
                $sampleNo = substr ( $key, 7 );
                $sampleTpath [$sampleNo] = $downloadData ['sample_tpath'];
            }
            if (! empty ( $downloadData ['link_tpath'] )) {
                $sampleNo = substr ( $key, 6 );
                $linkTpath [$sampleNo] = $downloadData ['link_tpath'];
            }
            if (! empty ( $downloadData ['slink_tpath'] )) {
                $sampleNo = substr ( $key, 9 );
                $slinkTpath [$sampleNo] = $downloadData ['slink_tpath'];
            }
        }
        /**
         * Getting downloadable product sample collection
         */
        $downloadableSample = $downloadSample->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );
        $downloadModel->deleteDownloadableSample ( $downloadableSample );

        /**
         * Getting downloadable product link collection
         */
        $downloadableLink = $downloadLink->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );
        $downloadModel->deleteDownloadableLinks ( $downloadableLink );



        try {
            /**
             * Storing Downloadable product sample data and link data
             */
            $downloadModel->saveDownLoadProductSample ( $downloadableData, $downloadProductId, $sampleTpath, $store );
            if (isset ( $downloadableData ['link'] )) {
                $downloadModel->saveDownLoadProductLink ( $downloadableData, $downloadProductId, $linkTpath, $slinkTpath, $store );
            }
        } catch ( Exception $e ) {
            $this->messageManager->addError ( __ ( 'Error.' ) );
        }
    }
}