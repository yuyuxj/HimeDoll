<?php
defined('ABSPATH') || exit;
final class HimeDoll_ERP_Installer {
    public static function install(): void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();
        $warehouses = $wpdb->prefix . 'hd_warehouses';
        $ledger = $wpdb->prefix . 'hd_inventory_ledger';
        $sql1 = "CREATE TABLE {$warehouses} (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            code varchar(40) NOT NULL,
            name varchar(120) NOT NULL,
            country varchar(2) NOT NULL DEFAULT 'JP',
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL,
            PRIMARY KEY (id), UNIQUE KEY code (code)
        ) {$charset};";
        $sql2 = "CREATE TABLE {$ledger} (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            product_id bigint unsigned NOT NULL,
            variation_id bigint unsigned NOT NULL DEFAULT 0,
            warehouse_id bigint unsigned NOT NULL,
            movement_type varchar(30) NOT NULL,
            quantity decimal(18,4) NOT NULL,
            reference_type varchar(30) NOT NULL DEFAULT '',
            reference_id bigint unsigned NOT NULL DEFAULT 0,
            note text NULL,
            created_by bigint unsigned NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY product_warehouse (product_id, variation_id, warehouse_id),
            KEY reference (reference_type, reference_id)
        ) {$charset};";
        dbDelta($sql1); dbDelta($sql2);
        $exists = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$warehouses} WHERE code=%s", 'jp-main'));
        if (!$exists) {
            $wpdb->insert($warehouses, ['code'=>'jp-main','name'=>'日本主仓','country'=>'JP','status'=>'active','created_at'=>current_time('mysql')]);
            $wpdb->insert($warehouses, ['code'=>'cn-main','name'=>'中国采购仓','country'=>'CN','status'=>'active','created_at'=>current_time('mysql')]);
        }
        update_option('himedoll_erp_db_version', '8.0.0');
    }
}
