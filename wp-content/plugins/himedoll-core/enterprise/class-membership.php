<?php
defined('ABSPATH') || exit;

final class HimeDoll_Membership {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'recalculate']);
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_discount']);
    }

    public function recalculate(int $order_id): void {
        $order = wc_get_order($order_id);
        if (!$order || !$order->get_user_id()) {
            return;
        }

        $user_id = $order->get_user_id();
        $orders = wc_get_orders([
            'customer_id' => $user_id,
            'status' => ['processing', 'completed'],
            'limit' => -1,
        ]);

        $spent = 0.0;
        foreach ($orders as $customer_order) {
            $spent += (float) $customer_order->get_total();
        }

        $thresholds = [
            'VIP' => (float) get_option('hd_tier_vip', 1500000),
            'Gold' => (float) get_option('hd_tier_gold', 800000),
            'Silver' => (float) get_option('hd_tier_silver', 300000),
            'Bronze' => 0,
        ];

        $tier = 'Bronze';
        foreach ($thresholds as $name => $threshold) {
            if ($spent >= $threshold) {
                $tier = $name;
                break;
            }
        }

        update_user_meta($user_id, 'hd_membership_tier', $tier);
        update_user_meta($user_id, 'hd_lifetime_spend', $spent);
    }

    public function apply_discount(WC_Cart $cart): void {
        if (!is_user_logged_in()) {
            return;
        }

        $tier = (string) get_user_meta(get_current_user_id(), 'hd_membership_tier', true);
        $rates = [
            'Silver' => (float) get_option('hd_discount_silver', 1),
            'Gold' => (float) get_option('hd_discount_gold', 2),
            'VIP' => (float) get_option('hd_discount_vip', 3),
        ];

        $rate = $rates[$tier] ?? 0;
        if ($rate <= 0) {
            return;
        }

        $discount = $cart->get_subtotal() * ($rate / 100);
        $cart->add_fee($tier . ' 会員割引', -$discount, false);
    }
}
