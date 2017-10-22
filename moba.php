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

add_action( 'admin_menu', 'moba_plugin_menu' );

function moba_plugin_menu() {
	add_menu_page( 'Mobile Batch', 'Mobile Batch', 'manage_options', 'moba/interface.php', '', 'dashicons-images-alt' );
}

add_action( 'init', 'register_moba_styles' );

function register_moba_styles() {
	wp_enqueue_style( 'moba', plugin_dir_url( __FILE__ ) . 'moba.css' );
	wp_enqueue_script( 'moba', plugin_dir_url( __FILE__ ) . 'moba.js' );
}

// Same handler function...
add_action( 'wp_ajax_moba_async_upload', 'moba_async_upload' );
function moba_async_upload() {

	$attachment_id = media_handle_sideload( $_FILES['file'], $_POST['post_id'] );

	if ( is_wp_error( $attachment_id ) ) {
		wp_send_json_error( [ 'error' => 'media_handle_sideload', 'attachment_id' => $attachment_id ] );
		wp_die();
	} else {
		wp_send_json_success( [
			'id'  => $attachment_id,
			'url' => wp_get_attachment_url( $attachment_id ),
		] );

		wp_die();
	}
}

// Same handler function...
add_action( 'wp_ajax_moba_async_create_post', 'moba_async_create_post' );
function moba_async_create_post() {
	$post_data = [
		'post_title'   => $_POST['title'],
		'post_content' => $_POST['content'],
		'post_status'  => $_POST['post_status'],
		'post_type'    => 'create_post_and_init_upload',
	];
	if ( $post_id = wp_insert_post( $post_data ) ) {
		wp_send_json_success( [ 'post_id' => $post_id ] );
		wp_die();
	} else {
		wp_send_json_error( [ 'error' => 'wp_insert_post' ] );
		wp_die();
	}

}

// Same handler function...
add_action( 'wp_ajax_moba_async_finalize_post', 'moba_async_finalize_post' );
function moba_async_finalize_post() {
	echo $_POST['attachments'];
	echo urldecode($_POST['attachments']);
	$attachments = json_decode(urldecode($_POST['attachments']));
	var_dump($attachments);
	$attachment_content = '';
	foreach ( $attachments as $attachment ) {
		$attachment_content .= sprintf( '<br /><img src="%s" />', $attachment['url'] );
	}

	$post_data = [
		'ID'           => $_POST['post_id'],
		'post_title'   => $_POST['title'],
		'post_status'  => $_POST['post_status'],
		'post_content' => sprintf( '%s%s', $_POST['content'], $attachment_content ),
	];


	if ( ! $post_id = wp_insert_post( $post_data ) ) {
		wp_send_json_error( [ 'error' => 'wp_insert_post' ] );
		wp_die();
	}


	if ( ! set_post_thumbnail( $post_id, $attachments[0]['id'] ) ) {

		wp_send_json_error( [ 'error' => 'set_post_thumbnail' ] );
		wp_die();
	}

	wp_send_json_success( [ 'post_id' => $post_id ] );
	wp_die();

}