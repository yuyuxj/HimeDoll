<?php
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function (): void {
    $version = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'himedoll-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        $version
    );

    wp_enqueue_style(
        'himedoll-operations',
        get_template_directory_uri() . '/assets/css/operations.css',
        ['himedoll-main'],
        $version
    );

    wp_enqueue_script(
        'himedoll-global',
        get_template_directory_uri() . '/assets/js/global.js',
        [],
        $version,
        true
    );

    if (is_product()) {
        wp_enqueue_style(
            'himedoll-product-detail',
            get_template_directory_uri() . '/assets/css/product-detail.css',
            ['himedoll-main'],
            $version
        );
        wp_enqueue_script(
            'himedoll-product-detail',
            get_template_directory_uri() . '/assets/js/product-detail.js',
            [],
            $version,
            true
        );
    } elseif (is_cart() || is_checkout()) {
        wp_enqueue_style(
            'himedoll-checkout',
            get_template_directory_uri() . '/assets/css/checkout.css',
            ['himedoll-main'],
            $version
        );
        wp_enqueue_script(
            'himedoll-checkout',
            get_template_directory_uri() . '/assets/js/checkout.js',
            ['jquery'],
            $version,
            true
        );
    } else {
        wp_enqueue_script(
            'himedoll-catalog',
            get_template_directory_uri() . '/assets/js/catalog.js',
            [],
            $version,
            true
        );
    }
});
