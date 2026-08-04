<?php
/**
 * Plugin Name: HimeDoll Core
 * Version: 4.0.0
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '4.0.0');
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

$files = [
    'includes/class-himedoll-core.php',
    'includes/class-catalog-filter.php',
    'includes/class-recent-products.php',
    'includes/class-wishlist.php',
    'includes/class-buy-now.php',
    'includes/class-security.php',
    'includes/class-newsletter.php',
    'includes/class-restock.php',
    'includes/class-home-banners.php',
    'includes/class-campaigns.php',
    'includes/class-review-requests.php',
    'includes/class-email-log.php',
    'includes/class-buying-guides.php',
    'includes/class-search-analytics.php',
    'includes/class-abandoned-checkout.php',
    'includes/class-customer-segments.php',
    'ai/class-ai-client.php',
    'ai/class-ai-queue.php',
    'ai/class-ai-product-generator.php',
    'ai/class-ai-logger.php',
    'erp/class-suppliers.php',
    'erp/class-purchase-orders.php',
    'erp/class-order-matcher.php',
    'erp/class-logistics.php',
    'erp/class-profit.php',
    'admin/class-product-fields.php',
    'admin/class-settings.php',
    'admin/class-setup-wizard.php',
    'admin/class-marketing-settings.php',
    'admin/class-growth-settings.php',
    'admin/class-product-importer.php',
    'admin/class-operations-dashboard.php',
    'admin/class-order-export.php',
    'admin/class-commerce-intelligence.php',
    'admin/class-system-health.php',
    'admin/class-retention-dashboard.php',
    'admin/class-ai-settings.php',
    'admin/class-ai-product-panel.php',
    'admin/class-erp-dashboard.php',
    'admin/class-purchase-importer.php',
    'admin/class-logistics-export.php',
];

foreach ($files as $file) {
    $path = HIMEDOLL_CORE_PATH . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}

add_action('plugins_loaded', static function (): void {
    foreach ([
        'HimeDoll_Core',
        'HimeDoll_Catalog_Filter',
        'HimeDoll_Recent_Products',
        'HimeDoll_Wishlist',
        'HimeDoll_Buy_Now',
        'HimeDoll_Security',
        'HimeDoll_Newsletter',
        'HimeDoll_Restock',
        'HimeDoll_Home_Banners',
        'HimeDoll_Campaigns',
        'HimeDoll_Review_Requests',
        'HimeDoll_Email_Log',
        'HimeDoll_Buying_Guides',
        'HimeDoll_Search_Analytics',
        'HimeDoll_Abandoned_Checkout',
        'HimeDoll_Customer_Segments',
        'HimeDoll_AI_Queue',
        'HimeDoll_AI_Product_Generator',
        'HimeDoll_AI_Logger',
        'HimeDoll_Suppliers',
        'HimeDoll_Purchase_Orders',
        'HimeDoll_Order_Matcher',
        'HimeDoll_Logistics',
        'HimeDoll_Profit',
    ] as $class) {
        if (class_exists($class) && method_exists($class, 'instance')) {
            $class::instance();
        }
    }

    if (is_admin()) {
        foreach ([
            'HimeDoll_Settings',
            'HimeDoll_Setup_Wizard',
            'HimeDoll_Marketing_Settings',
            'HimeDoll_Growth_Settings',
            'HimeDoll_Product_Importer',
            'HimeDoll_Operations_Dashboard',
            'HimeDoll_Order_Export',
            'HimeDoll_Commerce_Intelligence',
            'HimeDoll_System_Health',
            'HimeDoll_Retention_Dashboard',
            'HimeDoll_AI_Settings',
            'HimeDoll_AI_Product_Panel',
            'HimeDoll_ERP_Dashboard',
            'HimeDoll_Purchase_Importer',
            'HimeDoll_Logistics_Export',
        ] as $class) {
            if (class_exists($class) && method_exists($class, 'instance')) {
                $class::instance();
            }
        }

        if (class_exists('WooCommerce') && class_exists('HimeDoll_Product_Fields')) {
            HimeDoll_Product_Fields::instance();
        }
    }
});
