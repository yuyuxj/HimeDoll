<?php
defined('ABSPATH') || exit;

final class HimeDoll_Settings {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'register']);
    }

    public function menu(): void {
        add_menu_page(
            'HimeDoll Settings',
            'HimeDoll',
            'manage_options',
            'himedoll-settings',
            [$this, 'render'],
            'dashicons-store',
            58
        );
    }

    public function register(): void {
        register_setting('himedoll_settings', 'hd_site_announcement', ['sanitize_callback'=>'sanitize_text_field']);
        register_setting('himedoll_settings', 'hd_home_hero_title', ['sanitize_callback'=>'sanitize_text_field']);
        register_setting('himedoll_settings', 'hd_home_hero_text', ['sanitize_callback'=>'sanitize_textarea_field']);
        register_setting('himedoll_settings', 'hd_support_email', ['sanitize_callback'=>'sanitize_email']);
    }

    public function render(): void {
        ?>
        <div class="wrap">
            <h1>HimeDoll 设置</h1>
            <form method="post" action="options.php">
                <?php settings_fields('himedoll_settings'); ?>
                <table class="form-table">
                    <tr><th>顶部公告</th><td><input class="regular-text" name="hd_site_announcement" value="<?php echo esc_attr(get_option('hd_site_announcement')); ?>"></td></tr>
                    <tr><th>首页主标题</th><td><input class="regular-text" name="hd_home_hero_title" value="<?php echo esc_attr(get_option('hd_home_hero_title')); ?>"></td></tr>
                    <tr><th>首页说明</th><td><textarea class="large-text" name="hd_home_hero_text"><?php echo esc_textarea(get_option('hd_home_hero_text')); ?></textarea></td></tr>
                    <tr><th>客服邮箱</th><td><input type="email" class="regular-text" name="hd_support_email" value="<?php echo esc_attr(get_option('hd_support_email')); ?>"></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
