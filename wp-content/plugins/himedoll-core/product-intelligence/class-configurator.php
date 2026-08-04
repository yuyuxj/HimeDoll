<?php
defined('ABSPATH') || exit;

final class HimeDoll_Product_Configurator {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'render']);
        add_action('woocommerce_process_product_meta', [$this, 'save']);
        add_shortcode('himedoll_configurator', [$this, 'shortcode']);
        add_action('woocommerce_single_product_summary', [$this, 'summary'], 24);
    }
    private function options(string $type): array {
        $posts = get_posts(['post_type'=>$type,'post_status'=>'publish','numberposts'=>-1,'orderby'=>'title','order'=>'ASC']);
        $out = [''=>'選択なし']; foreach ($posts as $post) $out[(string)$post->ID] = $post->post_title; return $out;
    }
    public function render(): void {
        echo '<div class="options_group"><p class="form-field"><strong>Product Intelligence 構成</strong></p>';
        woocommerce_wp_select(['id'=>'_hd_default_body','label'=>'標準ボディ','options'=>$this->options('hd_body')]);
        woocommerce_wp_select(['id'=>'_hd_default_head','label'=>'標準ヘッド','options'=>$this->options('hd_head')]);
        woocommerce_wp_text_input(['id'=>'_hd_compatible_bodies','label'=>'対応ボディID','description'=>'カンマ区切り（例: 12,15,20）','desc_tip'=>true]);
        woocommerce_wp_text_input(['id'=>'_hd_compatible_heads','label'=>'対応ヘッドID','description'=>'カンマ区切り（例: 31,35）','desc_tip'=>true]);
        woocommerce_wp_text_input(['id'=>'_hd_related_accessories','label'=>'推奨アクセサリーID','description'=>'カンマ区切り','desc_tip'=>true]);
        woocommerce_wp_text_input(['id'=>'_hd_sku_pattern','label'=>'SKUパターン','placeholder'=>'HD-{BODY}-{HEAD}-{MATERIAL}']);
        echo '</div>';
    }
    public function save(int $post_id): void {
        if (!current_user_can('edit_post', $post_id)) return;
        foreach (['_hd_default_body','_hd_default_head','_hd_compatible_bodies','_hd_compatible_heads','_hd_related_accessories','_hd_sku_pattern'] as $key) {
            $value = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
            $value === '' ? delete_post_meta($post_id, $key) : update_post_meta($post_id, $key, $value);
        }
    }
    private function ids(int $product_id, string $key, string $fallback): array {
        $raw = (string)get_post_meta($product_id, $key, true);
        if ($raw === '') { $one = absint(get_post_meta($product_id, $fallback, true)); return $one ? [$one] : []; }
        return array_values(array_filter(array_map('absint', explode(',', $raw))));
    }
    public function shortcode(array $atts=[]): string {
        if (!function_exists('wc_get_product')) return '';
        $atts = shortcode_atts(['product_id'=>get_the_ID()], $atts, 'himedoll_configurator');
        $product_id = absint($atts['product_id']);
        if (!$product_id) return '';
        $bodies = $this->ids($product_id, '_hd_compatible_bodies', '_hd_default_body');
        $heads = $this->ids($product_id, '_hd_compatible_heads', '_hd_default_head');
        if (!$bodies && !$heads) return '';
        ob_start();
        echo '<section class="hd-configurator" data-product="'.esc_attr($product_id).'"><h3>組み合わせを選択</h3>';
        foreach ([['body','ボディ',$bodies],['head','ヘッド',$heads]] as [$name,$label,$ids]) {
            if (!$ids) continue; echo '<label>'.esc_html($label).'<select name="hd_'.$name.'">';
            foreach ($ids as $id) { $post=get_post($id); if ($post) echo '<option value="'.esc_attr($id).'">'.esc_html($post->post_title).'</option>'; }
            echo '</select></label>';
        }
        echo '<p class="description">選択内容は注文メモに保存できます。正式な価格差分は商品バリエーションで設定してください。</p></section>';
        return (string)ob_get_clean();
    }
    public function summary(): void { echo do_shortcode('[himedoll_configurator]'); }
}
