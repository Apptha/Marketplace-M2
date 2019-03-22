<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apptha\Marketplace\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Model\Review;

class Post extends \Magento\Review\Controller\Product\Post
{
    /**
     * Submit new review action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data = $this->getRequest()->getPostValue();
            $rating = $this->getRequest()->getParam('ratings', []);
        }
        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var \Magento\Review\Model\Review $review */
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');
            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $productId = $product->getId();
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                    $sellerId = $product->getSellerId();
                    $customerId = $this->customerSession->getCustomerId();
                    if($sellerId == $customerId) {
                        $this->messageManager->addError(__("Seller Can't add review for their own product."));
                    } else{
                    $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Review::STATUS_PENDING)
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();
                        $this->rating($rating,$review,$product);

                    $review->aggregate();
                    $this->messageManager->addSuccess(__('You submitted your review for moderation.'));
                    }
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            } else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                } else {
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            }
        }
        $redirectUrl = $this->reviewSession->getRedirectUrl(true);
        $resultRedirect->setUrl($redirectUrl ?: $this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
    public function rating($rating,$review,$product)
    {
        foreach ($rating as $ratingId => $optionId) {
            $this->ratingFactory->create()
            ->setRatingId($ratingId)
            ->setReviewId($review->getId())
            ->setCustomerId($this->customerSession->getCustomerId())
            ->addOptionVote($optionId, $product->getId());
        }
    }
}
