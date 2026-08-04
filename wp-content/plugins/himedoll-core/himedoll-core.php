<?php
/**
 * Plugin Name: HimeDoll Core
 * Description: Enterprise commerce, ERP and AI operations for HimeDoll.
 * Version: 5.0.0
 * Author: HimeDoll
 */
defined('ABSPATH') || exit;

define('HIMEDOLL_CORE_VERSION', '5.0.0');
define('HIMEDOLL_CORE_PATH', plugin_dir_path(__FILE__));

$autoload = [
    'enterprise/class-loyalty.php',
    'enterprise/class-membership.php',
    'enterprise/class-referral.php',
    'enterprise/class-enterprise-api.php',
    'enterprise/class-order-webhook.php',
    'admin/class-enterprise-settings.php',
    'admin/class-enterprise-dashboard.php',
];

foreach ($autoload as $file) {
    $path = HIMEDOLL_CORE_PATH . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}

add_action('plugins_loaded', static function (): void {
    foreach ([
        'HimeDoll_Loyalty',
        'HimeDoll_Membership',
        'HimeDoll_Referral',
        'HimeDoll_Enterprise_API',
        'HimeDoll_Order_Webhook',
    ] as $class) {
        if (class_exists($class) && method_exists($class, 'instance')) {
            $class::instance();
        }
    }

    if (is_admin()) {
        foreach ([
            'HimeDoll_Enterprise_Settings',
            'HimeDoll_Enterprise_Dashboard',
        ] as $class) {
            if (class_exists($class) && method_exists($class, 'instance')) {
                $class::instance();
            }
        }
    }
});
