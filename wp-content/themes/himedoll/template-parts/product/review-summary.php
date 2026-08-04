<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product) {
    return;
}

$count = $product->get_review_count();
$rating = $product->get_average_rating();
?>
<div class="hd-review-summary">
    <div class="hd-review-summary__score">
        <strong><?php echo esc_html(number_format((float) $rating, 1)); ?></strong>
        <span>/ 5.0</span>
    </div>
    <div>
        <?php echo wp_kses_post(wc_get_rating_html((float) $rating)); ?>
        <p><?php echo esc_html(sprintf('%d 件のレビュー', $count)); ?></p>
    </div>
</div>
