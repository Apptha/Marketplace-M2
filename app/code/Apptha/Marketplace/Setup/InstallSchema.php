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
namespace Apptha\Marketplace\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
class InstallSchema implements InstallSchemaInterface {
    /**
     * (non-PHPdoc)
     * 
     * @see \Apptha\Marketplace\Setup\InstallSchemaInterface::install()
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup ();
        $true = true;
        $sellerProfileTableName = $installer->getTable ( 'marketplace_seller' );        
        if ($installer->getConnection ()->isTableExists ( $sellerProfileTableName ) != $true) {
            $sellerProfileTable = $installer->getConnection ()->newTable ( $sellerProfileTableName )
            ->addColumn ( 'id', Table::TYPE_INTEGER, null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'ID' )->addColumn ( 'customer_id', Table::TYPE_TEXT, null, ['nullable' => false,'default' => ''], 'Customer Id' )
            ->addColumn ( 'email', Table::TYPE_TEXT, null, ['nullable' => false,'default' => ''], 'Email' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, ['nullable' => false], 'Created At' )
            ->addColumn ( 'status', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Status' )->addColumn ( 'facebook_id', Table::TYPE_TEXT, null, ['nullable' => false,'default' => ''], 'Facebook Id' )
            ->addColumn ( 'twitter_id', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Twitter Id' )->addColumn ( 'google_id', Table::TYPE_TEXT, null, ['nullable' => false,'default' => ''], 'Google Id' )
            ->addColumn ( 'linked_id', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Linked Id' )->addColumn ( 'desc', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Description' )
            ->addColumn ( 'paypal_id', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Paypal Id' )->addColumn ( 'bank_payment', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Bank Payment' )
            ->addColumn ( 'store_name', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Store Name' )->addColumn ( 'contact', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Contact' )
            ->addColumn ( 'commission', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Commission' )->addColumn ( 'country', Table::TYPE_TEXT, null, ['nullable' => false,'default' => '' ], 'Country' )
            ->addColumn ( 'state', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => '' ], 'State' )->addColumn ( 'meta_keywords', Table::TYPE_TEXT, null, ['nullable' => false,'default' => '' ], 'Meta Keywords' )
            ->addColumn ( 'meta_description', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Meta Description' )->addColumn ( 'logo_name', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Logo Name' )
            ->addColumn ( 'banner_name', Table::TYPE_TEXT, null, ['nullable' => false,'default' => '' ], 'Banner Name' )->addColumn ( 'store_url', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => ''], 'Store Url' )
            ->addColumn ( 'show_profile', Table::TYPE_TEXT, null, [ 'nullable' => false,'default' => '' ], 'Show Profile' )->addColumn ( 'address', Table::TYPE_TEXT, null, ['nullable' => false,'default' => ''], 'Address' )
            ->setComment ( 'Seller Profile Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
            $installer->getConnection ()->createTable ( $sellerProfileTable );
        }
        $sellerOrderTableName = $installer->getTable ( 'marketplace_sellerorder' );
        if ($installer->getConnection ()->isTableExists ( $sellerOrderTableName ) != $true) {
            $sellerOrderTable = $installer->getConnection ()->newTable ( $sellerOrderTableName )
            ->addColumn ( 'id', Table::TYPE_INTEGER, null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'Id' )
            ->addColumn ( 'order_id', Table::TYPE_INTEGER, null, [ 'unsigned' => true ], 'Order Id' )->addColumn ( 'seller_id', Table::TYPE_INTEGER, null, ['unsigned' => true ], 'Seller Id' )
            ->addColumn ( 'commission', Table::TYPE_DECIMAL, '12,4', [ ], 'Commission' )->addColumn ( 'seller_product_total', Table::TYPE_DECIMAL, '12,4', [ ], 'Seller Product Total' )
            ->addColumn ( 'seller_amount', Table::TYPE_DECIMAL, '12,4', [ ], 'Seller Amount' )->addColumn ( 'is_invoiced', Table::TYPE_SMALLINT, null, ['unsigned' => true ], 'Is Invoiced' )
            ->addColumn ( 'is_shipped', Table::TYPE_SMALLINT, null, ['unsigned' => true ], 'Is Shipped' )->addColumn ( 'is_refunded', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Refunded' )
            ->addColumn ( 'is_returned', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Returned' )->addColumn ( 'is_canceled', Table::TYPE_SMALLINT, null, ['unsigned' => true ], 'Is Canceled' )
            ->addColumn ( 'status', Table::TYPE_TEXT, 255, [ ], 'Status' )->addColumn ( 'increment_id', Table::TYPE_TEXT, 255, [ ], 'Increment Id' )
            ->addColumn ( 'billing_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Billing Id' )->addColumn ( 'shipping_id', Table::TYPE_INTEGER, null, ['unsigned' => true ], 'Shipping Id' )
            ->addColumn ( 'quote_id', Table::TYPE_INTEGER, null, [ 'unsigned' => true ], 'Quote Id' )->addColumn ( 'shipping_amount', Table::TYPE_DECIMAL, '12,4', [ ], 'Shipping Amount' )
            ->addColumn ( 'order_currency_code', Table::TYPE_TEXT, 3, [ ], 'Currency Code' )->addColumn ( 'customer_id', Table::TYPE_INTEGER, null, [ 'unsigned' => true], 'Customer Id' )
            ->setComment ( 'Seller Order Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
            $installer->getConnection ()->createTable ( $sellerOrderTable );
        }
        $sellerOrderItemsTableName = $installer->getTable ( 'marketplace_sellerorderitems' );
        if ($installer->getConnection ()->isTableExists ( $sellerOrderItemsTableName ) != $true) {
            $sellerOrderItemTable = $installer->getConnection ()->newTable ( $sellerOrderItemsTableName )
            ->addColumn ( 'id', Table::TYPE_INTEGER, null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'Id' )
            ->addColumn ( 'order_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Order Id' )->addColumn ( 'seller_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Seller Id' )
            ->addColumn ( 'order_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Order Item Id' )->addColumn ( 'product_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Product Id' )
            ->addColumn ( 'product_sku', Table::TYPE_TEXT, 255, [ ], 'Product Sku' )->addColumn ( 'product_qty', Table::TYPE_DECIMAL, '12,4', [ ], 'Product Qty' )
            ->addColumn ( 'product_name', Table::TYPE_TEXT, 255, [ ], 'Product Name' )->addColumn ( 'options', Table::TYPE_TEXT, null, [ ], 'Options' )
            ->addColumn ( 'is_canceled', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Canceled' )->addColumn ( 'status', Table::TYPE_TEXT, 255, [ ], 'Status' )
            ->addColumn ( 'parent_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Parent Id' )->addColumn ( 'quote_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Quote Item Id' )
            ->addColumn ( 'quote_id', Table::TYPE_INTEGER, null, ['unsigned' => true], 'Quote Id' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, ['nullable' => false], 'Created At' )
            ->addColumn ( 'qty_canceled', Table::TYPE_DECIMAL, '12,4', [ ], 'Qty Canceled' )->addColumn ( 'commission', Table::TYPE_DECIMAL, '12,4', [ ], 'Commission' )
            ->addColumn ( 'product_price', Table::TYPE_DECIMAL, '12,4', [ ], 'Product Price' )->addColumn ( 'base_product_price', Table::TYPE_DECIMAL, '12,4', [ ], 'Base Product Price' )
            ->addColumn ( 'is_buyer_canceled', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Buyer Canceled' )->addColumn ( 'is_buyer_refunded', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Buyer Refunded' )
            ->addColumn ( 'is_buyer_returned', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Buyer Returned' )->addColumn ( 'is_refunded', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Refunded' )
            ->addColumn ( 'is_returned', Table::TYPE_SMALLINT, null, ['unsigned' => true], 'Is Returned' )
            ->setComment ( 'Seller Order Items Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
            $installer->getConnection ()->createTable ( $sellerOrderItemTable );
        } 
        $true = true;
        $tableName = $setup->getTable ( 'marketplace_seller' );
            if ($setup->getConnection ()->isTableExists ( $tableName ) == $true) {
                $setup->getConnection ()->addColumn ( $setup->getTable ( $tableName ), 'national_shipping_amount', ['type' => Table::TYPE_DECIMAL,
                        'length' => '12,4','nullable' => false,'default' => '0','comment' => 'National Shipping Amount'] );
                $setup->getConnection ()->addColumn ( $setup->getTable ( $tableName ), 'international_shipping_amount', ['type' => Table::TYPE_DECIMAL,
                        'length' => '12,4','nullable' => false,'default' => '0','comment' => 'International Shipping Amount'] );
                $setup->getConnection ()->addColumn ( $setup->getTable ( $tableName ), 'received_amount', ['type' => Table::TYPE_DECIMAL,
                        'length' => '12,4','nullable' => false,'default' => '0','comment' => 'Received Amount From Admin(Owner)'] );
                $setup->getConnection ()->addColumn ( $setup->getTable ( $tableName ), 'remaining_amount', ['type' => Table::TYPE_DECIMAL,
                        'length' => '12,4','nullable' => false,'default' => '0','comment' => 'Remaining Amount From Admin(Owner)'] );
            }
            $sellerReviewTableName = $setup->getTable ( 'marketplace_sellerreview' );
            if ($setup->getConnection ()->isTableExists ( $sellerReviewTableName ) != $true) {
                $sellerReviewTable = $setup->getConnection ()->newTable ( $sellerReviewTableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                'identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'ID' )
                ->addColumn ( 'seller_id', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Seller Id' )->addColumn ( 'customer_id', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Customer Id' )->addColumn ( 'rating', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Rating' )->addColumn ( 'review', Table::TYPE_TEXT, 255, [ ], 'Review' )->addColumn ( 'store_id', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Store Id' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Created At' )->addColumn ( 'status', Table::TYPE_SMALLINT, null, [
                        'unsigned' => true
                ], 'Status' )->setComment ( 'Seller Review Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                $setup->getConnection ()->createTable ( $sellerReviewTable );
            }  
            $sellerSubscriptionPlansTableName = $setup->getTable ( 'marketplace_subscription_plans' );
            if ($setup->getConnection ()->isTableExists ( $sellerSubscriptionPlansTableName ) != $true) {
                $sellerSubscriptionPlans = $setup->getConnection ()->newTable ( $sellerSubscriptionPlansTableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                ], 'ID' )->addColumn ( 'plan_name', Table::TYPE_TEXT, 255, [ ], 'Plan Name' )->addColumn ( 'subscription_period_type', Table::TYPE_TEXT, 255, [ ], 'Subscription Period Type' )->addColumn ( 'period_frequency', Table::TYPE_INTEGER, null, [ ], 'Period Frequency' )->addColumn ( 'max_product_count', Table::TYPE_TEXT, 255, [ ], 'Max Product Count' )->addColumn ( 'fee', Table::TYPE_DECIMAL, '12,4', [ ], 'Fee' )->addColumn ( 'status', Table::TYPE_SMALLINT, null, [
                        'unsigned' => true
                ], 'Status' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Created At' )->addColumn ( 'updated_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Updated At' )->setComment ( 'Subscription Plans Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                $setup->getConnection ()->createTable ( $sellerSubscriptionPlans );
            }
            $sellerSubscriptionProfilesTableName = $setup->getTable ( 'marketplace_subscription_profiles' );
            if ($setup->getConnection ()->isTableExists ( $sellerSubscriptionProfilesTableName ) != $true) {
                $sellerSubscriptionProfiles = $setup->getConnection ()->newTable ( $sellerSubscriptionProfilesTableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                'identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'ID' )
                ->addColumn ( 'plan_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Plan Id' )
                ->addColumn ( 'plan_name', Table::TYPE_TEXT, 255, [ ], 'Plan Name' )
                ->addColumn ( 'subscription_period_type', Table::TYPE_TEXT, 255, [ ], 'Subscription Period Type' )->addColumn ( 'period_frequency', Table::TYPE_INTEGER, null, [ ], 'Period Frequency' )->addColumn ( 'max_product_count', Table::TYPE_TEXT, 255, [ ], 'Max Product Count' )->addColumn ( 'fee', Table::TYPE_DECIMAL, '12,4', [ ], 'Fee' )->addColumn ( 'payment_id', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Payment Id' )->addColumn ( 'seller_id', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Seller Id' )->addColumn ( 'txn_id', Table::TYPE_TEXT, 255, [ ], 'Transaction Id' )->addColumn ( 'invoice', Table::TYPE_TEXT, 255, [ ], 'Invoice' )->addColumn ( 'started_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Started At' )->addColumn ( 'ended_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Ended At' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Created At' )->addColumn ( 'status', Table::TYPE_SMALLINT, null, [
                        'unsigned' => true
                ], 'Status' )->addColumn ( 'receiver_email', Table::TYPE_TEXT, 255, [ ], 'Receiver Email' )->addColumn ( 'payer_email', Table::TYPE_TEXT, 255, [ ], 'Payer Email' )->addColumn ( 'payment_status', Table::TYPE_TEXT, 255, [ ], 'Payment Status' )->addColumn ( 'item_name', Table::TYPE_TEXT, 255, [ ], 'Item Name' )->addColumn ( 'base_currency_code', Table::TYPE_TEXT, 255, [ ], 'Base Currency Code' )->setComment ( 'Subscription Profiles Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                $setup->getConnection ()->createTable ( $sellerSubscriptionProfiles );
            }
            $sellerSellerPaymentsTableName = $setup->getTable ( 'marketplace_seller_payments' );
            if ($setup->getConnection ()->isTableExists ( $sellerSellerPaymentsTableName ) != $true) {
                $sellerSellerPaymentsTable = $setup->getConnection ()->newTable ( $sellerSellerPaymentsTableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                'identity' => true,'unsigned' => true,'nullable' => false,'primary' => true
                ], 'ID' )->addColumn ( 'paid_amount', Table::TYPE_DECIMAL, '12,4', [ ], 'Fee' )
                ->addColumn ( 'seller_id', Table::TYPE_INTEGER, null, [
                        'nullable' => false
                ], 'Seller Id' )->addColumn ( 'invoice', Table::TYPE_TEXT, 255, [ ], 'Invoice' )->addColumn ( 'created_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Created At' )->addColumn ( 'ack_at', Table::TYPE_DATETIME, null, [
                        'nullable' => false
                ], 'Acknowledge At' )->addColumn ( 'is_ack', Table::TYPE_SMALLINT, null, [
                        'unsigned' => true
                ], 'Is Acknowledged' )->addColumn ( 'comment', Table::TYPE_TEXT, 255, [ ], 'Comment' )->addColumn ( 'payment_type', Table::TYPE_TEXT, 255, [ ], 'Payment Type' )->setComment ( 'Seller Payments Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
                $setup->getConnection ()->createTable ( $sellerSellerPaymentsTable );
            }
        
        $installer->endSetup ();
    }
}