<?php
defined('ABSPATH') || exit;
$faqs = [
    ['梱包から商品内容が分かりますか？', '外箱や送り状から商品内容が分からないよう配慮して発送します。'],
    ['納期はどのくらいですか？', '在庫商品と受注商品で異なります。各商品ページに目安を表示します。'],
    ['購入前に相談できますか？', 'お問い合わせフォームから日本語でご相談いただけます。'],
];
?>
<section class="section">
    <div class="container faq-layout">
        <div class="faq-layout__intro">
            <p class="eyebrow">FAQ</p>
            <h2>よくあるご質問</h2>
            <p>購入前の不安を解消できるよう、配送・支払い・保証について分かりやすく案内します。</p>
            <a class="text-link" href="<?php echo esc_url(home_url('/faq/')); ?>">FAQをすべて見る →</a>
        </div>
        <div class="faq-list">
            <?php foreach ($faqs as [$question, $answer]) : ?>
                <details>
                    <summary><?php echo esc_html($question); ?></summary>
                    <p><?php echo esc_html($answer); ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
