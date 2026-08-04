<?php
defined('ABSPATH') || exit;

final class HimeDoll_Operations_Dashboard {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '运营仪表盘',
            '运营仪表盘',
            'manage_woocommerce',
            'himedoll-operations',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $published = wp_count_posts('product')->publish ?? 0;
        $drafts = wp_count_posts('product')->draft ?? 0;

        $low_stock = new WP_Query([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 20,
            'meta_query' => [
                'relation' => 'AND',
                ['key' => '_manage_stock', 'value' => 'yes'],
                ['key' => '_stock', 'value' => 5, 'compare' => '<=', 'type' => 'NUMERIC'],
            ],
        ]);

        $missing_image = new WP_Query([
            'post_type' => 'product',
            'post_status' => ['publish', 'draft'],
            'posts_per_page' => 20,
            'meta_query' => [
                ['key' => '_thumbnail_id', 'compare' => 'NOT EXISTS'],
            ],
        ]);

        $missing_seo = new WP_Query([
            'post_type' => 'product',
            'post_status' => ['publish', 'draft'],
            'posts_per_page' => 20,
            'meta_query' => [
                'relation' => 'OR',
                ['key' => 'hd_seo_title', 'compare' => 'NOT EXISTS'],
                ['key' => 'hd_seo_title', 'value' => ''],
            ],
        ]);
        ?>
        <div class="wrap">
            <h1>HimeDoll 运营仪表盘</h1>

            <div style="display:grid;grid-template-columns:repeat(4,minmax(160px,1fr));gap:16px;max-width:1100px">
                <?php
                $cards = [
                    ['已发布商品', $published],
                    ['草稿商品', $drafts],
                    ['低库存', $low_stock->found_posts],
                    ['缺少 SEO', $missing_seo->found_posts],
                ];
                foreach ($cards as [$label, $value]) :
                ?>
                    <div style="background:#fff;border:1px solid #ddd;padding:20px;border-radius:8px">
                        <strong style="font-size:28px"><?php echo esc_html((string) $value); ?></strong>
                        <p><?php echo esc_html($label); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php $this->render_table('低库存商品', $low_stock, true); ?>
            <?php $this->render_table('缺少主图商品', $missing_image, false); ?>
            <?php $this->render_table('缺少 SEO 标题商品', $missing_seo, false); ?>
        </div>
        <?php
        wp_reset_postdata();
    }

    private function render_table(string $title, WP_Query $query, bool $show_stock): void {
        ?>
        <h2 style="margin-top:35px"><?php echo esc_html($title); ?></h2>
        <table class="widefat striped" style="max-width:1100px">
            <thead>
                <tr>
                    <th>商品</th>
                    <th>SKU</th>
                    <?php if ($show_stock) : ?><th>库存</th><?php endif; ?>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$query->have_posts()) : ?>
                    <tr><td colspan="4">没有记录</td></tr>
                <?php else : ?>
                    <?php foreach ($query->posts as $post) :
                        $product = wc_get_product($post->ID);
                        ?>
                        <tr>
                            <td><?php echo esc_html(get_the_title($post)); ?></td>
                            <td><?php echo $product ? esc_html($product->get_sku()) : ''; ?></td>
                            <?php if ($show_stock) : ?>
                                <td><?php echo $product ? esc_html((string) $product->get_stock_quantity()) : ''; ?></td>
                            <?php endif; ?>
                            <td><a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>">编辑</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
    }
}
