<?php


class EDD_Funnels_Display_Funnel 
{

	static $doing;
	static $session;
	static $is_ajax;
	static $edd_ajax;

	static function init() {

		self::$doing = esc_attr( eddfunnels_set( $_GET, 'doing_funnel' ) );

		self::$session = EDD_Funnels_Loader::get_session_data();

		self::$is_ajax = esc_attr( eddfunnels_set( $_POST, 'ajax' ) );
		self::$edd_ajax = esc_attr( eddfunnels_set( $_POST, 'edd_ajax' ) );
		
	}

	static function enqueue() {
		self::init();
		
		$checkout_page = edd_get_option( 'purchase_page', false );
		wp_enqueue_script( 'edd-funnels-frontend', EDDFS_URL . 'assets/js/edd-funnels-frontend.js', array('jquery'), 1.0, true );

		if ( self::$doing || is_page( $checkout_page ) ) {
			$data = array(
				'ajaxurl'		=> admin_url('admin-ajax.php'),
				'nonce'			=> wp_create_nonce( NONCE_KEY ),
				'checkout_url'	=> get_permalink( edd_get_option( 'purchase_page', false ) )
			);
			wp_localize_script( 'edd-funnels-frontend', 'edd_funnels_data', $data );

			if ( ! wp_script_is( 'bootstrap', 'registered' ) ) {
				wp_enqueue_script( 'bootstrap', EDDFS_URL . 'assets/js/bootstrap.min.js', array('jquery'), '', true );
			}

			if ( esc_attr( eddfunnels_set( $_GET, 'show_modal') ) ) {
			}

		}
		wp_enqueue_style( 'edd-funnels-frontend', EDDFS_URL . 'assets/css/edd-funnels-frontend.css' );
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
			return self::modal($step, $index);
		} else if ( $type == 'bump' ) {
			return self::bump($step, $index);
		} else if ( $type == 'upsells' ) {
			return self::upsells($step, $index);
		} else {

			return self::pages($step, $index);
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
		if ( true ) {
			if ( self::$is_ajax || self::$edd_ajax ) {

				$obj_id = eddfunnels_set( $step, 'object_id' );
				
				if ( $obj_id ) {
					$content = apply_filters( 'the_content', $obj_id );
					EDD_Funnels_Loader::up_step();

					wp_send_json( array(
						'type'		=> 'show_modal',
						'content'	=> $content
					) );
				}
			}
		}

	}

	/**
	 * [bump description]
	 * 
	 * @param  array  $step  An array of data about current step.
	 * @param  integer $index Current step.
	 * @return [type]        [description]
	 */
	static protected function bump($step, $index) {

		if ( $index == 0 ) {
			
			EDD_Funnels_Loader::up_step();
			$session = EDD_Funnels_Loader::get_session_data();
			$step = eddfunnels_set( eddfunnels_set( $session, 'meta'), 1 );
			self::run($step, 1);
		} else {
			$obj_id = eddfunnels_set( $step, 'object_id' );
			
			if ( $obj_id ) {
				ob_start();

				self::render_bump($step);

				$content = ob_get_clean();

				if ( $content ) {
					EDD_Funnels_Loader::up_step();
					wp_send_json( array(
						'type'		=> 'show_modal',
						'content'	=> $content
					) );
				}
			}
		}
		exit;
	}

	/**
	 * [upsells description]
	 *
	 * @param  [type] $step  [description]
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	static protected function upsells($step, $index) {

		if ( eddfunnels_set( $step, 'object_id' ) ) {
			ob_start();

			include EDDFS_PATH . 'templates/upsells.php';

			$content = ob_get_clean();

			if ( $content ) {
				EDD_Funnels_Loader::up_step();
				wp_send_json( array(
					'type'		=> 'show_modal',
					'content'	=> $content
				) );
			}
		}
	}
	/**
	 * [pages description]
	 *
	 * @param  array  $step  An array of data about current step.
	 * @param  integer $index Current step.
	 * @return [type]        [description]
	 */
	static protected function pages($step, $index) {

		$obj_id = eddfunnels_set( $step, 'object_id' );
		$page = get_post( $obj_id );

		if ( $page ) {
			$url = add_query_arg('doing_funnel', true, get_permalink($page->ID));
			EDD_Funnels_Loader::up_step();
			if ( self::$is_ajax || self::$edd_ajax ) {
				wp_send_json( array('type' => 'redirect', 'next_url' => $url ) );
			} else {
				wp_redirect( esc_url( $url ) );exit;
			}
		}

		if ( EDD_Funnels_Loader::is_last_step($index) ) {
			EDD_Funnels_Loader::finish_funnel();
			if( self::$is_ajax || self::$edd_ajax ) {
				$checkout_page = edd_get_option( 'purchase_page', false );
				if ( $checkout_page ) {
					wp_send_json( array('type' => 'redirect', 'next_url' => get_permalink($checkout_page) ) );
				}
			}
		}
		exit('page');
	}

