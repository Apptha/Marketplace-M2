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
namespace Apptha\Marketplace\Model\Product\Gallery\Video;

use Magento\Framework\Model\AbstractModel;

/**
 * This class initiates subscription profiles model
 */
class Processor extends \Magento\Catalog\Model\Product\Gallery\Processor {
    /**
     * @var \Magento\Catalog\Model\Product\Gallery\CreateHandler
     */
    protected $createHandler;
    
    /**
     * Processor constructor.
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel
     * @param \Magento\Catalog\Model\Product\Gallery\CreateHandler $createHandler
     */
    public function __construct(
            \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
            \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
            \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
            \Magento\Framework\Filesystem $filesystem,
            \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel,
            \Magento\Catalog\Model\Product\Gallery\CreateHandler $createHandler
            )
    {
        parent::__construct($attributeRepository, $fileStorageDb, $mediaConfig, $filesystem, $resourceModel);
        $this->createHandler = $createHandler;
    }
    
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $videoData
     * @param array $mediaAttribute
     * @param bool $move
     * @param bool $exclude
     * @return string
     * @throws LocalizedException
     */
    public function addVideo(
            \Magento\Catalog\Model\Product $product,
            array $videoData,
            $mediaAttribute = null,
            $move = false,
            $exclude = true
            ) {
                $file = $this->mediaDirectory->getRelativePath('Productvideo/'.$videoData['video_id'].'/hqdefault.jpg');
                if (!$this->mediaDirectory->isFile($file)) {
                    (__('The image does not exist.'));
                }
                
                $pathinfo = pathinfo($file);
                $imgExtensions = ['jpg', 'jpeg', 'gif', 'png'];
                if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
                    (__('Please correct the image file type.'));
                }
                
                $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($pathinfo['basename']);
                $dispretionPath = \Magento\MediaStorage\Model\File\Uploader::getDispretionPath($fileName);
                $fileName = $dispretionPath . '/' . $fileName;
                
                $fileName = $this->getNotDuplicatedFilename($fileName, $dispretionPath);
                
                $destinationFile = $this->mediaConfig->getTmpMediaPath($fileName);
                
                try {
                    /** @var $storageHelper \Magento\MediaStorage\Helper\File\Storage\Database */
                    $storageHelper = $this->fileStorageDb;
                    if ($move) {
                        $this->mediaDirectory->renameFile($file, $destinationFile);
                        
                        //If this is used, filesystem should be configured properly
                        $storageHelper->saveFile($this->mediaConfig->getTmpMediaShortUrl($fileName));
                    } else {
                        $this->mediaDirectory->copyFile($file, $destinationFile);
                        
                        $storageHelper->saveFile($this->mediaConfig->getTmpMediaShortUrl($fileName));
                    }
                } catch (\Exception $e) {
                    (__('We couldn\'t move this file: %1.', $e->getMessage()));
                }
                
                $fileName = str_replace('\\', '/', $fileName);
                
                $attrCode = $this->getAttribute()->getAttributeCode();
                $mediaGalleryData = $product->getData($attrCode);
                $position = 0;
                if (!is_array($mediaGalleryData)) {
                    $mediaGalleryData = ['images' => []];
                }
                
                foreach ($mediaGalleryData['images'] as &$image) {
                    if (isset($image['position']) && $image['position'] > $position) {
                        $position = $image['position'];
                    }
                }
                
                $position++;
                
                unset($videoData['file']);
                $mediaGalleryData['images'][] = array_merge([
                        'file' => $fileName,
                        'label' => $videoData['video_title'],
                        'position' => $position
                ], $videoData);
                
                $product->setData($attrCode, $mediaGalleryData);
                
                if ($mediaAttribute !== null) {
                    $product->setMediaAttribute($product, $mediaAttribute, $fileName);
                }
                
                $this->createHandler->execute($product);
                
                return $fileName;
    }
}