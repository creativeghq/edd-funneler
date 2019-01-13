<?php

class EDD_Funnels_Loader 
{

	static $id = 0;
	static $session_key = 'edd_funnels_run';

	/**
	 * Main initatiation for Funnel.
	 * 
	 * @return [type] [description]
	 */
	static function init() {

		$is_ajax = eddfunnels_set( $_POST, 'edd_ajax' );

		if ( $is_ajax ) {
			return;
		}

		$cart = edd_get_cart_contents();

		$download = end( $cart );
		
		$download_id = eddfunnels_set( $download, 'id' );

		if ( ! $download_id ) {
			exit('not donwload id');
			return;
		}

		self::$id = $download_id;

		if ( self::already_done() ) {
			exit('already done');
			return;
		}

		if ( ! self::is_has_funnel() ) {
			exit('not has funnel');
			return;
		}

		do_action('edd_funnels/before_funnel_start', self::$id, self::get_session_data() );
		self::do_funnel();
		do_action('edd_funnels/after_funnel_start', self::$id, self::get_session_data() );

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
				if ( ! isset( $_SESSION[self::$session_key] ) ) {
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

		return eddfunnels_set( $meta, 'status', false );
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
				EDD_Funnels_Display_Funnel::run($step);
			}

		} else {
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

			if ( count( $meta ) ) {

				$step = array_shift($meta);

				if ( eddfunnels_set( $step, 'object_id' ) ) {
					EDD_Funnels_Display_Funnel::run($step);
				}
			}
		}
	}
}


add_action('edd_pre_process_purchase', array('EDD_Funnels_Loader', 'init'));
