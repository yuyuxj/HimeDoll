<?php
defined('ABSPATH') || exit;

final class HimeDoll_Catalog_Filter {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct() { add_action('pre_get_posts', [$this, 'apply_filters']); }

    public function apply_filters(WP_Query $query): void {
        if (is_admin() || !$query->is_main_query() || !function_exists('is_shop') || !(is_shop() || is_product_taxonomy())) return;

        $tax_query = array_filter((array) $query->get('tax_query'));
        $meta_query = array_filter((array) $query->get('meta_query'));

        $multi_meta = [
            'material'=>'hd_material', 'cup'=>'hd_cup', 'skin'=>'hd_skin',
            'skeleton'=>'hd_skeleton', 'stock_type'=>'hd_stock_type',
        ];
        foreach ($multi_meta as $request => $key) {
            $values = $this->array_param($request);
            if ($values) $meta_query[] = ['key'=>$key,'value'=>$values,'compare'=>'IN'];
        }

        $brands = $this->array_param('brand', 'sanitize_title');
        if ($brands) $tax_query[] = ['taxonomy'=>'product_brand','field'=>'slug','terms'=>$brands];

        foreach (['standing'=>'hd_standing','heating'=>'hd_heating','head_removable'=>'hd_head_removable'] as $request=>$key) {
            if (!empty($_GET[$request])) $meta_query[] = ['key'=>$key,'value'=>'yes'];
        }
        if (!empty($_GET['ai'])) $meta_query[] = ['key'=>'hd_ai','value'=>'','compare'=>'!='];
        if (!empty($_GET['in_stock'])) $meta_query[] = ['key'=>'_stock_status','value'=>'instock'];

        $ranges = ['under-130'=>[0,129],'130-149'=>[130,149],'150-159'=>[150,159],'160-169'=>[160,169],'170-plus'=>[170,300]];
        $height = isset($_GET['height']) ? sanitize_key(wp_unslash($_GET['height'])) : '';
        if (isset($ranges[$height])) $meta_query[] = ['key'=>'hd_height_numeric','value'=>$ranges[$height],'type'=>'NUMERIC','compare'=>'BETWEEN'];

        if (count($tax_query) > 1 && empty($tax_query['relation'])) $tax_query['relation'] = 'AND';
        if (count($meta_query) > 1 && empty($meta_query['relation'])) $meta_query['relation'] = 'AND';
        $query->set('tax_query', $tax_query);
        $query->set('meta_query', $meta_query);

        if (isset($_GET['min_price'])) $query->set('min_price', max(0, (float) wp_unslash($_GET['min_price'])));
        if (isset($_GET['max_price'])) $query->set('max_price', max(0, (float) wp_unslash($_GET['max_price'])));
    }

    private function array_param(string $key, string $sanitizer = 'sanitize_text_field'): array {
        if (empty($_GET[$key])) return [];
        $raw = (array) wp_unslash($_GET[$key]);
        return array_values(array_unique(array_filter(array_map($sanitizer, $raw))));
    }
}
