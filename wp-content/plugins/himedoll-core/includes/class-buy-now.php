<?php
defined('ABSPATH') || exit;

final class HimeDoll_Buy_Now {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_filter('woocommerce_add_to_cart_redirect', [$this, 'redirect']);
    }

    public function redirect(string $url): string {
        if (
            isset($_REQUEST['hd_buy_now']) &&
            sanitize_text_field(wp_unslash($_REQUEST['hd_buy_now'])) === '1' &&
            function_exists('wc_get_checkout_url')
        ) {
            return wc_get_checkout_url();
        }

        return $url;
    }
}
