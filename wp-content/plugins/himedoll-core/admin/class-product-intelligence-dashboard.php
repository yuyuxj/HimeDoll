<?php
defined('ABSPATH') || exit;
final class HimeDoll_Product_Intelligence_Dashboard {
    private static ?self $instance=null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct(){ add_action('admin_menu',[$this,'menu'],8); add_action('woocommerce_product_options_general_product_data',[$this,'sku_button']); }
    public function menu(): void {
        add_menu_page('Product Intelligence','Product Intelligence','manage_woocommerce','himedoll-product-intelligence',[$this,'page'],'dashicons-networking',56);
        add_submenu_page('himedoll-product-intelligence','概要','概要','manage_woocommerce','himedoll-product-intelligence',[$this,'page']);
    }
    public function sku_button(): void { echo '<div class="options_group"><p class="form-field"><label>SKU自動生成</label><input type="checkbox" name="_hd_generate_sku" value="1"> SKUが空の場合に保存時生成</p></div>'; }
    public function page(): void {
        if(!current_user_can('manage_woocommerce')) return;
        $counts=[]; foreach(['hd_body'=>'ボディ','hd_head'=>'ヘッド','hd_accessory'=>'アクセサリー'] as $type=>$label){$obj=wp_count_posts($type);$counts[$label]=(int)($obj->publish??0);}
        $configured=(int)(new WP_Query(['post_type'=>'product','post_status'=>'publish','meta_query'=>[['key'=>'_hd_default_body','compare'=>'EXISTS']], 'fields'=>'ids','posts_per_page'=>1]))->found_posts;
        echo '<div class="wrap"><h1>HimeDoll Product Intelligence</h1><p>ボディ、ヘッド、アクセサリーを独立管理し、販売商品へ再利用できる構成データ層です。</p><div style="display:flex;gap:16px;flex-wrap:wrap">';
        foreach($counts as $label=>$count) echo '<div class="card"><h2>'.esc_html($label).'</h2><p style="font-size:28px">'.esc_html((string)$count).'</p></div>';
        echo '<div class="card"><h2>構成済み商品</h2><p style="font-size:28px">'.esc_html((string)$configured).'</p></div></div>';
        echo '<h2>運用手順</h2><ol><li>ボディとヘッドを登録</li><li>商品編集画面で標準・対応コンポーネントを指定</li><li>必要に応じてSKU自動生成を有効化</li><li>商品ページの組み合わせ表示を確認</li></ol></div>';
    }
}
