<?php
defined('ABSPATH') || exit;

final class HimeDoll_Enterprise_Settings {
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
            '企业设置',
            '企业设置',
            'manage_options',
            'himedoll-enterprise-settings',
            [$this, 'render']
        );
    }

    public function register(): void {
        $fields = [
            'hd_points_per_yen_unit' => 'absint',
            'hd_yen_per_point' => 'absint',
            'hd_tier_silver' => 'absint',
            'hd_tier_gold' => 'absint',
            'hd_tier_vip' => 'absint',
            'hd_discount_silver' => 'floatval',
            'hd_discount_gold' => 'floatval',
            'hd_discount_vip' => 'floatval',
            'hd_referral_rate' => 'floatval',
            'hd_enterprise_api_key' => 'sanitize_text_field',
            'hd_order_webhook_url' => 'esc_url_raw',
            'hd_order_webhook_secret' => 'sanitize_text_field',
        ];

        foreach ($fields as $key => $sanitize) {
            register_setting('himedoll_enterprise', $key, ['sanitize_callback' => $sanitize]);
        }
    }

    public function render(): void {
        ?>
        <div class="wrap">
            <h1>HimeDoll 企业设置</h1>
            <form method="post" action="options.php">
                <?php settings_fields('himedoll_enterprise'); ?>

                <h2>积分</h2>
                <table class="form-table">
                    <tr><th>每多少日元获得1积分</th><td><input type="number" name="hd_points_per_yen_unit" value="<?php echo esc_attr((string) get_option('hd_points_per_yen_unit', 100)); ?>"></td></tr>
                    <tr><th>每积分抵扣日元</th><td><input type="number" name="hd_yen_per_point" value="<?php echo esc_attr((string) get_option('hd_yen_per_point', 1)); ?>"></td></tr>
                </table>

                <h2>会员等级</h2>
                <table class="form-table">
                    <tr><th>Silver 门槛</th><td><input type="number" name="hd_tier_silver" value="<?php echo esc_attr((string) get_option('hd_tier_silver', 300000)); ?>"></td></tr>
                    <tr><th>Gold 门槛</th><td><input type="number" name="hd_tier_gold" value="<?php echo esc_attr((string) get_option('hd_tier_gold', 800000)); ?>"></td></tr>
                    <tr><th>VIP 门槛</th><td><input type="number" name="hd_tier_vip" value="<?php echo esc_attr((string) get_option('hd_tier_vip', 1500000)); ?>"></td></tr>
                    <tr><th>Silver 折扣 %</th><td><input type="number" step="0.1" name="hd_discount_silver" value="<?php echo esc_attr((string) get_option('hd_discount_silver', 1)); ?>"></td></tr>
                    <tr><th>Gold 折扣 %</th><td><input type="number" step="0.1" name="hd_discount_gold" value="<?php echo esc_attr((string) get_option('hd_discount_gold', 2)); ?>"></td></tr>
                    <tr><th>VIP 折扣 %</th><td><input type="number" step="0.1" name="hd_discount_vip" value="<?php echo esc_attr((string) get_option('hd_discount_vip', 3)); ?>"></td></tr>
                </table>

                <h2>推荐与接口</h2>
                <table class="form-table">
                    <tr><th>推荐返佣 %</th><td><input type="number" step="0.1" name="hd_referral_rate" value="<?php echo esc_attr((string) get_option('hd_referral_rate', 3)); ?>"></td></tr>
                    <tr><th>企业 API Key</th><td><input class="regular-text" name="hd_enterprise_api_key" value="<?php echo esc_attr((string) get_option('hd_enterprise_api_key')); ?>"></td></tr>
                    <tr><th>订单 Webhook URL</th><td><input class="regular-text" name="hd_order_webhook_url" value="<?php echo esc_attr((string) get_option('hd_order_webhook_url')); ?>"></td></tr>
                    <tr><th>Webhook Secret</th><td><input class="regular-text" name="hd_order_webhook_secret" value="<?php echo esc_attr((string) get_option('hd_order_webhook_secret')); ?>"></td></tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
