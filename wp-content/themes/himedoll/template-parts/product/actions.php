<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}
?>
<div class="product-extra-actions">
    <button class="buy-now-button" type="button" data-buy-now>
        今すぐ購入
    </button>

    <button class="wishlist-button" type="button" data-wishlist aria-pressed="false">
        ♡ お気に入り
    </button>
</div>

<div class="mobile-purchase-bar" data-mobile-purchase>
    <div>
        <small><?php echo esc_html($product->get_name()); ?></small>
        <strong><?php echo wp_kses_post($product->get_price_html()); ?></strong>
    </div>
    <button type="button" data-scroll-to-cart>購入する</button>
</div>
