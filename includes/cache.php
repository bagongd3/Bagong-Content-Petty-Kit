<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ========================
// SIMPLE TRANSIENT CACHE
// ========================

function bgcpk_cache_key( $key ) {
    return 'bgcpk_' . $key;
}

function bgcpk_cache_get( $key ) {
    return get_transient( bgcpk_cache_key( $key ) );
}

function bgcpk_cache_set( $key, $data, $ttl = DAY_IN_SECONDS ) {
    set_transient( bgcpk_cache_key( $key ), $data, $ttl );
}

function bgcpk_cache_delete( $key ) {
    delete_transient( bgcpk_cache_key( $key ) );
}


// ========================
// CLEAR CACHE ON POST UPDATE
// ========================

function bgcpk_clear_post_cache( $post_id ) {

    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    bgcpk_cache_delete( 'content_' . $post_id );
}

add_action( 'save_post', 'bgcpk_clear_post_cache' );
add_action( 'deleted_post', 'bgcpk_clear_post_cache' );


// ========================
// CLEAR CACHE WHEN TAGS CHANGE
// ========================

function bgcpk_clear_cache_on_tag_change( $object_id, $terms, $tt_ids, $taxonomy ) {

    if ( $taxonomy !== 'post_tag' ) {
        return;
    }

    bgcpk_cache_delete( 'content_' . $object_id );
}

add_action( 'set_object_terms', 'bgcpk_clear_cache_on_tag_change', 10, 4 );
// add_action( 'deleted_term_relationships', 'bgcpk_clear_cache_on_tag_change', 10, 4 );
// add_action( 'edited_terms', 'bgcpk_clear_cache_on_tag_change', 10, 4 );
// add_action( 'created_term', 'bgcpk_clear_cache_on_tag_change', 10, 4 );
