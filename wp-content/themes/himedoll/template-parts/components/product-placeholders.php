<?php
defined('ABSPATH') || exit;
?>
<div class="placeholder-products" aria-label="商品プレースホルダー">
    <?php for ($i = 1; $i <= 4; $i++) : ?>
        <article class="placeholder-product">
            <div class="placeholder-product__image"></div>
            <p class="placeholder-product__brand">HimeDoll</p>
            <h3>商品サンプル <?php echo esc_html((string) $i); ?></h3>
            <strong>¥---,---</strong>
        </article>
    <?php endfor; ?>
</div>
