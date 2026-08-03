<?php
defined('ABSPATH') || exit;
?>
<section class="empty-cart">
    <div class="empty-cart__icon">H</div>
    <p class="eyebrow">Your cart</p>
    <h1>カートに商品がありません</h1>
    <p>気になる商品を探して、カートに追加してください。</p>
    <a class="button button--primary" href="<?php echo esc_url(himedoll_shop_url()); ?>">
        商品を見る
    </a>
</section>
