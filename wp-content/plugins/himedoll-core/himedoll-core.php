<?php
/**
 * Plugin Name: HimeDoll Core
 * Description: Core business features for HimeDoll.
 * Version: 0.2.0
 * Author: HimeDoll
 * Text Domain: himedoll-core
 */

defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '0.2.0');
define('HIMEDOLL_CORE_FILE', __FILE__);
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

require_once HIMEDOLL_CORE_PATH . 'includes/class-himedoll-core.php';

function himedoll_core_boot(): void {
    HimeDoll_Core::instance();
}
add_action('plugins_loaded', 'himedoll_core_boot');
