<?php
defined('ABSPATH') || exit;
?>
<div class="search-drawer" hidden data-search-drawer>
    <div class="search-drawer__backdrop" data-search-close></div>
    <div class="search-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('サイト内検索', 'himedoll'); ?>">
        <button class="search-drawer__close" type="button" data-search-close aria-label="<?php esc_attr_e('閉じる', 'himedoll'); ?>">×</button>
        <p class="eyebrow">Search</p>
        <h2>商品を検索</h2>
        <?php get_search_form(); ?>
        <p class="search-help">商品名、ブランド、身長、素材などから検索できます。</p>
    </div>
</div>
