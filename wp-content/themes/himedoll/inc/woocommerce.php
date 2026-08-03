<?php
defined('ABSPATH') || exit;

function himedoll_add_woocommerce_support(): void {
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 540,
        'single_image_width' => 1000,
        'product_grid' => [
            'default_rows' => 2,
            'min_rows' => 1,
            'max_rows' => 8,
            'default_columns' => 4,
            'min_columns' => 2,
            'max_columns' => 4,
        ],
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'himedoll_add_woocommerce_support');

function himedoll_cart_count(): int {
    if (!function_exists('WC') || !WC()->cart) {
        return 0;
    }
    return WC()->cart->get_cart_contents_count();
}

function himedoll_cart_count_fragment(array $fragments): array {
    ob_start();
    ?>
    <span class="cart-count"><?php echo esc_html((string) himedoll_cart_count()); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'himedoll_cart_count_fragment');
