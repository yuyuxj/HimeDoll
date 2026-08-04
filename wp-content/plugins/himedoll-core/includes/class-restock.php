<?php
defined('ABSPATH') || exit;

final class HimeDoll_Restock {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_single_product_summary', [$this, 'form'], 31);
        add_action('admin_post_nopriv_himedoll_restock_signup', [$this, 'signup']);
        add_action('admin_post_himedoll_restock_signup', [$this, 'signup']);
        add_action('admin_post_himedoll_export_restock', [$this, 'export']);
    }

    public function form(): void {
        global $product;
        if (!$product instanceof WC_Product || $product->is_in_stock()) {
            return;
        }
        ?>
        <form class="hd-restock-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="himedoll_restock_signup">
            <input type="hidden" name="product_id" value="<?php echo esc_attr((string) $product->get_id()); ?>">
            <?php wp_nonce_field('himedoll_restock_signup', 'hd_restock_nonce'); ?>
            <label>再入荷通知</label>
            <input type="email" name="email" required placeholder="メールアドレス">
            <button type="submit">登録する</button>
        </form>
        <?php
    }

    public function signup(): void {
        check_admin_referer('himedoll_restock_signup', 'hd_restock_nonce');

        $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
        $product_id = absint($_POST['product_id'] ?? 0);

        if (!$email || !$product_id) {
            wp_safe_redirect(wp_get_referer() ?: home_url('/'));
            exit;
        }

        $items = (array) get_option('hd_restock_subscribers', []);
        $items[] = [
            'email' => $email,
            'product_id' => $product_id,
            'created_at' => current_time('mysql'),
        ];

        update_option('hd_restock_subscribers', $items, false);
        wp_safe_redirect(add_query_arg('restock', 'success', wp_get_referer() ?: home_url('/')));
        exit;
    }

    public function export(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_export_restock');

        $items = (array) get_option('hd_restock_subscribers', []);

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="himedoll-restock.csv"');

        $out = fopen('php://output', 'wb');
        fwrite($out, "ï»¿");
        fputcsv($out, ['Email','Product ID','Product','Created At']);

        foreach ($items as $row) {
            fputcsv($out, [
                $row['email'] ?? '',
                $row['product_id'] ?? '',
                get_the_title((int) ($row['product_id'] ?? 0)),
                $row['created_at'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }
}
