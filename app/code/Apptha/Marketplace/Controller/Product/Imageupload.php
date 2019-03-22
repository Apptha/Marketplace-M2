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
namespace Apptha\Marketplace\Controller\Product;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * This class contains loading product image functions
 */
class Imageupload extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var $storeManager,
     * @var $resultRawFactory
     */
  
    protected $storeManager;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,\Magento\Store\Model\StoreManagerInterface $storeManager) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
    }
   /**
    * 
    * {@inheritDoc}
    * @see \Magento\Framework\App\ActionInterface::execute()
    */
    public function execute() {

        /**
         * To declare html result variable
         */
        $htmlResult = '';
        /**
         * To prepare image count
         */
        $imageCount = count ( $_FILES ['product_images'] ['name'] );
        /**
         * Set image increment zero
         */
        $imageInc = 0;
        /**
         * Iterate the product image files
         */
        foreach ( $_FILES ['product_images'] ['name'] as $key => $value ) {
            if ($imageInc < $imageCount && isset ( $key )) {
                $_FILES ['image_' . $imageInc] ['name'] = $_FILES ['product_images'] ['name'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['type'] = $_FILES ['product_images'] ['type'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['tmp_name'] = $_FILES ['product_images'] ['tmp_name'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['error'] = $_FILES ['product_images'] ['error'] [$imageInc];
                $_FILES ['image_' . $imageInc] ['size'] = $_FILES ['product_images'] ['size'] [$imageInc];
                /**
                 * To increment the product image count
                 */
                $imageInc = $imageInc + 1;
            } else {
                break;
            }
        }
        /**
         * Checking for product image exist or not
         */
        if (isset ( $_FILES ['product_images'] ) && isset ( $_FILES ['product_images'] ['name'] )) {
            /**
             * Iterate for product image
             */
            for($inc = 0; $inc < $imageCount; $inc ++) {
                /**
                 * Create uploader object
                 */
                try{
                $uploaderObject = $this->_objectManager->create ( 'Magento\MediaStorage\Model\File\Uploader', [
                        'fileId' => 'image_' . $inc
                ] );
                /**
                 * Set option for uploader object
                 */
                $uploaderObject->setAllowedExtensions ( [
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                ] );
                /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                $imageAdapter = $this->_objectManager->get ( 'Magento\Framework\Image\AdapterFactory' )->create ();
                $uploaderObject->addValidateCallback ( 'catalog_product_image', $imageAdapter, 'validateUploadFile' );
                $uploaderObject->setAllowRenameFiles ( true );
                $uploaderObject->setFilesDispersion ( true );
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get ( 'Magento\Framework\Filesystem' )->getDirectoryRead ( DirectoryList::MEDIA );
                $result = $uploaderObject->save ( $mediaDirectory->getAbsolutePath ( 'tmp/catalog/product' ) );
                /**
                 * To unset result
                 */
                unset ( $result ['tmp_name'] );
                unset ( $result ['path'] );
                /**
                 * Getting result url
                 */
                $result ['url'] = $this->_objectManager->get ( 'Magento\Catalog\Model\Product\Media\Config' )->getTmpMediaUrl ( $result ['file'] );
                $fileName = $result ['file'];
                
                /**
                 * Geting a absolute path for image upload
                 */
                $absPath = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'tmp/catalog/product' . $fileName;

                /**
                 * Create html result for image
                 */
                $htmlResult = $htmlResult . '<span><span class="image_close">x</span>
    <span class="base_image_container">
     <input class="base_image" type="radio" name="base_image" value="' . $fileName . '">
     </span>
    <img src="' . $absPath . '" alt="' . $absPath . '" height="200" width="200">
    <input class="hidden_uploaded_image_path" type="hidden" name="images_path[]" value="' . $fileName . '" /></span>';
                } catch (\Exception $e) {
                    ?> 
                    <div class="name-error" style="color: red;"> <?php echo $e->getMessage().' Allowed File types are .jpg, .jpeg, .gif, .png'; ?> </div>
                    <?php }
            }
        }
        /**
         * To return html result
         */
        echo $htmlResult;
    }
}