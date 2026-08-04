<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Product_Panel {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('add_meta_boxes', [$this, 'meta_box']);
        add_action('admin_post_himedoll_ai_product_action', [$this, 'action']);
        add_filter('bulk_actions-edit-product', [$this, 'bulk_actions']);
        add_filter('handle_bulk_actions-edit-product', [$this, 'handle_bulk'], 10, 3);
    }

    public function meta_box(): void {
        add_meta_box(
            'himedoll_ai_product',
            'HimeDoll AI',
            [$this, 'render'],
            'product',
            'side',
            'high'
        );
    }

    public function render(WP_Post $post): void {
        wp_nonce_field('himedoll_ai_product_action', 'himedoll_ai_nonce');

        foreach ([
            'description' => '生成商品描述',
            'seo' => '生成 SEO',
            'faq' => '生成 FAQ',
            'alt' => '生成主图 ALT',
        ] as $task => $label) {
            $url = wp_nonce_url(
                add_query_arg([
                    'action' => 'himedoll_ai_product_action',
                    'product_id' => $post->ID,
                    'task' => $task,
                ], admin_url('admin-post.php')),
                'himedoll_ai_product_action'
            );

            echo '<p><a class="button button-secondary" style="width:100%;text-align:center" href="' .
                esc_url($url) . '">' . esc_html($label) . '</a></p>';
        }

        $faq = get_post_meta($post->ID, 'hd_ai_faq', true);
        if ($faq) {
            echo '<p><strong>FAQ 草稿已生成</strong></p>';
        }
    }

    public function action(): void {
        if (!current_user_can('edit_products')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_ai_product_action');

        $product_id = absint($_GET['product_id'] ?? 0);
        $task = sanitize_key($_GET['task'] ?? '');

        if (!$product_id || !in_array($task, ['description','seo','faq','alt'], true)) {
            wp_die('Invalid AI task.');
        }

        HimeDoll_AI_Queue::instance()->add($task, $product_id);

        wp_safe_redirect(get_edit_post_link($product_id, 'url'));
        exit;
    }

    public function bulk_actions(array $actions): array {
        $actions['hd_ai_description'] = 'AI：生成商品描述';
        $actions['hd_ai_seo'] = 'AI：生成 SEO';
        $actions['hd_ai_faq'] = 'AI：生成 FAQ';
        $actions['hd_ai_alt'] = 'AI：生成主图 ALT';
        return $actions;
    }

    public function handle_bulk(string $redirect, string $action, array $post_ids): string {
        $map = [
            'hd_ai_description' => 'description',
            'hd_ai_seo' => 'seo',
            'hd_ai_faq' => 'faq',
            'hd_ai_alt' => 'alt',
        ];

        if (!isset($map[$action])) {
            return $redirect;
        }

        foreach ($post_ids as $product_id) {
            HimeDoll_AI_Queue::instance()->add($map[$action], absint($product_id));
        }

        return add_query_arg('hd_ai_queued', count($post_ids), $redirect);
    }
}
