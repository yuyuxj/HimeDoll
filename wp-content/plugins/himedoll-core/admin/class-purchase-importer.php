<?php
defined('ABSPATH') || exit;

final class HimeDoll_Purchase_Importer {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_himedoll_import_purchases', [$this, 'import']);
        add_action('admin_post_himedoll_export_purchases', [$this, 'export']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '采购单导入导出',
            '采购单导入导出',
            'manage_woocommerce',
            'himedoll-purchase-import',
            [$this, 'render']
        );
    }

    public function render(): void {
        ?>
        <div class="wrap">
            <h1>采购单导入导出</h1>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="himedoll_import_purchases">
                <?php wp_nonce_field('himedoll_import_purchases'); ?>
                <input type="file" name="purchase_csv" accept=".csv" required>
                <?php submit_button('导入采购单'); ?>
            </form>

            <p>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=himedoll_export_purchases'),'himedoll_export_purchases')); ?>">
                    导出全部采购单
                </a>
            </p>
        </div>
        <?php
    }

    public function import(): void {
        if (!current_user_can('manage_woocommerce')) wp_die('Permission denied.');
        check_admin_referer('himedoll_import_purchases');

        $file = $_FILES['purchase_csv']['tmp_name'] ?? '';
        if (!$file || !is_uploaded_file($file)) wp_die('CSV missing.');

        $handle = fopen($file, 'rb');
        $headers = array_map('sanitize_key', fgetcsv($handle) ?: []);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) continue;
            $data = array_combine($headers, $row);

            $post_id = wp_insert_post([
                'post_type' => 'hd_purchase_order',
                'post_status' => 'publish',
                'post_title' => sanitize_text_field($data['external_order_no'] ?: $data['product_name']),
            ]);

            if (!$post_id || is_wp_error($post_id)) continue;

            $map = [
                'supplier' => 'hd_po_supplier',
                'external_order_no' => 'hd_po_external_no',
                'product_name' => 'hd_po_product_name',
                'quantity' => 'hd_po_quantity',
                'unit_cost' => 'hd_po_unit_cost',
                'currency' => 'hd_po_currency',
                'purchase_note' => 'hd_po_note',
                'tracking_no' => 'hd_po_tracking_no',
                'warehouse_note' => 'hd_po_warehouse_note',
                'status' => 'hd_po_status',
            ];

            foreach ($map as $csv_key => $meta_key) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($data[$csv_key] ?? ''));
            }

            HimeDoll_Order_Matcher::instance()->match_purchase_order($post_id);
        }

        fclose($handle);
        wp_safe_redirect(admin_url('admin.php?page=himedoll-purchase-import'));
        exit;
    }

    public function export(): void {
        if (!current_user_can('manage_woocommerce')) wp_die('Permission denied.');
        check_admin_referer('himedoll_export_purchases');

        $posts = get_posts([
            'post_type' => 'hd_purchase_order',
            'post_status' => ['publish','draft','private'],
            'numberposts' => -1,
        ]);

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="himedoll-purchases.csv"');

        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['supplier','external_order_no','product_name','quantity','unit_cost','currency','purchase_note','tracking_no','warehouse_note','status','wc_order_id']);

        foreach ($posts as $post) {
            fputcsv($out, [
                get_post_meta($post->ID, 'hd_po_supplier', true),
                get_post_meta($post->ID, 'hd_po_external_no', true),
                get_post_meta($post->ID, 'hd_po_product_name', true),
                get_post_meta($post->ID, 'hd_po_quantity', true),
                get_post_meta($post->ID, 'hd_po_unit_cost', true),
                get_post_meta($post->ID, 'hd_po_currency', true),
                get_post_meta($post->ID, 'hd_po_note', true),
                get_post_meta($post->ID, 'hd_po_tracking_no', true),
                get_post_meta($post->ID, 'hd_po_warehouse_note', true),
                get_post_meta($post->ID, 'hd_po_status', true),
                get_post_meta($post->ID, 'hd_po_wc_order_id', true),
            ]);
        }

        fclose($out);
        exit;
    }
}
