<?php
defined('ABSPATH') || exit;

final class HimeDoll_Order_Export {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_himedoll_export_orders', [$this, 'export']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '订单导出',
            '订单导出',
            'manage_woocommerce',
            'himedoll-order-export',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>订单 CSV 导出</h1>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="himedoll_export_orders">
                <?php wp_nonce_field('himedoll_export_orders'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="date_from">开始日期</label></th>
                        <td><input id="date_from" type="date" name="date_from"></td>
                    </tr>
                    <tr>
                        <th><label for="date_to">结束日期</label></th>
                        <td><input id="date_to" type="date" name="date_to"></td>
                    </tr>
                    <tr>
                        <th><label for="status">订单状态</label></th>
                        <td>
                            <select id="status" name="status">
                                <option value="">全部</option>
                                <option value="processing">处理中</option>
                                <option value="completed">已完成</option>
                                <option value="on-hold">保留</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button('导出 CSV'); ?>
            </form>
        </div>
        <?php
    }

    public function export(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_export_orders');

        $args = [
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $status = sanitize_key($_POST['status'] ?? '');
        if ($status) {
            $args['status'] = $status;
        }

        $from = sanitize_text_field($_POST['date_from'] ?? '');
        $to = sanitize_text_field($_POST['date_to'] ?? '');

        if ($from || $to) {
            $args['date_created'] = trim($from . '...' . $to, '.');
        }

        $orders = wc_get_orders($args);

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="himedoll-orders-' . gmdate('Ymd-His') . '.csv"');

        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            '订单号','订单日期','状态','客户姓名','电话','邮箱',
            '邮编','都道府县','城市','地址1','地址2',
            '商品','数量','订单金额','支付方式','客户备注'
        ]);

        foreach ($orders as $order) {
            $items = [];
            $quantity = 0;

            foreach ($order->get_items() as $item) {
                $items[] = $item->get_name();
                $quantity += $item->get_quantity();
            }

            fputcsv($out, [
                $order->get_order_number(),
                $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '',
                wc_get_order_status_name($order->get_status()),
                trim($order->get_shipping_last_name() . ' ' . $order->get_shipping_first_name()),
                $order->get_billing_phone(),
                $order->get_billing_email(),
                $order->get_shipping_postcode(),
                $order->get_shipping_state(),
                $order->get_shipping_city(),
                $order->get_shipping_address_1(),
                $order->get_shipping_address_2(),
                implode(' / ', $items),
                $quantity,
                $order->get_total(),
                $order->get_payment_method_title(),
                $order->get_customer_note(),
            ]);
        }

        fclose($out);
        exit;
    }
}
