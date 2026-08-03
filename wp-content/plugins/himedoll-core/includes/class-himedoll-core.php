<?php
defined('ABSPATH') || exit;

final class HimeDoll_Core {
    private static ?HimeDoll_Core $instance = null;

    public static function instance(): HimeDoll_Core {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_product_brand_taxonomy']);
        add_action('admin_notices', [$this, 'woocommerce_notice']);
    }

    public function register_product_brand_taxonomy(): void {
        if (!post_type_exists('product')) {
            return;
        }

        register_taxonomy('product_brand', ['product'], [
            'labels' => [
                'name' => __('ブランド', 'himedoll-core'),
                'singular_name' => __('ブランド', 'himedoll-core'),
                'add_new_item' => __('ブランドを追加', 'himedoll-core'),
                'edit_item' => __('ブランドを編集', 'himedoll-core'),
                'search_items' => __('ブランドを検索', 'himedoll-core'),
            ],
            'public' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => ['slug' => 'brand'],
        ]);
    }

    public function woocommerce_notice(): void {
        if (!current_user_can('activate_plugins') || class_exists('WooCommerce')) {
            return;
        }

        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('HimeDoll Core requires WooCommerce for product features.', 'himedoll-core');
        echo '</p></div>';
    }
}
