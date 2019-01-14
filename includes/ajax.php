<?php

/**
 * EDD Funnels Ajax.
 */
class EDD_Funnels_Ajax {

	/**
	 * [init description]
	 *
	 * @return [type] [description]
	 */
	static function init() {

		$data = $_POST;
		$subaction = esc_attr( eddfunnels_set( $data, 'subaction') );

		if ( method_exists(__CLASS__, $subaction) ) {
			call_user_func(array(__CLASS__, $subaction));
		}
		exit;
	}

	/**
	 * return the required data.
	 * 
	 * @return [type] [description]
	 */
	static function pages_downloads_meta() {

		if ( ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), 'EDD_FUNNELS' ) ) {
			wp_send_json( array('message' => esc_html__( 'Refresh the page and try again', 'edd-funnels' )), 403 );
		}

		$post_id = esc_attr( eddfunnels_set( $_POST, 'id') );
		$meta = (array) get_post_meta( $post_id, '_edd_funnels_data', true );
		$status = eddfunnels_set( $meta, 'status' );
		if ( isset( $meta['status'] ) ) {
			unset( $meta['status'] );
		}

		wp_send_json( array(
			'pages'		=> get_pages(array('posts_per_page' => -1)),
			'downloads'	=> get_posts(array('post_type' => 'download', 'posts_per_page' => -1)),
			'meta'		=> $meta,
			'status'	=> $status
		) );
	}

	/**
	 * Run the funnel through ajax call.
	 * @return [type] [description]
	 */
	static function running_funnel() {
		if ( ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), NONCE_KEY ) ) {
			wp_send_json( array('message' => esc_html__( 'Refresh the page and try again', 'edd-funnels' )), 403 );
		}

		$step_data = EDD_Funnels_Loader::current_step();

		$response = EDD_Funnels_Display_Funnel::run($step_data['step'], $step_data['index']);

		wp_send_json( $response );
	}

	static function add_bump_to_cart() {
		if ( ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), NONCE_KEY ) ) {
			wp_send_json( array('message' => esc_html__( 'Refresh the page and try again', 'edd-funnels' )), 403 );
		}

		wp_send_json( array('message' => 'Added to cart' ) );
	}

	static function add_bump_remove_from_cart() {
		if ( ! wp_verify_nonce( esc_attr( $_POST['nonce'] ), NONCE_KEY ) ) {
			wp_send_json( array('message' => esc_html__( 'Refresh the page and try again', 'edd-funnels' )), 403 );
		}

		wp_send_json( array('message' => 'Removed from cart' ) );
	}
}


add_action('wp_ajax_edd_funnels_ajax', array('EDD_Funnels_Ajax', 'init'));
