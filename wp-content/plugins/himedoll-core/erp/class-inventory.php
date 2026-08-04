<?php
defined('ABSPATH') || exit;
final class HimeDoll_Inventory {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    public function warehouses(): array { global $wpdb; return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hd_warehouses WHERE status='active' ORDER BY id", ARRAY_A) ?: []; }
    public function move(int $product_id, int $warehouse_id, float $quantity, string $type, array $args=[]): int {
        global $wpdb;
        if ($product_id < 1 || $warehouse_id < 1 || 0.0 === $quantity) return 0;
        $allowed=['opening','purchase_in','sale_out','return_in','adjustment','transfer_in','transfer_out','reserved','released'];
        if (!in_array($type,$allowed,true)) return 0;
        $wpdb->insert($wpdb->prefix.'hd_inventory_ledger',[
            'product_id'=>$product_id,'variation_id'=>absint($args['variation_id']??0),'warehouse_id'=>$warehouse_id,
            'movement_type'=>$type,'quantity'=>$quantity,'reference_type'=>sanitize_key($args['reference_type']??''),
            'reference_id'=>absint($args['reference_id']??0),'note'=>sanitize_textarea_field($args['note']??''),
            'created_by'=>get_current_user_id(),'created_at'=>current_time('mysql')
        ],['%d','%d','%d','%s','%f','%s','%d','%s','%d','%s']);
        return (int)$wpdb->insert_id;
    }
    public function stock(int $product_id, int $warehouse_id=0): float {
        global $wpdb; $table=$wpdb->prefix.'hd_inventory_ledger';
        if ($warehouse_id) return (float)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(quantity),0) FROM {$table} WHERE product_id=%d AND warehouse_id=%d",$product_id,$warehouse_id));
        return (float)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(quantity),0) FROM {$table} WHERE product_id=%d",$product_id));
    }
    public function receive_purchase(int $purchase_id, int $warehouse_id): bool {
        $product_id=absint(get_post_meta($purchase_id,'hd_po_product_id',true));
        $qty=(float)get_post_meta($purchase_id,'hd_po_quantity',true);
        if (!$product_id || $qty<=0) return false;
        if (get_post_meta($purchase_id,'hd_po_received_at',true)) return false;
        $id=$this->move($product_id,$warehouse_id,$qty,'purchase_in',['reference_type'=>'purchase_order','reference_id'=>$purchase_id]);
        if ($id) { update_post_meta($purchase_id,'hd_po_received_at',current_time('mysql')); update_post_meta($purchase_id,'hd_po_status','received'); }
        return (bool)$id;
    }
}
