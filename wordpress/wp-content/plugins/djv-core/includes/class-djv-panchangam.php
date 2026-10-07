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
            $normalized = $this->normalize_payload( $cached, $timezone, $latitude, $longitude, $date );
            return array_merge( $normalized, [ '_cache_hit' => 'transient' ] );
        }

        // 2. CPT database cache (version-aware)
        $from_cpt = $this->get_from_cpt_cache( $date, $latitude, $longitude, $timezone );
        if ( $from_cpt ) {
            $normalized = $this->normalize_payload( $from_cpt, $timezone, $latitude, $longitude, $date );
            // Refresh transient from CPT data
            set_transient( $cache_key, $normalized, HOUR_IN_SECONDS );
            return array_merge( $normalized, [ '_cache_hit' => 'cpt' ] );
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

        // Normalize result before caching
        $normalized = $this->normalize_payload( $result, $timezone, $latitude, $longitude, $date );

        // Store result in both caches
        $this->store_in_cpt_cache( $date, $latitude, $longitude, $normalized, $timezone );
        set_transient( $cache_key, $normalized, HOUR_IN_SECONDS );

        return array_merge( $normalized, [ '_cache_hit' => 'live' ] );
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
        update_post_meta( $post_id, '_djv_panchangam_data',    wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) ) );
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

    /**
     * Normalize and defensively enrich Panchangam payload.
     * Ensures all solar, lunar, and timing fields have human-readable formatted strings,
     * so templates and REST consumers never receive empty/dash values even on remote API fallback.
     *
     * @param array  $data
     * @param string $timezone
     * @param float  $latitude
     * @param float  $longitude
     * @param string $date
     * @return array
     */
    public function normalize_payload( array $data, string $timezone, float $latitude, float $longitude, string $date ): array {
        if ( empty( $data ) ) {
            return $data;
        }

        // 1. Solar calculations & formatting
        if ( isset( $data['solar'] ) && is_array( $data['solar'] ) ) {
            if ( empty( $data['solar']['sunriseStr'] ) && ! empty( $data['solar']['sunrise'] ) ) {
                $data['solar']['sunriseStr'] = $this->format_iso_time( $data['solar']['sunrise'], $timezone );
            }
            if ( empty( $data['solar']['sunsetStr'] ) && ! empty( $data['solar']['sunset'] ) ) {
                $data['solar']['sunsetStr'] = $this->format_iso_time( $data['solar']['sunset'], $timezone );
            }
            if ( empty( $data['solar']['solarNoonStr'] ) && ! empty( $data['solar']['solarNoon'] ) ) {
                $data['solar']['solarNoonStr'] = $this->format_iso_time( $data['solar']['solarNoon'], $timezone );
            }
            if ( empty( $data['solar']['dayLengthStr'] ) && ! empty( $data['solar']['sunrise'] ) && ! empty( $data['solar']['sunset'] ) ) {
                $rise_ts = strtotime( $data['solar']['sunrise'] );
                $set_ts  = strtotime( $data['solar']['sunset'] );
                if ( $rise_ts && $set_ts && $set_ts > $rise_ts ) {
                    $diff_mins = (int) round( ( $set_ts - $rise_ts ) / 60 );
                    $data['solar']['dayLengthMinutes'] = $diff_mins;
                    $hrs  = floor( $diff_mins / 60 );
                    $mins = $diff_mins % 60;
                    $data['solar']['dayLengthStr'] = "{$hrs}h {$mins}m";
                }
            }
        }

        // 2. Timings intervals formatting & missing calculations
        if ( isset( $data['timings'] ) && is_array( $data['timings'] ) ) {
            // If Dur Muhurtham is missing or empty, calculate using astronomical formula
            if ( empty( $data['timings']['durMuhurtham'] ) && ! empty( $data['solar']['sunrise'] ) && ! empty( $data['solar']['sunset'] ) ) {
                $weekday_id = isset( $data['vara']['id'] ) ? (int) $data['vara']['id'] : (int) date( 'w', strtotime( $date ) );
                $data['timings']['durMuhurtham'] = self::calculate_dur_muhurtham( $data['solar']['sunrise'], $data['solar']['sunset'], $weekday_id, $timezone );
            }

            // If Brahma Muhurtham is missing or empty, calculate (96 to 48 min before sunrise)
            if ( empty( $data['timings']['brahmaMuhurtham'] ) && ! empty( $data['solar']['sunrise'] ) ) {
                $bm = self::calculate_brahma_muhurtham( $data['solar']['sunrise'], $timezone );
                if ( ! empty( $bm ) ) {
                    $data['timings']['brahmaMuhurtham'] = $bm;
                }
            }

            // If Varjyam or Amrit Kalam is missing, calculate based on Nakshatra
            if ( ( empty( $data['timings']['varjyam'] ) || empty( $data['timings']['amritKalam'] ) ) && ! empty( $data['nakshatra'] ) ) {
                $nak_id      = $data['nakshatra']['nakshatra']['id'] ?? ( $data['nakshatra']['id'] ?? 1 );
                $nak_start   = $data['nakshatra']['start'] ?? null;
                $nak_end     = $data['nakshatra']['end'] ?? null;
                $deg_in_nak  = $data['nakshatra']['degreeInNakshatra'] ?? null;
                $sunrise_iso = $data['solar']['sunrise'] ?? null;

                $va = self::calculate_varjyam_and_amrit_kalam( (int) $nak_id, $nak_start, $nak_end, $sunrise_iso, $deg_in_nak ? (float) $deg_in_nak : null, $timezone );
                if ( empty( $data['timings']['varjyam'] ) && ! empty( $va['varjyam'] ) ) {
                    $data['timings']['varjyam'] = $va['varjyam'];
                }
                if ( empty( $data['timings']['amritKalam'] ) && ! empty( $va['amritKalam'] ) ) {
                    $data['timings']['amritKalam'] = $va['amritKalam'];
                }
            }

            $timing_keys = [ 'rahuKalam', 'yamagandam', 'gulikaKalam', 'abhijitMuhurtham', 'brahmaMuhurtham', 'amritKalam', 'varjyam' ];
            foreach ( $timing_keys as $t_key ) {
                if ( ! empty( $data['timings'][ $t_key ] ) && is_array( $data['timings'][ $t_key ] ) ) {
                    $t = &$data['timings'][ $t_key ];
                    if ( empty( $t['startStr'] ) && ! empty( $t['start'] ) ) {
                        $t['startStr'] = $this->format_iso_time( $t['start'], $timezone );
                    }
                    if ( empty( $t['endStr'] ) && ! empty( $t['end'] ) ) {
                        $t['endStr'] = $this->format_iso_time( $t['end'], $timezone );
                    }
                    if ( empty( $t['text'] ) && ! empty( $t['startStr'] ) && ! empty( $t['endStr'] ) ) {
                        $t['text'] = $t['startStr'] . ' – ' . $t['endStr'];
                    }
                }
            }

            // Dur Muhurtham (single interval or array of intervals)
            if ( ! empty( $data['timings']['durMuhurtham'] ) ) {
                if ( isset( $data['timings']['durMuhurtham']['start'] ) ) {
                    $dm = &$data['timings']['durMuhurtham'];
                    if ( empty( $dm['startStr'] ) && ! empty( $dm['start'] ) ) {
                        $dm['startStr'] = $this->format_iso_time( $dm['start'], $timezone );
                    }
                    if ( empty( $dm['endStr'] ) && ! empty( $dm['end'] ) ) {
                        $dm['endStr'] = $this->format_iso_time( $dm['end'], $timezone );
                    }
                    if ( empty( $dm['text'] ) && ! empty( $dm['startStr'] ) && ! empty( $dm['endStr'] ) ) {
                        $dm['text'] = $dm['startStr'] . ' – ' . $dm['endStr'];
                    }
                } elseif ( is_array( $data['timings']['durMuhurtham'] ) ) {
                    foreach ( $data['timings']['durMuhurtham'] as &$dm ) {
                        if ( is_array( $dm ) ) {
                            if ( empty( $dm['startStr'] ) && ! empty( $dm['start'] ) ) {
                                $dm['startStr'] = $this->format_iso_time( $dm['start'], $timezone );
                            }
                            if ( empty( $dm['endStr'] ) && ! empty( $dm['end'] ) ) {
                                $dm['endStr'] = $this->format_iso_time( $dm['end'], $timezone );
                            }
                            if ( empty( $dm['text'] ) && ! empty( $dm['startStr'] ) && ! empty( $dm['endStr'] ) ) {
                                $dm['text'] = $dm['startStr'] . ' – ' . $dm['endStr'];
                            }
                        }
                    }
                }
            }

            // Sanitize any literal unescaped u2013 in strings across timings
            array_walk_recursive( $data['timings'], function( &$val ) {
                if ( is_string( $val ) && strpos( $val, 'u2013' ) !== false ) {
                    $val = str_replace( 'u2013', '–', $val );
                }
            } );
        }

        // 3. Moonrise and Moonset
        $needs_moon_calc = empty( $data['moonrise'] ) || ! is_array( $data['moonrise'] ) || empty( $data['moonrise']['time'] ) || $data['moonrise']['time'] === '—';
        if ( $needs_moon_calc ) {
            $parts = explode( '-', $date );
            if ( count( $parts ) === 3 ) {
                $y = (int) $parts[0];
                $m = (int) $parts[1];
                $d = (int) $parts[2];
                $moon_times = self::calculate_moon_rise_set( $y, $m, $d, $latitude, $longitude, $timezone );
                $data['moonrise'] = $moon_times['moonrise'];
                $data['moonset']  = $moon_times['moonset'];
            }
        }

        if ( isset( $data['lunar'] ) && is_array( $data['lunar'] ) ) {
            if ( empty( $data['lunar']['moonrise'] ) && ! empty( $data['moonrise'] ) ) {
                $data['lunar']['moonrise'] = $data['moonrise'];
            }
            if ( empty( $data['lunar']['moonset'] ) && ! empty( $data['moonset'] ) ) {
                $data['lunar']['moonset'] = $data['moonset'];
            }
        }

        if ( isset( $data['solar'] ) && is_array( $data['solar'] ) ) {
            if ( empty( $data['solar']['moonrise'] ) && ! empty( $data['moonrise']['time'] ) ) {
                $data['solar']['moonrise'] = $data['moonrise']['time'];
            }
            if ( empty( $data['solar']['moonset'] ) && ! empty( $data['moonset']['time'] ) ) {
                $data['solar']['moonset'] = $data['moonset']['time'];
            }
        }

        // 4. Pancha Angas span strings
        foreach ( [ 'tithi', 'nakshatra', 'yoga', 'karana' ] as $anga_key ) {
            if ( ! empty( $data[ $anga_key ] ) && is_array( $data[ $anga_key ] ) ) {
                $item = &$data[ $anga_key ];
                $end_time = $item['end'] ?? ( $item['endTime'] ?? null );
                if ( $end_time && empty( $item['endStr'] ) ) {
                    $item['endStr'] = $this->format_iso_time( $end_time, $timezone );
                }
                if ( empty( $item['spanStr'] ) && ! empty( $item['endStr'] ) ) {
                    $item['spanStr'] = 'Up to ' . $item['endStr'];
                }
            }
        }

        return $data;
    }

    /**
     * Format ISO 8601 UTC timestamp to formatted local time in target timezone.
     *
     * @param string|int|null $time
     * @param string          $timezone
     * @param string          $format
     * @return string
     */
    private function format_iso_time( $time, string $timezone = 'Asia/Kolkata', string $format = 'g:i A' ): string {
        if ( empty( $time ) || ! is_string( $time ) ) {
            return '';
        }
        try {
            $dt = new DateTimeImmutable( $time );
            return $dt->setTimezone( new DateTimeZone( $timezone ) )->format( $format );
        } catch ( Exception $e ) {
            return '';
        }
    }

    /**
     * Astronomical Moonrise and Moonset calculation (Meeus Ch 15, 47).
     * Provides instantaneous parity with packages/panchangam-engine.
     *
     * @param int    $year
     * @param int    $month
     * @param int    $day
     * @param float  $lat
     * @param float  $lon
     * @param string $timezone
     * @return array
     */
    public static function calculate_moon_rise_set( int $year, int $month, int $day, float $lat, float $lon, string $timezone = 'Asia/Kolkata' ): array {
        try {
            $dtz = new DateTimeZone( $timezone );
            $dt = new DateTimeImmutable( "{$year}-{$month}-{$day} 12:00:00", $dtz );
            $tzOffset = $dtz->getOffset( $dt ) / 3600.0;
        } catch ( Exception $e ) {
            $tzOffset = 5.5;
        }

        $jdMidnightUTC = self::gregorian_to_jd_calc( $year, $month, $day ) - $tzOffset / 24.0;
        $stepHours = 0.25;
        $riseJD = null;
        $setJD  = null;

        $prevDiff = self::calc_moon_altitude( $jdMidnightUTC - 6.0 / 24.0, $lat, $lon )['diff'];

        for ( $t = -6.0 + $stepHours; $t <= 30.0; $t += $stepHours ) {
            $jd = $jdMidnightUTC + $t / 24.0;
            $diff = self::calc_moon_altitude( $jd, $lat, $lon )['diff'];

            if ( $prevDiff < 0 && $diff >= 0 ) {
                $rootJD = self::find_moon_crossing( $jdMidnightUTC + ( $t - $stepHours ) / 24.0, $jd, $lat, $lon );
                $utcTs = (int) round( ( $rootJD - 2440587.5 ) * 86400.0 );
                try {
                    $dtLocal = ( new DateTimeImmutable( "@{$utcTs}" ) )->setTimezone( new DateTimeZone( $timezone ) );
                    if ( $dtLocal->format( 'Y-m-d' ) === sprintf( '%04d-%02d-%02d', $year, $month, $day ) && ! $riseJD ) {
                        $riseJD = $rootJD;
                    }
                } catch ( Exception $e ) {}
            }

            if ( $prevDiff > 0 && $diff <= 0 ) {
                $rootJD = self::find_moon_crossing( $jdMidnightUTC + ( $t - $stepHours ) / 24.0, $jd, $lat, $lon );
                $utcTs = (int) round( ( $rootJD - 2440587.5 ) * 86400.0 );
                try {
                    $dtLocal = ( new DateTimeImmutable( "@{$utcTs}" ) )->setTimezone( new DateTimeZone( $timezone ) );
                    if ( $dtLocal->format( 'Y-m-d' ) === sprintf( '%04d-%02d-%02d', $year, $month, $day ) && ! $setJD ) {
                        $setJD = $rootJD;
                    }
                } catch ( Exception $e ) {}
            }

            $prevDiff = $diff;
        }

        $riseRes = [ 'time' => 'No Moonrise', 'datetime' => null, 'status' => 'no_event' ];
        if ( $riseJD ) {
            $utcTs = (int) round( ( $riseJD - 2440587.5 ) * 86400.0 );
            try {
                $dtLocal = ( new DateTimeImmutable( "@{$utcTs}" ) )->setTimezone( new DateTimeZone( $timezone ) );
                $riseRes = [
                    'time'     => $dtLocal->format( 'g:i A' ),
                    'datetime' => $dtLocal->format( DateTimeInterface::ATOM ),
                    'status'   => 'normal',
                ];
            } catch ( Exception $e ) {}
        }

        $setRes = [ 'time' => 'No Moonset', 'datetime' => null, 'status' => 'no_event' ];
        if ( $setJD ) {
            $utcTs = (int) round( ( $setJD - 2440587.5 ) * 86400.0 );
            try {
                $dtLocal = ( new DateTimeImmutable( "@{$utcTs}" ) )->setTimezone( new DateTimeZone( $timezone ) );
                $setRes = [
                    'time'     => $dtLocal->format( 'g:i A' ),
                    'datetime' => $dtLocal->format( DateTimeInterface::ATOM ),
                    'status'   => 'normal',
                ];
            } catch ( Exception $e ) {}
        }

        return [ 'moonrise' => $riseRes, 'moonset' => $setRes ];
    }

    private static function gregorian_to_jd_calc( int $year, int $month, int $day ): float {
        if ( $month <= 2 ) {
            $year -= 1;
            $month += 12;
        }
        $A = floor( $year / 100 );
        $B = 2 - $A + floor( $A / 4 );
        return floor( 365.25 * ( $year + 4716 ) ) + floor( 30.6001 * ( $month + 1 ) ) + $day + $B - 1524.5;
    }

    private static function normalize_deg_360( float $deg ): float {
        $d = fmod( $deg, 360.0 );
        if ( $d < 0 ) {
            $d += 360.0;
        }
        return $d;
    }

    private static function normalize_deg_180( float $deg ): float {
        $d = fmod( $deg + 180.0, 360.0 );
        if ( $d < 0 ) {
            $d += 360.0;
        }
        return $d - 180.0;
    }

    private static function calc_moon_position( float $jd ): array {
        $deg2rad = M_PI / 180.0;
        $rad2deg = 180.0 / M_PI;
        $T = ( $jd - 2451545.0 ) / 36525.0;

        $L0 = self::normalize_deg_360( 218.3164477 + 481267.88123421 * $T - 0.0015786 * $T * $T + $T * $T * $T / 538841.0 - $T * $T * $T * $T / 65194000.0 );
        $D  = self::normalize_deg_360( 297.8501921 + 445267.1114034  * $T - 0.0018819 * $T * $T + $T * $T * $T / 545868.0 - $T * $T * $T * $T / 113065000.0 );
        $M  = self::normalize_deg_360( 357.5291092 + 35999.0502909   * $T - 0.0001536 * $T * $T + $T * $T * $T / 24490000.0 );
        $Mp = self::normalize_deg_360( 134.9633964 + 477198.8675055  * $T + 0.0087414 * $T * $T + $T * $T * $T / 69699.0 - $T * $T * $T * $T / 14712000.0 );
        $F  = self::normalize_deg_360( 93.2720950  + 483202.0175233  * $T - 0.0036539 * $T * $T - $T * $T * $T / 3526000.0 + $T * $T * $T * $T / 863310000.0 );

        $D_rad  = $D  * $deg2rad;
        $M_rad  = $M  * $deg2rad;
        $Mp_rad = $Mp * $deg2rad;
        $F_rad  = $F  * $deg2rad;

        $SigmaL =
            6.288774 * sin( $Mp_rad ) +
            1.274027 * sin( 2 * $D_rad - $Mp_rad ) +
            0.658314 * sin( 2 * $D_rad ) +
            0.213618 * sin( 2 * $Mp_rad ) -
            0.185116 * sin( $M_rad ) -
            0.114332 * sin( 2 * $F_rad ) +
            0.058793 * sin( 2 * $D_rad - 2 * $Mp_rad ) +
            0.057066 * sin( 2 * $D_rad - $M_rad - $Mp_rad ) +
            0.053322 * sin( 2 * $D_rad + $Mp_rad ) +
            0.045758 * sin( 2 * $D_rad - $M_rad ) -
            0.040923 * sin( $M_rad - $Mp_rad ) -
            0.034720 * sin( $D_rad ) -
            0.030383 * sin( $M_rad + $Mp_rad );

        $SigmaB =
            5.128154 * sin( $F_rad ) +
            0.280602 * sin( $Mp_rad + $F_rad ) +
            0.277693 * sin( $Mp_rad - $F_rad ) +
            0.173237 * sin( 2 * $D_rad - $F_rad ) +
            0.055413 * sin( 2 * $D_rad - $Mp_rad + $F_rad ) +
            0.046271 * sin( 2 * $D_rad - $Mp_rad - $F_rad ) +
            0.032573 * sin( 2 * $D_rad + $F_rad ) +
            0.017198 * sin( 2 * $Mp_rad + $F_rad );

        $lambda = self::normalize_deg_360( $L0 + $SigmaL );
        $beta   = $SigmaB;

        $eps = 23.43929111 - 0.013004167 * $T - 0.000000164 * $T * $T + 0.000000504 * $T * $T * $T;

        $lR = $lambda * $deg2rad;
        $bR = $beta   * $deg2rad;
        $eR = $eps    * $deg2rad;

        $x = cos( $bR ) * cos( $lR );
        $y = cos( $bR ) * cos( $eR ) * sin( $lR ) - sin( $bR ) * sin( $eR );
        $z = sin( $bR ) * cos( $eR ) + cos( $bR ) * sin( $eR ) * sin( $lR );

        $ra  = self::normalize_deg_360( atan2( $y, $x ) * $rad2deg );
        $dec = asin( max( -1.0, min( 1.0, $z ) ) ) * $rad2deg;

        $Delta = 385000.56 -
            20905.355 * cos( $Mp_rad ) -
             3699.111 * cos( 2 * $D_rad - $Mp_rad ) -
             2955.968 * cos( 2 * $D_rad ) -
              569.925 * cos( 2 * $Mp_rad ) +
               48.888 * cos( $M_rad );

        $parallax = asin( 6378.14 / $Delta ) * $rad2deg;
        $h0 = 0.727507 * $parallax - 0.566667;

        return [ 'ra' => $ra, 'dec' => $dec, 'h0' => $h0 ];
    }

    private static function calc_moon_altitude( float $jd, float $lat, float $lon ): array {
        $deg2rad = M_PI / 180.0;
        $rad2deg = 180.0 / M_PI;

        $pos = self::calc_moon_position( $jd );
        $ra  = $pos['ra'];
        $dec = $pos['dec'];
        $h0  = $pos['h0'];

        $Ddays = $jd - 2451545.0;
        $T     = $Ddays / 36525.0;
        $gmst  = self::normalize_deg_360( 280.46061837 + 360.98564736629 * $Ddays + 0.000387933 * $T * $T - ( $T * $T * $T / 38710000.0 ) );
        $lst   = self::normalize_deg_360( $gmst + $lon );

        $H    = self::normalize_deg_180( $lst - $ra ) * $deg2rad;
        $latR = $lat * $deg2rad;
        $decR = $dec * $deg2rad;

        $sinAlt = sin( $latR ) * sin( $decR ) + cos( $latR ) * cos( $decR ) * cos( $H );
        $alt    = asin( max( -1.0, min( 1.0, $sinAlt ) ) ) * $rad2deg;

        return [ 'alt' => $alt, 'h0' => $h0, 'diff' => $alt - $h0 ];
    }

    private static function find_moon_crossing( float $jd1, float $jd2, float $lat, float $lon, int $maxIter = 30 ): float {
        $low  = $jd1;
        $high = $jd2;
        for ( $i = 0; $i < $maxIter; $i++ ) {
            $mid  = ( $low + $high ) / 2.0;
            $dMid = self::calc_moon_altitude( $mid, $lat, $lon )['diff'];
            $dLow = self::calc_moon_altitude( $low, $lat, $lon )['diff'];
            if ( $dLow * $dMid <= 0 ) {
                $high = $mid;
            } else {
                $low = $mid;
            }
        }
        return ( $low + $high ) / 2.0;
    }

    /**
     * Standard Varjyam start ghatis for all 27 Nakshatras (Ashwini to Revati).
     * Sourced identically from packages/panchangam-engine/src/core.js.
     */
    const VARJYAM_START_GHATIS = [
        50, 24, 30, 40, 14, 21, 30, 20, 32, // 1–9: Ashwini to Ashlesha
        30, 20, 18, 21, 20, 14, 14, 10, 14, // 10–18: Magha to Jyeshtha
        56, 24, 20, 10, 10, 18, 16, 24, 30  // 19–27: Mula to Revati
    ];

    /**
     * Calculate Dur Muhurtham slots for a day based on local sunrise, sunset, and weekday.
     * Identical to packages/panchangam-engine/src/core.js calcDurMuhurtham.
     *
     * @param string $sunrise_iso
     * @param string $sunset_iso
     * @param int    $weekday  0=Sunday, 6=Saturday
     * @param string $timezone
     * @return array
     */
    public static function calculate_dur_muhurtham( string $sunrise_iso, string $sunset_iso, int $weekday, string $timezone = 'Asia/Kolkata' ): array {
        $rise_ts = strtotime( $sunrise_iso );
        $set_ts  = strtotime( $sunset_iso );
        if ( ! $rise_ts || ! $set_ts || $set_ts <= $rise_ts ) {
            return [];
        }

        $day_sec = $set_ts - $rise_ts;
        $m_len   = $day_sec / 15.0;

        $slots_map = [
            0 => [ 14 ],
            1 => [ 8, 12 ],
            2 => [ 4, 11 ],
            3 => [ 5 ],
            4 => [ 6, 7 ],
            5 => [ 4, 9 ],
            6 => [ 2, 3 ],
        ];

        $slots   = $slots_map[ $weekday % 7 ] ?? [ 14 ];
        $results = [];
        try {
            $tz = new DateTimeZone( $timezone );
        } catch ( Exception $e ) {
            $tz = new DateTimeZone( 'Asia/Kolkata' );
        }

        foreach ( $slots as $slot ) {
            $s = (int) round( $rise_ts + ( $slot - 1 ) * $m_len );
            $e = (int) round( $rise_ts + $slot * $m_len );
            $s_str = ( new DateTimeImmutable( "@{$s}" ) )->setTimezone( $tz )->format( 'g:i A' );
            $e_str = ( new DateTimeImmutable( "@{$e}" ) )->setTimezone( $tz )->format( 'g:i A' );

            $results[] = [
                'start'    => gmdate( 'Y-m-d\TH:i:s.000\Z', $s ),
                'end'      => gmdate( 'Y-m-d\TH:i:s.000\Z', $e ),
                'slot'     => $slot,
                'startStr' => $s_str,
                'endStr'   => $e_str,
                'text'     => $s_str . ' – ' . $e_str,
            ];
        }

        return $results;
    }

    /**
     * Calculate Brahma Muhurtham (96m to 48m prior to sunrise).
     *
     * @param string $sunrise_iso
     * @param string $timezone
     * @return array
     */
    public static function calculate_brahma_muhurtham( string $sunrise_iso, string $timezone = 'Asia/Kolkata' ): array {
        $rise_ts = strtotime( $sunrise_iso );
        if ( ! $rise_ts ) {
            return [];
        }
        $s = $rise_ts - ( 96 * 60 );
        $e = $rise_ts - ( 48 * 60 );
        try {
            $tz = new DateTimeZone( $timezone );
        } catch ( Exception $e ) {
            $tz = new DateTimeZone( 'Asia/Kolkata' );
        }
        $s_str = ( new DateTimeImmutable( "@{$s}" ) )->setTimezone( $tz )->format( 'g:i A' );
        $e_str = ( new DateTimeImmutable( "@{$e}" ) )->setTimezone( $tz )->format( 'g:i A' );

        return [
            'start'    => gmdate( 'Y-m-d\TH:i:s.000\Z', $s ),
            'end'      => gmdate( 'Y-m-d\TH:i:s.000\Z', $e ),
            'startStr' => $s_str,
            'endStr'   => $e_str,
            'text'     => $s_str . ' – ' . $e_str,
        ];
    }

    /**
     * Calculate Varjyam (Tyajyam) and Amrit Kalam based on Nakshatra.
     * Identical to packages/panchangam-engine/src/core.js calcVarjyamAndAmritKalam.
     *
     * @param int         $nak_id 1–27
     * @param string|null $nak_start_iso
     * @param string|null $nak_end_iso
     * @param string|null $sunrise_iso
     * @param float|null  $deg_in_nak
     * @param string      $timezone
     * @return array
     */
    public static function calculate_varjyam_and_amrit_kalam( int $nak_id, ?string $nak_start_iso, ?string $nak_end_iso, ?string $sunrise_iso, ?float $deg_in_nak = null, string $timezone = 'Asia/Kolkata' ): array {
        try {
            $tz = new DateTimeZone( $timezone );
        } catch ( Exception $e ) {
            $tz = new DateTimeZone( 'Asia/Kolkata' );
        }

        if ( $nak_start_iso && $nak_end_iso ) {
            $n_start = strtotime( $nak_start_iso );
            $n_end   = strtotime( $nak_end_iso );
            $span    = max( 18 * 3600, min( 30 * 3600, $n_end - $n_start ) );
            $ghati   = $span / 60.0;
        } else {
            $ref_ts = $sunrise_iso ? strtotime( $sunrise_iso ) : time();
            $deg    = ( $deg_in_nak !== null ) ? $deg_in_nak : 6.666;
            $frac   = max( 0.0, min( 1.0, $deg / 13.3333333 ) );
            $span   = 24 * 3600;
            $ghati  = $span / 60.0;
            $n_start = (int) round( $ref_ts - $frac * $span );
        }

        $idx = max( 0, min( 26, ( $nak_id - 1 ) % 27 ) );
        $start_ghati = self::VARJYAM_START_GHATIS[ $idx ];

        $v_start = (int) round( $n_start + $start_ghati * $ghati );
        $v_end   = (int) round( $v_start + 4 * $ghati );

        $a_start = (int) round( $v_start + 14 * $ghati );
        $a_end   = (int) round( $a_start + 4 * $ghati );

        $vs = ( new DateTimeImmutable( "@{$v_start}" ) )->setTimezone( $tz )->format( 'g:i A' );
        $ve = ( new DateTimeImmutable( "@{$v_end}" ) )->setTimezone( $tz )->format( 'g:i A' );
        $as = ( new DateTimeImmutable( "@{$a_start}" ) )->setTimezone( $tz )->format( 'g:i A' );
        $ae = ( new DateTimeImmutable( "@{$a_end}" ) )->setTimezone( $tz )->format( 'g:i A' );

        return [
            'varjyam'    => [
                'start'    => gmdate( 'Y-m-d\TH:i:s.000\Z', $v_start ),
                'end'      => gmdate( 'Y-m-d\TH:i:s.000\Z', $v_end ),
                'startStr' => $vs,
                'endStr'   => $ve,
                'text'     => $vs . ' – ' . $ve,
            ],
            'amritKalam' => [
                'start'    => gmdate( 'Y-m-d\TH:i:s.000\Z', $a_start ),
                'end'      => gmdate( 'Y-m-d\TH:i:s.000\Z', $a_end ),
                'startStr' => $as,
                'endStr'   => $ae,
                'text'     => $as . ' – ' . $ae,
            ],
        ];
    }
}
