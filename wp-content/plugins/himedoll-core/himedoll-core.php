<?php
/**
 * Plugin Name: HimeDoll Core
 * Description: Core commerce, catalog, SEO and setup features for HimeDoll.
 * Version: 1.0.0
 * Author: HimeDoll
 * Text Domain: himedoll-core
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '1.0.0');
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

require_once HIMEDOLL_CORE_PATH . 'includes/class-himedoll-core.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-catalog-filter.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-recent-products.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-wishlist.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-buy-now.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-security.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-fields.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-settings.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-setup-wizard.php';

add_action('plugins_loaded', static function (): void {
    HimeDoll_Core::instance();
    HimeDoll_Catalog_Filter::instance();
    HimeDoll_Recent_Products::instance();
    HimeDoll_Wishlist::instance();
    HimeDoll_Buy_Now::instance();
    HimeDoll_Security::instance();

    if (is_admin()) {
        HimeDoll_Settings::instance();
        HimeDoll_Setup_Wizard::instance();

        if (class_exists('WooCommerce')) {
            HimeDoll_Product_Fields::instance();
        }
    }
});
