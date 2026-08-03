<?php
defined('ABSPATH') || exit;
get_header('shop');
?>
<main class="single-product-page"><div class="container"><?php woocommerce_breadcrumb(); while(have_posts()): the_post(); wc_get_template_part('content','single-product'); endwhile; ?></div></main>
<?php get_footer('shop'); ?>
