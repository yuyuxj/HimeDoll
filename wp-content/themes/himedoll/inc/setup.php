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
        'height' => 100,
        'width' => 360,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ]);

    register_nav_menus([
        'primary' => __('メインメニュー', 'himedoll'),
        'footer'  => __('フッターメニュー', 'himedoll'),
    ]);
}
add_action('after_setup_theme', 'himedoll_setup');

function himedoll_widgets_init(): void {
    register_sidebar([
        'name' => __('フッターウィジェット', 'himedoll'),
        'id' => 'footer-widgets',
        'before_widget' => '<section class="footer-widget">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'himedoll_widgets_init');
