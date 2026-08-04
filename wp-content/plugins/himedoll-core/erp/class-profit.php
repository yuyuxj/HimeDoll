<?php
defined('ABSPATH') || exit;
final class HimeDoll_Profit { private static ?self $instance=null; public static function instance():self{return self::$instance??=new self();}
 public function purchase_total_cost(int $pid):float{$unit=(float)get_post_meta($pid,'hd_po_unit_cost',true);$qty=max(1,(float)get_post_meta($pid,'hd_po_quantity',true));return $unit*$qty+(float)get_post_meta($pid,'hd_po_domestic_freight',true)+(float)get_post_meta($pid,'hd_po_international_freight',true)+(float)get_post_meta($pid,'hd_po_customs_cost',true);}
 public function order_cost(int $oid):float{$pid=absint(get_post_meta($oid,'_hd_purchase_order_id',true));return $pid?$this->purchase_total_cost($pid):0.0;}
 public function order_profit(int $oid):float{$o=function_exists('wc_get_order')?wc_get_order($oid):false;if(!$o)return 0.0;$fees=(float)get_post_meta($oid,'_hd_payment_fee',true)+(float)get_post_meta($oid,'_hd_ad_cost',true)+(float)get_post_meta($oid,'_hd_jp_shipping_cost',true);return (float)$o->get_total()-$this->order_cost($oid)-$fees;}
 public function order_margin(int $oid):float{$o=function_exists('wc_get_order')?wc_get_order($oid):false;if(!$o||$o->get_total()<=0)return 0.0;return $this->order_profit($oid)/(float)$o->get_total()*100;}
}
