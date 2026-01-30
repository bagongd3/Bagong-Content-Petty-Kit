<?php

if(!defined('ABSPATH')) exit;

add_filter('the_content','bgcpk_tag_link_power',12);

function bgcpk_tag_link_power($content){

    if(!is_single()) return $content;

    $post_id = get_the_ID();

    if(get_post_meta($post_id,'_bgcpk_disable',true)) return $content;

    $cache_key = 'content_' . $post_id;

    $cached = bgcpk_cache_get($cache_key);
    if($cached !== false){
        return $cached;
    }

    $tags = get_the_tags();
    if(!$tags) return $content;

    // protect headings
    $holders = [];
    $content = preg_replace_callback(
        '/<h[1-6][^>]*>.*?<\/h[1-6]>/is',
        function($m) use (&$holders){
            $k = '##H'.count($holders).'##';
            $holders[$k] = $m[0];
            return $k;
        },
        $content
    );

    // split per paragraph (SEO natural)
    if (strpos($content, '<p') !== false) {
        $paragraphs = preg_split('/(<p[^>]*>.*?<\/p>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    } else {
        $paragraphs = preg_split("/\R\R+/", $content);
    }

    foreach($paragraphs as &$para){

        if ( trim( wp_strip_all_tags( $para ) ) === '' ) continue;

        $linkCount = 0;

        foreach($tags as $tag){

            if($linkCount >= 2) break;

            $url  = get_tag_link($tag->term_id);
            $word = preg_quote($tag->name,'/');

            $para = preg_replace_callback(
                '/\b('.$word.')\b/ui',
                function($m) use ($url, &$linkCount){

                    if($linkCount >= 2) return $m[0];

                    $linkCount++;
                    return '<a href="'.esc_url($url).'" class="bgcpk-tag-link">'.$m[0].'</a>';
                },
                $para,
                1
            );
        }
    }

    $content = implode('', $paragraphs);

    // restore headings
    foreach($holders as $k=>$v){
        $content = str_replace($k,$v,$content);
    }

    bgcpk_cache_set($cache_key,$content);

    return $content;
}

// ========================
// TAG-LINK SETTINGS GUI
// ========================

function bgcpk_tag_settings(){

    if( ! current_user_can('manage_options') ){
        return;
    }

    $mode = get_option('bgcpk_tag_mode','tag');

    if(
        isset($_POST['bgcpk_tag_nonce']) &&
        wp_verify_nonce(
            sanitize_text_field( wp_unslash($_POST['bgcpk_tag_nonce']) ),
            'bgcpk_tag_save'
        )
    ){

        if(isset($_POST['mode'])){
            $mode = sanitize_text_field( wp_unslash($_POST['mode']) );
            update_option('bgcpk_tag_mode', $mode);
        }
    }
    ?>
    <div class="wrap">
        <h1>Tag-Link Settings</h1>

        <form method="post">
            <?php wp_nonce_field('bgcpk_tag_save','bgcpk_tag_nonce'); ?>

            <select name="mode">
                <option value="tag" <?php selected($mode,'tag'); ?>>Tag URL</option>
                <option value="site" <?php selected($mode,'site'); ?>>Site URL</option>
            </select>

            <br><br>
            <button class="button button-primary">Save</button>
        </form>
    </div>
    <?php
}





