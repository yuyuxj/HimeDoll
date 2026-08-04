<?php
defined('ABSPATH') || exit;

if (!class_exists('HimeDoll_Product_SEO')) {
add_filter('pre_get_document_title', function (string $title): string {
    if (is_singular('product')) {
        $custom = get_post_meta(get_queried_object_id(), 'hd_seo_title', true);
        if ($custom) {
            return sanitize_text_field($custom);
        }
    }
    return $title;
});

add_action('wp_head', function (): void {
    if (!is_singular()) return;

    $post_id = get_queried_object_id();
    $description = get_post_meta($post_id, 'hd_seo_description', true);
    if (!$description) $description = wp_strip_all_tags(get_the_excerpt($post_id));

    if ($description) {
        echo '<meta name="description" content="' . esc_attr(wp_trim_words($description, 32, '')) . '">' . "
";
        echo '<meta property="og:description" content="' . esc_attr(wp_trim_words($description, 32, '')) . '">' . "
";
    }

    echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '">' . "
";
    echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '">' . "
";
    echo '<meta property="og:type" content="' . (is_singular('product') ? 'product' : 'article') . '">' . "
";

    if (has_post_thumbnail($post_id)) {
        echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url($post_id, 'large')) . '">' . "
";
    }
}, 5);

}

add_action('wp_head', function (): void {
    $org = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'email' => sanitize_email((string) get_option('hd_support_email')),
    ];

    $line = trim((string) get_option('hd_line_url'));
    if ($line) $org['sameAs'] = [$line];

    echo '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}, 20);

add_action('wp_head', function (): void {
    if (!is_page('faq')) return;

    $faq = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => '梱包から商品内容が分かりますか？',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => '商品内容が分からないよう配慮して発送します。'],
            ],
            [
                '@type' => 'Question',
                'name' => '購入前に相談できますか？',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'LINEまたはメールでご相談いただけます。'],
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}, 25);
