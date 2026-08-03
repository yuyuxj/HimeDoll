<?php
defined('ABSPATH') || exit;
$steps = [
    ['01', '商品を選ぶ', 'カテゴリーやブランドから理想の商品を選びます。'],
    ['02', '仕様を確認', 'サイズ、素材、納期、オプションを確認します。'],
    ['03', 'ご注文', '安全な決済方法で注文を確定します。'],
    ['04', 'お届け', '匿名梱包で、ご指定の住所へお届けします。'],
];
?>
<section id="shopping-flow" class="section section--soft">
    <div class="container">
        <div class="section-heading section-heading--center">
            <p class="eyebrow">How to order</p>
            <h2>ご購入の流れ</h2>
        </div>
        <div class="flow-grid">
            <?php foreach ($steps as [$number, $title, $text]) : ?>
                <article class="flow-card">
                    <span class="flow-card__number"><?php echo esc_html($number); ?></span>
                    <h3><?php echo esc_html($title); ?></h3>
                    <p><?php echo esc_html($text); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
