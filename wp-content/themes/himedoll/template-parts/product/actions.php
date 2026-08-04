<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}

$wishlist = is_user_logged_in()
    ? (array) get_user_meta(get_current_user_id(), 'hd_wishlist', true)
    : [];

$active = in_array($product->get_id(), array_map('absint', $wishlist), true);
?>
<div class="product-extra-actions">
    <button class="buy-now-button" type="button" data-buy-now>今すぐ購入</button>

    <button class="wishlist-button"
            type="button"
            data-wishlist
            data-product-id="<?php echo esc_attr((string) $product->get_id()); ?>"
            data-nonce="<?php echo esc_attr(wp_create_nonce('hd_wishlist')); ?>"
            data-login-url="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
            aria-pressed="<?php echo $active ? 'true' : 'false'; ?>">
        <?php echo $active ? '♥ お気に入り済み' : '♡ お気に入り'; ?>
    </button>
</div>
