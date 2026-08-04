<?php
defined('ABSPATH') || exit;

final class HimeDoll_Growth_Settings {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'register']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '增长设置',
            '增长设置',
            'manage_options',
            'himedoll-growth',
            [$this, 'render']
        );
    }

    public function register(): void {
        register_setting('himedoll_growth', 'hd_ga4_id', ['sanitize_callback'=>'sanitize_text_field']);
        register_setting('himedoll_growth', 'hd_gtm_id', ['sanitize_callback'=>'sanitize_text_field']);
        register_setting('himedoll_growth', 'hd_promo_deadline', ['sanitize_callback'=>'sanitize_text_field']);
    }

    public function render(): void {
        $restock = (array) get_option('hd_restock_subscribers', []);
        ?>
        <div class="wrap">
            <h1>HimeDoll 增长设置</h1>
            <form method="post" action="options.php">
                <?php settings_fields('himedoll_growth'); ?>
                <table class="form-table">
                    <tr><th>GA4 ID</th><td><input class="regular-text" name="hd_ga4_id" value="<?php echo esc_attr(get_option('hd_ga4_id')); ?>" placeholder="G-XXXXXXXXXX"></td></tr>
                    <tr><th>GTM ID</th><td><input class="regular-text" name="hd_gtm_id" value="<?php echo esc_attr(get_option('hd_gtm_id')); ?>" placeholder="GTM-XXXXXXX"></td></tr>
                    <tr><th>活动截止时间</th><td><input type="datetime-local" name="hd_promo_deadline" value="<?php echo esc_attr(get_option('hd_promo_deadline')); ?>"></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <hr>
            <h2>到货通知</h2>
            <p>当前登记：<?php echo esc_html((string) count($restock)); ?></p>
            <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=himedoll_export_restock'),'himedoll_export_restock')); ?>">导出 CSV</a>
        </div>
        <?php
    }
}
