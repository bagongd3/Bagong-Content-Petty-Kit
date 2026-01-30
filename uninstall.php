<?php
/**
 * Uninstall cleanup for Bagong Content Petty Kit
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Delete plugin options
|--------------------------------------------------------------------------
*/

$bgcpk_option_keys = array(
    'bgcpk_tag_mode',
    'bgcpk_split_type',
    'bgcpk_split_value',
);

foreach ( $bgcpk_option_keys as $bgcpk_option_key ) {
    delete_option( $bgcpk_option_key );
}

/*
|--------------------------------------------------------------------------
| Delete transients (if any in future)
|--------------------------------------------------------------------------
*/

$bgcpk_transient_keys = array(
    'bgcpk_cache',
);

foreach ( $bgcpk_transient_keys as $bgcpk_transient_key ) {
    delete_transient( $bgcpk_transient_key );
}
