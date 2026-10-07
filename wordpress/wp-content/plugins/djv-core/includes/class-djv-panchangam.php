<?php
/**
 * ============================================================
 * DJV Panchangam Bridge
 * File: wordpress/plugins/djv-core/includes/class-djv-panchangam.php
 *
 * PHP class that bridges the WordPress REST API to the
 * JavaScript Panchangam Engine.
 *
 * Strategy:
 * 1. WordPress transient (short-lived in-memory cache)
 * 2. djv_panchangam CPT (long-lived database cache)
 * 3. Node.js subprocess (live local astronomical calculation)
 * 4. Remote Node.js microservice API fallback (shared hosting safe)
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

if ( ! function_exists( 'djv_normalize_error_message' ) ) {
    /**
     * Safely normalize error objects, arrays, or strings to a clean string message.
     * Prevents literal "Array" or PHP conversion errors.
     *
     * @param mixed $error
     * @return string
     */
    function djv_normalize_error_message( $error ): string {
        if ( is_wp_error( $error ) ) {
            return $error->get_error_message();
        }

        if ( is_array( $error ) ) {
            if ( isset( $error['message'] ) ) {
                return is_array( $error['message'] ) ? wp_json_encode( $error['message'] ) : (string) $error['message'];
            }

            return wp_json_encode( $error );
        }

        if ( is_object( $error ) ) {
            if ( isset( $error->message ) ) {
                return is_array( $error->message ) ? wp_json_encode( $error->message ) : (string) $error->message;
            }
            return wp_json_encode( $error );
        }

        return (string) $error;
    }
}

class DJV_Panchangam {

