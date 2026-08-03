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
    if (!$product instanceof WC_Product) {
        return;
    }

    $brand = himedoll_product_brand($product->get_id());
    if ($brand) {
        echo '<span class="product-brand-badge">' . esc_html($brand->name) . '</span>';
    }
}, 8);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/summary-meta');
}, 6);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/delivery-card');
}, 25);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/actions');
}, 32);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/trust');
}, 38);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/content-tabs');
}, 7);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/specifications');
}, 8);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/related-brand');
}, 18);

add_filter('woocommerce_checkout_fields', function (array $fields): array {
    $priority = [
        'billing_last_name'  => 10,
        'billing_first_name' => 20,
        'billing_postcode'   => 30,
        'billing_state'      => 40,
        'billing_city'       => 50,
        'billing_address_1'  => 60,
        'billing_address_2'  => 70,
        'billing_phone'      => 80,
        'billing_email'      => 90,
    ];

    foreach ($priority as $key => $value) {
        if (isset($fields['billing'][$key])) {
            $fields['billing'][$key]['priority'] = $value;
        }
    }

    if (isset($fields['billing']['billing_company'])) {
        $fields['billing']['billing_company']['required'] = false;
        $fields['billing']['billing_company']['priority'] = 25;
    }

    if (isset($fields['order']['order_comments'])) {
        $fields['order']['order_comments']['label'] = 'ご注文に関するご要望';
        $fields['order']['order_comments']['placeholder'] = '配送希望、連絡方法、その他のご要望をご記入ください。';
    }

    return $fields;
});

add_action('woocommerce_before_checkout_form', function (): void {
    get_template_part('template-parts/checkout/privacy-notice');
}, 8);

add_action('woocommerce_review_order_before_payment', function (): void {
    get_template_part('template-parts/checkout/trust-panel');
}, 5);
