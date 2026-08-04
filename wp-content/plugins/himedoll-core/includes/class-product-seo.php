<?php
defined('ABSPATH') || exit;
final class HimeDoll_Product_SEO {
    private static ?self $instance=null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct(){ add_action('wp_head',[$this,'head'],3); add_filter('pre_get_document_title',[$this,'title'],20); }
    public function title(string $title): string { if(is_singular('product')){ $custom=get_post_meta(get_queried_object_id(),'hd_seo_title',true); if($custom)return sanitize_text_field($custom); } return $title; }
    public function head(): void {
        if(!is_singular('product')||!function_exists('wc_get_product'))return; $id=get_queried_object_id(); $p=wc_get_product($id); if(!$p)return;
        $url=get_permalink($id); $title=get_post_meta($id,'hd_seo_title',true)?:wp_get_document_title();
        $desc=get_post_meta($id,'hd_seo_description',true)?:wp_strip_all_tags($p->get_short_description()?:get_the_excerpt($id)); $desc=wp_trim_words($desc,42,'');
        $image=get_the_post_thumbnail_url($id,'full');
        echo '<link rel="canonical" href="'.esc_url($url).'">'."\n";
        foreach(['description'=>$desc,'twitter:card'=>'summary_large_image','twitter:title'=>$title,'twitter:description'=>$desc] as $name=>$value) if($value) echo '<meta name="'.esc_attr($name).'" content="'.esc_attr($value).'">'."\n";
        foreach(['og:type'=>'product','og:title'=>$title,'og:description'=>$desc,'og:url'=>$url,'og:image'=>$image] as $property=>$value) if($value) echo '<meta property="'.esc_attr($property).'" content="'.esc_attr($value).'">'."\n";
        $schema=['@context'=>'https://schema.org','@type'=>'Product','name'=>$p->get_name(),'url'=>$url,'sku'=>$p->get_sku(),'description'=>$desc,'image'=>$image?[$image]:[]];
        $brand=wp_get_post_terms($id,'product_brand',['fields'=>'names']); if(!is_wp_error($brand)&&$brand)$schema['brand']=['@type'=>'Brand','name'=>$brand[0]];
        $schema['offers']=['@type'=>'Offer','url'=>$url,'priceCurrency'=>get_woocommerce_currency(),'price'=>$p->get_price(),'availability'=>$p->is_in_stock()?'https://schema.org/InStock':'https://schema.org/OutOfStock','itemCondition'=>'https://schema.org/NewCondition'];
        if($p->get_review_count()>0)$schema['aggregateRating']=['@type'=>'AggregateRating','ratingValue'=>$p->get_average_rating(),'reviewCount'=>$p->get_review_count()];
        $this->json($schema); $faq=json_decode((string)get_post_meta($id,'hd_ai_faq',true),true);
        if(is_array($faq)&&$faq){ $entities=[]; foreach($faq as $row) if(!empty($row['question'])&&!empty($row['answer']))$entities[]=['@type'=>'Question','name'=>wp_strip_all_tags($row['question']),'acceptedAnswer'=>['@type'=>'Answer','text'=>wp_strip_all_tags($row['answer'])]]; if($entities)$this->json(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$entities]); }
        $items=[['@type'=>'ListItem','position'=>1,'name'=>get_bloginfo('name'),'item'=>home_url('/')],['@type'=>'ListItem','position'=>2,'name'=>'商品一覧','item'=>get_post_type_archive_link('product')],['@type'=>'ListItem','position'=>3,'name'=>$p->get_name(),'item'=>$url]];
        $this->json(['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$items]);
    }
    private function json(array $data): void { echo '<script type="application/ld+json">'.wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n"; }
}
