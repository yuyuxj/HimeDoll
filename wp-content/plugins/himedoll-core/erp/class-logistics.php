<?php
defined('ABSPATH') || exit;

final class HimeDoll_Logistics {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    public function assign(int $order_id, string $carrier): void {
        update_post_meta($order_id, '_hd_logistics_carrier', sanitize_key($carrier));
    }

    public function get(int $order_id): string {
        return (string) get_post_meta($order_id, '_hd_logistics_carrier', true);
    }

    public function carriers(): array {
        return [
            'junjia' => '深圳骏佳',
            'lindao' => '青岛林道',
            'shanghai' => '上海小包',
        ];
    }
}
