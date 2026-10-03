<?php
/**
 * Plugin Name:       Dharma Jyothi Vedika — Core
 * Plugin URI:        https://dharmajyothivedika.com/
 * Description:       Core WordPress plugin for Dharma Jyothi Vedika platform.
 *                    Registers custom post types, REST API endpoints, and
 *                    provides the central content management infrastructure.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            DJV Engineering Team
 * Author URI:        https://dharmajyothivedika.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       djv-core
 * Domain Path:       /languages
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─── Constants ─────────────────────────────────────────────── */
define( 'DJV_VERSION',          '1.0.0' );
define( 'DJV_PLUGIN_VERSION',   '1.0.0' );
define( 'DJV_PLUGIN_DIR',       plugin_dir_path( __FILE__ ) );
define( 'DJV_PLUGIN_URL',       plugin_dir_url( __FILE__ ) );
define( 'DJV_PLUGIN_BASENAME',  plugin_basename( __FILE__ ) );
define( 'DJV_API_NAMESPACE',    'djv/v1' );
define( 'DJV_ENGINE_VERSION',   '1.0.0' );
define( 'DJV_RULE_VERSION',     'telugu-1.0' );
define( 'DJV_CRON_HOOK',        'djv_nightly_precalculate' );

/* ─── Autoload / Include ─────────────────────────────────────── */
require_once DJV_PLUGIN_DIR . 'includes/class-djv-post-types.php';
require_once DJV_PLUGIN_DIR . 'includes/class-djv-rest-api.php';
require_once DJV_PLUGIN_DIR . 'includes/class-djv-panchangam.php';
require_once DJV_PLUGIN_DIR . 'includes/class-djv-admin.php';

/* ─── Init ───────────────────────────────────────────────────── */
function djv_load(): void {
	DJV_REST_API::init();
	if ( is_admin() ) {
		new DJV_Admin();
	}
	add_action( DJV_CRON_HOOK, 'djv_run_nightly_precalculate' );
}
add_action( 'plugins_loaded', 'djv_load' );

function djv_init_textdomain(): void {
	load_plugin_textdomain( 'djv-core', false, dirname( DJV_PLUGIN_BASENAME ) . '/languages' );
}
add_action( 'init', 'djv_init_textdomain', 5 );

add_action( 'init', [ 'DJV_Post_Types', 'register' ] );

/* ─── Cron ───────────────────────────────────────────────────── */
function djv_add_cron_schedules( $schedules ) {
	if ( ! is_array( $schedules ) ) {
		$schedules = [];
	}
	$schedules['djv_nightly'] = [
		'interval' => DAY_IN_SECONDS,
		'display'  => __( 'DJV Nightly (Once per day)', 'djv-core' ),
	];
	return $schedules;
}
add_filter( 'cron_schedules', 'djv_add_cron_schedules' );

function djv_run_nightly_precalculate(): void {
	$lat      = floatval( get_option( 'djv_default_lat', 17.3850 ) );
	$lon      = floatval( get_option( 'djv_default_lon', 78.4867 ) );
	$timezone = sanitize_text_field( get_option( 'djv_default_tz', 'Asia/Kolkata' ) );
	$panchangam = new DJV_Panchangam();
	for ( $i = 0; $i < 7; $i++ ) {
		$date = date( 'Y-m-d', strtotime( "+{$i} days" ) );
		$panchangam->get_panchangam( $date, $lat, $lon, $timezone );
	}
}

/* ─── Activation ─────────────────────────────────────────────── */
function djv_activate(): void {
	DJV_Post_Types::register();
	flush_rewrite_rules();
	if ( ! wp_next_scheduled( DJV_CRON_HOOK ) ) {
		wp_schedule_event( time(), 'djv_nightly', DJV_CRON_HOOK );
	}
	add_option( 'djv_default_city',  'Hyderabad' );
	add_option( 'djv_default_lat',   17.3850 );
	add_option( 'djv_default_lon',   78.4867 );
	add_option( 'djv_default_tz',    'Asia/Kolkata' );
	add_option( 'djv_ayanamsa',      'lahiri' );
	add_option( 'djv_month_system',  'amanta' );
	add_option( 'djv_cache_enabled', 1 );
}
register_activation_hook( __FILE__, 'djv_activate' );

/* ─── Deactivation ───────────────────────────────────────────── */
function djv_deactivate(): void {
	$ts = wp_next_scheduled( DJV_CRON_HOOK );
	if ( $ts ) { wp_unschedule_event( $ts, DJV_CRON_HOOK ); }
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'djv_deactivate' );
