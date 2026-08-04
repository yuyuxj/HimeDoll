<?php
defined('ABSPATH') || exit;

$recent = isset($_COOKIE['hd_recent_products'])
    ? array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_COOKIE['hd_recent_products'])))))
    : [];

if (!$recent || !shortcode_exists('products')) {
    return;
}
?>
<section class="account-recent">
    <div class="section-heading">
        <p class="eyebrow">Recently viewed</p>
        <h2>最近見た商品</h2>
    </div>
    <?php echo do_shortcode('[products ids="' . esc_attr(implode(',', array_slice($recent, 0, 4))) . '" columns="4"]'); ?>
</section>
