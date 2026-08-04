<?php
/**
 * Plugin Name: HimeDoll Core
 * Description: Japanese commerce, growth, AI and ERP operations for HimeDoll.
 * Version: 9.0.0
 * Author: HimeDoll
 * Requires PHP: 8.0
 * Requires at least: 6.0
 * Text Domain: himedoll-core
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '9.0.0');
define('HIMEDOLL_CORE_FILE', __FILE__);
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));
define('HIMEDOLL_CORE_URL', plugin_dir_url(__FILE__));

/**
 * Load every module explicitly so production behaviour does not depend on
 * README promises or incidental includes from the active theme.
 */
$himedoll_core_files = [
    'includes/class-himedoll-core.php',
    'includes/class-security.php',
    'includes/class-catalog-filter.php',
    'includes/class-product-recommendations.php',
    'includes/class-product-seo.php',
    'includes/class-wishlist.php',
    'includes/class-recent-products.php',
    'includes/class-buy-now.php',
    'includes/class-newsletter.php',
    'includes/class-restock.php',
    'includes/class-search-analytics.php',
    'includes/class-home-banners.php',
    'includes/class-buying-guides.php',
    'includes/class-campaigns.php',
    'includes/class-email-log.php',
    'includes/class-abandoned-checkout.php',
    'includes/class-review-requests.php',
    'includes/class-customer-segments.php',
    'erp/class-erp-installer.php',
    'erp/class-inventory.php',
    'erp/class-rma.php',
    'erp/class-suppliers.php',
    'erp/class-purchase-orders.php',
    'erp/class-order-matcher.php',
    'erp/class-logistics.php',
    'erp/class-profit.php',
    'product-intelligence/class-components.php',
    'product-intelligence/class-configurator.php',
    'product-intelligence/class-sku-generator.php',
    'ai/class-ai-client.php',
    'ai/class-ai-logger.php',
    'ai/class-ai-product-generator.php',
    'ai/class-ai-queue.php',
    'enterprise/class-loyalty.php',
    'enterprise/class-membership.php',
    'enterprise/class-referral.php',
    'enterprise/class-enterprise-api.php',
    'enterprise/class-order-webhook.php',
];

$himedoll_admin_files = [
    'admin/class-settings.php',
    'admin/class-product-fields.php',
    'admin/class-product-importer.php',
    'admin/class-marketing-settings.php',
    'admin/class-growth-settings.php',
    'admin/class-retention-dashboard.php',
    'admin/class-setup-wizard.php',
    'admin/class-system-health.php',
    'admin/class-ai-settings.php',
    'admin/class-ai-product-panel.php',
    'admin/class-commerce-intelligence.php',
    'admin/class-operations-dashboard.php',
    'admin/class-order-export.php',
    'admin/class-logistics-export.php',
    'admin/class-erp-dashboard.php',
    'admin/class-inventory-dashboard.php',
    'admin/class-purchase-importer.php',
    'admin/class-enterprise-settings.php',
    'admin/class-enterprise-dashboard.php',
    'admin/class-product-intelligence-dashboard.php',
];

foreach (array_merge($himedoll_core_files, is_admin() ? $himedoll_admin_files : []) as $himedoll_file) {
    $himedoll_path = HIMEDOLL_CORE_PATH . $himedoll_file;
    if (is_readable($himedoll_path)) {
        require_once $himedoll_path;
    }
}

function himedoll_core_boot(): void {
    $classes = [
        'HimeDoll_Core', 'HimeDoll_Security', 'HimeDoll_Catalog_Filter',
        'HimeDoll_Product_Recommendations', 'HimeDoll_Product_SEO',
        'HimeDoll_Wishlist', 'HimeDoll_Recent_Products', 'HimeDoll_Buy_Now',
        'HimeDoll_Newsletter', 'HimeDoll_Restock', 'HimeDoll_Search_Analytics',
        'HimeDoll_Home_Banners', 'HimeDoll_Buying_Guides', 'HimeDoll_Campaigns',
        'HimeDoll_Email_Log', 'HimeDoll_Abandoned_Checkout', 'HimeDoll_Review_Requests',
        'HimeDoll_Customer_Segments', 'HimeDoll_Inventory', 'HimeDoll_RMA', 'HimeDoll_Suppliers', 'HimeDoll_Purchase_Orders',
        'HimeDoll_AI_Product_Generator', 'HimeDoll_AI_Queue', 'HimeDoll_Loyalty',
        'HimeDoll_Product_Components', 'HimeDoll_Product_Configurator', 'HimeDoll_SKU_Generator',
        'HimeDoll_Membership', 'HimeDoll_Referral', 'HimeDoll_Enterprise_API',
        'HimeDoll_Order_Webhook',
    ];

    if (is_admin()) {
        $classes = array_merge($classes, [
            'HimeDoll_Settings', 'HimeDoll_Product_Fields', 'HimeDoll_Product_Importer',
            'HimeDoll_Marketing_Settings', 'HimeDoll_Growth_Settings',
            'HimeDoll_Retention_Dashboard', 'HimeDoll_Setup_Wizard',
            'HimeDoll_System_Health', 'HimeDoll_AI_Settings', 'HimeDoll_AI_Product_Panel',
            'HimeDoll_Commerce_Intelligence', 'HimeDoll_Operations_Dashboard',
            'HimeDoll_Order_Export', 'HimeDoll_Logistics_Export', 'HimeDoll_ERP_Dashboard', 'HimeDoll_Inventory_Dashboard',
            'HimeDoll_Purchase_Importer', 'HimeDoll_Enterprise_Settings',
            'HimeDoll_Enterprise_Dashboard', 'HimeDoll_Product_Intelligence_Dashboard',
        ]);
    }

    foreach ($classes as $class) {
        if (class_exists($class) && is_callable([$class, 'instance'])) {
            $class::instance();
        }
    }
}
add_action('plugins_loaded', 'himedoll_core_boot', 20);

function himedoll_core_activate(): void {
    if (class_exists('HimeDoll_ERP_Installer')) { HimeDoll_ERP_Installer::install(); }
    update_option('himedoll_core_version', HIMEDOLL_CORE_VERSION);
    update_option('himedoll_core_activated_at', current_time('mysql'));
    set_transient('himedoll_core_activation_redirect', 1, 60);
}
register_activation_hook(__FILE__, 'himedoll_core_activate');

add_action('admin_init', static function (): void {
    if (!get_transient('himedoll_core_activation_redirect')) {
        return;
    }
    delete_transient('himedoll_core_activation_redirect');
    if (!wp_doing_ajax() && current_user_can('manage_options')) {
        wp_safe_redirect(admin_url('admin.php?page=himedoll-setup'));
        exit;
    }
});

add_action('admin_notices', static function (): void {
    if (!current_user_can('activate_plugins') || class_exists('WooCommerce')) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>HimeDoll Core:</strong> 商品・注文機能を利用するには WooCommerce を有効化してください。</p></div>';
});

add_action('admin_init', static function (): void { if (get_option('himedoll_erp_db_version') !== '8.0.0' && class_exists('HimeDoll_ERP_Installer')) { HimeDoll_ERP_Installer::install(); } });
