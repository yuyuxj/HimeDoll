<?php
defined('ABSPATH') || exit;

final class HimeDoll_Retention_Dashboard {
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
            '保留率分析',
            '保留率分析',
            'manage_woocommerce',
            'himedoll-retention',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $users = get_users([
            'fields' => ['ID', 'display_name', 'user_email'],
            'number' => 500,
        ]);

        $segments = ['new' => 0, 'repeat' => 0, 'vip' => 0, 'unknown' => 0];

        foreach ($users as $user) {
            $segment = get_user_meta($user->ID, 'hd_customer_segment', true) ?: 'unknown';
            $segments[$segment] = ($segments[$segment] ?? 0) + 1;
        }

        $search_logs = array_values((array) get_option('hd_search_logs', []));
        ?>
        <div class="wrap">
            <h1>HimeDoll 保留率与搜索分析</h1>

            <div style="display:grid;grid-template-columns:repeat(4,minmax(150px,1fr));gap:16px;max-width:1000px">
                <?php foreach ($segments as $label => $count) : ?>
                    <div style="background:#fff;border:1px solid #ddd;padding:20px">
                        <strong style="font-size:28px"><?php echo esc_html((string) $count); ?></strong>
                        <p><?php echo esc_html($label); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>热门搜索词</h2>
            <table class="widefat striped" style="max-width:900px">
                <thead><tr><th>关键词</th><th>次数</th><th>最后搜索</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($search_logs, 0, 100) as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row['query'] ?? ''); ?></td>
                        <td><?php echo esc_html((string) ($row['count'] ?? 0)); ?></td>
                        <td><?php echo esc_html($row['last_searched'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>客户分层</h2>
            <table class="widefat striped" style="max-width:1000px">
                <thead><tr><th>客户</th><th>邮箱</th><th>分层</th><th>完成订单数</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo esc_html($user->display_name); ?></td>
                        <td><?php echo esc_html($user->user_email); ?></td>
                        <td><?php echo esc_html(get_user_meta($user->ID, 'hd_customer_segment', true) ?: 'unknown'); ?></td>
                        <td><?php echo esc_html((string) get_user_meta($user->ID, 'hd_completed_order_count', true)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
