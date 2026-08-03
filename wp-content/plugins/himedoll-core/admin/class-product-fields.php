<?php
defined('ABSPATH') || exit;
final class HimeDoll_Product_Fields {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct() {
        add_action('woocommerce_product_options_general_product_data',[$this,'render']);
        add_action('woocommerce_process_product_meta',[$this,'save']);
    }
    public function render(): void {
        $fields=['hd_height'=>'身長','hd_weight'=>'重量','hd_material'=>'素材','hd_cup'=>'カップ','hd_skin'=>'肌色','hd_ai'=>'AI機能','hd_delivery'=>'納期目安','hd_warranty'=>'保証'];
        foreach($fields as $id=>$label) woocommerce_wp_text_input(['id'=>$id,'label'=>$label,'desc_tip'=>true]);
    }
    public function save(int $post_id): void {
        foreach(['hd_height','hd_weight','hd_material','hd_cup','hd_skin','hd_ai','hd_delivery','hd_warranty'] as $key){
            if(isset($_POST[$key])) update_post_meta($post_id,$key,sanitize_text_field(wp_unslash($_POST[$key])));
        }
    }
}
