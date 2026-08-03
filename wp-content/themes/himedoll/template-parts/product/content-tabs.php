<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}

$product_id = $product->get_id();
$package = himedoll_product_meta($product_id, 'hd_package', '商品本体、基本付属品、取扱案内');
$care = himedoll_product_meta($product_id, 'hd_care', '使用後は清潔にし、直射日光と高温多湿を避けて保管してください。');
$warranty = himedoll_product_meta($product_id, 'hd_warranty', '保証内容は商品および注文条件により異なります。');
$video = get_post_meta($product_id, 'hd_video_url', true);
?>
<section class="product-information" data-product-tabs>
    <div class="product-tabs" role="tablist">
        <button class="is-active" type="button" role="tab" data-tab="description">商品説明</button>
        <button type="button" role="tab" data-tab="package">付属品</button>
        <button type="button" role="tab" data-tab="care">保管・お手入れ</button>
        <button type="button" role="tab" data-tab="shipping">配送・保証</button>
        <?php if ($video) : ?>
            <button type="button" role="tab" data-tab="video">動画</button>
        <?php endif; ?>
    </div>

    <div class="product-tab-panels">
        <article class="product-tab-panel is-active" data-panel="description">
            <?php the_content(); ?>
        </article>

        <article class="product-tab-panel" data-panel="package" hidden>
            <h2>付属品・梱包内容</h2>
            <p><?php echo esc_html($package); ?></p>
        </article>

        <article class="product-tab-panel" data-panel="care" hidden>
            <h2>保管・お手入れ</h2>
            <p><?php echo esc_html($care); ?></p>
        </article>

        <article class="product-tab-panel" data-panel="shipping" hidden>
            <h2>配送と保証</h2>
            <p>商品内容が外箱から分からないよう配慮して発送します。</p>
            <p><?php echo esc_html($warranty); ?></p>
        </article>

        <?php if ($video) : ?>
            <article class="product-tab-panel" data-panel="video" hidden>
                <h2>商品動画</h2>
                <div class="product-video">
                    <a href="<?php echo esc_url($video); ?>" target="_blank" rel="noopener">
                        動画を別画面で見る
                    </a>
                </div>
            </article>
        <?php endif; ?>
    </div>
</section>
