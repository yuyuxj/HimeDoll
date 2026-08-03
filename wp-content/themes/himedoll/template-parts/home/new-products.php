<?php
defined('ABSPATH') || exit;
?>
<section class="section">
    <div class="container">
        <div class="section-heading section-heading--split">
            <div><p class="eyebrow">New arrivals</p><h2>新着商品</h2></div>
            <a class="text-link" href="<?php echo esc_url(himedoll_shop_url()); ?>">新着一覧を見る →</a>
        </div>
        <?php if (shortcode_exists('products')) : ?>
            <?php echo do_shortcode('[products limit="8" columns="4" orderby="date"]'); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/components/product-placeholders'); ?>
        <?php endif; ?>
    </div>
</section>
