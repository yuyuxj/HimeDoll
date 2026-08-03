<?php
defined('ABSPATH') || exit;
get_header('shop');
?>
<main class="catalog-page">
<section class="catalog-hero"><div class="container"><?php woocommerce_breadcrumb(); ?><p class="eyebrow">HimeDoll Collection</p><h1><?php woocommerce_page_title(); ?></h1><?php do_action('woocommerce_archive_description'); ?></div></section>
<div class="container catalog-layout">
<aside class="catalog-sidebar" data-filter-panel><?php get_template_part('template-parts/archive/filters'); ?></aside>
<section class="catalog-results">
<button class="filter-toggle" type="button" data-filter-toggle>絞り込み</button>
<?php if (woocommerce_product_loop()) : do_action('woocommerce_before_shop_loop'); woocommerce_product_loop_start();
while (have_posts()) : the_post(); wc_get_template_part('content','product'); endwhile;
woocommerce_product_loop_end(); do_action('woocommerce_after_shop_loop');
else : do_action('woocommerce_no_products_found'); endif; ?>
</section></div></main>
<?php get_footer('shop'); ?>
