<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}

$product_id = $product->get_id();
$delivery = himedoll_product_meta($product_id, 'hd_delivery', '商品ごとに確認');
$material = himedoll_product_meta($product_id, 'hd_material');
$height = himedoll_product_meta($product_id, 'hd_height');
?>
<div class="product-delivery-card">
    <div>
        <span class="product-delivery-card__label">納期目安</span>
        <strong><?php echo esc_html($delivery); ?></strong>
    </div>
    <div>
        <span class="product-delivery-card__label">素材</span>
        <strong><?php echo esc_html($material); ?></strong>
    </div>
    <div>
        <span class="product-delivery-card__label">身長</span>
        <strong><?php echo esc_html($height); ?></strong>
    </div>
</div>
