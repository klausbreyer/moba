<?php
/**
 * @package moba
 * @version 0.1
 *
 * /*
 * Plugin Name: moba
 * Plugin URI: https://klaus-breyer.de/projekte/moba/
 * Description: mobile batch uploading plugin
 * Author: Klaus Breyer
 * Version: 0.1
 * Author URI: https://klaus-breyer.de
 */

//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_menu', 'moba_plugin_menu' );
function moba_plugin_menu() {
	add_menu_page( 'Mobile Batch', 'Mobile Batch', 'manage_options', 'moba/interface.php', '', 'dashicons-images-alt' );
}

add_action( 'init', 'register_moba_styles' );
function register_moba_styles() {
	wp_enqueue_style( 'moba',
		plugin_dir_url( __FILE__ ) . 'moba.css',
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'moba.css' ) );
	wp_enqueue_script( 'moba',
		plugin_dir_url( __FILE__ ) . 'moba.js',
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'moba.css' ) );
}

require_once( plugin_dir_path( __FILE__ ) . 'processing.php' );