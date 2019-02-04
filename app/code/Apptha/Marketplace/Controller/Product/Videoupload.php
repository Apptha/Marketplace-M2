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
/**
 * This class contains loading product image functions
 */
class Videoupload extends \Magento\Framework\App\Action\Action {
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
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {
        parent::__construct ( $context );
        $this->_scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
    }
    /**
     *
     * {@inheritdoc}
     *
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute() {
        $youTubeApiKey = $this->_scopeConfig->getValue ( 'catalog/product_video/youtube_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        $videoStatus = $this->_scopeConfig->getValue ( 'marketplace/product/product_video', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        if (! empty ( $youTubeApiKey ) && $videoStatus =='1') {
            $youtube = "http://www.youtube.com/oembed?url=" . $_POST ['url'] . "&format=json";
            $curl = curl_init ( $youtube );
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $return = curl_exec ( $curl );
            $url = $_POST ['url'];
            parse_str ( parse_url ( $url, PHP_URL_QUERY ), $my_array_of_vars );
            if(empty($my_array_of_vars ['v'])){
                $resultJson = $this->resultJsonFactory->create ();
                return $resultJson->setData ( [
                        'error' => 'Invalid URL',
                ] );
            }
            $videoId = $my_array_of_vars ['v'];
            $youtubeDesc = "https://www.googleapis.com/youtube/v3/videos?key=" . $youTubeApiKey . "&part=snippet&id=" . $videoId;
            $curl = curl_init ( $youtubeDesc );
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $returnDesc = curl_exec ( $curl );
            curl_close ( $curl );
            $resultJson = $this->resultJsonFactory->create ();
            return $resultJson->setData ( [ 
                    'success' => $return,
                    'description' => $returnDesc 
            ] );
        } else {
            ?>
<div class="name-error" style="color: red;"> <?php  ( __ ( 'Please contact administrator to update Youtube API key.' ) )?> </div>
<?php
        }
    }
}