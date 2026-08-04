<?php
defined('ABSPATH') || exit;

final class HimeDoll_Recent_Products {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('template_redirect', [$this, 'track']);
    }

    public function track(): void {
        if (!is_singular('product')) {
            return;
        }

        $product_id = get_queried_object_id();
        $recent = isset($_COOKIE['hd_recent_products'])
            ? array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_COOKIE['hd_recent_products'])))))
            : [];

        $recent = array_values(array_diff($recent, [$product_id]));
        array_unshift($recent, $product_id);
        $recent = array_slice($recent, 0, 12);

        setcookie(
            'hd_recent_products',
            implode(',', $recent),
            [
                'expires' => time() + MONTH_IN_SECONDS,
                'path' => COOKIEPATH ?: '/',
                'secure' => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }
}
