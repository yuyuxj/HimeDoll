<?php
defined('ABSPATH') || exit;
final class HimeDoll_SKU_Generator {
    private static ?self $instance=null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct(){ add_action('woocommerce_process_product_meta',[$this,'generate'],30); }
    public function generate(int $post_id): void {
        if (!isset($_POST['_hd_generate_sku']) || !current_user_can('edit_post',$post_id)) return;
        $product=wc_get_product($post_id); if(!$product || $product->get_sku()) return;
        $pattern=(string)get_post_meta($post_id,'_hd_sku_pattern',true); if($pattern==='') $pattern='HD-{ID}-{BODY}-{HEAD}';
        $body=$this->code(absint(get_post_meta($post_id,'_hd_default_body',true)),'B');
        $head=$this->code(absint(get_post_meta($post_id,'_hd_default_head',true)),'H');
        $material=strtoupper((string)get_post_meta($post_id,'hd_material',true));
        $sku=strtr($pattern,['{ID}'=>(string)$post_id,'{BODY}'=>$body,'{HEAD}'=>$head,'{MATERIAL}'=>$material ?: 'STD']);
        $sku=preg_replace('/[^A-Z0-9_-]/i','-',strtoupper($sku));
        try { $product->set_sku($sku); $product->save(); } catch (Throwable $e) { update_post_meta($post_id,'_hd_sku_error',$e->getMessage()); }
    }
    private function code(int $id,string $prefix): string { if(!$id)return $prefix.'00'; $code=(string)get_post_meta($id,'_hd_component_code',true); return $code!==''?strtoupper($code):$prefix.$id; }
}
