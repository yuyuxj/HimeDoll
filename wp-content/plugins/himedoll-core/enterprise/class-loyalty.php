<?php
defined('ABSPATH') || exit;

final class HimeDoll_Loyalty {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'award']);
        add_action('woocommerce_review_order_before_payment', [$this, 'checkout_ui']);
        add_action('woocommerce_checkout_update_order_review', [$this, 'capture_redemption']);
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_redemption']);
    }

    public function balance(int $user_id): int {
        return max(0, (int) get_user_meta($user_id, 'hd_loyalty_points', true));
    }

    public function add(int $user_id, int $points, string $reason, int $order_id = 0): void {
        if (!$user_id || !$points) {
            return;
        }

        $balance = max(0, $this->balance($user_id) + $points);
        update_user_meta($user_id, 'hd_loyalty_points', $balance);

        $ledger = (array) get_user_meta($user_id, 'hd_points_ledger', true);
        $ledger[] = [
            'points' => $points,
            'reason' => sanitize_text_field($reason),
            'order_id' => $order_id,
            'created_at' => current_time('mysql'),
        ];
        update_user_meta($user_id, 'hd_points_ledger', array_slice($ledger, -300));
    }

    public function award(int $order_id): void {
        $order = wc_get_order($order_id);
        if (!$order || !$order->get_user_id()) {
            return;
        }

        if ($order->get_meta('_hd_points_awarded')) {
            return;
        }

        $rate = max(1, absint(get_option('hd_points_per_yen_unit', 100)));
        $points = (int) floor((float) $order->get_total() / $rate);

        if ($points > 0) {
            $this->add($order->get_user_id(), $points, '订单奖励', $order_id);
            $order->update_meta_data('_hd_points_awarded', $points);
            $order->save();
        }
    }

    public function checkout_ui(): void {
        if (!is_user_logged_in()) {
            return;
        }

        $balance = $this->balance(get_current_user_id());
        if ($balance <= 0) {
            return;
        }
        ?>
        <div class="hd-points-balance">
            <strong>保有ポイント：<?php echo esc_html(number_format_i18n($balance)); ?></strong>
            <p>
                <label>
                    <input type="number" min="0" max="<?php echo esc_attr((string) $balance); ?>"
                           name="hd_redeem_points" value="0">
                    ポイントを利用
                </label>
            </p>
        </div>
        <?php
    }

    public function capture_redemption(string $posted_data): void {
        parse_str($posted_data, $data);
        $points = isset($data['hd_redeem_points']) ? absint($data['hd_redeem_points']) : 0;

        if (WC()->session) {
            WC()->session->set('hd_redeem_points', $points);
        }
    }

    public function apply_redemption(WC_Cart $cart): void {
        if (!is_user_logged_in() || !WC()->session) {
            return;
        }

        $requested = absint(WC()->session->get('hd_redeem_points', 0));
        $balance = $this->balance(get_current_user_id());
        $points = min($requested, $balance);

        if ($points <= 0) {
            return;
        }

        $yen_per_point = max(1, absint(get_option('hd_yen_per_point', 1)));
        $discount = min($cart->get_subtotal(), $points * $yen_per_point);

        if ($discount > 0) {
            $cart->add_fee('ポイント利用', -$discount, false);
        }
    }
}
