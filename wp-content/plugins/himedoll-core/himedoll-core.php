<?php
/**
 * Plugin Name: HimeDoll Core
 * Version: 0.8.0
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

require_once HIMEDOLL_CORE_PATH . 'includes/class-himedoll-core.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-catalog-filter.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-recent-products.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-wishlist.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-fields.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-settings.php';

add_action('plugins_loaded', function (): void {
    HimeDoll_Core::instance();
    HimeDoll_Catalog_Filter::instance();
    HimeDoll_Recent_Products::instance();
    HimeDoll_Wishlist::instance();

    if (is_admin() && class_exists('WooCommerce')) {
        HimeDoll_Product_Fields::instance();
        HimeDoll_Settings::instance();
    }
});
