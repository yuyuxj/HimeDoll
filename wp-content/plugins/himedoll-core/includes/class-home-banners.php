<?php
defined('ABSPATH') || exit;

final class HimeDoll_Home_Banners {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'meta_box']);
        add_action('save_post_hd_home_banner', [$this, 'save']);
    }

    public function register(): void {
        register_post_type('hd_home_banner', [
            'labels' => [
                'name' => '首页 Banner',
                'singular_name' => '首页 Banner',
                'add_new_item' => '添加 Banner',
                'edit_item' => '编辑 Banner',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-format-image',
            'supports' => ['title','editor','excerpt','thumbnail','page-attributes'],
        ]);
    }

    public function meta_box(): void {
        add_meta_box('hd_banner_link', 'Banner 链接', [$this, 'render'], 'hd_home_banner', 'side');
    }

    public function render(WP_Post $post): void {
        wp_nonce_field('hd_banner_link', 'hd_banner_nonce');
        $url = get_post_meta($post->ID, 'hd_banner_url', true);
        echo '<input type="url" style="width:100%" name="hd_banner_url" value="' . esc_attr($url) . '" placeholder="https://">';
    }

    public function save(int $post_id): void {
        if (!isset($_POST['hd_banner_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hd_banner_nonce'])), 'hd_banner_link')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        update_post_meta($post_id, 'hd_banner_url', esc_url_raw(wp_unslash($_POST['hd_banner_url'] ?? '')));
    }
}
