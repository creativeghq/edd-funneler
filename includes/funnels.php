<?php

class EDD_Funnels_Loader 
{

	static $id = 0;
	static $session_key = 'edd_funnels_run';

	static function newinit() {

		if ( ! function_exists('EDD') ) {
			return;
		}
		$checkout_page = edd_get_option( 'purchase_page', false );

		if ( ! $checkout_page ) {
			return;
		}
		if ( ! is_page( $checkout_page ) ) {
			return;
		}

		if ( ! self::initial_checks() ) {
			return;
		}
		
		$session_data = self::get_session_data();

		if ( ! eddfunnels_set($session_data, 'id') ) {
			self::initiate_session();
		}
	}
	/**
	 * Main initatiation for Funnel.
	 * 
	 * @return [type] [description]
	 */
	static function init() {

		$is_ajax = eddfunnels_set( $_POST, 'edd_ajax' );

		if( ! self::initial_checks() ) {
			return;
		}

		
		if ( $is_ajax ) {

			/*$is_modal = self::is_modal();
			if ( $is_modal ) {
				exit;
			}
			return;*/
		}

		do_action('edd_funnels/before_funnel_start', self::$id, self::get_session_data() );
		self::do_funnel();
		do_action('edd_funnels/after_funnel_start', self::$id, self::get_session_data() );

	}

	/**
	 * [initial_checks description]
	 * @return [type] [description]
	 */
	static function initial_checks() {
		$cart = edd_get_cart_contents();

		$download = end( $cart );
		
		$download_id = eddfunnels_set( $download, 'id' );

		if ( ! $download_id ) {
			return;
		}

		self::$id = $download_id;

		if ( self::already_done() ) {
			return;
		}

		if ( ! self::is_has_funnel() ) {
			return;
		}

		return true;
	}

	/**
	 * Check whether is session.
	 *
	 * @return boolean [description]
	 */
	static function is_modal() {

		$session_data = self::get_session_data();

		if ( ! $session_data ) {
			self::initiate_session();
			$session_data = self::get_session_data();
		}

		if ( self::already_done() ) {
			return false;
		}

		if ( ! self::is_has_funnel() ) {
			return false;
		}

		$index = eddfunnels_set( $session_data, 'step' );
		$meta = eddfunnels_set( $session_data, 'meta' );
		$step = isset( $meta[$index] ) ? $meta[$index] : false;

		if ( $step ) {
			return (eddfunnels_set( $step, 'type') == 'modal');
		}

		return false;
	}
	/**
	 * Check whether we have done funnel or not.
	 *
	 * @return [type] [description]
	 */
	static function already_done() {

		$session = self::get_session_data();

		$done = eddfunnels_set( $session, 'finished' );

		return $done;
	}

	/**
	 * Get the session key.
	 *
	 * @return [type] [description]
	 */
	static function get_session_key() {

		if ( function_exists('session_start') ) {

			if ( session_id() ) {
				if ( ! eddfunnels_set( $_SESSION, self::$session_key ) ) {
					$key = base64_encode( uniqid() );
					$_SESSION[self::$session_key] = $key;
				} else {
					$key = $_SESSION[self::$session_key];
				}

				return $key;
			}
		}

		return false;
	}

	/**
	 * Get the funnel data from session.
	 *
	 * @return [type] [description]
	 */
	static function get_session_data() {

		if ( $key = self::get_session_key() ) {

			if ( isset( $_SESSION[$key] ) ) {
				return ( $_SESSION[ $key ] ) ? json_decode( $_SESSION[ $key ], true ) : array();
			}
		}

		return false;
	}

	/**
	 * Set the provided session data.
	 * 
	 * @param array $data [description]
	 */
	static function set_session_data( $data = array() ) {

		if ( function_exists('session_start') && session_id() ) {
			$key = self::get_session_key();
			$_SESSION[ $key ] = wp_json_encode( $data );

			return true;
		}

		return false;
	}

	/**
	 * Increase the step with number 1
	 * 
	 * @return [type] [description]
	 */
	static function up_step() {
		$session_data = self::get_session_data();

		if ( $session_data ) {
			$session_data['step'] = (int) eddfunnels_set( $session_data, 'step' ) + 1;

			self::set_session_data($session_data);
		}
	}

