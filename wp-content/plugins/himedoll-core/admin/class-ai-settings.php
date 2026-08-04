<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Settings {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'register']);
        add_action('admin_post_himedoll_ai_process_now', [$this, 'process_now']);
        add_action('admin_post_himedoll_ai_clear_logs', [$this, 'clear_logs']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            'AI 运营中心',
            'AI 运营中心',
            'manage_woocommerce',
            'himedoll-ai',
            [$this, 'render']
        );
    }

    public function register(): void {
        register_setting('himedoll_ai', 'hd_ai_base_url', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('himedoll_ai', 'hd_ai_api_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('himedoll_ai', 'hd_ai_model', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('himedoll_ai', 'hd_ai_timeout', ['sanitize_callback' => 'absint']);
        register_setting('himedoll_ai', 'hd_ai_prompt_description', ['sanitize_callback' => 'sanitize_textarea_field']);
        register_setting('himedoll_ai', 'hd_ai_prompt_seo', ['sanitize_callback' => 'sanitize_textarea_field']);
        register_setting('himedoll_ai', 'hd_ai_prompt_faq', ['sanitize_callback' => 'sanitize_textarea_field']);
        register_setting('himedoll_ai', 'hd_ai_prompt_alt', ['sanitize_callback' => 'sanitize_textarea_field']);
    }

    public function render(): void {
        $queue = array_reverse(HimeDoll_AI_Queue::instance()->get_queue());
        $logs = array_reverse((array) get_option('hd_ai_logs', []));
        ?>
        <div class="wrap">
            <h1>HimeDoll AI 运营中心</h1>

            <form method="post" action="options.php">
                <?php settings_fields('himedoll_ai'); ?>

                <h2>API 设置</h2>
                <table class="form-table">
                    <tr>
                        <th>Base URL</th>
                        <td><input class="regular-text" name="hd_ai_base_url"
                            value="<?php echo esc_attr(get_option('hd_ai_base_url', 'https://api.openai.com/v1')); ?>"></td>
                    </tr>
                    <tr>
                        <th>API Key</th>
                        <td><input type="password" class="regular-text" name="hd_ai_api_key"
                            value="<?php echo esc_attr(get_option('hd_ai_api_key')); ?>" autocomplete="new-password"></td>
                    </tr>
                    <tr>
                        <th>模型</th>
                        <td><input class="regular-text" name="hd_ai_model"
                            value="<?php echo esc_attr(get_option('hd_ai_model', 'gpt-4.1-mini')); ?>"></td>
                    </tr>
                    <tr>
                        <th>超时秒数</th>
                        <td><input type="number" name="hd_ai_timeout"
                            value="<?php echo esc_attr((string) get_option('hd_ai_timeout', 60)); ?>"></td>
                    </tr>
                </table>

                <h2>Prompt 模板</h2>
                <table class="form-table">
                    <?php foreach ([
                        'description' => '商品描述',
                        'seo' => 'SEO',
                        'faq' => 'FAQ',
                        'alt' => '图片 ALT',
                    ] as $key => $label) : ?>
                        <tr>
                            <th><?php echo esc_html($label); ?></th>
                            <td>
                                <textarea class="large-text" rows="5"
                                    name="hd_ai_prompt_<?php echo esc_attr($key); ?>"><?php
                                    echo esc_textarea(get_option('hd_ai_prompt_' . $key));
                                ?></textarea>
                                <p class="description">使用 {context} 插入商品信息。</p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <?php submit_button(); ?>
            </form>

            <hr>

            <h2>任务队列</h2>
            <p>
                <a class="button button-primary"
                   href="<?php echo esc_url(wp_nonce_url(
                       admin_url('admin-post.php?action=himedoll_ai_process_now'),
                       'himedoll_ai_process_now'
                   )); ?>">
                    立即处理队列
                </a>
            </p>

            <table class="widefat striped">
                <thead><tr><th>时间</th><th>任务</th><th>商品</th><th>状态</th><th>重试</th><th>错误</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($queue, 0, 100) as $job) : ?>
                    <tr>
                        <td><?php echo esc_html($job['created_at'] ?? ''); ?></td>
                        <td><?php echo esc_html($job['task'] ?? ''); ?></td>
                        <td><?php echo esc_html(get_the_title(absint($job['product_id'] ?? 0))); ?></td>
                        <td><?php echo esc_html($job['status'] ?? ''); ?></td>
                        <td><?php echo esc_html((string) ($job['attempts'] ?? 0)); ?></td>
                        <td><?php echo esc_html($job['last_error'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>AI 日志</h2>
            <p>
                <a class="button"
                   href="<?php echo esc_url(wp_nonce_url(
                       admin_url('admin-post.php?action=himedoll_ai_clear_logs'),
                       'himedoll_ai_clear_logs'
                   )); ?>">
                    清空日志
                </a>
            </p>

            <table class="widefat striped">
                <thead><tr><th>时间</th><th>状态</th><th>任务</th><th>商品</th><th>模型</th><th>Tokens</th><th>错误</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($logs, 0, 100) as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log['created_at'] ?? ''); ?></td>
                        <td><?php echo esc_html($log['status'] ?? ''); ?></td>
                        <td><?php echo esc_html($log['task'] ?? ''); ?></td>
                        <td><?php echo esc_html(get_the_title(absint($log['product_id'] ?? 0))); ?></td>
                        <td><?php echo esc_html($log['model'] ?? ''); ?></td>
                        <td><?php echo esc_html((string) (
                            absint($log['prompt_tokens'] ?? 0) +
                            absint($log['completion_tokens'] ?? 0)
                        )); ?></td>
                        <td><?php echo esc_html($log['error'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function process_now(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_ai_process_now');
        HimeDoll_AI_Queue::instance()->process();

        wp_safe_redirect(admin_url('admin.php?page=himedoll-ai'));
        exit;
    }

    public function clear_logs(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_ai_clear_logs');
        delete_option('hd_ai_logs');

        wp_safe_redirect(admin_url('admin.php?page=himedoll-ai'));
        exit;
    }
}
