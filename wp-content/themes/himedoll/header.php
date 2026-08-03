<?php defined('ABSPATH') || exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<header class="site-header">
<div class="announcement">日本全国送料無料・匿名配送対応</div>
<div class="container header-main">
<a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>">HimeDoll</a>
<nav><?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'menu_class'=>'main-menu']); ?></nav>
<div class="header-links"><a href="<?php echo esc_url(himedoll_shop_url()); ?>">商品一覧</a><?php if(function_exists('wc_get_cart_url')): ?><a href="<?php echo esc_url(wc_get_cart_url()); ?>">カート</a><?php endif; ?></div>
</div></header>
