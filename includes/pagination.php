<?php

if(!defined('ABSPATH')) exit;

add_filter('the_posts', 'bgcpk_inject_pagination_engine');

function bgcpk_inject_pagination_engine($posts){

    if (empty($posts)) return $posts;
    if (!is_single()) return $posts;

    $post = $posts[0];

    if (get_post_meta($post->ID,'_bgcpk_disable',true)) return $posts;

    // Jangan proses ulang
    if (strpos($post->post_content, '<!--nextpage-->') !== false) {
        return $posts;
    }

    $type = get_option('bgcpk_split_type','pages');
    $val  = get_option('bgcpk_split_value',2);

    $content = $post->post_content;

    if ($type === 'h2') {

        $sections = preg_split(
            '/(<h2[^>]*>.*?<\/h2>)/is',
            $content,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        $out = '';

        foreach ($sections as $section) {

            $out .= $section;

            // insert break after each H2 block
            if (stripos($section, '</h2>') !== false) {
                $out = rtrim($out, "<!--nextpage-->");
            }
        }

        $post->post_content = trim($out);
        $posts[0] = $post;

        return $posts;
    }

    $paras = preg_split("/\R\R+/",$content);

    if($type === 'pages'){

        $total = count($paras);
        $pages = max(1, intval($val));

        if($pages === 1){
            $post->post_content = $content;
        } else {

            $per = ceil($total / $pages);
            $chunks = array_chunk($paras, $per);

            $out = '';
            $last = count($chunks) - 1;

            foreach($chunks as $i => $chunk){
                $out .= implode("\n\n", $chunk);

                if($i !== $last){
                    $out .= "\n\n<!--nextpage-->\n\n";
                }
            }

            $post->post_content = trim($out);
        }
    } else {

        $out = '';
        $count = 0;
        $total = count($paras);
        $i = 0;

        foreach($paras as $p){

            $i++;
            $count += str_word_count( wp_strip_all_tags($p) );
            $out .= $p . "\n\n";

            if($count >= $val && $i < $total){
                $out .= "<!--nextpage-->\n\n";
                $count = 0;
            }
        }

        $post->post_content = trim($out);
    }

    $posts[0] = $post;

    return $posts;
}

function bgcpk_pagination_settings(){

    if( ! current_user_can('manage_options') ){
        return;
    }

    $type = get_option('bgcpk_split_type','pages');
    $val  = get_option('bgcpk_split_value',2);

    if(
        isset($_POST['bgcpk_page_nonce']) &&
        wp_verify_nonce(
    sanitize_text_field( wp_unslash($_POST['bgcpk_page_nonce']) ),
    'bgcpk_page_save')

    ){

        if(isset($_POST['type'])){
            $type = sanitize_text_field( wp_unslash($_POST['type']) );
            if($type === 'h2'){
                $val = 0; // tidak dipakai di H2 mode
            } else {
                $val = isset($_POST['value']) ? intval(wp_unslash($_POST['value'])) : 1;
            }

            update_option('bgcpk_split_type', $type);
            update_option('bgcpk_split_value', $val);
        }
    }
    ?>

    <div class="wrap">
        <h1>Smart Pagination</h1>

        <form method="post">

            <?php wp_nonce_field('bgcpk_page_save','bgcpk_page_nonce'); ?>

            <label>Mode:</label>
            <select name="type">
                <option value="pages" <?php selected($type,'pages'); ?>>Split to Pages</option>
                <option value="words" <?php selected($type,'words'); ?>>Split to Words</option>
                <option value="h2" <?php selected($type,'h2'); ?>>Split by H2 (SEO Optimal)</option>
            </select>

            <br><br>

            <label>Value:</label>
            <input type="number" name="value" min="1" value="<?php echo esc_attr($val); ?>">

            <p class="description">
                Pages = jumlah halaman.<br>
                Words = maksimal kata per halaman.<br>
                H2 = otomatis memecah konten setiap ada heading H2.
            </p>

            <button class="button button-primary">Save Settings</button>

        </form>
            <script>
                document.addEventListener('DOMContentLoaded', function(){

                    const mode = document.querySelector('select[name="type"]');
                    const valueField = document.querySelector('input[name="value"]');

                    function toggleValue(){
                        if(mode.value === 'h2'){
                            valueField.disabled = true;
                            valueField.value = '';
                            valueField.style.opacity = '0.5';
                        } else {
                            valueField.disabled = false;
                            if(!valueField.value){
                                valueField.value = <?php echo (int) $val; ?>;
                            }
                            valueField.style.opacity = '1';
                        }
                    }

                    mode.addEventListener('change', toggleValue);
                    toggleValue();
                });
            </script>
    </div>

    <?php
}