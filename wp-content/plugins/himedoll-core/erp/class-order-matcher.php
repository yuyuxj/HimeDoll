<?php
defined('ABSPATH') || exit;

final class HimeDoll_Order_Matcher {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    public function extract_order_number(string $text): string {
        if (preg_match_all('/\d{6,}/', $text, $matches) && !empty($matches[0])) {
            return end($matches[0]);
        }
        return '';
    }

    public function match_purchase_order(int $purchase_id): int {
        $note = (string) get_post_meta($purchase_id, 'hd_po_note', true);
        $number = $this->extract_order_number($note);
        if (!$number || !function_exists('wc_get_orders')) return 0;

        $orders = wc_get_orders([
            'limit' => 1,
            'type' => 'shop_order',
            'search' => $number,
            'return' => 'ids',
        ]);

        $order_id = !empty($orders) ? absint($orders[0]) : 0;

        if ($order_id) {
            update_post_meta($purchase_id, 'hd_po_wc_order_id', $order_id);
            update_post_meta($order_id, '_hd_purchase_order_id', $purchase_id);
        }

        return $order_id;
    }
}
