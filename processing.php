
<?php
//print_r($_FILES);
//if(isset($_POST['file'])) {
//	wp_send_json_success($_POST);
//}
$messages = [];
if ( isset( $_POST['title'] ) && isset( $_POST['content'] ) ) {
	$post_data = [
		'post_title'   => $_POST['title'],
		'post_content' => $_POST['content'],
		'post_status'   => $_POST['post_status'],
		'post_type'    => 'moba_create_post_and_init_upload',
	];
	if ( $post_id = wp_insert_post( $post_data ) ) {
		$messages[] = 'Post created';
	}

	$attachment_urls    = [];
	$attachment_ids     = [];
	$attachment_content = "";

	if ( isset( $_FILES['moba_upload_files']['name'][0] ) && strlen( $_FILES['moba_upload_files']['name'][0] ) > 1 ) {
		for ( $i = 0; $i < count( $_FILES['moba_upload_files']['name'] ); $i ++ ) {
			$file_array = [
				'name'     => $_FILES['moba_upload_files']['name'][ $i ],
				'type'     => $_FILES['moba_upload_files']['type'][ $i ],
				'tmp_name' => $_FILES['moba_upload_files']['tmp_name'][ $i ],
				'error'    => $_FILES['moba_upload_files']['error'][ $i ],
				'size'     => $_FILES['moba_upload_files']['size'][ $i ],
			];
			$desc       = sprintf( 'mobile batch uploaded on %s', date( 'd.m.Y - H:i' ) );

			// do the validation and storage stuff
			$attachment_id = media_handle_sideload( $file_array, $post_id, $desc );

			// If error storing permanently, unlink
			if ( is_wp_error( $attachment_id ) ) {
				@unlink( $file_array['tmp_name'] );

				return $attachment_id;
			}

			$attachment_url     = wp_get_attachment_url( $attachment_id );
			$attachment_ids[]   = $attachment_id;
			$attachment_urls[]  = $attachment_url;
			$attachment_content .= sprintf( '<br /><img src="%s" />', $attachment_url );
			$messages[]         = sprintf( 'Uploaded %s', $file_array['name'] );
		}
	}


	$post_data = [
		'ID'           => $post_id,
		'post_title'   => $_POST['title'],
		'post_status'   => $_POST['post_status'],
		'post_content' => sprintf( '%s%s', $_POST['content'], $attachment_content ),
	];


	if ( $post_id = wp_insert_post( $post_data ) ) {
		$messages[] = 'Post saved';
	}

	if ( isset( $attachment_ids[0] ) ) {
		if ( set_post_thumbnail( $post_id, $attachment_ids[0] ) ) {
			$messages[] = 'Thumbnail saved';
		}
	}
}


?>