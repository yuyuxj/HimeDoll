<?php
defined('ABSPATH') || exit;
$brands = ['WM Doll', 'SE Doll', 'Irontech', 'Zelex', 'JY Doll', 'Starpery'];
?>
<section class="brand-showcase">
    <div class="container">
        <div class="section-heading section-heading--center">
            <p class="eyebrow">Brands</p>
            <h2>厳選ブランド</h2>
            <p>品質、造形、サポート体制を基準に選定しています。</p>
        </div>
        <div class="brand-grid">
            <?php foreach ($brands as $brand) : ?>
                <a class="brand-card" href="<?php echo esc_url(home_url('/brand/' . sanitize_title($brand) . '/')); ?>">
                    <span><?php echo esc_html($brand); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
