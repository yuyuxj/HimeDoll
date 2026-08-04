<?php
defined('ABSPATH') || exit;

final class HimeDoll_Campaigns {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post_hd_campaign', [$this, 'save']);
    }

    public function register(): void {
        register_post_type('hd_campaign', [
            'labels' => [
                'name' => '营销活动',
                'singular_name' => '营销活动',
                'add_new_item' => '添加营销活动',
                'edit_item' => '编辑营销活动',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-megaphone',
            'supports' => ['title','editor','excerpt','thumbnail','page-attributes'],
        ]);
    }

    public function meta_boxes(): void {
        add_meta_box('hd_campaign_data', '活动设置', [$this, 'render'], 'hd_campaign', 'side');
    }

    public function render(WP_Post $post): void {
        wp_nonce_field('hd_campaign_data', 'hd_campaign_nonce');

        $coupon = get_post_meta($post->ID, 'hd_campaign_coupon', true);
        $deadline = get_post_meta($post->ID, 'hd_campaign_deadline', true);

        echo '<p><label>优惠券代码</label>';
        echo '<input type="text" name="hd_campaign_coupon" style="width:100%" value="' . esc_attr($coupon) . '"></p>';

        echo '<p><label>截止时间</label>';
        echo '<input type="datetime-local" name="hd_campaign_deadline" style="width:100%" value="' . esc_attr($deadline) . '"></p>';
    }

    public function save(int $post_id): void {
        if (
            !isset($_POST['hd_campaign_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['hd_campaign_nonce'])),
                'hd_campaign_data'
            )
        ) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        update_post_meta(
            $post_id,
            'hd_campaign_coupon',
            sanitize_text_field(wp_unslash($_POST['hd_campaign_coupon'] ?? ''))
        );

        update_post_meta(
            $post_id,
            'hd_campaign_deadline',
            sanitize_text_field(wp_unslash($_POST['hd_campaign_deadline'] ?? ''))
        );
    }
}
