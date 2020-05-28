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
if (!defined('WPINC')) {
	die;
}

if (!class_exists('CMB2')) {
	if (file_exists(__DIR__ . '/public/includes/lib/cmb2/init.php')) {
		require_once  __DIR__ . '/public/includes/lib/cmb2/init.php';
	} elseif (file_exists(__DIR__ . '/public/includes/lib/CMB2/init.php')) {
		require_once  __DIR__ . '/public/includes/lib/CMB2/init.php';
	}
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once(plugin_dir_path(__FILE__) . 'public/class-boligvelger.php');

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, array('BoligVelger', 'activate'));
register_deactivation_hook(__FILE__, array('BoligVelger', 'deactivate'));

add_action('plugins_loaded', array('BoligVelger', 'get_instance'));

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {

	require_once(plugin_dir_path(__FILE__) . 'admin/class-boligvelger-admin.php');
	add_action('plugins_loaded', array('BoligVelger_Admin', 'get_instance'));
}

function na_remove_slug($post_link, $post, $leavename)
{

	if ('bv_image' != $post->post_type) {
		return $post_link;
	}

	$post_link = str_replace('/' . $post->post_type . '/', '/', $post_link);

	return $post_link;
}
add_filter('post_type_link', 'na_remove_slug', 10, 3);

function na_parse_request($query)
{

	if (!$query->is_main_query() || 2 != count($query->query) || !isset($query->query['page'])) {
		return;
	}

	if (!empty($query->query['name'])) {
		$query->set('post_type', array('post', 'bv_image', 'page'));
	}
}
add_action('pre_get_posts', 'na_parse_request');



// Leilighet metabox
add_action('cmb2_init', 'cmb2_leilighet_metabox');
//Opprettet ny metode for custom fields til leiligheten
function cmb2_leilighet_metabox()
{

	$cmb = new_cmb2_box(array(
		'id'           => 'cmb2_leilighet_metabox',
		'title'        => 'Informasjon',
		'object_types' => array('leilighet'),
	));

	// Nummer
	$cmb->add_field(array(
		'name' => 'Leilighet (nummer)',
		'id'   => '_cmb2_leilighet_nr',
		'type' => 'text',
		'desc' => 'Dette er leiligheten sitt nummer (Eksempel: A101)',
	));

	// Etasje
	$cmb->add_field(array(
		'name' => 'Etasje',
		'id'   => '_cmb2_leilighet_etasje',
		'desc' => 'Dette er en etasje',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	));

	// Bruttoareal
	$cmb->add_field(array(
		'name' => 'Bruttoareal',
		'id'   => '_cmb2_leilighet_bruttoareal',
		'desc' => 'Dette er bruttoarealet til leiligheten',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	));

	// Antall soverom
	$cmb->add_field(array(
		'name' => 'Antall soverom',
		'id'   => '_cmb2_leilighet_sove_antall',
		'desc' => 'Beskrivelse av antall soverom i leilighet',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	));

	// Antall rom
	$cmb->add_field(array(
		'name' => 'Antall rom',
		'id'   => '_cmb2_leilighet_antall',
		'desc' => 'Beskrivelse av antall rom i leilighet',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	));

	// Pris
	$cmb->add_field(array(
		'name' => 'Pris',
		'id'   => '_cmb2_leilighet_pris',
		'desc' => 'Pris på leiligheten',
		'type' => 'text',
		'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
		'escape_cb'       => 'absint',
	));

	// Status
	$cmb->add_field(array(
		'name'             => 'Status',
		'desc'             => 'Oversikt om leiligheten er solgt eller ledig',
		'id'               => '_cmb2_leilighet_status',
		'type'             => 'select',
		'default'          => 'custom',
		'options'          => array(
			'Ledig' => __('Ledig'),
			'Opptatt'   => __('Opptatt'),
		),
	));

	$cmb->add_field( array(
		'name'             => 'Solforhold',
		'desc'             => 'Solforhold til boligen',
		'id'               => '_cmb2_leilighet_solforhold',
		'type'             => 'select',
		'default'          => 'custom',
		'options'          => array(
			'ost' => __( 'Øst' ),
			'vest'   => __( 'Vest' ),
			'nord'     => __( 'Nord' ),
			'sor'     => __( 'Sør' ),
		),
	) );




	add_action('cmb2_admin_init', 'cmb2_leilighet_metabox');
}
