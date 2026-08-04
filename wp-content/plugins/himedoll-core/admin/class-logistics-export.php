<?php
defined('ABSPATH') || exit;

final class HimeDoll_Logistics_Export {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_himedoll_logistics_export', [$this, 'export']);
        add_action('add_meta_boxes_shop_order', [$this, 'order_meta_box']);
        add_action('woocommerce_process_shop_order_meta', [$this, 'save_order_meta']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '物流导出',
            '物流导出',
            'manage_woocommerce',
            'himedoll-logistics-export',
            [$this, 'render']
        );
    }

    public function render(): void {
        $orders = wc_get_orders(['limit' => 50, 'orderby' => 'date', 'order' => 'DESC']);
        ?>
        <div class="wrap">
            <h1>物流导出</h1>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="himedoll_logistics_export">
                <?php wp_nonce_field('himedoll_logistics_export'); ?>

                <p>
                    <select name="carrier" required>
                        <?php foreach (HimeDoll_Logistics::instance()->carriers() as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <table class="widefat striped">
                    <thead><tr><th></th><th>订单号</th><th>客户</th><th>状态</th><th>当前物流</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><input type="checkbox" name="order_ids[]" value="<?php echo esc_attr((string) $order->get_id()); ?>"></td>
                            <td><?php echo esc_html($order->get_order_number()); ?></td>
                            <td><?php echo esc_html($order->get_formatted_shipping_full_name()); ?></td>
                            <td><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></td>
                            <td><?php echo esc_html(HimeDoll_Logistics::instance()->get($order->get_id())); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php submit_button('分配物流并导出'); ?>
            </form>
        </div>
        <?php
    }

    public function order_meta_box(WP_Post $post): void {
        wp_nonce_field('hd_order_erp', 'hd_order_erp_nonce');
        $carrier = get_post_meta($post->ID, '_hd_logistics_carrier', true);
        $tracking = get_post_meta($post->ID, '_hd_tracking_no', true);
        $warehouse_note = get_post_meta($post->ID, '_hd_warehouse_note', true);

        echo '<p><label>物流公司</label><select name="_hd_logistics_carrier" style="width:100%">';
        echo '<option value="">未选择</option>';
        foreach (HimeDoll_Logistics::instance()->carriers() as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($carrier, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select></p>';

        echo '<p><label>运单号</label><input style="width:100%" name="_hd_tracking_no" value="' . esc_attr($tracking) . '"></p>';
        echo '<p><label>仓库备注</label><textarea style="width:100%" name="_hd_warehouse_note">' . esc_textarea($warehouse_note) . '</textarea></p>';
    }

    public function save_order_meta(int $order_id): void {
        if (!isset($_POST['hd_order_erp_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hd_order_erp_nonce'])), 'hd_order_erp')) return;
        if (!current_user_can('edit_shop_order', $order_id)) return;

        foreach (['_hd_logistics_carrier','_hd_tracking_no','_hd_warehouse_note'] as $key) {
            if (isset($_POST[$key])) update_post_meta($order_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
        }
    }

    public function export(): void {
        if (!current_user_can('manage_woocommerce')) wp_die('Permission denied.');
        check_admin_referer('himedoll_logistics_export');

        $carrier = sanitize_key($_POST['carrier'] ?? '');
        $order_ids = array_values(array_filter(array_map('absint', (array) ($_POST['order_ids'] ?? []))));
        if (!$carrier || !$order_ids) wp_die('请选择物流和订单。');

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $carrier . '-' . gmdate('Ymd-His') . '.csv"');

        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF");

        if ($carrier === 'junjia') {
            fputcsv($out, ['收件人','收件人电话','收件人地址','收件人邮编']);
        } elseif ($carrier === 'lindao') {
            fputcsv($out, ['收件人名','收件人片假名','收件人公司','收件人公司（英文）','收件人电话','收件人地址','收件人邮编']);
        } else {
            fputcsv($out, ['平台单号','客户备注','仓库备注','发货日期','运费','国际转运单号','重量','收件人姓名','收件人电话','收件人地址','邮编']);
        }

        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);
            if (!$order) continue;

            HimeDoll_Logistics::instance()->assign($order_id, $carrier);

            $name = trim($order->get_shipping_last_name() . ' ' . $order->get_shipping_first_name());
            $phone = $order->get_billing_phone();
            $address = trim(
                $order->get_shipping_state() .
                $order->get_shipping_city() .
                $order->get_shipping_address_1() .
                $order->get_shipping_address_2()
            );
            $postcode = $order->get_shipping_postcode();

            if ($carrier === 'junjia') {
                fputcsv($out, [$name,$phone,$address,$postcode]);
            } elseif ($carrier === 'lindao') {
                fputcsv($out, [$name,'','','',$phone,$address,$postcode]);
            } else {
                fputcsv($out, [
                    $order->get_order_number(),
                    $order->get_customer_note(),
                    get_post_meta($order_id, '_hd_warehouse_note', true),
                    '',
                    '',
                    get_post_meta($order_id, '_hd_tracking_no', true),
                    '',
                    $name,
                    $phone,
                    $address,
                    $postcode
                ]);
            }
        }

        fclose($out);
        exit;
    }
}
