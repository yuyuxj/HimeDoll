<?php
defined('ABSPATH') || exit;

add_filter('wp_lazy_loading_enabled', '__return_true');

add_filter('jpeg_quality', static fn(): int => 88);

add_action('wp_enqueue_scripts', static function (): void {
    if (!is_admin()) {
        wp_dequeue_style('wp-block-library-theme');
    }
}, 100);

add_filter('script_loader_tag', static function (string $tag, string $handle): string {
    $defer_handles = ['himedoll-global', 'himedoll-catalog', 'himedoll-product-detail'];

    if (in_array($handle, $defer_handles, true) && !str_contains($tag, ' defer')) {
        return str_replace(' src=', ' defer src=', $tag);
    }

    return $tag;
}, 10, 2);
