<?php
defined('ABSPATH') || exit;

final class HimeDoll_ERP_Dashboard {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            'ERP 仪表盘',
            'ERP 仪表盘',
            'manage_woocommerce',
            'himedoll-erp',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) return;

        $purchase_count = wp_count_posts('hd_purchase_order');
        $matched = new WP_Query([
            'post_type' => 'hd_purchase_order',
            'post_status' => ['publish','draft','private'],
            'posts_per_page' => 1,
            'meta_query' => [['key' => 'hd_po_wc_order_id', 'value' => 0, 'compare' => '>', 'type' => 'NUMERIC']],
        ]);

        $unmatched = new WP_Query([
            'post_type' => 'hd_purchase_order',
            'post_status' => ['publish','draft','private'],
            'posts_per_page' => 20,
            'meta_query' => [
                'relation' => 'OR',
                ['key' => 'hd_po_wc_order_id', 'compare' => 'NOT EXISTS'],
                ['key' => 'hd_po_wc_order_id', 'value' => ''],
            ],
        ]);
        ?>
        <div class="wrap">
            <h1>HimeDoll ERP 仪表盘</h1>

            <div style="display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:16px;max-width:900px">
                <div style="background:#fff;border:1px solid #ddd;padding:20px">
                    <strong style="font-size:28px"><?php echo esc_html((string) ($purchase_count->publish ?? 0)); ?></strong>
                    <p>采购单</p>
                </div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px">
                    <strong style="font-size:28px"><?php echo esc_html((string) $matched->found_posts); ?></strong>
                    <p>已匹配订单</p>
                </div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px">
                    <strong style="font-size:28px"><?php echo esc_html((string) $unmatched->found_posts); ?></strong>
                    <p>未匹配采购单</p>
                </div>
            </div>

            <h2>未匹配采购单</h2>
            <table class="widefat striped" style="max-width:1000px">
                <thead><tr><th>采购单</th><th>供应商</th><th>采购备注</th><th>操作</th></tr></thead>
                <tbody>
                <?php foreach ($unmatched->posts as $post) : ?>
                    <tr>
                        <td><?php echo esc_html(get_the_title($post)); ?></td>
                        <td><?php echo esc_html(get_post_meta($post->ID, 'hd_po_supplier', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($post->ID, 'hd_po_note', true)); ?></td>
                        <td><a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>">编辑</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
