<?php
defined('ABSPATH') || exit;

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
    if (!is_singular()) {
        return;
    }

    $post_id = get_queried_object_id();
    $description = get_post_meta($post_id, 'hd_seo_description', true);

    if (!$description) {
        $description = wp_strip_all_tags(get_the_excerpt($post_id));
    }

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

add_action('wp_head', function (): void {
    if (!is_singular('product') || !function_exists('wc_get_product')) {
        return;
    }

    $product = wc_get_product(get_queried_object_id());
    if (!$product) {
        return;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->get_name(),
        'sku' => $product->get_sku(),
        'url' => get_permalink($product->get_id()),
        'description' => wp_strip_all_tags($product->get_short_description() ?: $product->get_description()),
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => get_woocommerce_currency(),
            'price' => $product->get_price(),
            'availability' => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'url' => get_permalink($product->get_id()),
        ],
    ];

    $brand = himedoll_product_brand($product->get_id());
    if ($brand) {
        $schema['brand'] = ['@type' => 'Brand', 'name' => $brand->name];
    }

    echo '<script type="application/ld+json">' .
        wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
        '</script>' . "
";
}, 30);
