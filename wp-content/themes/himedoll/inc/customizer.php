<?php
defined('ABSPATH') || exit;

function himedoll_customize_register(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('himedoll_homepage', [
        'title' => __('HimeDoll Homepage', 'himedoll'),
        'priority' => 30,
    ]);

    $settings = [
        'announcement' => ['日本全国送料無料・匿名配送対応', 'sanitize_text_field'],
        'hero_eyebrow' => ['HimeDoll Japan', 'sanitize_text_field'],
        'hero_title' => ['あなたの理想を、リアルに。', 'sanitize_text_field'],
        'hero_description' => ['日本のお客様のために設計された、安心・高品質な専門ストア。', 'sanitize_textarea_field'],
        'hero_primary_label' => ['商品を見る', 'sanitize_text_field'],
        'hero_primary_url' => ['/shop/', 'esc_url_raw'],
        'hero_secondary_label' => ['初めての方へ', 'sanitize_text_field'],
        'hero_secondary_url' => ['#shopping-flow', 'esc_url_raw'],
    ];

    foreach ($settings as $key => [$default, $sanitize]) {
        $wp_customize->add_setting('himedoll_' . $key, [
            'default' => $default,
            'sanitize_callback' => $sanitize,
        ]);

        $wp_customize->add_control('himedoll_' . $key, [
            'label' => ucwords(str_replace('_', ' ', $key)),
            'section' => 'himedoll_homepage',
            'type' => str_contains($key, 'description') ? 'textarea' : 'text',
        ]);
    }
}
add_action('customize_register', 'himedoll_customize_register');
