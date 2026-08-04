<?php
defined('ABSPATH') || exit;
final class HimeDoll_Product_Recommendations {
    private static ?self $instance=null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct(){
        add_action('woocommerce_after_single_product_summary',[$this,'render'],24);
        add_shortcode('himedoll_recommendations',[$this,'shortcode']);
    }
    public function shortcode(array $atts=[]): string { ob_start(); $this->render(absint($atts['product_id']??0)); return (string)ob_get_clean(); }
    public function render(int $product_id=0): void {
        if(!function_exists('wc_get_product')) return;
        $product_id=$product_id?:get_the_ID(); $ids=$this->get_ids($product_id,4); if(!$ids) return;
        echo '<section class="hd-ai-recommendations"><div class="hd-section-heading"><p class="hd-eyebrow">RECOMMENDED</p><h2>あなたにおすすめの商品</h2><p>ブランド・仕様・価格帯が近い商品を自動選定しています。</p></div>';
        echo '<ul class="products columns-4">';
        $GLOBALS['post'] ??= null; $original=$GLOBALS['post'];
        foreach($ids as $id){ $GLOBALS['post']=get_post($id); setup_postdata($GLOBALS['post']); wc_get_template_part('content','product'); }
        $GLOBALS['post']=$original; wp_reset_postdata(); echo '</ul></section>';
    }
    public function get_ids(int $product_id,int $limit=4): array {
        $product=wc_get_product($product_id); if(!$product) return [];
        $tax=[]; foreach(['product_brand','product_cat','product_tag'] as $taxonomy){ if(taxonomy_exists($taxonomy)) $tax=array_merge($tax,wp_get_post_terms($product_id,$taxonomy,['fields'=>'ids'])); }
        $args=['post_type'=>'product','post_status'=>'publish','posts_per_page'=>24,'post__not_in'=>[$product_id],'fields'=>'ids','orderby'=>'date','order'=>'DESC'];
        if($tax) $args['tax_query']=[['taxonomy'=>'product_cat','field'=>'term_id','terms'=>wp_get_post_terms($product_id,'product_cat',['fields'=>'ids']),'operator'=>'IN']];
        $candidates=get_posts($args); $base=(float)$product->get_price(); $scored=[];
        foreach($candidates as $id){ $p=wc_get_product($id); if(!$p)continue; $score=0;
            foreach(['hd_material','hd_height','hd_cup','hd_sale_type'] as $key) if(get_post_meta($product_id,$key,true)!=='' && get_post_meta($product_id,$key,true)===get_post_meta($id,$key,true))$score+=3;
            $price=(float)$p->get_price(); if($base>0&&$price>0)$score+=max(0,3-abs($price-$base)/max($base,1)*3);
            $score+=(float)$p->get_average_rating(); $scored[$id]=$score;
        }
        arsort($scored); return array_slice(array_keys($scored),0,$limit);
    }
}
