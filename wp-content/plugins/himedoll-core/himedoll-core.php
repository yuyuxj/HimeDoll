<?php
/**
 * Plugin Name: HimeDoll Core
 * Description: Core commerce, catalog, marketing and operations features for HimeDoll.
 * Version: 1.5.0
 * Author: HimeDoll
 * Text Domain: himedoll-core
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '1.5.0');
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

require_once HIMEDOLL_CORE_PATH . 'includes/class-himedoll-core.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-catalog-filter.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-recent-products.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-wishlist.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-buy-now.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-security.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-newsletter.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-fields.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-settings.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-setup-wizard.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-marketing-settings.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-importer.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-operations-dashboard.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-order-export.php';

add_action('plugins_loaded', static function (): void {
    HimeDoll_Core::instance();
    HimeDoll_Catalog_Filter::instance();
    HimeDoll_Recent_Products::instance();
    HimeDoll_Wishlist::instance();
    HimeDoll_Buy_Now::instance();
    HimeDoll_Security::instance();
    HimeDoll_Newsletter::instance();

    if (is_admin()) {
        HimeDoll_Settings::instance();
        HimeDoll_Setup_Wizard::instance();
        HimeDoll_Marketing_Settings::instance();
        HimeDoll_Product_Importer::instance();
        HimeDoll_Operations_Dashboard::instance();
        HimeDoll_Order_Export::instance();

        if (class_exists('WooCommerce')) {
            HimeDoll_Product_Fields::instance();
        }
    }
});
