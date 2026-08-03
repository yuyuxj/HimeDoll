<?php
defined('ABSPATH') || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="site-search"><?php esc_html_e('検索', 'himedoll'); ?></label>
    <input id="site-search" type="search" class="search-field" placeholder="商品名・ブランドを入力" value="<?php echo get_search_query(); ?>" name="s">
    <?php if (post_type_exists('product')) : ?>
        <input type="hidden" name="post_type" value="product">
    <?php endif; ?>
    <button type="submit" class="button button--primary">検索</button>
</form>
