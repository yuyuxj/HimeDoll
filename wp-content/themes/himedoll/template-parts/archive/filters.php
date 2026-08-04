<?php
defined('ABSPATH') || exit;

$brands = get_terms([
    'taxonomy' => 'product_brand',
    'hide_empty' => true,
]);

$selected_brands = isset($_GET['brand']) ? array_map('sanitize_title', (array) $_GET['brand']) : [];
$selected_materials = isset($_GET['material']) ? array_map('sanitize_title', (array) $_GET['material']) : [];
$selected_height = isset($_GET['height']) ? sanitize_text_field(wp_unslash($_GET['height'])) : '';
$selected_ai = isset($_GET['ai']) ? '1' : '';
$selected_stock = isset($_GET['in_stock']) ? '1' : '';
?>
<div class="filter-panel__header"><h2>絞り込み</h2><button type="button" data-filter-close>×</button></div>

<form class="catalog-filter-form" method="get">
    <?php if (!is_wp_error($brands) && $brands) : ?>
        <fieldset class="filter-group">
            <legend>ブランド</legend>
            <?php foreach ($brands as $brand) : ?>
                <label>
                    <input type="checkbox" name="brand[]" value="<?php echo esc_attr($brand->slug); ?>"
                        <?php checked(in_array($brand->slug, $selected_brands, true)); ?>>
                    <span><?php echo esc_html($brand->name); ?></span>
                </label>
            <?php endforeach; ?>
        </fieldset>
    <?php endif; ?>

    <fieldset class="filter-group">
        <legend>素材</legend>
        <?php foreach (['tpe'=>'TPE', 'silicone'=>'シリコン'] as $slug => $label) : ?>
            <label>
                <input type="checkbox" name="material[]" value="<?php echo esc_attr($slug); ?>"
                    <?php checked(in_array($slug, $selected_materials, true)); ?>>
                <span><?php echo esc_html($label); ?></span>
            </label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="filter-group">
        <legend>身長</legend>
        <?php foreach (['under-130'=>'130cm未満','130-149'=>'130〜149cm','150-159'=>'150〜159cm','160-plus'=>'160cm以上'] as $value=>$label) : ?>
            <label><input type="radio" name="height" value="<?php echo esc_attr($value); ?>" <?php checked($selected_height, $value); ?>><span><?php echo esc_html($label); ?></span></label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="filter-group">
        <legend>機能・在庫</legend>
        <label><input type="checkbox" name="ai" value="1" <?php checked($selected_ai, '1'); ?>><span>AI対応</span></label>
        <label><input type="checkbox" name="in_stock" value="1" <?php checked($selected_stock, '1'); ?>><span>在庫あり</span></label>
    </fieldset>

    <fieldset class="filter-group">
        <legend>価格</legend>
        <input type="number" name="min_price" placeholder="最低価格" value="<?php echo isset($_GET['min_price']) ? esc_attr(absint($_GET['min_price'])) : ''; ?>">
        <input type="number" name="max_price" placeholder="最高価格" value="<?php echo isset($_GET['max_price']) ? esc_attr(absint($_GET['max_price'])) : ''; ?>">
    </fieldset>

    <button class="button button--primary" type="submit">この条件で検索</button>
    <a class="filter-reset" href="<?php echo esc_url(himedoll_shop_url()); ?>">条件をリセット</a>
</form>
