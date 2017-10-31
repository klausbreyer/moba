<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


add_action( 'wp_ajax_moba_async_create_post', 'moba_async_create_post' );
function moba_async_create_post() {

	check_ajax_referer( 'moba-upload' );

	//sanitize
	$post_title          = (string) sanitize_text_field( $_POST['title'] );
	$post_status         = (string) sanitize_text_field( $_POST['status'] );
	$post_content        = (string) sanitize_textarea_field( $_POST['content'] );

	//validate
	if ( strlen( $post_title ) < 1 ) {
		wp_send_json_error( [ 'error' => 'post_title_corrupt' ] );
		wp_die();
	}

	if ( strlen( $post_content ) < 1 ) {
		wp_send_json_error( [ 'error' => 'post_content_corrupt' ] );
		wp_die();
	}

	if ( $post_status !== 'draft' && $post_status !== 'publish' ) {
		wp_send_json_error( [ 'error' => 'post_status_corrupt' ] );
		wp_die();
	}


	//process
	$post_data = [
		'post_title'   => $post_title,
		'post_content' => $post_content,
		'post_status'  => $post_status,
		'post_type'    => 'post',
	];

	//save
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

	//sanitize
	$file_name = sanitize_file_name( $_FILES['file']['name'] );
	$file_size = (int) $_FILES['file']['size'];
	$file_type = (string) $_FILES['file']['type'];
	$tmp_name  = tempnam( sys_get_temp_dir(), 'moba' );
	$post_id   = (int) $_POST['post_id'];
	$image     = imagecreatefromstring( file_get_contents( $_FILES['file']['tmp_name'] ) );
	$exif      = exif_read_data( $_FILES['file']['tmp_name'] );


	//validate
	if ( $post_id < 1 ) {
		wp_send_json_error( [ 'error' => 'post_id_corrupt' ] );
		wp_die();
	}

	if ( $file_type !== 'image/png' && $file_type !== 'image/jpeg' ) {
		wp_send_json_error( [ 'error' => 'mime_type_corrupt' ] );
		wp_die();
	}


	//process
	if ( ! empty( $exif['Orientation'] ) ) {
		switch ( $exif['Orientation'] ) {
			case 8:
				$image = imagerotate( $image, 90, 0 );
				break;
			case 3:
				$image = imagerotate( $image, 180, 0 );
				break;
			case 6:
				$image = imagerotate( $image, - 90, 0 );
				break;
		}
	}
	imagejpeg( $image, $tmp_name );

	$file_array = [
		'name'     => $file_name,
		'type'     => $file_type,
		'tmp_name' => $tmp_name,
		'error'    => 0,
		'size'     => $file_size,
	];

	//save
	$attachment_id = media_handle_sideload( $file_array, $post_id );

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

	//sanitize
	$post_id             = (int) $_POST['post_id'];
	$post_title          = (string) sanitize_text_field( $_POST['title'] );
	$post_status         = (string) sanitize_text_field( $_POST['status'] );
	$post_content        = (string) sanitize_textarea_field( $_POST['content'] );
	$first_attachment_id = (int) end($_POST['attachment_ids']);


	//Validate
	if ( $post_id < 1 ) {
		wp_send_json_error( [ 'error' => 'post_id_corrupt' ] );
		wp_die();
	}

	if ( strlen( $post_title ) < 1 ) {
		wp_send_json_error( [ 'error' => 'post_title_corrupt' ] );
		wp_die();
	}

	if ( strlen( $post_content ) < 1 ) {
		wp_send_json_error( [ 'error' => 'post_content_corrupt' ] );
		wp_die();
	}

	if ( $post_status !== 'draft' && $post_status !== 'publish' ) {
		wp_send_json_error( [ 'error' => 'post_status_corrupt' ] );
		wp_die();
	}

	if ( $first_attachment_id < 1 ) {
		wp_send_json_error( [ 'error' => 'post_attachment_id_corrupt' ] );
		wp_die();
	}

	foreach ( $_POST['attachment_urls'] as $attachment ) {
		if ( filter_var( $attachment, FILTER_VALIDATE_URL ) === false ) {
			wp_send_json_error( [ 'error' => 'post_attachment_urls_corrupt' ] );
			wp_die();
		}
	}

	$attachment_content = '';
	foreach ( $_POST['attachment_urls'] as $attachment ) {
		$attachment_content .= sprintf( '<br /><br /><img src="%s" />', esc_url_raw( $attachment ) );
	}

	//process
	$post_data = [
		'ID'           => $post_id,
		'post_title'   => $post_title,
		'post_status'  => $post_status,
		'post_content' => sprintf( '%s%s', $post_content, $attachment_content ),
	];

	//save
	if ( ! $post_id = wp_insert_post( $post_data ) ) {
		wp_send_json_error( [ 'error' => 'wp_insert_post' ] );
		wp_die();
	}


	if ( ! set_post_thumbnail( $post_id, $first_attachment_id ) ) {

		wp_send_json_error( [ 'error' => 'set_post_thumbnail' ] );
		wp_die();
	}

	wp_send_json_success( [ 'post_id' => $post_id ] );
	wp_die();

}