<?php
defined('ABSPATH') || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('本文へ移動', 'himedoll'); ?></a>

<header class="site-header" data-site-header>
    <?php get_template_part('template-parts/header/announcement'); ?>
    <?php get_template_part('template-parts/header/main'); ?>
    <?php get_template_part('template-parts/header/mobile-menu'); ?>
    <?php get_template_part('template-parts/header/search-drawer'); ?>
</header>
