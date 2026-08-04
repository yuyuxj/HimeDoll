<?php
defined('ABSPATH') || exit;

final class HimeDoll_Enterprise_API {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'routes']);
    }

    public function routes(): void {
        register_rest_route('himedoll-enterprise/v1', '/orders', [
            'methods' => 'GET',
            'permission_callback' => [$this, 'auth'],
            'callback' => [$this, 'orders'],
        ]);

        register_rest_route('himedoll-enterprise/v1', '/products', [
            'methods' => 'GET',
            'permission_callback' => [$this, 'auth'],
            'callback' => [$this, 'products'],
        ]);

        register_rest_route('himedoll/v1', '/manifest', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [$this, 'manifest'],
        ]);
    }

    public function auth(WP_REST_Request $request): bool {
        $expected = defined('HIMEDOLL_ENTERPRISE_API_KEY')
            ? HIMEDOLL_ENTERPRISE_API_KEY
            : (string) get_option('hd_enterprise_api_key');

        $provided = (string) $request->get_header('X-HimeDoll-Key');

        return $expected && hash_equals($expected, $provided);
    }

    public function orders(WP_REST_Request $request): WP_REST_Response {
        $limit = min(100, max(1, absint($request->get_param('limit') ?: 20)));
        $orders = wc_get_orders(['limit' => $limit, 'orderby' => 'date', 'order' => 'DESC']);

        $data = array_map(static function (WC_Order $order): array {
            return [
                'id' => $order->get_id(),
                'number' => $order->get_order_number(),
                'status' => $order->get_status(),
                'total' => $order->get_total(),
                'currency' => $order->get_currency(),
                'created_at' => $order->get_date_created()?->date(DATE_ATOM),
            ];
        }, $orders);

        return new WP_REST_Response(['items' => $data]);
    }

    public function products(WP_REST_Request $request): WP_REST_Response {
        $products = wc_get_products([
            'limit' => min(100, max(1, absint($request->get_param('limit') ?: 20))),
            'status' => 'publish',
        ]);

        $data = array_map(static function (WC_Product $product): array {
            return [
                'id' => $product->get_id(),
                'sku' => $product->get_sku(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'stock_status' => $product->get_stock_status(),
            ];
        }, $products);

        return new WP_REST_Response(['items' => $data]);
    }

    public function manifest(): WP_REST_Response {
        return new WP_REST_Response([
            'name' => get_bloginfo('name'),
            'short_name' => 'HimeDoll',
            'start_url' => home_url('/'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#111111',
            'icons' => [],
        ]);
    }
}
