<?php
defined('ABSPATH') || exit;

final class HimeDoll_Abandoned_Checkout {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_checkout_update_order_meta', [$this, 'schedule'], 10, 2);
        add_action('himedoll_abandoned_checkout_email', [$this, 'send']);
        add_action('template_redirect', [$this, 'restore']);
    }

    public function schedule(int $order_id, array $data): void {
        $order = wc_get_order($order_id);
        if (!$order || $order->is_paid()) {
            return;
        }

        if (!wp_next_scheduled('himedoll_abandoned_checkout_email', [$order_id])) {
            wp_schedule_single_event(
                time() + 6 * HOUR_IN_SECONDS,
                'himedoll_abandoned_checkout_email',
                [$order_id]
            );
        }
    }

    public function send(int $order_id): void {
        $order = wc_get_order($order_id);

        if (!$order || $order->is_paid() || !in_array($order->get_status(), ['pending','failed'], true)) {
            return;
        }

        $email = $order->get_billing_email();
        if (!$email) {
            return;
        }

        $token = hash_hmac(
            'sha256',
            $order_id . '|' . $order->get_order_key(),
            wp_salt('auth')
        );

        $url = add_query_arg([
            'hd_restore_order' => $order_id,
            'hd_restore_token' => $token,
        ], home_url('/'));

        $subject = 'HimeDoll ご注文手続きが完了していません';
        $message = "カートの商品が残っています。

";
        $message .= "以下のリンクからご注文手続きを再開できます。
";
        $message .= esc_url_raw($url);

        wp_mail($email, $subject, $message);
    }

    public function restore(): void {
        if (
            empty($_GET['hd_restore_order']) ||
            empty($_GET['hd_restore_token']) ||
            !function_exists('WC')
        ) {
            return;
        }

        $order_id = absint($_GET['hd_restore_order']);
        $token = sanitize_text_field(wp_unslash($_GET['hd_restore_token']));
        $order = wc_get_order($order_id);

        if (!$order || $order->is_paid()) {
            return;
        }

        $expected = hash_hmac(
            'sha256',
            $order_id . '|' . $order->get_order_key(),
            wp_salt('auth')
        );

        if (!hash_equals($expected, $token)) {
            return;
        }

        WC()->cart->empty_cart();

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $variation_id = $item->get_variation_id();
            $quantity = $item->get_quantity();

            WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                $variation_id
            );
        }

        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }
}
