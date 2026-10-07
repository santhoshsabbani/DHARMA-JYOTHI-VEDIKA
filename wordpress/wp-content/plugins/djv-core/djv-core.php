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

// Disable WordPress Admin Bar on the Frontend for all users (including Administrators)
add_filter( 'show_admin_bar', function( $show ) {
	return is_admin() ? $show : false;
}, 999 );

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
	if ( empty( get_option('djv_engine_api_url') ) ) {
		error_log('[DJV] Nightly cron skipped: Panchangam engine API URL is not configured.');
		return;
	}
	$lat      = floatval( get_option( 'djv_default_lat', 17.3850 ) );
	$lon      = floatval( get_option( 'djv_default_lon', 78.4867 ) );
	$timezone = sanitize_text_field( get_option( 'djv_default_tz', 'Asia/Kolkata' ) );
	$panchangam = new DJV_Panchangam();
	$panchangam->precalculate_next_30_days( $lat, $lon, $timezone );
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
