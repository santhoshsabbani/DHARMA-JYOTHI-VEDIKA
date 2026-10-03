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
     * @param float  $tz_offset Timezone offset in hours (e.g. 5.5 for IST)
     * @return array|WP_Error Panchangam data or WP_Error on failure
     */
    public function get_panchangam( $date, $latitude, $longitude, $tz_offset ) {

        // Validate inputs
        $validated = $this->validate_inputs( $date, $latitude, $longitude, $tz_offset );
        if ( is_wp_error( $validated ) ) {
            return $validated;
        }

        list( $date, $latitude, $longitude, $tz_offset ) = $validated;

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

        // 3. Live calculation via Node subprocess
        $result = $this->calculate_via_node( $date, $latitude, $longitude, $tz_offset );
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
     * @param float $tz_offset
     * @return array|WP_Error
     */
    public function get_today( $latitude, $longitude, $tz_offset ) {
        // Use timezone-aware "today" — not server UTC today
        $now     = new DateTimeImmutable( 'now', new DateTimeZone( $this->tz_offset_to_name( $tz_offset ) ) );
        $date    = $now->format( 'Y-m-d' );
        return $this->get_panchangam( $date, $latitude, $longitude, $tz_offset );
    }

    /**
     * Pre-calculate and cache an entire month of Panchangam data.
     * Called by WP-Cron nightly job.
     *
     * @param int   $year
     * @param int   $month
     * @param float $latitude
     * @param float $longitude
     * @param float $tz_offset
     * @return array Results summary
     */
    public function precalculate_month( $year, $month, $latitude, $longitude, $tz_offset ) {
        $success = 0;
        $failed  = 0;
        $days    = cal_days_in_month( CAL_GREGORIAN, $month, $year );

        for ( $day = 1; $day <= $days; $day++ ) {
            $date   = sprintf( '%04d-%02d-%02d', $year, $month, $day );
            $result = $this->get_panchangam( $date, $latitude, $longitude, $tz_offset );

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
     * Calculate Panchangam by calling the Node.js engine subprocess.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param float  $tz_offset
     * @return array|WP_Error
     */
    private function calculate_via_node( $date, $latitude, $longitude, $tz_offset ) {
        // Check if Node is available
        if ( ! $this->node_is_available() ) {
            return new WP_Error(
                'djv_node_unavailable',
                __( 'Node.js is not available on this server. Panchangam cannot be calculated live.', 'djv-core' ),
                [ 'status' => 503 ]
            );
        }

        // Check if runner script exists
        if ( ! file_exists( $this->runner_path ) ) {
            return new WP_Error(
                'djv_runner_missing',
                sprintf(
                    __( 'Panchangam runner script not found at: %s', 'djv-core' ),
                    esc_html( $this->runner_path )
                ),
                [ 'status' => 500 ]
            );
        }

        // Build safe command
        $cmd = sprintf(
            '%s %s %s %s %s %s 2>&1',
            escapeshellcmd( $this->node_bin ),
            escapeshellarg( realpath( $this->runner_path ) ),
            escapeshellarg( $date ),
            escapeshellarg( (string) $latitude ),
            escapeshellarg( (string) $longitude ),
            escapeshellarg( (string) $tz_offset )
        );

        // Execute with timeout
        $output     = [];
        $return_code = null;

        // Use proc_open for timeout control
        $result_json = $this->exec_with_timeout( $cmd, $this->subprocess_timeout );
        if ( is_wp_error( $result_json ) ) {
            return $result_json;
        }

        $data = json_decode( $result_json, true );
        if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
            return new WP_Error(
                'djv_engine_invalid_output',
                __( 'Panchangam engine returned invalid JSON output.', 'djv-core' ),
                [ 'status' => 500, 'raw_output' => substr( $result_json, 0, 500 ) ]
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
     * Execute a shell command with a timeout using proc_open.
     *
     * @param string $cmd
     * @param int    $timeout_seconds
     * @return string|WP_Error
     */
    private function exec_with_timeout( $cmd, $timeout_seconds ) {
        $desc = [
            0 => [ 'pipe', 'r' ],  // stdin
            1 => [ 'pipe', 'w' ],  // stdout
            2 => [ 'pipe', 'w' ],  // stderr
        ];

        $proc = proc_open( $cmd, $desc, $pipes );
        if ( ! is_resource( $proc ) ) {
            return new WP_Error( 'djv_proc_failed', 'Failed to open process for Panchangam calculation.' );
        }

        fclose( $pipes[0] );

        $start  = microtime( true );
        $output = '';
        $errors = '';

        stream_set_blocking( $pipes[1], false );
        stream_set_blocking( $pipes[2], false );

        while ( true ) {
            $read   = [ $pipes[1], $pipes[2] ];
            $write  = null;
            $except = null;
            $changed = stream_select( $read, $write, $except, 0, 200000 );

            if ( $changed === false ) break;

            foreach ( $read as $pipe ) {
                $chunk = fread( $pipe, 8192 );
                if ( $chunk !== false ) {
                    if ( $pipe === $pipes[1] ) $output .= $chunk;
                    else $errors .= $chunk;
                }
            }

            $status = proc_get_status( $proc );
            if ( ! $status['running'] ) break;

            if ( ( microtime( true ) - $start ) > $timeout_seconds ) {
                proc_terminate( $proc, 9 );
                fclose( $pipes[1] );
                fclose( $pipes[2] );
                proc_close( $proc );
                return new WP_Error(
                    'djv_engine_timeout',
                    sprintf( __( 'Panchangam engine timed out after %d seconds.', 'djv-core' ), $timeout_seconds ),
                    [ 'status' => 503 ]
                );
            }
        }

        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $proc );

        if ( empty( $output ) && ! empty( $errors ) ) {
            return new WP_Error( 'djv_engine_stderr', 'Engine error: ' . esc_html( substr( $errors, 0, 300 ) ), [ 'status' => 500 ] );
        }

        return trim( $output );
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
    private function validate_inputs( $date, $latitude, $longitude, $tz_offset ) {
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

        // Timezone offset
        $tz = floatval( $tz_offset );
        if ( $tz < -14 || $tz > 14 ) {
            return new WP_Error( 'djv_invalid_tz', 'Timezone offset must be between -14 and +14.', [ 'status' => 400 ] );
        }

        return [ $date, $lat, $lon, $tz ];
    }

    /**
     * Check if the node binary is available.
     *
     * @return bool
     */
    private function node_is_available() {
        $which = shell_exec( escapeshellcmd( $this->node_bin ) . ' --version 2>&1' );
        return ( ! empty( $which ) && strpos( $which, 'v' ) === 0 );
    }

    /**
     * Convert timezone offset (hours) to a timezone name.
     * Falls back to UTC.
     *
     * @param float $tz_offset
     * @return string Timezone name
     */
    private function tz_offset_to_name( $tz_offset ) {
        $tz_map = [
             5.5  => 'Asia/Kolkata',
             5.75 => 'Asia/Kathmandu',
             6.0  => 'Asia/Dhaka',
             7.0  => 'Asia/Bangkok',
             8.0  => 'Asia/Singapore',
             9.0  => 'Asia/Tokyo',
             0.0  => 'UTC',
            -5.0  => 'America/New_York',
            -8.0  => 'America/Los_Angeles',
        ];

        return $tz_map[ $tz_offset ] ?? 'UTC';
    }
}
