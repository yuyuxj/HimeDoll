<?php
defined('ABSPATH') || exit;
$items = [
    ['全国送料無料', '対象地域へ安心配送'],
    ['匿名配送', '外箱から内容が分からない梱包'],
    ['品質保証', '購入後も日本語でサポート'],
    ['安全決済', '安心できる決済環境'],
];
?>
<section class="trust-strip">
    <div class="container trust-grid">
        <?php foreach ($items as [$title, $text]) : ?>
            <div class="trust-item">
                <span class="trust-item__icon" aria-hidden="true">✓</span>
                <div><strong><?php echo esc_html($title); ?></strong><span><?php echo esc_html($text); ?></span></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
