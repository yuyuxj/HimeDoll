<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}

$brand = himedoll_product_brand($product->get_id());
?>
<div class="product-summary-meta">
    <?php if ($brand) : ?>
        <a class="product-summary-meta__brand" href="<?php echo esc_url(get_term_link($brand)); ?>">
            <?php echo esc_html($brand->name); ?>
        </a>
    <?php endif; ?>

    <?php if ($product->get_sku()) : ?>
        <span>商品番号：<?php echo esc_html($product->get_sku()); ?></span>
    <?php endif; ?>

    <span class="<?php echo $product->is_in_stock() ? 'is-in-stock' : 'is-out-of-stock'; ?>">
        <?php echo $product->is_in_stock() ? '在庫あり' : '在庫切れ'; ?>
    </span>
</div>
