<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

add_action( 'wp_ajax_moba_async_upload', 'moba_async_upload' );
function moba_async_upload() {

	$attachment_id = media_handle_sideload( $_FILES['file'], mysqli_real_escape_string( $_POST['post_id'] ) );

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

add_action( 'wp_ajax_moba_async_create_post', 'moba_async_create_post' );
function moba_async_create_post() {
	$post_data = [
		'post_title'   => mysqli_real_escape_string( $_POST['title'] ),
		'post_content' => mysqli_real_escape_string( $_POST['content'] ),
		'post_status'  => mysqli_real_escape_string( $_POST['post_status'] ),
		'post_type'    => 'post',
	];
	if ( $post_id = wp_insert_post( $post_data ) ) {
		wp_send_json_success( [ 'post_id' => $post_id ] );
		wp_die();
	} else {
		wp_send_json_error( [ 'error' => 'wp_insert_post' ] );
		wp_die();
	}

}

add_action( 'wp_ajax_moba_async_finalize_post', 'moba_async_finalize_post' );
function moba_async_finalize_post() {

	$attachment_content = '';
	foreach ( $_POST['attachment_urls'] as $attachment ) {
		$attachment_content .= sprintf( '<br /><img src="%s" />', mysqli_real_escape_string( $attachment ) );
	}

	$post_data = [
		'ID'           => mysqli_real_escape_string( $_POST['post_id'] ),
		'post_title'   => mysqli_real_escape_string( $_POST['title'] ),
		'post_status'  => mysqli_real_escape_string( $_POST['post_status'] ),
		'post_content' => sprintf( '%s%s', mysqli_real_escape_string( $_POST['content'] ), $attachment_content ),
	];


	if ( ! $post_id = wp_insert_post( $post_data ) ) {
		wp_send_json_error( [ 'error' => 'wp_insert_post' ] );
		wp_die();
	}


	if ( ! set_post_thumbnail( $post_id, mysqli_real_escape_string( $_POST['attachment_ids'][0] ) ) ) {

		wp_send_json_error( [ 'error' => 'set_post_thumbnail' ] );
		wp_die();
	}

	wp_send_json_success( [ 'post_id' => $post_id ] );
	wp_die();

}