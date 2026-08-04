<?php
defined('ABSPATH') || exit;

$brands = get_terms(['taxonomy'=>'product_brand','hide_empty'=>true]);
$selected = static function (string $key, string $sanitizer = 'sanitize_text_field'): array {
    return isset($_GET[$key]) ? array_map($sanitizer, (array) wp_unslash($_GET[$key])) : [];
};
$selected_brands = $selected('brand', 'sanitize_title');
$selected_materials = $selected('material');
$selected_cups = $selected('cup');
$selected_skeletons = $selected('skeleton');
$selected_stock_types = $selected('stock_type');
$selected_height = isset($_GET['height']) ? sanitize_key(wp_unslash($_GET['height'])) : '';
?>
<div class="filter-panel__header"><h2>商品を絞り込む</h2><button type="button" data-filter-close aria-label="閉じる">×</button></div>
<form class="catalog-filter-form" method="get">
    <?php if (!is_wp_error($brands) && $brands) : ?>
    <fieldset class="filter-group"><legend>ブランド</legend>
        <?php foreach ($brands as $brand) : ?>
        <label><input type="checkbox" name="brand[]" value="<?php echo esc_attr($brand->slug); ?>" <?php checked(in_array($brand->slug,$selected_brands,true)); ?>><span><?php echo esc_html($brand->name); ?></span></label>
        <?php endforeach; ?>
    </fieldset>
    <?php endif; ?>

    <fieldset class="filter-group"><legend>素材</legend>
        <?php foreach (['tpe'=>'TPE','silicone'=>'シリコン'] as $value=>$label) : ?>
        <label><input type="checkbox" name="material[]" value="<?php echo esc_attr($value); ?>" <?php checked(in_array($value,$selected_materials,true)); ?>><span><?php echo esc_html($label); ?></span></label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="filter-group"><legend>身長</legend>
        <?php foreach (['under-130'=>'130cm未満','130-149'=>'130〜149cm','150-159'=>'150〜159cm','160-169'=>'160〜169cm','170-plus'=>'170cm以上'] as $value=>$label) : ?>
        <label><input type="radio" name="height" value="<?php echo esc_attr($value); ?>" <?php checked($selected_height,$value); ?>><span><?php echo esc_html($label); ?></span></label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="filter-group"><legend>カップ</legend>
        <?php foreach (['A','B','C','D','E','F','G','H','I','J','K','L','M'] as $cup) : ?>
        <label><input type="checkbox" name="cup[]" value="<?php echo esc_attr($cup); ?>" <?php checked(in_array($cup,$selected_cups,true)); ?>><span><?php echo esc_html($cup); ?></span></label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="filter-group"><legend>骨格</legend>
        <?php foreach (['standard'=>'標準骨格','evo'=>'EVO骨格','soft'=>'ソフト骨格'] as $value=>$label) : ?>
        <label><input type="checkbox" name="skeleton[]" value="<?php echo esc_attr($value); ?>" <?php checked(in_array($value,$selected_skeletons,true)); ?>><span><?php echo esc_html($label); ?></span></label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="filter-group"><legend>機能</legend>
        <label><input type="checkbox" name="standing" value="1" <?php checked(!empty($_GET['standing'])); ?>><span>自立対応</span></label>
        <label><input type="checkbox" name="heating" value="1" <?php checked(!empty($_GET['heating'])); ?>><span>加熱対応</span></label>
        <label><input type="checkbox" name="head_removable" value="1" <?php checked(!empty($_GET['head_removable'])); ?>><span>ヘッド交換対応</span></label>
        <label><input type="checkbox" name="ai" value="1" <?php checked(!empty($_GET['ai'])); ?>><span>AI対応</span></label>
    </fieldset>

    <fieldset class="filter-group"><legend>販売区分</legend>
        <?php foreach (['in-stock'=>'即納','preorder'=>'受注生産','outlet'=>'アウトレット'] as $value=>$label) : ?>
        <label><input type="checkbox" name="stock_type[]" value="<?php echo esc_attr($value); ?>" <?php checked(in_array($value,$selected_stock_types,true)); ?>><span><?php echo esc_html($label); ?></span></label>
        <?php endforeach; ?>
        <label><input type="checkbox" name="in_stock" value="1" <?php checked(!empty($_GET['in_stock'])); ?>><span>在庫ありのみ</span></label>
    </fieldset>

    <fieldset class="filter-group"><legend>価格</legend>
        <div class="filter-price-range">
            <input type="number" min="0" step="1000" name="min_price" inputmode="numeric" placeholder="最低価格" value="<?php echo isset($_GET['min_price']) ? esc_attr(absint($_GET['min_price'])) : ''; ?>">
            <span>〜</span>
            <input type="number" min="0" step="1000" name="max_price" inputmode="numeric" placeholder="最高価格" value="<?php echo isset($_GET['max_price']) ? esc_attr(absint($_GET['max_price'])) : ''; ?>">
        </div>
    </fieldset>

    <?php foreach ($_GET as $key=>$value) : if (in_array($key,['brand','material','height','cup','skeleton','standing','heating','head_removable','ai','stock_type','in_stock','min_price','max_price'],true)) continue; ?>
        <?php if (!is_array($value)) : ?><input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($value))); ?>"><?php endif; ?>
    <?php endforeach; ?>
    <button class="button button--primary" type="submit">この条件で検索</button>
    <a class="filter-reset" href="<?php echo esc_url(himedoll_shop_url()); ?>">条件をすべてリセット</a>
</form>
