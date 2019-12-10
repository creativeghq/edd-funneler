<?php 
/**
 * Plugin Name: Edd Funneler
 * Plugin URI:
 * Description: EDD Funneler
 * Author: Rajiv Shakya
 * Version: 1.0.1
 * Text Domain:
 */

global $edd_custom_funneler_db_version;
add_option("edd_custom_funneler", "1.0.0"); // new vesrion with mlocation_town table updated
register_activation_hook(__FILE__, 'edd_custom_funneler_installation');

function edd_custom_funneler_installation()
{
    global $wpdb;
    global $edd_custom_funneler_db_version;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name = $wpdb->prefix . "edd_custom_funneler";
    $sqle       = "DROP TABLE IF EXISTS $table_name;";
    $sql        = "CREATE TABLE $table_name (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `funneler_used` varchar(255) DEFAULT NULL,
        `funneler_used_from_product` int(11) DEFAULT NULL,
        `funneler_used_products` varchar(255) DEFAULT NULL,
        `created` datetime DEFAULT NULL,
        `modified` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
            );";
    dbDelta($sqle);
    dbDelta($sql);

    add_option("edd_custom_funneler_db_version", $edd_custom_funneler_db_version);
    return true;
}


defined('CEDDF_PATH') || define('CEDDF_PATH', plugin_dir_path( __FILE__ ) );
defined('CEDDF_URL') || define('CEDDF_URL', plugin_dir_url( __FILE__ ) );




require_once CEDDF_PATH . 'classes/class.metabox.php';
require_once CEDDF_PATH. 'classes/class.funneler.php';

