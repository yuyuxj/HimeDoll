<?php
defined('ABSPATH') || exit;

final class HimeDoll_Review_Requests {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'schedule']);
        add_action('himedoll_send_review_request', [$this, 'send']);
    }

    public function schedule(int $order_id): void {
        if (!wp_next_scheduled('himedoll_send_review_request', [$order_id])) {
            wp_schedule_single_event(time() + (7 * DAY_IN_SECONDS), 'himedoll_send_review_request', [$order_id]);
        }
    }

    public function send(int $order_id): void {
        $order = wc_get_order($order_id);
        if (!$order) return;

        $email = $order->get_billing_email();
        if (!$email) return;

        $links = [];
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product) {
                $links[] = $product->get_name() . '：' . get_permalink($product->get_id()) . '#reviews';
            }
        }

        $subject = 'HimeDoll ご購入商品のレビューをお願いします';
        $message = "この度はHimeDollをご利用いただきありがとうございます。

";
        $message .= "商品はいかがでしたでしょうか。今後のお客様のためにレビューをご投稿ください。

";
        $message .= implode("
", $links);

        wp_mail($email, $subject, $message);
    }
}
