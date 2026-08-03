<?php
defined('ABSPATH') || exit;

function himedoll_setup(): void {
    load_theme_textdomain('himedoll', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 320,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    register_nav_menus([
        'primary' => __('メインメニュー', 'himedoll'),
        'footer'  => __('フッターメニュー', 'himedoll'),
    ]);
}
add_action('after_setup_theme', 'himedoll_setup');

function himedoll_content_width(): void {
    $GLOBALS['content_width'] = apply_filters('himedoll_content_width', 1200);
}
add_action('after_setup_theme', 'himedoll_content_width', 0);
