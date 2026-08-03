<?php
defined('ABSPATH') || exit;
$groups = [
 'ブランド'=>['WM Doll','SE Doll','Irontech','Zelex'],
 '素材'=>['TPE','シリコン'],
 '身長'=>['100–129cm','130–149cm','150–159cm','160cm以上'],
 '機能'=>['AI対応','日本在庫','受注生産'],
];
?>
<div class="filter-panel__header"><h2>絞り込み</h2><button type="button" data-filter-close>×</button></div>
<form class="catalog-filter-form" method="get">
<?php foreach($groups as $title=>$items): ?>
<fieldset class="filter-group"><legend><?php echo esc_html($title); ?></legend>
<?php foreach($items as $item): ?><label><input type="checkbox" name="hd_filter[]" value="<?php echo esc_attr(sanitize_title($item)); ?>"><span><?php echo esc_html($item); ?></span></label><?php endforeach; ?>
</fieldset><?php endforeach; ?>
<fieldset class="filter-group"><legend>価格帯</legend>
<label><input type="radio" name="price_range" value="0-100000"><span>10万円以下</span></label>
<label><input type="radio" name="price_range" value="100000-200000"><span>10万〜20万円</span></label>
<label><input type="radio" name="price_range" value="200000-"><span>20万円以上</span></label>
</fieldset>
<button class="button button--primary" type="submit">この条件で検索</button>
</form>
