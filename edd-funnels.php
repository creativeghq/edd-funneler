<?php
/**
 * Plugin Name:     EDD Funnels
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          CreativeG
 * Author URI:      YOUR SITE HERE
 * Text Domain:     edd-funnels
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Edd_Funnels
 */

// Your code starts here.

defined('EDDFS_PATH') || define('EDDFS_PATH', plugin_dir_path( __FILE__ ) );
defined('EDDFS_URL') || define('EDDFS_URL', plugin_dir_url( __FILE__ ) );


/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function eddfunnels_load_textdomain() {
  load_plugin_textdomain( 'edd-funnels', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

add_action( 'init', 'eddfunnels_load_textdomain' );

/**
 * Resolve arrays and objects to check for specific key.
 *
 * @param  mix $var [description]
 * @param  string $key [description]
 * @param  string $def [description]
 * @return [type]      [description]
 */
function eddfunnels_set( $var, $key, $def = '' ) {

	if (is_array($var) && isset( $var[$key] ) ) {
		return $var[$key];
	} else if ( is_object($var) && isset($var->{$key} ) ) {
		return $var->{$key};
	}

	return $def;
}

if ( ! function_exists('printr') ) {

	function printr($var) {
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		exit;
	}
}

require_once EDDFS_PATH . 'includes/metabox.php';
require_once EDDFS_PATH . 'includes/ajax.php';
require_once EDDFS_PATH . 'includes/funnels.php';
require_once EDDFS_PATH . 'includes/display-funnel.php';