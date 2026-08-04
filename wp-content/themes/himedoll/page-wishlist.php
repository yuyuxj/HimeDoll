<?php
/**
 * Template Name: Wishlist
 */
defined('ABSPATH') || exit;
get_header();

$ids = is_user_logged_in()
    ? (array) get_user_meta(get_current_user_id(), 'hd_wishlist', true)
    : [];
?>
<main class="wishlist-page">
    <div class="container">
        <p class="eyebrow">Wishlist</p>
        <h1>お気に入り</h1>

        <?php if ($ids && shortcode_exists('products')) : ?>
            <?php echo do_shortcode('[products ids="' . esc_attr(implode(',', array_map('absint', $ids))) . '" columns="4"]'); ?>
        <?php else : ?>
            <div class="wishlist-empty">
                <h2>お気に入り商品はまだありません</h2>
                <p>商品ページの「お気に入り」から保存できます。</p>
                <a class="button button--primary" href="<?php echo esc_url(himedoll_shop_url()); ?>">商品を見る</a>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
