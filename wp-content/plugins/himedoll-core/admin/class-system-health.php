<?php
defined('ABSPATH') || exit;

final class HimeDoll_System_Health {
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
            '系统状态',
            '系统状态',
            'manage_options',
            'himedoll-system-health',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_options')) return;

        $upload = wp_upload_dir();
        $checks = [
            ['WordPress', get_bloginfo('version'), true],
            ['PHP', PHP_VERSION, version_compare(PHP_VERSION, '8.0', '>=')],
            ['WooCommerce', defined('WC_VERSION') ? WC_VERSION : '未启用', defined('WC_VERSION')],
            ['HTTPS', is_ssl() ? '正常' : '未启用', is_ssl()],
            ['上传目录', is_writable($upload['basedir']) ? '可写' : '不可写', is_writable($upload['basedir'])],
            ['WP-Cron', defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? '已禁用' : '启用', !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON)],
            ['固定链接', get_option('permalink_structure') ? '已设置' : '未设置', (bool) get_option('permalink_structure')],
        ];

        $logs = array_reverse((array) get_option('hd_email_logs', []));
        ?>
        <div class="wrap">
            <h1>HimeDoll 系统状态</h1>

            <table class="widefat striped" style="max-width:900px">
                <thead><tr><th>项目</th><th>状态</th><th>结果</th></tr></thead>
                <tbody>
                <?php foreach ($checks as [$label, $value, $ok]) : ?>
                    <tr>
                        <td><?php echo esc_html($label); ?></td>
                        <td><?php echo esc_html((string) $value); ?></td>
                        <td><?php echo $ok ? '✅' : '⚠️'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>最近邮件日志</h2>
            <table class="widefat striped" style="max-width:1100px">
                <thead><tr><th>时间</th><th>状态</th><th>收件人</th><th>标题</th><th>错误</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($logs, 0, 50) as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log['created_at'] ?? ''); ?></td>
                        <td><?php echo esc_html($log['status'] ?? ''); ?></td>
                        <td><?php echo esc_html($log['to'] ?? ''); ?></td>
                        <td><?php echo esc_html($log['subject'] ?? ''); ?></td>
                        <td><?php echo esc_html($log['error'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