	/**
	 * [content_filter description]
	 * 
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	static function content_filter($content) {
		self::init();

		$checkout_page = edd_get_option( 'purchase_page', false );

		if ( self::$doing && ! is_page( $checkout_page ) ) {

			$session = EDD_Funnels_Loader::get_session_data();
			$index = eddfunnels_set( $session, 'step' );

			if ( self::is_next_bump($index) ) {
				$bump_data = self::get_next($index);

				ob_start();

				self::render_bump($bump_data);

				$content .= ob_get_clean();
				EDD_Funnels_Loader::up_step();
			}

		}

		return $content;
	}

	/**
	 * Check whether next step is bump item so we append bump with the current step content.
	 *
	 * @param  [type]  $index [description]
	 * @return boolean        [description]
	 */
	static function is_next_bump($index) {

		$next = self::get_next($index);

		if ( $next ) {
			$type = eddfunnels_set( $next, 'type' );

			return ( $type === 'bump' );
		}

		return false;
	}

	/**
	 * GEt the data for next step
	 * 
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	static function get_next($index) {
		$session = EDD_Funnels_Loader::get_session_data();
		$meta = eddfunnels_set( $session, 'meta' );

		$next_index = $index + 1;

		$next = eddfunnels_set( $meta, $next_index );

		return $next;		
	}
	/**
	 * [checkout_bump description]
	 * 
	 * @return [type] [description]
	 */
	static function checkout_bump() {

		self::init();

		/*if ( ! self::$doing ) {
			return;
		}*/
		if ( ! eddfunnels_set( self::$session, 'id' ) ) {
			return;
		}
		if ( eddfunnels_set( self::$session, 'finished' ) ) {
			return;
		}

		$meta = eddfunnels_set( self::$session, 'meta' );
		//$index = eddfunnels_set( self::$session, 'step' );
		$step = eddfunnels_set( $meta, 0 );
		
		if ( $step && eddfunnels_set( $step, 'type') === 'bump' ) {
			//EDD_Funnels_Loader::up_step();
			self::render_bump($step);
		}
	}

	/**
	 * [render_bump description]
	 *
	 * @param  [type] $step [description]
	 * @return [type]       [description]
	 */
	static function render_bump($step) {
		$obj_id = eddfunnels_set( $step, 'object_id' );
		$download = get_post( $obj_id );

		if ( $obj_id && ! is_wp_error( $download ) ) {
			require EDDFS_PATH . 'templates/bump.php';
		}
	}

	/**
	 * If nothing happened then simply redirect back to checkout.
	 *
	 * @return [type] [description]
	 */
	static function redirect_checkout() {
		$checkout_page = edd_get_option( 'purchase_page', false );

		if ( $checkout_page && get_page( $checkout_page ) && ! is_page( $checkout_page ) ) {
			wp_redirect( esc_url(get_permalink( $checkout_page )) );exit;
		}
	}

	/**
	 * [modal_content description]
	 *
	 * @return [type] [description]
	 */
	static function modal_content() {
		include EDDFS_PATH . 'templates/funnel-modal.php';
	}

	/**
	 * [head_content description]
	 *
	 * @return [type] [description]
	 */
	static function head_content() {
		$session = EDD_Funnels_Loader::get_session_data();

		if ( eddfunnels_set( $session, 'id' ) && ! eddfunnels_set( $session, 'finished') ) {
			echo '<meta name="edd-funnels-session-enabled" content="true" />'."\n";
		}
	}

	/**
	 * EDD Funnels button shortcode.
	 *
	 * @param  [type] $atts    [description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	static function shortcode_button( $atts, $content = null ) {

		extract(shortcode_atts( array(
			'classes'		=> ''
		), $atts));

		$output = '';

		self::init();

		if ( self::$doing ) {

			$session = EDD_Funnels_Loader::get_session_data();
			$index = eddfunnels_set( $session, 'step' );

			$output .= '<div class="clearfix"></div><div class="edd-funnels-steps-buttons">';
			$output .= '<a href="javascript:void(0);" class="edd-funnels-btn-next btn btn-primary '.$classes.'">' . $content . '</a><span class="edd-loading-ajax edd-loading hide"></span>';

			$output .= '</div>';

		}

		return $output;
	}
}

add_filter('the_content', array('EDD_Funnels_Display_Funnel', 'content_filter'));

add_action('edd_purchase_form_before_cc_form', array('EDD_Funnels_Display_Funnel', 'checkout_bump') );

add_action( 'wp_enqueue_scripts', array( 'EDD_Funnels_Display_Funnel', 'enqueue' ) );

add_action('wp_footer', array('EDD_Funnels_Display_Funnel', 'modal_content'));
add_action('wp_head', array('EDD_Funnels_Display_Funnel', 'head_content'));

add_shortcode('edd_funnels_button', array('EDD_Funnels_Display_Funnel', 'shortcode_button'));
