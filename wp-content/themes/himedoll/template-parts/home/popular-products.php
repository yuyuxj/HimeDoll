<?php
defined('ABSPATH') || exit;
?>
<section class="section section--muted">
    <div class="container">
        <div class="section-heading section-heading--split">
            <div><p class="eyebrow">Best sellers</p><h2>人気商品</h2></div>
            <a class="text-link" href="<?php echo esc_url(himedoll_shop_url()); ?>">ランキングを見る →</a>
        </div>
        <?php if (shortcode_exists('products')) : ?>
            <?php echo do_shortcode('[products limit="8" columns="4" orderby="popularity"]'); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/components/product-placeholders'); ?>
        <?php endif; ?>
    </div>
</section>
