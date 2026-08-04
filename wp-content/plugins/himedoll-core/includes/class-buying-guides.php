<?php
defined('ABSPATH') || exit;

final class HimeDoll_Buying_Guides {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post_product', [$this, 'save_product_guides']);
    }

    public function register(): void {
        register_post_type('hd_buying_guide', [
            'labels' => [
                'name' => '购买指南',
                'singular_name' => '购买指南',
                'add_new_item' => '添加购买指南',
                'edit_item' => '编辑购买指南',
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'guide'],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-book-alt',
            'supports' => ['title','editor','excerpt','thumbnail','author','revisions'],
        ]);
    }

    public function meta_boxes(): void {
        add_meta_box(
            'hd_related_guides',
            '关联购买指南',
            [$this, 'render_product_guides'],
            'product',
            'side'
        );
    }

    public function render_product_guides(WP_Post $post): void {
        wp_nonce_field('hd_related_guides', 'hd_related_guides_nonce');

        $selected = array_map(
            'absint',
            (array) get_post_meta($post->ID, 'hd_related_guides', true)
        );

        $guides = get_posts([
            'post_type' => 'hd_buying_guide',
            'post_status' => 'publish',
            'numberposts' => 100,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        foreach ($guides as $guide) {
            echo '<label style="display:block;margin:6px 0">';
            echo '<input type="checkbox" name="hd_related_guides[]" value="' . esc_attr((string) $guide->ID) . '" ' .
                checked(in_array($guide->ID, $selected, true), true, false) . '>';
            echo ' ' . esc_html($guide->post_title);
            echo '</label>';
        }
    }

    public function save_product_guides(int $post_id): void {
        if (
            !isset($_POST['hd_related_guides_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['hd_related_guides_nonce'])),
                'hd_related_guides'
            )
        ) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $ids = isset($_POST['hd_related_guides'])
            ? array_values(array_filter(array_map('absint', (array) $_POST['hd_related_guides'])))
            : [];

        update_post_meta($post_id, 'hd_related_guides', $ids);
    }
}
