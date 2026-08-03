<?php
defined('ABSPATH') || exit;

function himedoll_site_name(): string {
    return get_bloginfo('name') ?: 'HimeDoll';
}
