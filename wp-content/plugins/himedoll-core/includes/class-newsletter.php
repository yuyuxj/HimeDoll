<?php
defined('ABSPATH') || exit;

final class HimeDoll_Newsletter {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_post_nopriv_himedoll_newsletter_signup', [$this, 'signup']);
        add_action('admin_post_himedoll_newsletter_signup', [$this, 'signup']);
    }

    public function signup(): void {
        check_admin_referer('himedoll_newsletter_signup', 'hd_newsletter_nonce');

        $email = isset($_POST['email'])
            ? sanitize_email(wp_unslash($_POST['email']))
            : '';

        if (!$email || !is_email($email)) {
            wp_safe_redirect(add_query_arg('newsletter', 'invalid', wp_get_referer() ?: home_url('/')));
            exit;
        }

        $subscribers = (array) get_option('hd_newsletter_subscribers', []);
        $subscribers[$email] = [
            'email' => $email,
            'created_at' => current_time('mysql'),
        ];

        update_option('hd_newsletter_subscribers', $subscribers, false);

        wp_safe_redirect(add_query_arg('newsletter', 'success', wp_get_referer() ?: home_url('/')));
        exit;
    }
}
