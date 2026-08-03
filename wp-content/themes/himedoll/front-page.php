<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="main-content">
    <?php get_template_part('template-parts/home/hero'); ?>
    <?php get_template_part('template-parts/home/trust'); ?>
    <?php get_template_part('template-parts/home/categories'); ?>
    <?php get_template_part('template-parts/home/popular-products'); ?>
    <?php get_template_part('template-parts/home/brand-showcase'); ?>
    <?php get_template_part('template-parts/home/new-products'); ?>
    <?php get_template_part('template-parts/home/feature-banner'); ?>
    <?php get_template_part('template-parts/home/shopping-flow'); ?>
    <?php get_template_part('template-parts/home/faq-preview'); ?>
    <?php get_template_part('template-parts/home/newsletter'); ?>
</main>
<?php get_footer(); ?>
