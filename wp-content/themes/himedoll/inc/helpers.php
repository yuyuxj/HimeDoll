<?php
defined('ABSPATH') || exit;

function himedoll_site_name(): string {
    return get_bloginfo('name') ?: 'HimeDoll';
}

function himedoll_get_theme_mod(string $key, string $default = ''): string {
    return (string) get_theme_mod($key, $default);
}

function himedoll_shop_url(): string {
    return function_exists('wc_get_page_permalink')
        ? wc_get_page_permalink('shop')
        : home_url('/shop/');
}

function himedoll_menu_fallback(): void {
    echo '<ul class="desktop-nav__list">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
    echo '<li><a href="' . esc_url(himedoll_shop_url()) . '">すべての商品</a></li>';
    echo '<li><a href="' . esc_url(home_url('/brand/')) . '">ブランド</a></li>';
    echo '<li><a href="' . esc_url(home_url('/faq/')) . '">FAQ</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact/')) . '">お問い合わせ</a></li>';
    echo '</ul>';
}
