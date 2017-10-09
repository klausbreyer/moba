<?php
/**
 * @package moba
 * @version 0.0.1
 */
/*
Plugin Name: moba
Plugin URI: https://klaus-breyer.de/projekte/moba
Description: mobile batch uploading plugin
Author: Klaus Breyer
Version: 0.0.1
Author URI: https://klaus-breyer.de
*/

add_action( 'admin_menu', 'moba_plugin_menu' );

function moba_plugin_menu() {

    add_menu_page('Mobile Batch', 'Mobile Batch', 'manage_options', 'moba/interface.php');
}

?>