<?php
defined('ABSPATH') || exit;
add_action('wp_enqueue_scripts', function (): void {
    $v = wp_get_theme()->get('Version');
    wp_enqueue_style('himedoll-main', get_template_directory_uri() . '/assets/css/main.css', [], $v);
    wp_enqueue_script('himedoll-catalog', get_template_directory_uri() . '/assets/js/catalog.js', [], $v, true);
});
