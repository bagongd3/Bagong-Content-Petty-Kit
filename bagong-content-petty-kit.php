<?php
/**
 * Bagong Content Petty Kit
 *
 * Plugin Name: Bagong Content Petty Kit
 * Plugin URI:  https://github.com/bagongd3/Bagong-Content-Petty-Kit
 * Description: Small tools that solve annoying content writing problems — smart pagination, SEO split, and formatting helpers.
 * Version:     1.0.0
 * Author:      Bagong De
 * Author URI:  https://github.com/bagongd3
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bagong-content-petty-kit
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if(!defined('ABSPATH')) exit;

define('BGCPK_PATH', plugin_dir_path(__FILE__));

foreach ( [
    'admin',
    'tag-link',
    'pagination',
    'metabox',
    'cache'
] as $bgcpk_file ) {

    require BGCPK_PATH . "includes/{$bgcpk_file}.php";
}

