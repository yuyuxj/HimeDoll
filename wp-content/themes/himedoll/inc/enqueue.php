<?php
defined('ABSPATH') || exit;

function himedoll_enqueue_assets(): void {
    $version = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'himedoll-main',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        $version
    );

    wp_enqueue_script(
        'himedoll-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        $version,
        true
    );
}
add_action('wp_enqueue_scripts', 'himedoll_enqueue_assets');
