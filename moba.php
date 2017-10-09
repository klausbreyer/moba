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

//require_once(dirname(__FILE__).'/interface.php');

add_action( 'admin_menu', 'moba_plugin_menu' );

function moba_plugin_menu() {
    add_menu_page('Mobile Batch Upload', 'Mobile Batch Upload', 'manage_options', 'moba/interface.php');
}


?>