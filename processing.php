<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


add_action( 'wp_ajax_moba_async_create_post', 'moba_async_create_post' );
function moba_async_create_post() {
	if ( ! is_string( $_POST['title'] ) ) {
		wp_send_json_error( [ 'error' => 'post_title_corrupt' ] );
		wp_die();
	}

	if ( ! is_string( $_POST['content'] ) ) {
		wp_send_json_error( [ 'error' => 'post_content_corrupt' ] );
		wp_die();
	}

	if ( ! is_string( $_POST['post_status'] ) || ( $_POST['post_status'] !== 'draft' && $_POST['post_status'] !== 'publish' ) ) {
		wp_send_json_error( [ 'error' => 'post_status_corrupt' ] );
		wp_die();
	}


	$post_data = [
		'post_title'   => sanitize_title( $_POST['title'] ),
		'post_content' => sanitize_textarea_field( $_POST['content'] ),
		'post_status'  => $_POST['post_status'],
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


add_action( 'wp_ajax_moba_async_upload', 'moba_async_upload' );
function moba_async_upload() {
	if ( (int) $_POST['post_id'] < 1 ) {
		wp_send_json_error( [ 'error' => 'post_id_corrupt' ] );
		wp_die();
	}

	$attachment_id = media_handle_sideload( $_FILES['file'], (int) $_POST['post_id'] );

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


add_action( 'wp_ajax_moba_async_finalize_post', 'moba_async_finalize_post' );
function moba_async_finalize_post() {
	if ( (int) $_POST['post_id'] < 1 ) {
		wp_send_json_error( [ 'error' => 'post_id_corrupt' ] );
		wp_die();
	}

	if ( ! is_string( $_POST['title'] ) ) {
		wp_send_json_error( [ 'error' => 'post_title_corrupt' ] );
		wp_die();
	}

	if ( ! is_string( $_POST['content'] ) ) {
		wp_send_json_error( [ 'error' => 'post_content_corrupt' ] );
		wp_die();
	}

	if ( ! is_string( $_POST['post_status'] ) || ( $_POST['post_status'] !== 'draft' && $_POST['post_status'] !== 'publish' ) ) {
		wp_send_json_error( [ 'error' => 'post_status_corrupt' ] );
		wp_die();
	}

	foreach ( $_POST['attachment_urls'] as $attachment ) {
		if ( filter_var( $attachment, FILTER_VALIDATE_URL ) === false ) {
			wp_send_json_error( [ 'error' => 'post_attachment_urls_corrupt' ] );
			wp_die();
		}
	}

	foreach ( $_POST['attachment_ids'] as $id ) {
		$id = (int) $id;
		if ( $id < 1 ) {
			wp_send_json_error( [ 'error' => 'post_attachment_ids_corrupt' ] );
			wp_die();
		}
	}

	$attachment_content = '';
	foreach ( $_POST['attachment_urls'] as $attachment ) {
		$attachment_content .= sprintf( '<br /><img src="%s" />', esc_url_raw( $attachment ) );
	}

	$post_data = [
		'ID'           => (int) $_POST['post_id'],
		'post_title'   => sanitize_title( $_POST['title'] ),
		'post_status'  => $_POST['post_status'],
		'post_content' => sprintf( '%s%s', sanitize_textarea_field( $_POST['content'] ), $attachment_content ),
	];


	if ( ! $post_id = wp_insert_post( $post_data ) ) {
		wp_send_json_error( [ 'error' => 'wp_insert_post' ] );
		wp_die();
	}


	if ( ! set_post_thumbnail( $post_id, (int) $_POST['attachment_ids'][0] ) ) {

		wp_send_json_error( [ 'error' => 'set_post_thumbnail' ] );
		wp_die();
	}

	wp_send_json_success( [ 'post_id' => $post_id ] );
	wp_die();

}