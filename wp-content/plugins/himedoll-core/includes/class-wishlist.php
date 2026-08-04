<?php
defined('ABSPATH') || exit;

final class HimeDoll_Wishlist {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('wp_ajax_hd_toggle_wishlist', [$this, 'toggle']);
    }

    public function toggle(): void {
        check_ajax_referer('hd_wishlist', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'login_required'], 401);
        }

        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        if (!$product_id) {
            wp_send_json_error(['message' => 'invalid_product'], 400);
        }

        $wishlist = (array) get_user_meta(get_current_user_id(), 'hd_wishlist', true);

        if (in_array($product_id, $wishlist, true)) {
            $wishlist = array_values(array_diff($wishlist, [$product_id]));
            $active = false;
        } else {
            $wishlist[] = $product_id;
            $wishlist = array_values(array_unique(array_map('absint', $wishlist)));
            $active = true;
        }

        update_user_meta(get_current_user_id(), 'hd_wishlist', $wishlist);
        wp_send_json_success(['active' => $active]);
    }
}
