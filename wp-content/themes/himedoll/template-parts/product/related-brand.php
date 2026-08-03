<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}

$brand = himedoll_product_brand($product->get_id());
if (!$brand || !shortcode_exists('products')) {
    return;
}
?>
<section class="brand-related-products">
    <div class="section-heading">
        <p class="eyebrow">Same brand</p>
        <h2><?php echo esc_html($brand->name); ?> のおすすめ商品</h2>
    </div>

    <?php
    echo do_shortcode(sprintf(
        '[products limit="4" columns="4" tax_name="product_brand" tax_operator="IN" tax_term="%s" exclude="%d"]',
        esc_attr($brand->slug),
        absint($product->get_id())
    ));
    ?>
</section>
