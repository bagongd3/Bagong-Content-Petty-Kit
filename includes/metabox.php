<?php

if(!defined('ABSPATH')) exit;

add_action('add_meta_boxes', function(){
    add_meta_box('bgcpk_toggle','Smart Toolkit', 'bgcpk_toggle_box','post');
});

function bgcpk_toggle_box($post){
    wp_nonce_field('bgcpk_metabox_save','bgcpk_metabox_nonce');
    $v = get_post_meta($post->ID,'_bgcpk_disable',true);
    ?>
    <label>
        <input type="checkbox" name="bgcpk_disable" value="1" <?php checked($v,1,false) ?>>
        Disable Smart Toolkit on this post
    </label>
    <?php
}

function bgcpk_save_metabox($post_id){

    if( ! isset($_POST['bgcpk_metabox_nonce']) ) return;

    if(
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash($_POST['bgcpk_metabox_nonce']) ),
            'bgcpk_metabox_save'
        )
    ){
        return;
    }

    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if( ! current_user_can('edit_post', $post_id) ) return;

    if(isset($_POST['bgcpk_enable'])){
        update_post_meta($post_id,'_bgcpk_enabled',1);
    } else {
        delete_post_meta($post_id,'_bgcpk_enabled');
    }
}

