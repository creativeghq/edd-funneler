<?php
/**
 * Download meta box class
 */
class EDD_Funnels_Metabox {

	static function register() {
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue'));
		add_meta_box( 'edd-funnels-settings', __( 'EDD Funnels Settings', 'edd-funnels' ), array(__CLASS__, 'output'), 'download' );
	}

	static function output() {
		self::enqueue();

		include EDDFS_PATH . 'templates/metabox/funnels.php';
	}

	static function enqueue() {
		global $post_type;

		if ( $post_type !== 'download' ) {
			return;
		}
		wp_enqueue_script(array('jquery-ui-sortabel') );
		wp_enqueue_script( 'vue', 'https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.16/vue.js', array(), '2.5.16', true );
		wp_enqueue_script( 'edd-funnels-admin', EDDFS_URL . 'assets/js/edd-funnels-admin.js', array('vue'), '1.0', true );

		wp_enqueue_style( 'edd-funnels-metabox', EDDFS_URL . 'assets/css/metabox.css'  );
	}

	/**
	 * Finally save the data.
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $post    [description]
	 * @return [type]          [description]
	 */
	static function save($post_id, $post = null) {
		
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) )
			return;

		$nonce = esc_attr( eddfunnels_set( $_POST, 'edd_funnels_nonce' ) );

		if ( ! wp_verify_nonce( $nonce, 'EDD_FUNNELS' ) ) {
			return;
		}

		$funnels = eddfunnels_set( $_POST, 'edd_funnels' );

		if ( $funnels ) {
			update_post_meta( $post_id, '_edd_funnels_data', $funnels );
		}
	}
}


add_action('add_meta_boxes', array('EDD_Funnels_Metabox', 'register') );

add_action('publish_download', array('EDD_Funnels_Metabox', 'save'), 20, 2 );

