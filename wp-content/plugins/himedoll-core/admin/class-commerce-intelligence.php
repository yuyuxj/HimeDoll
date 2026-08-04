<?php
defined('ABSPATH') || exit;

final class HimeDoll_Commerce_Intelligence {
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
            '销售分析',
            '销售分析',
            'manage_woocommerce',
            'himedoll-commerce-intelligence',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) return;

        $orders = wc_get_orders([
            'limit' => -1,
            'status' => ['processing','completed'],
            'date_created' => '>' . (time() - 30 * DAY_IN_SECONDS),
        ]);

        $revenue = 0.0;
        $item_sales = [];
        $status_count = [];

        foreach ($orders as $order) {
            $revenue += (float) $order->get_total();
            $status = $order->get_status();
            $status_count[$status] = ($status_count[$status] ?? 0) + 1;

            foreach ($order->get_items() as $item) {
                $name = $item->get_name();
                $item_sales[$name] = ($item_sales[$name] ?? 0) + $item->get_quantity();
            }
        }

        arsort($item_sales);
        $aov = count($orders) ? $revenue / count($orders) : 0;
        ?>
        <div class="wrap">
            <h1>HimeDoll 销售分析</h1>
            <p>最近30天</p>

            <div style="display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:16px;max-width:1000px">
                <div style="background:#fff;border:1px solid #ddd;padding:20px">
                    <strong style="font-size:28px"><?php echo wp_kses_post(wc_price($revenue)); ?></strong>
                    <p>销售额</p>
                </div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px">
                    <strong style="font-size:28px"><?php echo esc_html((string) count($orders)); ?></strong>
                    <p>订单数</p>
                </div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px">
                    <strong style="font-size:28px"><?php echo wp_kses_post(wc_price($aov)); ?></strong>
                    <p>平均客单价</p>
                </div>
            </div>

            <h2>畅销商品</h2>
            <table class="widefat striped" style="max-width:1000px">
                <thead><tr><th>商品</th><th>销量</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($item_sales, 0, 20, true) as $name => $qty) : ?>
                    <tr><td><?php echo esc_html($name); ?></td><td><?php echo esc_html((string) $qty); ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>订单状态</h2>
            <table class="widefat striped" style="max-width:600px">
                <thead><tr><th>状态</th><th>数量</th></tr></thead>
                <tbody>
                <?php foreach ($status_count as $status => $qty) : ?>
                    <tr><td><?php echo esc_html(wc_get_order_status_name($status)); ?></td><td><?php echo esc_html((string) $qty); ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
