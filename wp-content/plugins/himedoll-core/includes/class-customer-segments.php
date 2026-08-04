<?php
defined('ABSPATH') || exit;

final class HimeDoll_Customer_Segments {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'update_segment']);
    }

    public function update_segment(int $order_id): void {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $user_id = $order->get_user_id();
        if (!$user_id) {
            return;
        }

        $orders = wc_get_orders([
            'customer_id' => $user_id,
            'status' => ['processing','completed'],
            'limit' => -1,
            'return' => 'ids',
        ]);

        $count = count($orders);

        if ($count >= 5) {
            $segment = 'vip';
        } elseif ($count >= 2) {
            $segment = 'repeat';
        } else {
            $segment = 'new';
        }

        update_user_meta($user_id, 'hd_customer_segment', $segment);
        update_user_meta($user_id, 'hd_completed_order_count', $count);
    }
}
