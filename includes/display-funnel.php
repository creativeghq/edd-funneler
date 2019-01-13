<?php


class EDD_Funnels_Display_Funnel 
{

	static $doing;
	static $session;
	static $is_ajax;

	static function init() {

		self::$doing = esc_attr( eddfunnels_set( $_GET, 'doing_funnel' ) );

		self::$session = EDD_Funnels_Loader::get_session_data();

		self::$is_ajax = esc_attr( eddfunnels_set( $_POST, 'ajax' ) );
	}

	/**
	 * Run funnel
	 * 
	 * @param  array  $step  An array of data about current step.
	 * @param  integer $index Current step.
	 * @return [type]         [description]
	 */
	static function run($step, $index = 0) {

		self::init();

		$type = eddfunnels_set( $step, 'type');
		$id = eddfunnels_set( $step, 'object_id' );
		//exit($type);
		if ( $type == 'modal' ) {
			self::modal($step, $index);
		} else if ( $type == 'bump' ) {
			self::bump($step, $index);
		} else if ( $type == 'upsells' ) {
			self::pages($step, $index);
		} else {
			self::pages($step, $index);
		}

	}

	/**
	 * [modal description]
	 *
	 * @param  array  $step  An array of data about current step.
	 * @param  integer $index Current step.
	 * @return [type]        [description]
	 */
	static protected function modal($step, $index) {
		exit('modal');
	}

	/**
	 * [bump description]
	 * 
	 * @param  array  $step  An array of data about current step.
	 * @param  integer $index Current step.
	 * @return [type]        [description]
	 */
	static protected function bump($step, $index) {
		$checkout_page = edd_get_option( 'purchase_page', false );

		if ( $checkout_page && get_page( $checkout_page ) ) {

			EDD_Funnels_Loader::up_step();

			$url = add_query_arg('doing_funnel', true, get_permalink($checkout_page));

			wp_redirect( esc_url($url) );exit;
		}
		exit('dump');
	}

	/**
	 * [pages description]
	 *
	 * @param  array  $step  An array of data about current step.
	 * @param  integer $index Current step.
	 * @return [type]        [description]
	 */
	static protected function pages($step, $index) {
		exit('pages');
	}

	/**
	 * [content_filter description]
	 * 
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	static function content_filter($content) {
		self::init();

		if ( self::$doing ) {
			$buttons = '<div class="clearfix"></div><div class="edd-funnels-steps-buttons">';
			$buttons = '<a href="javascript:void(0);" class="edd-funnels-btn-next">' . esc_html__( 'Next', 'edd-funnels' ) . '</a>';
			$buttons .= '<a href="javascript:void(0);" class="edd-funnels-btn-skip">' . esc_html__( 'Skip', 'edd-funnels' ) . '</a>';
			$buttons .= '</div>';

			$content .= $buttons;
		}

		return $content;
	}

	/**
	 * [checkout_bump description]
	 * 
	 * @return [type] [description]
	 */
	static function checkout_bump() {

		self::init();

		if ( ! self::$doing ) {
			return;
		}
		if ( ! eddfunnels_set( self::$session, 'id' ) ) {
			return;
		}
		if ( eddfunnels_set( self::$session, 'finished' ) ) {
			return;
		}
		$meta = eddfunnels_set( self::$session, 'meta' );
		$step = array_shift( $meta );
		$obj_id = eddfunnels_set( $step, 'object_id' );
		$download = get_post( $obj_id );

		if ( $obj_id && ! is_wp_error( $download ) ) {
			require EDDFS_PATH . 'templates/bump.php';
		}
	}
}

add_filter('the_content', array('EDD_Funnels_Display_Funnel', 'content_filter'));

add_action('edd_purchase_form_before_cc_form', array('EDD_Funnels_Display_Funnel', 'checkout_bump') );
