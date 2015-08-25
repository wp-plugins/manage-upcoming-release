<?php

function unknown_release_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function unknown_release_add_meta_box() {
	add_meta_box(
		'unknown_release-unknown-release',
		__( 'Unknown Release', 'unknown_release' ),
		'unknown_release_html',
		'release',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'unknown_release_add_meta_box' );

function unknown_release_html( $post) {
	wp_nonce_field( '_unknown_release_nonce', 'unknown_release_nonce' ); ?>

	<p>

		<input type="checkbox" name="unknown_release_tba_to_be_announced_" id="unknown_release_tba_to_be_announced_" value="tba-to-be-announced" <?php echo ( unknown_release_get_meta( 'unknown_release_tba_to_be_announced_' ) === 'tba-to-be-announced' ) ? 'checked' : ''; ?>>
		<label for="unknown_release_tba_to_be_announced_"><?php _e( 'TBA (To be announced)', 'unknown_release' ); ?></label>	</p><?php
}

function unknown_release_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['unknown_release_nonce'] ) || ! wp_verify_nonce( $_POST['unknown_release_nonce'], '_unknown_release_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['unknown_release_tba_to_be_announced_'] ) )
		update_post_meta( $post_id, 'unknown_release_tba_to_be_announced_', esc_attr( $_POST['unknown_release_tba_to_be_announced_'] ) );
	else
		update_post_meta( $post_id, 'unknown_release_tba_to_be_announced_', null );
}
add_action( 'save_post', 'unknown_release_save' );

/*
	Usage: unknown_release_get_meta( 'unknown_release_tba_to_be_announced_' )
*/
