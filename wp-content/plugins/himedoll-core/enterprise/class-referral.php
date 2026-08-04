<?php
defined('ABSPATH') || exit;

final class HimeDoll_Referral {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('init', [$this, 'capture']);
        add_action('user_register', [$this, 'create_code']);
        add_action('woocommerce_order_status_completed', [$this, 'commission']);
    }

    public function ensure_code(int $user_id): string {
        $code = (string) get_user_meta($user_id, 'hd_referral_code', true);

        if (!$code) {
            $code = strtoupper(wp_generate_password(8, false, false));
            update_user_meta($user_id, 'hd_referral_code', $code);
        }

        return $code;
    }

    public function create_code(int $user_id): void {
        $this->ensure_code($user_id);
    }

    public function capture(): void {
        if (empty($_GET['ref'])) {
            return;
        }

        $code = sanitize_text_field(wp_unslash($_GET['ref']));

        setcookie('hd_referral_code', $code, [
            'expires' => time() + 30 * DAY_IN_SECONDS,
            'path' => COOKIEPATH ?: '/',
            'secure' => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public function commission(int $order_id): void {
        $order = wc_get_order($order_id);
        if (!$order || $order->get_meta('_hd_referral_processed')) {
            return;
        }

        $code = isset($_COOKIE['hd_referral_code'])
            ? sanitize_text_field(wp_unslash($_COOKIE['hd_referral_code']))
            : '';

        if (!$code) {
            return;
        }

        $users = get_users([
            'meta_key' => 'hd_referral_code',
            'meta_value' => $code,
            'number' => 1,
            'fields' => 'ids',
        ]);

        if (!$users) {
            return;
        }

        $referrer_id = absint($users[0]);
        $rate = (float) get_option('hd_referral_rate', 3);
        $commission = (float) $order->get_total() * ($rate / 100);

        $ledger = (array) get_user_meta($referrer_id, 'hd_referral_ledger', true);
        $ledger[] = [
            'order_id' => $order_id,
            'amount' => $commission,
            'status' => 'pending',
            'created_at' => current_time('mysql'),
        ];
        update_user_meta($referrer_id, 'hd_referral_ledger', array_slice($ledger, -300));

        $order->update_meta_data('_hd_referral_processed', 1);
        $order->update_meta_data('_hd_referrer_user_id', $referrer_id);
        $order->save();
    }
}
