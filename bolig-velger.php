<?php
/*
 * Plugin Name: Bolig Velger
 * Plugin URI: https://blogg.hiof.no/b20it22/
 * Description: Dette er en interaktiv boligvelger av bachelorgruppe 22
 * Author: Gruppe 22
 * Version: 1.0.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: boligvelger
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/class-boligvelger.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'BoligVelger', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BoligVelger', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'BoligVelger', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-boligvelger-admin.php' );
	add_action( 'plugins_loaded', array( 'BoligVelger_Admin', 'get_instance' ) );

}