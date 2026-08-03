<?php
/**
 * Plugin Name: HimeDoll Core
 * Version: 0.5.0
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

require_once HIMEDOLL_CORE_PATH . 'includes/class-himedoll-core.php';
require_once HIMEDOLL_CORE_PATH . 'admin/class-product-fields.php';
require_once HIMEDOLL_CORE_PATH . 'includes/class-buy-now.php';

add_action('plugins_loaded', function (): void {
    HimeDoll_Core::instance();
    HimeDoll_Buy_Now::instance();

    if (is_admin() && class_exists('WooCommerce')) {
        HimeDoll_Product_Fields::instance();
    }
});
