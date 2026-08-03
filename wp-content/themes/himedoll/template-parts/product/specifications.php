<?php
defined('ABSPATH') || exit;
global $product;
if (!$product instanceof WC_Product) return;
$id = $product->get_id();
$brand = himedoll_product_brand($id);
$rows = [
 'ブランド'=>$brand ? $brand->name : '—',
 '身長'=>himedoll_product_meta($id,'hd_height'),
 '重量'=>himedoll_product_meta($id,'hd_weight'),
 '素材'=>himedoll_product_meta($id,'hd_material'),
 'カップ'=>himedoll_product_meta($id,'hd_cup'),
 '肌色'=>himedoll_product_meta($id,'hd_skin'),
 'AI機能'=>himedoll_product_meta($id,'hd_ai'),
 '納期目安'=>himedoll_product_meta($id,'hd_delivery'),
];
?>
<section class="product-specs"><h2>商品仕様</h2><dl class="product-specs__table">
<?php foreach($rows as $label=>$value): ?><div class="product-specs__row"><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div><?php endforeach; ?>
</dl></section>
