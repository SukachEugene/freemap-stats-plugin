<?php
/*
Plugin Name: Freemap Stats
Description: Display Freemap site general statistics
Version: 1.0
Author: Eugene Sukach
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require_once('functions.php');


function add_page_stats_menu_item()
{
    add_menu_page('Freemap Stats', 'Freemap Stats', 'manage_options', 'page-stats', 'page_stats_admin_page', 'dashicons-visibility');
    add_submenu_page('page-stats', 'Maps without Districts', 'Maps without Districts', 'manage_options', 'maps-without-districts', 'maps_without_districts_admin_page');
    add_submenu_page('page-stats', 'Origin SEO Maps', 'Origin SEO Maps', 'manage_options', 'origin-seo-maps', 'origin_seo_maps_admin_page');
    add_submenu_page('page-stats', 'Origin SEO Pages', 'Origin SEO Pages', 'manage_options', 'origin-seo-pages', 'origin_seo_pages_admin_page');
    add_submenu_page('page-stats', 'Empty Maps', 'Empty Maps', 'manage_options', 'empty-maps', 'empty_maps_admin_page');
}
add_action('admin_menu', 'add_page_stats_menu_item', 1);





