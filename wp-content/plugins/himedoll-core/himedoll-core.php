<?php
/**
 * Plugin Name: HimeDoll Core
 * Version: 2.0.0
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '2.0.0');
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

require_once HIMEDOLL_CORE_PATH . 'includes/class-himedoll-core.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-catalog-filter.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-recent-products.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-wishlist.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-buy-now.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-security.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-newsletter.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-restock.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-home-banners.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-campaigns.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-review-requests.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-email-log.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-fields.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-settings.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-setup-wizard.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-marketing-settings.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-growth-settings.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-importer.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-operations-dashboard.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-order-export.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-commerce-intelligence.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-system-health.php';

add_action('plugins_loaded', static function (): void {
    HimeDoll_Core::instance();
    HimeDoll_Catalog_Filter::instance();
    HimeDoll_Recent_Products::instance();
    HimeDoll_Wishlist::instance();
    HimeDoll_Buy_Now::instance();
    HimeDoll_Security::instance();
    HimeDoll_Newsletter::instance();
    HimeDoll_Restock::instance();
    HimeDoll_Home_Banners::instance();
    HimeDoll_Campaigns::instance();
    HimeDoll_Review_Requests::instance();
    HimeDoll_Email_Log::instance();

    if (is_admin()) {
        HimeDoll_Settings::instance();
        HimeDoll_Setup_Wizard::instance();
        HimeDoll_Marketing_Settings::instance();
        HimeDoll_Growth_Settings::instance();
        HimeDoll_Product_Importer::instance();
        HimeDoll_Operations_Dashboard::instance();
        HimeDoll_Order_Export::instance();
        HimeDoll_Commerce_Intelligence::instance();
        HimeDoll_System_Health::instance();

        if (class_exists('WooCommerce')) {
            HimeDoll_Product_Fields::instance();
        }
    }
});
