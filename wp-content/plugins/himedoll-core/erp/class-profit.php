<?php
defined('ABSPATH') || exit;

final class HimeDoll_Profit {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    public function order_cost(int $order_id): float {
        $purchase_id = absint(get_post_meta($order_id, '_hd_purchase_order_id', true));
        if (!$purchase_id) return 0.0;

        $unit = (float) get_post_meta($purchase_id, 'hd_po_unit_cost', true);
        $qty = max(1, absint(get_post_meta($purchase_id, 'hd_po_quantity', true)));

        return $unit * $qty;
    }

    public function order_profit(int $order_id): float {
        $order = wc_get_order($order_id);
        if (!$order) return 0.0;

        return (float) $order->get_total() - $this->order_cost($order_id);
    }
}