	/**
	 * Check whether this id has funnel
	 * @return boolean [description]
	 */
	static function is_has_funnel() {
		$meta = get_post_meta(self::$id, '_edd_funnels_data', true);

		$status = eddfunnels_set( $meta, 'status', false );

		if ( ! $status ) {
			return false;
		}

		array_shift($meta);

		if ( count( $meta ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the current Step.
	 *
	 * @return [type] [description]
	 */
	static function current_step() {
		$session = self::get_session_data();

		$meta = eddfunnels_set( $session, 'meta' );

		$index = eddfunnels_set( $session, 'step' );

		$step = eddfunnels_set( $meta, $index );

		return compact('step', 'index');
	}

	/**
	 * Get the current Step.
	 *
	 * @return [type] [description]
	 */
	static function prev_step() {
		$session = self::get_session_data();

		$meta = eddfunnels_set( $session, 'meta' );

		$index = eddfunnels_set( $session, 'step' );
		$index = $index - 1;
		
		$step = eddfunnels_set( $meta, $index );

		return compact('step', 'index');
	}

	static function initiate_session() {
		$meta = get_post_meta(self::$id, '_edd_funnels_data', true);

		unset($meta['status']);
		$meta = (array) $meta;

		$data = array(
			'id'			=> self::$id,
			'meta'			=> $meta,
			'step'			=> 0,
			'finished'		=> count($meta) ? false : true,
			'total_steps'	=> count( $meta )
		);

		self::set_session_data( $data );

		return $meta;
	}

	/**
	 * Start the funnel.
	 * 
	 * @return [type] [description]
	 */
	static function do_funnel() {

		$session_data = self::get_session_data();

		if ( eddfunnels_set( $session_data, 'id' ) ) {

			$meta = eddfunnels_set( $session_data, 'meta' );
			$index = eddfunnels_set( $session_data, 'step' );
			$step = eddfunnels_set( $meta, $index );

			if ( eddfunnels_set( $step, 'object_id' ) ) {
				//printr($step);
				EDD_Funnels_Display_Funnel::run($step);
			} else if ( $index && self::is_last_step($index) ) {
				//self::finish_funnel();
			}

		} else {
			$meta = self::initiate_session();

			if ( count( $meta ) ) {

				$step = array_shift($meta);

				if ( eddfunnels_set( $step, 'object_id' ) ) {
					EDD_Funnels_Display_Funnel::run($step);
				}
			}
		}
	}

	/**
	 * [finish_funnel description]
	 * @return [type] [description]
	 */
	static function finish_funnel() {

		$checkout_page = edd_get_option( 'purchase_page', false );

		if ( $checkout_page && get_page( $checkout_page ) && ! is_page( $checkout_page ) ) {

			EDD_Funnels_Loader::up_step();

			$url = add_query_arg('doing_funnel', true, get_permalink($checkout_page));

			$session = self::get_session_data();
			$session['finished'] = true;

			self::set_session_data( $session );
			$is_ajax = esc_attr( eddfunnels_set( $_POST, 'ajax' ) );
			$edd_ajax = esc_attr( eddfunnels_set( $_POST, 'edd_ajax' ) );
			if ( !$is_ajax && !$edd_ajax ) {
				wp_redirect( esc_url($url) );exit;
			}
		}
	}

	/**
	 * check whether the given index of step is last.
	 *
	 * @param  integer  $index [description]
	 * @return boolean        [description]
	 */
	static function is_last_step($index) {

		$session = self::get_session_data();
		$meta = eddfunnels_set( $session, 'meta' );

		return ( $index >= (count($meta) - 1) );
	}

	/**
	 * [init_funnel description]
	 * @return [type] [description]
	 */
	static function init_funnel() {

	}

	/**
	 * Empty our session on EDD cart empty.
	 * 
	 * @return [type] [description]
	 */
	static function on_empty_cart() {

		if( $enc_key = eddfunnels_set( $_SESSION, self::$session_key ) ) {
			if ( isset( $_SESSION[$enc_key] ) ) {
				unset($_SESSION[$enc_key]);
			}
		}
	}
}


add_action('edd_pre_process_purchase', array('EDD_Funnels_Loader', 'init'));
add_action('edd_empty_cart', array('EDD_Funnels_Loader', 'on_empty_cart' ) );
//add_action('edd_checkout_form_top', array('EDD_Funnels_Loader', 'init_funnel') );

add_action('wp_enqueue_scripts', array('EDD_Funnels_Loader', 'newinit'));