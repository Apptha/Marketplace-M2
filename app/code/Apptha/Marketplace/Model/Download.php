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
namespace Apptha\Marketplace\Model;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * This class initiates seller model
 */
class Download extends AbstractModel {
    /**
     * Save downloadable product sample information
     * @param object $downloadableData, int $downloadProductId, int $store, string $sampleTpath
     * @return void
     */
    public function saveDownLoadProductSample($downloadableData, $downloadProductId, $sampleTpath, $store) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $downloadSample = $objectManager->get ( 'Magento\Downloadable\Model\Sample');
        $helperFile = $objectManager->get ( 'Magento\Downloadable\Helper\File');
        $helperDownload = $objectManager->get ( 'Magento\Downloadable\Helper\Download');
        $storeManager = $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface');



        /**
         * Storing Downloadable product sample data
         */
        if (isset ( $downloadableData ['sample'] )) {
            foreach ( $downloadableData ['sample'] as $sampleItem ) {
                $sampleId = '';
                $sample = array ();
                $sampleId = $sampleItem ['sample_id'];
                if (isset ( $sampleTpath [$sampleId] )) {
                    $sample [] = $sampleTpath [$sampleId];
                }
                $sampleModel = $downloadSample;
                $sampleModel->setData ( $sample )->setSampleType ( $sampleItem ['type'] )->setProductId ( $downloadProductId )->setStoreId ( 0 )->setWebsiteIds ( array (
                        $storeManager->getStore ( $store )->getWebsiteId ()
                ) )->setTitle ( $sampleItem ['title'] )->setDefaultTitle ( false )->setSortOrder ( $sampleItem ['sort_order'] );
                $sampleModel->save();
                if ($sampleItem ['type'] == 'url') {
                    $sampleModel->setSampleUrl ( $sampleItem ['sample_url'] );
                }
                if (isset ( $sampleTpath [$sampleId] ) && $sampleItem ['type'] == 'file') {
                    if ($sampleModel->getSampleType () == $helperDownload::LINK_TYPE_FILE) {

                        $sampleFileName = $helperFile->moveFileFromTmp ( $downloadSample->getBaseTmpPath (), $downloadSample->getBasePath (), $sample );
                    }
                    $sampleModel->setSampleFile ( $sampleFileName );
                } else {
                    if (isset ( $sampleItem ['sample_file'] )) {
                        $sampleFileName = $sampleItem ['sample_file'];
                        $sampleModel->setSampleFile ( $sampleFileName );
                    }
                }
                $sampleModel->save();


            }
        }
    }

    /**
     * Save download product link
     *
     * @param array $downloadableData
     * @param number $downloadProductId
     * @param array $linkTpath
     * @param array $slinkTpath
     *
     * @return void
     *
     */
    public function saveDownLoadProductLink($downloadableData, $downloadProductId, $linkTpath, $slinkTpath, $store) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $downloadModel = $objectManager->get ( 'Apptha\Marketplace\Model\Download');
        $downloadLink = $objectManager->get ( 'Magento\Downloadable\Model\Link');
        $helperFile = $objectManager->get ( 'Magento\Downloadable\Helper\File');
        $storeManager = $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface');
        $jsonDecode = $objectManager->get ( 'Magento\Framework\Json\Decoder');

        /**
         * Storing Downloadable product sample data
         */
        foreach ( $downloadableData ['link'] as $linkItem ) {
            $linkId = '';
            $linkFile = $sampleFile = array ();
            $linkId = $linkItem ['link_id'];

            $linkFile = $this->assignLinkFile ( $linkTpath, $linkId, $linkFile );
            $sampleFile = $this->assignLinkFile ( $slinkTpath, $linkId, $sampleFile );

            $linkModel = $downloadLink->setData ( $linkFile )->setLinkType ( $linkItem ['type'] )->setProductId ( $downloadProductId )->setWebsiteIds ( array (
                    $storeManager->getStore ( $store )->getWebsiteId ()
            ) )->setStoreId ( 0 )->setSortOrder ( $linkItem ['sort_order'] )->setTitle ( $linkItem ['title'] )->setIsShareable ( $linkItem ['is_shareable'] );
            $linkModel->save();
            if ($linkItem ['type'] == 'url') {
                $linkModel->setLinkUrl ( $linkItem ['link_url'] );
            }
            $linkModel->setPrice ( $linkItem ['price'] );
            $linkModel->setNumberOfDownloads ( $linkItem ['number_of_downloads'] );
            if (isset ( $linkItem ['sample'] ['type'] )) {
                if ($linkItem ['sample'] ['type'] == 'url') {
                    $linkModel->setSampleUrl ( $linkItem ['sample'] ['url'] );
                }
                $linkModel->setSampleType ( $linkItem ['sample'] ['type'] );
            }

            $sampleFile = '';
            $sampleFile = $jsonDecode->decode ( json_encode ( $sampleFile ) );
            if (isset ( $linkTpath [$linkId] ) && $linkItem ['type'] == 'file') {
                $linkFileName = $helperFile->moveFileFromTmp ( $downloadLink->getBaseTmpPath (), $downloadLink->getBasePath (), $linkFile );
                $linkModel->setLinkFile ( $linkFileName );
            } else {
                if (isset ( $linkItem ['link_file'] )) {
                    $linkFileName = $linkItem ['link_file'];
                    $linkModel->setLinkFile ( $linkFileName );
                }
            }

            if (isset ( $slinkTpath [$linkId] ) && isset ( $sampleFile ) && $linkItem ['sample'] ['type'] = 'file') {
                $linkSampleFileName = $helperFile->moveFileFromTmp ( $downloadLink->getBaseSampleTmpPath (), $downloadModel->getBaseSamplePath (), $sampleFile );
                $linkModel->setSampleFile ( $linkSampleFileName );
            } else {
                if (isset ( $linkItem ['link_sample_file'] )) {
                    $linkSampleFileName = $linkItem ['link_sample_file'];
                    $linkModel->setSampleFile ( $linkSampleFileName );
                }
            }
            $linkModel->save();

        }
    }

    /**
     * Assign link file
     *
     * @param array $linkTpath
     * @param array $linkId
     * @param array $linkFile
     */
    public function assignLinkFile($linkTpath, $linkId, $linkFile) {

        if (isset ( $linkTpath [$linkId] )) {
            $linkFile [] = $linkTpath [$linkId];
        }
        return $linkFile;
    }

    /**
     * Get sample path
     *
     * @param string $type
     * @param number $key
     * @param array $sampleTpath
     * @param string $filePath
     * @param string $fileName
     * @param number $fileSize
     * @return array $sampleTpath
     */
    public function getSampleTpath($type, $filePath, $fileName, $fileSize) {
        $sampleTpath = array ();
        if ($type == 'samples') {
            $sampleTpath = array (
                    'file' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSize,
                    'status' => 'new'
            );
        }
        return $sampleTpath;
    }

    /**
     * Get link path
     *
     * @param string $type
     * @param number $key
     * @param array $linkTpath
     * @param string $filePath
     * @param string $fileName
     * @param number $fileSize
     * @return array $sampleTpath
     */
    public function getLinkTpath($type, $filePath, $fileName, $fileSize) {
        $linkTpath = array ();
        if ($type == 'links') {
            $linkTpath = array (
                    'file' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSize,
                    'status' => 'new'
            );
        }

        return $linkTpath;
    }

    /**
     * Get sample link path
     *
     * @param string $type
     * @param number $key
     * @param array $slinkTpath
     * @param string $filePath
     * @param string $fileName
     * @param number $fileSize
     * @return array $sampleTpath
     */
    public function getSLinkTpath($type, $filePath, $fileName, $fileSize) {
        $slinkTpath = array ();
        if ($type == 'link_samples') {
            $slinkTpath = array (
                    'file' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSize,
                    'status' => 'new'
            );
        }
        return $slinkTpath;
    }

    /**
     * Save full path
     *
     * @param array $result
     * @param string $tmpPath
     * @return void
     */
    public function saveFullPath($result, $tmpPath) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $storageModel = $objectManager->get ( 'Magento\MediaStorage\Helper\File\Storage\Database');
        if (isset ( $result ['file'] )) {
            $fullPath = rtrim ( $tmpPath, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . ltrim ( $result ['file'], DIRECTORY_SEPARATOR );
            $storageModel->saveFile ( $fullPath );
        }
    }

    /**
     * Prepare downloadable product data
     *
     * @param array $filesDataArray
     * @param number $key
     * @param array $result
     * @return array
     *
     */
    public function prepareDownloadProductData($filesDataArray, $key, $result) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $downloadModel = $objectManager->get ( 'Apptha\Marketplace\Model\Download');

        $generalHelper = $objectManager->get ( 'Apptha\Marketplace\Helper\General');
        $downloadData = array ();
        if ($this->checkingForFilesDataArray ( $filesDataArray, $key )) {
            $type = '';
            $tmpPathResult = $this->getTmpPathForDownloadable ( $key );
            $tmpPath = $tmpPathResult ['tmp_path'];
            $type = $tmpPathResult ['type'];
            if ($type == 'samples' || $type == 'links' || $type == 'link_samples') {
                $result = array ();
                /**
                 * Initilize validate flag
                 */
                $validateFlag = 0;
                /**
                 * Getting uploaded file extension type
                 */
                $uploaderExtension = pathinfo ( $filesDataArray [$key] ['name'], PATHINFO_EXTENSION );
                $validateImage = array (
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                );

                $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $uploader = $objectGroupManager->create ( 'Magento\MediaStorage\Model\File\Uploader', [
                                'fileId' => $key] );
                if (in_array ( $uploaderExtension, $validateImage )) {
                    $imgSize = getimagesize ( $filesDataArray [$key] ['tmp_name'] );
                    $uploaderArray = $generalHelper->getImageValidation ( $uploader, $imgSize, $validateFlag );
                    $uploader = $uploaderArray ['uploader'];
                    $validateFlag = $uploaderArray ['validate_flag'];
                }
                $validateFlag = 0;
                /**
                 * Disallow php file for downloadable product
                 */
                if ($uploaderExtension != 'php' && $validateFlag == 0) {
                    $uploader->setAllowRenameFiles ( true );
                    $result = $uploader->save ( $tmpPath );
                    $result ['tmp_name'] = str_replace ( DIRECTORY_SEPARATOR, "/", $result ['tmp_name'] );
                    $result ['path'] = str_replace ( DIRECTORY_SEPARATOR, "/", $result ['path'] );

                    $downloadModel->saveFullPath ( $result, $tmpPath );

                    $fileName = $filePath = $fileSize = '';
                    $fileName = $uploader->getUploadedFileName ();
                    $filePath = ltrim ( $result ['file'], DIRECTORY_SEPARATOR );
                    $fileSize = $result ['size'];

                    $downloadData ['sample_tpath'] = $downloadModel->getSampleTpath ( $type, $filePath, $fileName, $fileSize );
                    $downloadData ['link_tpath'] = $downloadModel->getLinkTpath ( $type, $filePath, $fileName, $fileSize );
                    $downloadData ['slink_tpath'] = $downloadModel->getSLinkTpath ( $type, $filePath, $fileName, $fileSize );
                } else {
                    $this->messageManager->addError ( __ ( 'Disallowed file type.' ) );
                }
            }
        }
        return $downloadData;
    }

    /**
     * Get temporary path for downloadable product
     *
     * @param array $key
     * @return array $tmpPathResult
     */
    public function getTmpPathForDownloadable($key) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $downloadSample = $objectManager->get ( 'Magento\Downloadable\Model\Sample');
        $downloadLink = $objectManager->get ( 'Magento\Downloadable\Model\Link');

        $mediaAbsolutePath = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList')->getPath('media');
        $type = $tmpPath = '';
        $tmpPathResult = array ();
        if (substr ( $key, 0, 5 ) == 'sampl') {
            $tmpPath = $mediaAbsolutePath.'/'.$downloadSample->getBaseTmpPath ();
            $type = 'samples';
        }
        if (substr ( $key, 0, 5 ) == 'links') {
            $tmpPath = $mediaAbsolutePath.'/'.$downloadLink->getBaseTmpPath ();
            $type = 'links';
        }
        if (substr ( $key, 0, 5 ) == 'l_sam') {
            $tmpPath = $mediaAbsolutePath.'/'.$downloadLink->getBaseSampleTmpPath ();
            $type = 'link_samples';
        }

        $tmpPathResult ['type'] = $type;
        $tmpPathResult ['tmp_path'] = $tmpPath;

        return $tmpPathResult;
    }

    /**
     * Checking files data array for downloadable product
     */
    public function checkingForFilesDataArray($filesDataArray, $key) {
        if (isset ( $filesDataArray [$key] ['name'] ) && (file_exists ( $filesDataArray [$key] ['tmp_name'] ))) {
            return 1;
        }
        return 0;
    }

    /**
     * Delete downloadable sample
     *
     * @param
     *            $downloadableSample
     * @return void
     */
    public function deleteDownloadableSample($downloadableSample) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $resourceSample = $objectManager->get ( 'Magento\Downloadable\Model\ResourceModel\Sample');
        $sampleDeleteItems = array ();
        /**
         * Removing all sample data
         */
        foreach ( $downloadableSample as $sampleDelete ) {
            $sampleDeleteItems [] = $sampleDelete->getSampleId ();
        }
        if (! empty ( $sampleDeleteItems )) {
            $resourceSample->deleteItems ( $sampleDeleteItems );
        }
    }
    /**
     * Delete downloadable link
     *
     * @param
     *            $downloadableLink
     * @return void
     */
    public function deleteDownloadableLinks($downloadableLink) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $resourceLink = $objectManager->get ( 'Magento\Downloadable\Model\ResourceModel\Link');
        /**
         * Removing all link data
         */
        $linkDeleteItems = array ();
        foreach ( $downloadableLink as $linkDelete ) {
            $linkDeleteItems [] = $linkDelete->getLinkId ();
        }
        if (! empty ( $linkDeleteItems )) {
            $resourceLink->deleteItems ( $linkDeleteItems );
        }
    }

}