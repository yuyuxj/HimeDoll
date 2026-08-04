<?php
defined('ABSPATH') || exit;
final class HimeDoll_RMA {
    private static ?self $instance=null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct(){ add_action('init',[$this,'register']); add_action('add_meta_boxes',[$this,'boxes']); add_action('save_post_hd_rma',[$this,'save']); }
    public function register(): void { register_post_type('hd_rma',['labels'=>['name'=>'售后 / RMA','singular_name'=>'RMA','add_new_item'=>'新建售后单','edit_item'=>'编辑售后单'],'public'=>false,'show_ui'=>true,'show_in_menu'=>'himedoll-settings','supports'=>['title','editor'],'capability_type'=>'post','map_meta_cap'=>true]); }
    public function boxes(): void { add_meta_box('hd_rma_data','售后信息',[$this,'render'],'hd_rma','normal'); }
    public function render(WP_Post $post): void { wp_nonce_field('hd_rma_save','hd_rma_nonce'); $fields=['hd_rma_order_id'=>'WooCommerce 订单 ID','hd_rma_type'=>'类型（refund/return/repair/parts）','hd_rma_status'=>'状态','hd_rma_amount'=>'退款金额','hd_rma_tracking'=>'退货运单号']; echo '<table class="form-table">'; foreach($fields as $k=>$l){echo '<tr><th>'.esc_html($l).'</th><td><input class="regular-text" name="'.esc_attr($k).'" value="'.esc_attr(get_post_meta($post->ID,$k,true)).'"></td></tr>';} echo '</table>'; }
    public function save(int $id): void { if(!isset($_POST['hd_rma_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hd_rma_nonce'])),'hd_rma_save')||!current_user_can('edit_post',$id))return; foreach(['hd_rma_order_id','hd_rma_type','hd_rma_status','hd_rma_amount','hd_rma_tracking'] as $k){ if(isset($_POST[$k]))update_post_meta($id,$k,sanitize_text_field(wp_unslash($_POST[$k]))); } }
}
