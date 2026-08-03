<?php
defined('ABSPATH') || exit;
$categories = [
    ['TPE Collection', '柔らかな質感と豊富なラインナップ', '/product-category/tpe/', 'category-card--tpe'],
    ['Silicone Collection', '精密な造形と上質な仕上がり', '/product-category/silicone/', 'category-card--silicone'],
    ['AI Series', '会話・音声・スマート機能', '/product-category/ai/', 'category-card--ai'],
];
?>
<section class="section">
    <div class="container">
        <div class="section-heading section-heading--split">
            <div><p class="eyebrow">Collections</p><h2>カテゴリーから探す</h2></div>
            <a class="text-link" href="<?php echo esc_url(himedoll_shop_url()); ?>">すべての商品を見る →</a>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as [$title, $text, $url, $class]) : ?>
                <a class="category-card <?php echo esc_attr($class); ?>" href="<?php echo esc_url(home_url($url)); ?>">
                    <span class="category-card__content">
                        <strong><?php echo esc_html($title); ?></strong>
                        <small><?php echo esc_html($text); ?></small>
                    </span>
                    <span class="category-card__arrow">↗</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
