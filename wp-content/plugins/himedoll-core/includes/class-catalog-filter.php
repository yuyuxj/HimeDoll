<?php
defined('ABSPATH') || exit;

final class HimeDoll_Catalog_Filter {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('pre_get_posts', [$this, 'apply_filters']);
    }

    public function apply_filters(WP_Query $query): void {
        if (is_admin() || !$query->is_main_query() || !(is_shop() || is_product_taxonomy())) {
            return;
        }

        $tax_query = (array) $query->get('tax_query');
        $meta_query = (array) $query->get('meta_query');

        if (!empty($_GET['brand'])) {
            $tax_query[] = [
                'taxonomy' => 'product_brand',
                'field' => 'slug',
                'terms' => array_map('sanitize_title', (array) $_GET['brand']),
            ];
        }

        if (!empty($_GET['material'])) {
            $meta_query[] = [
                'key' => 'hd_material',
                'value' => array_map('sanitize_text_field', (array) $_GET['material']),
                'compare' => 'IN',
            ];
        }

        if (!empty($_GET['ai'])) {
            $meta_query[] = [
                'key' => 'hd_ai',
                'value' => '',
                'compare' => '!=',
            ];
        }

        if (!empty($_GET['in_stock'])) {
            $meta_query[] = [
                'key' => '_stock_status',
                'value' => 'instock',
            ];
        }

        if (!empty($_GET['height'])) {
            $ranges = [
                'under-130' => [0,129],
                '130-149' => [130,149],
                '150-159' => [150,159],
                '160-plus' => [160,300],
            ];

            $selected = sanitize_text_field(wp_unslash($_GET['height']));
            if (isset($ranges[$selected])) {
                $meta_query[] = [
                    'key' => 'hd_height_numeric',
                    'value' => $ranges[$selected],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                ];
            }
        }

        $query->set('tax_query', $tax_query);
        $query->set('meta_query', $meta_query);
    }
}