    /**
     * Panchangam engine version identifier.
     */
    const ENGINE_VERSION = '1.2.0';

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
        $this->runner_path = $this->resolve_runner_path();
        $this->node_bin    = $this->resolve_node_binary();
    }

    /**
     * Locate the runner.js script across various server filesystem structures.
     *
     * @return string|false
     */
    private function resolve_runner_path() {
        $candidates = [
            trailingslashit( DJV_PLUGIN_DIR ) . '../../../packages/panchangam-engine/src/runner.js',
            trailingslashit( ABSPATH ) . '../packages/panchangam-engine/src/runner.js',
            trailingslashit( dirname( ABSPATH ) ) . 'packages/panchangam-engine/src/runner.js',
            trailingslashit( dirname( dirname( ABSPATH ) ) ) . 'packages/panchangam-engine/src/runner.js',
            'E:/Ai/DHARMA JYOTHI VEDIKA/packages/panchangam-engine/src/runner.js',
            'e:/Ai/DHARMA JYOTHI VEDIKA/packages/panchangam-engine/src/runner.js',
        ];

        foreach ( $candidates as $path ) {
            if ( file_exists( $path ) ) {
                return realpath( $path ) ?: $path;
            }
        }

        return false;
    }

    /**
     * Locate the node executable.
     *
     * @return string
     */
    private function resolve_node_binary(): string {
        if ( defined( 'DJV_NODE_PATH' ) && ! empty( DJV_NODE_PATH ) ) {
            return DJV_NODE_PATH;
        }

        $candidates = [
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
            '/usr/bin/node',
            '/usr/local/bin/node',
            'node',
        ];

        foreach ( $candidates as $bin ) {
            if ( $bin === 'node' || file_exists( $bin ) ) {
                return $bin;
            }
        }

        return 'node';
    }

    /**
     * Get Panchangam for a specific date and location.
     *
     * Resolution order:
     * 1. WordPress transient (short-lived in-memory cache)
     * 2. djv_panchangam CPT (long-lived database cache)
     * 3. Node.js subprocess (live calculation — stored back to cache)
     * 4. External microservice API fallback
     *
     * @param string $date      ISO 8601 date (YYYY-MM-DD)
     * @param float  $latitude  Latitude decimal degrees
     * @param float  $longitude Longitude decimal degrees
     * @param string $timezone  IANA Timezone identifier (e.g. 'Asia/Kolkata')
     * @param string $region    Region identifier (e.g. 'telugu')
     * @param string $language  Language code (e.g. 'en' or 'te')
     * @return array|WP_Error Panchangam data or WP_Error on failure
     */
    public function get_panchangam( $date, $latitude, $longitude, $timezone, $region = 'telugu', $language = 'en' ) {

        // Validate inputs
        $validated = $this->validate_inputs( $date, $latitude, $longitude, $timezone );
        if ( is_wp_error( $validated ) ) {
            return $validated;
        }

        list( $date, $latitude, $longitude, $timezone ) = $validated;

        // 1. Transient cache (fast in-memory)
        $cache_key = $this->get_cache_key( $date, $latitude, $longitude, $timezone, $region, $language );
        $cached    = get_transient( $cache_key );
        if ( false !== $cached && is_array( $cached ) ) {
            return array_merge( $cached, [ '_cache_hit' => 'transient' ] );
        }

        // 2. CPT database cache (version-aware)
        $from_cpt = $this->get_from_cpt_cache( $date, $latitude, $longitude, $timezone );
        if ( $from_cpt ) {
            // Refresh transient from CPT data
            set_transient( $cache_key, $from_cpt, HOUR_IN_SECONDS );
            return array_merge( $from_cpt, [ '_cache_hit' => 'cpt' ] );
        }

        // 3. Try live calculation via Node.js subprocess
        $result = $this->calculate_via_subprocess( $date, $latitude, $longitude, $timezone );

        // 4. If subprocess fails or is unavailable, fallback to HTTP microservice API
        if ( is_wp_error( $result ) ) {
            $api_result = $this->calculate_via_api( $date, $latitude, $longitude, $timezone, $region, $language );
            if ( ! is_wp_error( $api_result ) ) {
                $result = $api_result;
            } else {
                // If both fail, return the error
                return $result;
            }
        }

        // Store result in both caches
        $this->store_in_cpt_cache( $date, $latitude, $longitude, $result, $timezone );
        set_transient( $cache_key, $result, HOUR_IN_SECONDS );

        return array_merge( $result, [ '_cache_hit' => 'live' ] );
    }

    /**
     * Get Panchangam for today.
     *
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @param string $region
     * @param string $language
     * @return array|WP_Error
     */
    public function get_today( $latitude, $longitude, $timezone, $region = 'telugu', $language = 'en' ) {
        // Use timezone-aware "today" — not server UTC today
        $now  = new DateTimeImmutable( 'now', new DateTimeZone( $timezone ) );
        $date = $now->format( 'Y-m-d' );
        return $this->get_panchangam( $date, $latitude, $longitude, $timezone, $region, $language );
    }

    /**
     * Calculate Panchangam locally via Node.js subprocess using proc_open.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @return array|WP_Error
     */
    private function calculate_via_subprocess( $date, $latitude, $longitude, $timezone ) {
        if ( ! function_exists( 'proc_open' ) ) {
            return new WP_Error( 'proc_open_disabled', 'proc_open is disabled on this server.' );
        }

        if ( empty( $this->runner_path ) || ! file_exists( $this->runner_path ) ) {
            return new WP_Error( 'runner_not_found', 'Panchangam runner script not found.' );
        }

        $cmd_str = sprintf(
            '"%s" "%s" %s %s %s %s',
            $this->node_bin,
            $this->runner_path,
            escapeshellarg( $date ),
            escapeshellarg( (string) $latitude ),
            escapeshellarg( (string) $longitude ),
            escapeshellarg( $timezone )
        );

        $descriptors = [
            0 => [ 'pipe', 'r' ], // stdin
            1 => [ 'pipe', 'w' ], // stdout
            2 => [ 'pipe', 'w' ], // stderr
        ];

        $process = proc_open( $cmd_str, $descriptors, $pipes, dirname( $this->runner_path ) );
        if ( ! is_resource( $process ) ) {
            return new WP_Error( 'proc_open_failed', 'Failed to launch Node.js subprocess.' );
        }

        fclose( $pipes[0] );
        $stdout = stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        $stderr = stream_get_contents( $pipes[2] );
        fclose( $pipes[2] );
        $return_value = proc_close( $process );

        if ( 0 !== $return_value || empty( $stdout ) ) {
            error_log( "[DJV] Node subprocess error (code {$return_value}): " . substr( $stderr, 0, 500 ) );
            return new WP_Error( 'node_execution_error', 'Subprocess returned error: ' . $stderr );
        }

        $data = json_decode( $stdout, true );
        if ( ! is_array( $data ) || ! empty( $data['error'] ) ) {
            $msg = is_array( $data ) && ! empty( $data['error'] ) ? djv_normalize_error_message( $data['error'] ) : 'Invalid JSON from Node engine.';
            return new WP_Error( 'engine_error', $msg );
        }

        return $data;
    }

    /**
     * Call the external Panchangam Node.js microservice API.
     * Hostinger disables shell_exec(), so we must use a remote API fallback.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @param string $region
     * @param string $language
     * @return array|WP_Error
     */
    private function calculate_via_api( $date, $latitude, $longitude, $timezone, $region = 'telugu', $language = 'en' ) {
        $api_url = get_option( 'djv_engine_api_url', '' );
        if ( empty( $api_url ) && defined( 'DJV_ENGINE_API_URL' ) ) {
            $api_url = DJV_ENGINE_API_URL;
        }
        if ( empty( $api_url ) ) {
            $api_url = 'https://dharma-jyothi-vedika.vercel.app';
        }

        $base_url = preg_replace( '#/panchangam/?$#', '', trim( $api_url ) );
        $endpoint = rtrim( $base_url, '/' ) . '/panchangam';

        $url = add_query_arg( [
            'date'      => $date,
            'latitude'  => $latitude,
            'longitude' => $longitude,
            'timezone'  => $timezone,
            'region'    => $region,
            'language'  => $language,
        ], $endpoint );

        $response = wp_remote_get( $url, [ 'timeout' => 15 ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'djv_engine_unreachable',
                __( 'Panchangam engine could not be reached.', 'djv-core' ),
                [
                    'status'  => 504,
                    'details' => [ 'http_status' => 0 ],
                ]
            );
        }

        $http_code = (int) wp_remote_retrieve_response_code( $response );
        $body      = wp_remote_retrieve_body( $response );

        if ( $http_code === 0 ) {
            return new WP_Error(
                'djv_engine_unreachable',
                __( 'Panchangam engine could not be reached.', 'djv-core' ),
                [
                    'status'  => 504,
                    'details' => [ 'http_status' => 0 ],
                ]
            );
        }

        $data = json_decode( $body, true );

        if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
            error_log( '[DJV] Invalid JSON from Panchangam engine: ' . substr( $body, 0, 200 ) );
            return new WP_Error(
                'djv_engine_invalid_output',
                __( 'Panchangam engine API returned invalid JSON output.', 'djv-core' ),
                [ 'status' => 500 ]
            );
        }

        // Check for engine-level error
        if ( ! empty( $data['error'] ) ) {
            $err_code = ( is_array( $data['error'] ) && ! empty( $data['error']['code'] ) )
                ? sanitize_key( $data['error']['code'] )
                : 'djv_engine_error';

            $err_msg = djv_normalize_error_message( $data['error'] );

            return new WP_Error(
                $err_code,
                esc_html( $err_msg ),
                [ 'status' => ( $http_code >= 400 ? $http_code : 500 ) ]
            );
        }

        if ( $http_code >= 400 ) {
            return new WP_Error(
                'djv_engine_error',
                sprintf( __( 'Panchangam engine returned HTTP %d.', 'djv-core' ), $http_code ),
                [ 'status' => $http_code ]
            );
        }

        // Unwrap standard { success: true, data: { ... }, meta: { ... } } response
        if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
            $unwrapped = $data['data'];
            if ( isset( $data['meta'] ) && is_array( $data['meta'] ) ) {
                $unwrapped['meta'] = $data['meta'];
            }
            return $unwrapped;
        }

        return $data;
    }

    /**
     * Look up cached Panchangam from the djv_panchangam CPT.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @return array|false
     */
    private function get_from_cpt_cache( $date, $latitude, $longitude, $timezone = 'Asia/Kolkata' ) {
        $posts = get_posts([
            'post_type'      => 'djv_panchangam',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [ 'key' => '_djv_panchangam_date',    'value' => $date ],
                [ 'key' => '_djv_panchangam_lat',     'value' => round( (float)$latitude,  4 ) ],
                [ 'key' => '_djv_panchangam_lon',     'value' => round( (float)$longitude, 4 ) ],
                [ 'key' => '_djv_panchangam_version', 'value' => self::ENGINE_VERSION ],
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
     * @param string $timezone
     */
    private function store_in_cpt_cache( $date, $latitude, $longitude, $data, $timezone = 'Asia/Kolkata' ) {
        $post_title = sprintf( 'Panchangam %s @ %.4f,%.4f', $date, $latitude, $longitude );

        $post_id = wp_insert_post([
            'post_type'   => 'djv_panchangam',
            'post_title'  => $post_title,
            'post_name'   => sanitize_title( $post_title ),
            'post_status' => 'publish',
        ]);

        if ( is_wp_error( $post_id ) || 0 === $post_id ) return;

        update_post_meta( $post_id, '_djv_panchangam_date',    $date );
        update_post_meta( $post_id, '_djv_panchangam_lat',     round( (float)$latitude,  4 ) );
        update_post_meta( $post_id, '_djv_panchangam_lon',     round( (float)$longitude, 4 ) );
        update_post_meta( $post_id, '_djv_panchangam_tz',      $timezone );
        update_post_meta( $post_id, '_djv_panchangam_version', self::ENGINE_VERSION );
        update_post_meta( $post_id, '_djv_panchangam_data',    wp_json_encode( $data ) );
    }

    /**
     * Generate a unique cache key for transients.
     * Accounts for date, lat, lon, timezone, region, language, and engine version.
     *
     * @param string $date
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @param string $region
     * @param string $language
     * @return string
     */
    public function get_cache_key( $date, $latitude, $longitude, $timezone = 'Asia/Kolkata', $region = 'telugu', $language = 'en' ): string {
        return 'djv_pc_' . md5(
            $date . '|' .
            round( (float) $latitude, 4 ) . '|' .
            round( (float) $longitude, 4 ) . '|' .
            $timezone . '|' .
            $region . '|' .
            $language . '|' .
            self::ENGINE_VERSION
        );
    }

    /**
     * Pre-calculate and cache next N days of Panchangam data.
     *
     * @param float  $latitude
     * @param float  $longitude
     * @param string $timezone
     * @param int    $days
     * @return array
     */
    public function precalculate_next_30_days( $latitude, $longitude, $timezone, $days = 30 ) {
        $success = 0;
        $failed  = 0;

        for ( $i = 0; $i < $days; $i++ ) {
            $date   = date( 'Y-m-d', strtotime( "+{$i} days" ) );
            $result = $this->get_panchangam( $date, $latitude, $longitude, $timezone );

            if ( is_wp_error( $result ) ) {
                $failed++;
                error_log( "[DJV] Precalculation failed for {$date}: " . $result->get_error_message() );
            } else {
                $success++;
            }

            usleep( 20000 ); // 20ms
        }

        return [
            'success' => $success,
            'failed'  => $failed,
            'total'   => $days,
        ];
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
}
