<?php

if(!defined('ABSPATH')) exit;

add_action('admin_menu', function(){

    add_menu_page(
        'Bagong Content Petty Kit',
        'Content Petty Kit',
        'manage_options',
        'bgcpk-main',
        'bgcpk_dashboard',
        'dashicons-admin-links'
    );

    add_submenu_page(
        'bgcpk-main',
        'Tag Link',
        'Tag-Link',
        'manage_options',
        'bgcpk-tag',
        'bgcpk_tag_settings'
    );

    add_submenu_page(
        'bgcpk-main',
        'Pagination',
        'Pagination',
        'manage_options',
        'bgcpk-pagination',
        'bgcpk_pagination_settings'
    );

});


function bgcpk_dashboard(){
    echo '<div class="wrap"><h1>Bagong Content Petty Kit</h1>
    <p>Auto Internal Link + Smart Pagination is active 🚀</p></div>';
}
