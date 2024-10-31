<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * @package Pagezii SEO Agency Plugin
 */
/*
Plugin Name: Pagezii SEO Agency Plugin
Plugin URI: https://pagezii.com/wordpress
Description: Pagezii SEO Agency lets Digital Marketing agencies offer a simple way for website visitors to automatically create SEO, UX and Social audit PDF reports for their domains.
Version: 1.0.3
Author: Pagezii
Author URI: https://pagezii.com
License: GPLv2 or later

Pagezii SEO Agency Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Pagezii SEO Agency Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Pagezii SEO Agency Plugin. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'PAGEZII_SNAPSHOT_VERSION', '1.0.3' );
define( 'PAGEZII__MINIMUM_WP_VERSION', '3.5' );
define( 'PAGEZII__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( PAGEZII__PLUGIN_DIR . 'class.pagezii-snapshot.php' );

register_activation_hook( __FILE__, array( 'Pagezii_Snapshot', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Pagezii_Snapshot', 'plugin_deactivation' ) );

if ( is_admin() ) {
	require_once( PAGEZII__PLUGIN_DIR . 'class.pagezii-admin.php' );
	add_action( 'init', array( 'Pagezii_Admin', 'init' ) );
}

add_shortcode( 'pagezii', array( 'Pagezii_Snapshot', 'display_form') );
add_action( 'init', array( 'Pagezii_Snapshot', 'buffering' ) );
add_action( 'pagezii_snapshot_cron_job', array( 'Pagezii_Snapshot', 'cron_job' ), 10, 4 );
