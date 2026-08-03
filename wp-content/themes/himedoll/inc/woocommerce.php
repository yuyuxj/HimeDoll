<?php
defined('ABSPATH') || exit;
add_filter('loop_shop_columns', fn(): int => 3);
add_filter('loop_shop_per_page', fn(): int => 18);

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

add_action('woocommerce_before_shop_loop', function (): void {
    get_template_part('template-parts/archive/toolbar');
}, 20);

add_action('woocommerce_before_shop_loop_item_title', function (): void {
    global $product;
    if (!$product instanceof WC_Product) return;
    $brand = himedoll_product_brand($product->get_id());
    if ($brand) echo '<span class="product-brand-badge">' . esc_html($brand->name) . '</span>';
}, 8);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/trust');
}, 35);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/specifications');
}, 8);
