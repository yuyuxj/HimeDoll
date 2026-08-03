<?php
defined('ABSPATH') || exit;
function himedoll_shop_url(): string {
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
}
function himedoll_product_meta(int $product_id, string $key, string $default = '—'): string {
    $value = get_post_meta($product_id, $key, true);
    return $value !== '' ? (string) $value : $default;
}
function himedoll_product_brand(int $product_id): ?WP_Term {
    $terms = get_the_terms($product_id, 'product_brand');
    return (!is_wp_error($terms) && !empty($terms)) ? $terms[0] : null;
}
