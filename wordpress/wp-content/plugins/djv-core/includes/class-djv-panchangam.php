<?php
/**
 * ============================================================
 * DJV Panchangam Bridge
 * File: wordpress/plugins/djv-core/includes/class-djv-panchangam.php
 *
 * PHP class that bridges the WordPress REST API to the
 * JavaScript Panchangam Engine.
 *
 * Strategy: The JS engine is pre-run via WP-Cron (nightly)
 * to cache all Panchangam data as CPT entries.
 * Real-time requests are served from cache.
 * Cache miss → synchronous Node.js subprocess call (fallback).
 *
 * Engine Location: packages/panchangam-engine/src/core.js
 * Runner Script:   packages/panchangam-engine/src/runner.js
 * ============================================================
 *
 * @package DJV_Core
 * @since   1.0.0
 * @license GPL-2.0+
 */

defined('ABSPATH') || exit;

class DJV_Panchangam {

    /**
     * Path to the Node.js runner script.
     * @var string
     */
    private $runner_path;

    /**
     * Node.js executable path. Configurable via wp-config.php.
     * @var string
     */
    private $node_bin;

    /**
     * Maximum execution timeout for Node subprocess (seconds).
     * @var int
     */
    private $subprocess_timeout = 10;

    /**
     * Cache TTL — 24 hours. Panchangam changes daily.
     * @var int
     */
    private $cache_ttl = DAY_IN_SECONDS;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->runner_path = trailingslashit( DJV_PLUGIN_DIR ) . '../../../packages/panchangam-engine/src/runner.js';
        $this->node_bin    = defined('DJV_NODE_PATH') ? DJV_NODE_PATH : 'node';
    }

    /**
     * Get Panchangam for a specific date and location.
     *
     * Resolution order:
     * 1. WordPress transient (short-lived in-memory cache)
     * 2. djv_panchangam CPT (long-lived database cache)
     * 3. Node.js subprocess (live calculation — stored back to cache)
     *
     * @param string $date     ISO 8601 date (YYYY-MM-DD)
     * @param float  $latitude  Latitude decimal degrees
     * @param float  $longitude Longitude decimal degrees
     * @param string $timezone  IANA Timezone identifier (e.g. 'Asia/Kolkata')
     * @return array|WP_Error Panchangam data or WP_Error on failure
     */
    public function get_panchangam( $date, $latitude, $longitude, $timezone ) {

        // Validate inputs
        $validated = $this->validate_inputs( $date, $latitude, $longitude, $timezone );
        if ( is_wp_error( $validated ) ) {
            return $validated;
        }

        list( $date, $latitude, $longitude, $timezone ) = $validated;

        // 1. Transient cache (fast in-memory)
        $cache_key = $this->get_cache_key( $date, $latitude, $longitude );
        $cached    = get_transient( $cache_key );
        if ( false !== $cached ) {
            return array_merge( $cached, [ '_cache_hit' => 'transient' ] );
        }

        // 2. CPT database cache
        $from_cpt = $this->get_from_cpt_cache( $date, $latitude, $longitude );
        if ( $from_cpt ) {
            // Refresh transient from CPT data
            set_transient( $cache_key, $from_cpt, HOUR_IN_SECONDS );
            return array_merge( $from_cpt, [ '_cache_hit' => 'cpt' ] );
        }

        // 3. Live calculation via external Microservice API (Hostinger safe)
        $result = $this->calculate_via_api( $date, $latitude, $longitude, $timezone );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Store result in both caches
        $this->store_in_cpt_cache( $date, $latitude, $longitude, $result );
        set_transient( $cache_key, $result, HOUR_IN_SECONDS );

        return array_merge( $result, [ '_cache_hit' => 'live' ] );
    }

    /**
     * Get Panchangam for today.
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $timezone
     * @return array|WP_Error
     */
    public function get_today( $latitude, $longitude, $timezone ) {
        // Use timezone-aware "today" — not server UTC today
        $now     = new DateTimeImmutable( 'now', new DateTimeZone( $timezone ) );
        $date    = $now->format( 'Y-m-d' );
        return $this->get_panchangam( $date, $latitude, $longitude, $timezone );
    }

    /**
     * Pre-calculate and cache an entire month of Panchangam data.
     * Called by WP-Cron nightly job.
     *
     * @param int   $year
     * @param int   $month
     * @param float $latitude
     * @param float $longitude
     * @param string $timezone
     * @return array Results summary
     */
    public function precalculate_month( $year, $month, $latitude, $longitude, $timezone ) {
        $success = 0;
        $failed  = 0;
        $days    = cal_days_in_month( CAL_GREGORIAN, $month, $year );

        for ( $day = 1; $day <= $days; $day++ ) {
            $date   = sprintf( '%04d-%02d-%02d', $year, $month, $day );
            $result = $this->get_panchangam( $date, $latitude, $longitude, $timezone );

            if ( is_wp_error( $result ) ) {
                $failed++;
                error_log( "[DJV] Precalculation failed for {$date}: " . $result->get_error_message() );
            } else {
                $success++;
            }

            // Small sleep to avoid overwhelming the server
            usleep( 50000 ); // 50ms
        }

        return [
            'year'    => $year,
            'month'   => $month,
            'success' => $success,
            'failed'  => $failed,
            'total'   => $days,
        ];
    }

    /**
     * Call the external Panchangam Node.js microservice API.
     * Hostinger disables shell_exec(), so we must use a remote API.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @return array|WP_Error
     */
    private function calculate_via_api( $date, $latitude, $longitude, $timezone ) {
        $api_url = get_option( 'djv_engine_api_url', '' );

        if ( empty( $api_url ) ) {
            return new WP_Error(
                'djv_engine_unconfigured',
                __( 'Panchangam engine API URL is not configured. Please set the API URL in DJV Core settings or deploy the Node.js engine as a microservice.', 'djv-core' ),
                [ 'status' => 503 ]
            );
        }

        $url = add_query_arg( [
            'date'      => $date,
            'latitude'  => $latitude,
            'longitude' => $longitude,
            'timezone'  => $timezone,
        ], $api_url );

        $response = wp_remote_get( $url, [ 'timeout' => 15 ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'djv_engine_timeout',
                __( 'Panchangam engine API failed to respond.', 'djv-core' ),
                [ 'status' => 504 ]
            );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
            return new WP_Error(
                'djv_engine_invalid_output',
                __( 'Panchangam engine API returned invalid JSON output.', 'djv-core' ),
                [ 'status' => 500, 'raw_output' => substr( $body, 0, 500 ) ]
            );
        }

        // Check for engine-level error
        if ( ! empty( $data['error'] ) ) {
            return new WP_Error(
                'djv_engine_error',
                esc_html( $data['error'] ),
                [ 'status' => 500 ]
            );
        }

        return $data;
    }

    /**
     * Look up cached Panchangam from the djv_panchangam CPT.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @return array|false
     */
    private function get_from_cpt_cache( $date, $latitude, $longitude ) {
        $posts = get_posts([
            'post_type'      => 'djv_panchangam',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [ 'key' => '_djv_panchangam_date',    'value' => $date ],
                [ 'key' => '_djv_panchangam_lat',     'value' => round( $latitude,  4 ) ],
                [ 'key' => '_djv_panchangam_lon',     'value' => round( $longitude, 4 ) ],
            ],
        ]);

        if ( empty( $posts ) ) return false;

        $raw = get_post_meta( $posts[0]->ID, '_djv_panchangam_data', true );
        if ( empty( $raw ) ) return false;

        $data = json_decode( $raw, true );
        return is_array( $data ) ? $data : false;
    }

    /**
     * Store Panchangam result in the djv_panchangam CPT.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param array  $data
     */
    private function store_in_cpt_cache( $date, $latitude, $longitude, $data ) {
        $post_title = sprintf( 'Panchangam %s @ %.4f,%.4f', $date, $latitude, $longitude );

        $post_id = wp_insert_post([
            'post_type'   => 'djv_panchangam',
            'post_title'  => $post_title,
            'post_name'   => sanitize_title( $post_title ),
            'post_status' => 'publish',
        ]);

        if ( is_wp_error( $post_id ) || 0 === $post_id ) return;

        update_post_meta( $post_id, '_djv_panchangam_date', $date );
        update_post_meta( $post_id, '_djv_panchangam_lat',  round( $latitude,  4 ) );
        update_post_meta( $post_id, '_djv_panchangam_lon',  round( $longitude, 4 ) );
        update_post_meta( $post_id, '_djv_panchangam_data', wp_json_encode( $data ) );
    }

    /**
     * Generate a unique cache key for transients.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @return string
     */
    private function get_cache_key( $date, $latitude, $longitude ) {
        return 'djv_pc_' . md5( $date . '|' . round( $latitude, 4 ) . '|' . round( $longitude, 4 ) );
    }

    /**
     * Validate and sanitize all inputs.
     *
     * @return array|WP_Error
     */
    private function validate_inputs( $date, $latitude, $longitude, $timezone ) {
        // Date
        $dt = DateTimeImmutable::createFromFormat( 'Y-m-d', $date );
        if ( ! $dt || $dt->format( 'Y-m-d' ) !== $date ) {
            return new WP_Error( 'djv_invalid_date', 'Invalid date format. Use YYYY-MM-DD.', [ 'status' => 400 ] );
        }

        // Range: 1900–2100
        $year = (int) $dt->format('Y');
        if ( $year < 1900 || $year > 2100 ) {
            return new WP_Error( 'djv_date_out_of_range', 'Date must be between 1900 and 2100.', [ 'status' => 400 ] );
        }

        // Latitude
        $lat = floatval( $latitude );
        if ( $lat < -90 || $lat > 90 ) {
            return new WP_Error( 'djv_invalid_lat', 'Latitude must be between -90 and 90.', [ 'status' => 400 ] );
        }

        // Longitude
        $lon = floatval( $longitude );
        if ( $lon < -180 || $lon > 180 ) {
            return new WP_Error( 'djv_invalid_lon', 'Longitude must be between -180 and 180.', [ 'status' => 400 ] );
        }

        // Timezone
        $tz = sanitize_text_field( $timezone );
        if ( ! in_array( $tz, timezone_identifiers_list(), true ) ) {
            return new WP_Error( 'djv_invalid_tz', 'Invalid timezone identifier. Use IANA timezone (e.g. Asia/Kolkata).', [ 'status' => 400 ] );
        }

        return [ $date, $lat, $lon, $tz ];
    }

    /**
     * Check if the node binary is available.
     *
     * @return bool
     */
    // node_is_available removed as we are using HTTP API
}
