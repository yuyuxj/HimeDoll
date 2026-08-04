<?php
defined('ABSPATH') || exit;

final class HimeDoll_Product_Fields {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'render']);
        add_action('woocommerce_process_product_meta', [$this, 'save']);
    }

    public function render(): void {
        echo '<div class="options_group"><p class="form-field"><strong>HimeDoll 商品仕様</strong></p>';

        $text_fields = [
            'hd_height' => ['身長表示', '例：160cm'],
            'hd_height_numeric' => ['身長数値（cm）', '例：160'],
            'hd_weight' => ['重量', '例：35kg'],
            'hd_cup' => ['カップ', '例：D'],
            'hd_skin' => ['肌色', '例：ナチュラル'],
            'hd_ai' => ['AI機能', '例：音声会話対応'],
            'hd_delivery' => ['納期目安', '例：7～14営業日'],
            'hd_warranty' => ['保証', '保証内容'],
            'hd_package' => ['付属品', '付属品'],
            'hd_care' => ['保管・お手入れ', '保管方法'],
            'hd_seo_title' => ['SEOタイトル', 'Google表示タイトル'],
            'hd_seo_description' => ['SEO説明', 'Google表示説明'],
        ];
        foreach ($text_fields as $id => [$label, $placeholder]) {
            woocommerce_wp_text_input(['id'=>$id,'label'=>$label,'placeholder'=>$placeholder,'desc_tip'=>true]);
        }

        $select_fields = [
            'hd_material' => ['素材', [''=>'選択してください','tpe'=>'TPE','silicone'=>'シリコン']],
            'hd_skeleton' => ['骨格', [''=>'選択してください','standard'=>'標準骨格','evo'=>'EVO骨格','soft'=>'ソフト骨格']],
            'hd_standing' => ['自立機能', [''=>'選択してください','yes'=>'対応','no'=>'非対応']],
            'hd_heating' => ['加熱機能', [''=>'選択してください','yes'=>'対応','no'=>'非対応']],
            'hd_head_removable' => ['ヘッド交換', [''=>'選択してください','yes'=>'対応','no'=>'非対応']],
            'hd_stock_type' => ['販売区分', [''=>'選択してください','in-stock'=>'即納','preorder'=>'受注生産','outlet'=>'アウトレット']],
        ];
        foreach ($select_fields as $id => [$label, $options]) {
            woocommerce_wp_select(['id'=>$id,'label'=>$label,'options'=>$options]);
        }
        echo '</div>';
    }

    public function save(int $post_id): void {
        if (!current_user_can('edit_post', $post_id)) return;
        $keys = [
            'hd_height','hd_height_numeric','hd_weight','hd_material','hd_cup','hd_skin','hd_ai',
            'hd_delivery','hd_warranty','hd_package','hd_care','hd_seo_title','hd_seo_description',
            'hd_skeleton','hd_standing','hd_heating','hd_head_removable','hd_stock_type',
        ];
        foreach ($keys as $key) {
            if (array_key_exists($key, $_POST)) {
                $value = sanitize_text_field(wp_unslash($_POST[$key]));
                $value === '' ? delete_post_meta($post_id, $key) : update_post_meta($post_id, $key, $value);
            }
        }
    }
}
