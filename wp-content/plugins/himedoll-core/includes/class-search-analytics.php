<?php
defined('ABSPATH') || exit;

final class HimeDoll_Search_Analytics {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('template_redirect', [$this, 'record']);
        add_action('rest_api_init', [$this, 'rest']);
    }

    public function record(): void {
        if (!is_search()) {
            return;
        }

        $query = sanitize_text_field(get_search_query());
        if (!$query) {
            return;
        }

        $logs = (array) get_option('hd_search_logs', []);
        $key = mb_strtolower($query);

        if (!isset($logs[$key])) {
            $logs[$key] = [
                'query' => $query,
                'count' => 0,
                'last_searched' => '',
            ];
        }

        $logs[$key]['count']++;
        $logs[$key]['last_searched'] = current_time('mysql');

        uasort($logs, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);
        $logs = array_slice($logs, 0, 500, true);

        update_option('hd_search_logs', $logs, false);
    }

    public function rest(): void {
        register_rest_route('himedoll/v1', '/search-suggestions', [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => [$this, 'suggestions'],
            'args' => [
                'q' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);
    }

    public function suggestions(WP_REST_Request $request): WP_REST_Response {
        $query = trim((string) $request->get_param('q'));

        if (mb_strlen($query) < 2) {
            return new WP_REST_Response(['items' => []]);
        }

        $posts = get_posts([
            'post_type' => ['product', 'hd_buying_guide'],
            'post_status' => 'publish',
            'numberposts' => 8,
            's' => $query,
        ]);

        $items = array_map(static function (WP_Post $post): array {
            return [
                'title' => get_the_title($post),
                'url' => get_permalink($post),
                'type' => $post->post_type,
            ];
        }, $posts);

        return new WP_REST_Response(['items' => $items]);
    }
}
