<?php
defined('ABSPATH') || exit;

final class HimeDoll_Marketing_Settings {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'register']);
        add_action('admin_post_himedoll_export_newsletter', [$this, 'export']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '营销设置',
            '营销设置',
            'manage_options',
            'himedoll-marketing',
            [$this, 'render']
        );
    }

    public function register(): void {
        register_setting('himedoll_marketing', 'hd_line_url', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('himedoll_marketing', 'hd_promotion_text', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('himedoll_marketing', 'hd_company_name', ['sanitize_callback' => 'sanitize_text_field']);
    }

    public function render(): void {
        $subscribers = (array) get_option('hd_newsletter_subscribers', []);
        ?>
        <div class="wrap">
            <h1>HimeDoll 营销设置</h1>

            <form method="post" action="options.php">
                <?php settings_fields('himedoll_marketing'); ?>
                <table class="form-table">
                    <tr>
                        <th>LINE URL</th>
                        <td><input class="regular-text" name="hd_line_url" value="<?php echo esc_attr(get_option('hd_line_url')); ?>"></td>
                    </tr>
                    <tr>
                        <th>活动文案</th>
                        <td><input class="large-text" name="hd_promotion_text" value="<?php echo esc_attr(get_option('hd_promotion_text')); ?>"></td>
                    </tr>
                    <tr>
                        <th>公司名称</th>
                        <td><input class="regular-text" name="hd_company_name" value="<?php echo esc_attr(get_option('hd_company_name')); ?>"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <hr>
            <h2>Newsletter</h2>
            <p>当前订阅人数：<?php echo esc_html((string) count($subscribers)); ?></p>
            <a class="button button-secondary"
               href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=himedoll_export_newsletter'), 'himedoll_export_newsletter')); ?>">
                导出 CSV
            </a>
        </div>
        <?php
    }

    public function export(): void {
        if (!current_user_can('manage_options')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_export_newsletter');

        $subscribers = (array) get_option('hd_newsletter_subscribers', []);

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="himedoll-newsletter.csv"');

        $output = fopen('php://output', 'wb');
        fwrite($output, "ï»¿");
        fputcsv($output, ['Email', 'Created At']);

        foreach ($subscribers as $row) {
            fputcsv($output, [
                $row['email'] ?? '',
                $row['created_at'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}
