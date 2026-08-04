<?php
defined('ABSPATH') || exit;

final class HimeDoll_Order_Webhook {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_order_status_changed', [$this, 'send'], 10, 4);
    }

    public function send(int $order_id, string $from, string $to, WC_Order $order): void {
        $url = esc_url_raw((string) get_option('hd_order_webhook_url'));
        $secret = (string) get_option('hd_order_webhook_secret');

        if (!$url) {
            return;
        }

        $payload = [
            'event' => 'order.status_changed',
            'order_id' => $order_id,
            'order_number' => $order->get_order_number(),
            'from' => $from,
            'to' => $to,
            'total' => $order->get_total(),
            'currency' => $order->get_currency(),
            'created_at' => current_time('mysql'),
        ];

        $body = wp_json_encode($payload);
        $signature = $secret ? hash_hmac('sha256', $body, $secret) : '';

        wp_remote_post($url, [
            'timeout' => 15,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-HimeDoll-Signature' => $signature,
            ],
            'body' => $body,
        ]);
    }
}
