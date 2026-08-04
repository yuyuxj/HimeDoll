<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Product_Generator {
    private static ?self $instance = null;
    private HimeDoll_AI_Client $client;

    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct() { $this->client = new HimeDoll_AI_Client(); }

    public function run(string $task, int $product_id): array {
        if (!function_exists('wc_get_product')) return ['success'=>false,'error'=>'WooCommerce が有効ではありません'];
        $product = wc_get_product($product_id);
        if (!$product) return ['success'=>false,'error'=>'商品不存在'];
        $prompt = $this->build_prompt($task, $this->product_context($product));
        if (!$prompt) return ['success'=>false,'error'=>'不支持的 AI 任务'];

        $result = $this->client->chat([
            ['role'=>'system','content'=>'あなたは日本向けECサイトの編集責任者です。自然な日本語を使い、誇張、虚偽、医療効果の断定、未確認の仕様を避けてください。JSON指定時は説明文やMarkdownを付けず有効なJSONだけを返してください。'],
            ['role'=>'user','content'=>$prompt],
        ], ['temperature'=>0.3,'max_tokens'=>2400]);

        HimeDoll_AI_Logger::instance()->log([
            'status'=>!empty($result['success'])?'success':'failed','task'=>$task,'product_id'=>$product_id,
            'model'=>$result['model']??'','error'=>$result['error']??'',
            'prompt_tokens'=>absint($result['usage']['prompt_tokens']??0),
            'completion_tokens'=>absint($result['usage']['completion_tokens']??0),
        ]);
        if (empty($result['success'])) return $result;
        return $this->save_result($task,$product_id,(string)$result['content']);
    }

    private function product_context(WC_Product $product): string {
        $id=$product->get_id();
        $fields=[
            '商品ID'=>$id,'現在の商品名'=>$product->get_name(),'SKU'=>$product->get_sku(),
            '通常価格'=>$product->get_regular_price(),'販売価格'=>$product->get_sale_price(),
            '現在の短い説明'=>wp_strip_all_tags($product->get_short_description()),
            'ブランド'=>$this->terms($id,'product_brand'),'カテゴリー'=>$this->terms($id,'product_cat'),
            '身長'=>get_post_meta($id,'hd_height',true),'重量'=>get_post_meta($id,'hd_weight',true),
            '素材'=>get_post_meta($id,'hd_material',true),'カップ'=>get_post_meta($id,'hd_cup',true),
            '肌色'=>get_post_meta($id,'hd_skin',true),'骨格'=>get_post_meta($id,'hd_skeleton',true),
            '自立'=>get_post_meta($id,'hd_standing',true),'加熱'=>get_post_meta($id,'hd_heating',true),
            'ヘッド交換'=>get_post_meta($id,'hd_head_replaceable',true),'AI機能'=>get_post_meta($id,'hd_ai',true),
            '販売タイプ'=>get_post_meta($id,'hd_sale_type',true),'納期'=>get_post_meta($id,'hd_delivery',true),
            '保証'=>get_post_meta($id,'hd_warranty',true),
        ];
        $lines=[]; foreach($fields as $label=>$value){ if($value!=='' && $value!==null) $lines[]=$label.'：'.$value; }
        return implode("\n",$lines);
    }

    private function terms(int $id,string $taxonomy): string {
        if(!taxonomy_exists($taxonomy)) return '';
        $terms=wp_get_post_terms($id,$taxonomy,['fields'=>'names']);
        return is_wp_error($terms)?'':implode('、',$terms);
    }

    private function build_prompt(string $task,string $context): string {
        $defaults=[
            'title'=>"以下の商品情報から、日本の検索ユーザーに分かりやすい商品名を1件だけ作成してください。60文字以内、未確認の仕様は追加しないでください。\n\n{context}",
            'short_description'=>"以下の商品情報から、商品ページ冒頭用の短い紹介文を自然な日本語で作成してください。120〜180文字、HTMLなし。\n\n{context}",
            'description'=>"以下の商品情報を基に、日本向けECの商品説明をHTMLで作成してください。h2/h3、段落、箇条書きを使用し、特徴、仕様、選び方、配送・取扱上の注意を含めてください。未確認情報は書かないでください。\n\n{context}",
            'seo'=>"以下の商品情報を基にJSONを出力してください。キーは title（60文字以内）、description（120〜160文字）、keywords（文字列配列）、slug（英数字とハイフン）です。\n\n{context}",
            'faq'=>"以下の商品について購入前によくある質問と回答を5件、JSON配列で出力してください。各要素のキーは question と answer。断定できない内容は販売店への確認を案内してください。\n\n{context}",
            'alt'=>"以下の商品情報を基に、主画像用の自然で具体的な日本語ALTテキストを1文、80文字以内で作成してください。キーワードの羅列は禁止。\n\n{context}",
            'catalog'=>"以下の商品情報を基にJSONを出力してください。キーは tags（日本語タグ5〜10件の配列）、search_terms（検索語5〜10件の配列）、recommended_category（最適なカテゴリ名1件）です。\n\n{context}",
            'package'=>"以下の商品情報から商品ページ一式をJSONで出力してください。キーは title、short_description、description_html、seo_title、seo_description、keywords（配列）、faq（question/answer配列）、alt、tags（配列）、slug。未確認の仕様は追加しないでください。\n\n{context}",
        ];
        if(!isset($defaults[$task])) return '';
        $custom=(string)get_option('hd_ai_prompt_'.$task,'');
        return str_replace('{context}',$context,$custom!==''?$custom:$defaults[$task]);
    }

    private function decode_json(string $content): ?array {
        $content=trim($content);
        $content=preg_replace('/^```(?:json)?\s*|\s*```$/i','',$content);
        $decoded=json_decode($content,true);
        if(is_array($decoded)) return $decoded;
        $start=min(array_filter([strpos($content,'{')===false?PHP_INT_MAX:strpos($content,'{'),strpos($content,'[')===false?PHP_INT_MAX:strpos($content,'[')]));
        $end=max(strrpos($content,'}'),strrpos($content,']'));
        if($start!==PHP_INT_MAX && $end!==false && $end>$start){
            $decoded=json_decode(substr($content,$start,$end-$start+1),true);
            if(is_array($decoded)) return $decoded;
        }
        return null;
    }

    private function save_result(string $task,int $product_id,string $content): array {
        if($task==='title') wp_update_post(['ID'=>$product_id,'post_title'=>sanitize_text_field($content)]);
        elseif($task==='short_description') wp_update_post(['ID'=>$product_id,'post_excerpt'=>sanitize_textarea_field($content)]);
        elseif($task==='description') wp_update_post(['ID'=>$product_id,'post_content'=>wp_kses_post($content)]);
        elseif($task==='alt'){
            $thumbnail_id=get_post_thumbnail_id($product_id); if(!$thumbnail_id) return ['success'=>false,'error'=>'商品没有主图'];
            update_post_meta($thumbnail_id,'_wp_attachment_image_alt',sanitize_text_field($content));
        } elseif(in_array($task,['seo','faq','catalog','package'],true)) {
            $json=$this->decode_json($content); if(!is_array($json)) return ['success'=>false,'error'=>'AI 返回不是有效 JSON'];
            if($task==='seo') $this->save_seo($product_id,$json);
            if($task==='faq') update_post_meta($product_id,'hd_ai_faq',wp_json_encode($this->sanitize_faq($json),JSON_UNESCAPED_UNICODE));
            if($task==='catalog') $this->save_catalog($product_id,$json);
            if($task==='package') $this->save_package($product_id,$json);
        }
        update_post_meta($product_id,'hd_ai_last_task',$task);
        update_post_meta($product_id,'hd_ai_last_generated_at',current_time('mysql'));
        clean_post_cache($product_id);
        return ['success'=>true,'content'=>$content];
    }

    private function save_seo(int $id,array $json): void {
        update_post_meta($id,'hd_seo_title',sanitize_text_field($json['title']??$json['seo_title']??''));
        update_post_meta($id,'hd_seo_description',sanitize_textarea_field($json['description']??$json['seo_description']??''));
        update_post_meta($id,'hd_seo_keywords',array_values(array_filter(array_map('sanitize_text_field',(array)($json['keywords']??[])))));
        if(!empty($json['slug'])) wp_update_post(['ID'=>$id,'post_name'=>sanitize_title($json['slug'])]);
    }

    private function sanitize_faq(array $rows): array {
        $safe=[]; foreach($rows as $row){ if(!is_array($row)) continue; $q=sanitize_text_field($row['question']??''); $a=sanitize_textarea_field($row['answer']??''); if($q&&$a)$safe[]=['question'=>$q,'answer'=>$a]; }
        return array_slice($safe,0,10);
    }

    private function save_catalog(int $id,array $json): void {
        $tags=array_values(array_filter(array_map('sanitize_text_field',(array)($json['tags']??[]))));
        if($tags) wp_set_object_terms($id,$tags,'product_tag',true);
        update_post_meta($id,'hd_ai_search_terms',array_values(array_filter(array_map('sanitize_text_field',(array)($json['search_terms']??[])))));
        update_post_meta($id,'hd_ai_recommended_category',sanitize_text_field($json['recommended_category']??''));
    }

    private function save_package(int $id,array $json): void {
        $post=['ID'=>$id];
        if(!empty($json['title'])) $post['post_title']=sanitize_text_field($json['title']);
        if(!empty($json['short_description'])) $post['post_excerpt']=sanitize_textarea_field($json['short_description']);
        if(!empty($json['description_html'])) $post['post_content']=wp_kses_post($json['description_html']);
        if(count($post)>1) wp_update_post($post);
        $this->save_seo($id,$json);
        if(!empty($json['faq'])) update_post_meta($id,'hd_ai_faq',wp_json_encode($this->sanitize_faq((array)$json['faq']),JSON_UNESCAPED_UNICODE));
        $this->save_catalog($id,$json);
        if(!empty($json['alt']) && ($thumb=get_post_thumbnail_id($id))) update_post_meta($thumb,'_wp_attachment_image_alt',sanitize_text_field($json['alt']));
    }
}
