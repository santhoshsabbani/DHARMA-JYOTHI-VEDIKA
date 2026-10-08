<?php
/**
 * DJV REST API — Custom Endpoints
 *
 * Provides all /wp-json/djv/v1/ endpoints.
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DJV_REST_API {

	public static function init(): void {
		add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
	}

	/**
	 * Register all DJV REST API routes.
	 */
	public static function register_routes(): void {
		$ns = DJV_API_NAMESPACE;

		// ── Panchangam ────────────────────────────────────────────
		register_rest_route( $ns, '/panchangam', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_panchangam' ],
			'permission_callback' => '__return_true',
			'args'                => self::panchangam_args(),
		] );

		register_rest_route( $ns, '/today', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_today' ],
			'permission_callback' => '__return_true',
			'args'                => self::panchangam_args(),
		] );

		// ── Festivals ─────────────────────────────────────────────
		register_rest_route( $ns, '/festivals', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_festivals' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'year'      => [ 'type' => 'integer', 'default' => (int) date('Y') ],
				'month'     => [ 'type' => 'integer', 'default' => 0 ],
				'latitude'  => [ 'type' => 'number', 'default' => 17.3850 ],
				'longitude' => [ 'type' => 'number', 'default' => 78.4867 ],
				'timezone'  => [ 'type' => 'string', 'default' => 'Asia/Kolkata' ],
				'city'      => [ 'type' => 'string', 'default' => 'Hyderabad' ],
				'state'     => [ 'type' => 'string', 'default' => 'Telangana' ],
				'scope'     => [ 'type' => 'string', 'default' => 'relevant' ],
				'region'    => [ 'type' => 'string', 'default' => 'all' ],
				'category'  => [ 'type' => 'string', 'default' => 'all' ],
				'deity'     => [ 'type' => 'string', 'default' => 'all' ],
				'language'  => [ 'type' => 'string', 'default' => 'en' ],
				'search'    => [ 'type' => 'string', 'default' => '' ],
			],
		] );

		register_rest_route( $ns, '/festivals/(?P<slug>[a-z0-9-]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_festival_single' ],
			'permission_callback' => '__return_true',
		] );

		// ── Muhurtham ─────────────────────────────────────────────
		register_rest_route( $ns, '/muhurtham', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_muhurtham' ],
			'permission_callback' => '__return_true',
			'args'                => self::panchangam_args(),
		] );

		// ── Pooja ─────────────────────────────────────────────────
		register_rest_route( $ns, '/pooja', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_pooja' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
			],
		] );

		register_rest_route( $ns, '/pooja/(?P<slug>[a-z0-9-]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_pooja_single' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
			],
		] );

		// ── Mantras ───────────────────────────────────────────────
		register_rest_route( $ns, '/mantras', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_mantras' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'deity'    => [ 'type' => 'string', 'default' => '' ],
				'category' => [ 'type' => 'string', 'default' => '' ],
				'search'   => [ 'type' => 'string', 'default' => '' ],
				'page'     => [ 'type' => 'integer', 'default' => 1 ],
				'per_page' => [ 'type' => 'integer', 'default' => 18, 'maximum' => 100 ],
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
			],
		] );

		register_rest_route( $ns, '/mantras/(?P<slug>[a-z0-9-]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_mantra_single' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
			],
		] );

		// ── Temples ───────────────────────────────────────────────
		register_rest_route( $ns, '/temples', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_temples' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'state'     => [ 'type' => 'string', 'default' => '' ],
				'district'  => [ 'type' => 'string', 'default' => '' ],
				'city'      => [ 'type' => 'string', 'default' => '' ],
				'deity'     => [ 'type' => 'string', 'default' => '' ],
				'category'  => [ 'type' => 'string', 'default' => '' ],
				'tradition' => [ 'type' => 'string', 'default' => '' ],
				'search'    => [ 'type' => 'string', 'default' => '' ],
				'page'      => [ 'type' => 'integer', 'default' => 1 ],
				'per_page'  => [ 'type' => 'integer', 'default' => 20, 'maximum' => 100 ],
				'language'  => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
				'lat'       => [ 'type' => 'number', 'required' => false ],
				'lon'       => [ 'type' => 'number', 'required' => false ],
				'radius'    => [ 'type' => 'number', 'required' => false ],
			],
		] );

		register_rest_route( $ns, '/temples/nearby', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_temples_nearby' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'lat'      => [ 'type' => 'number', 'default' => 17.3850 ],
				'lon'      => [ 'type' => 'number', 'default' => 78.4867 ],
				'radius'   => [ 'type' => 'number', 'default' => 100 ],
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
				'limit'    => [ 'type' => 'integer', 'default' => 12, 'maximum' => 50 ],
			],
		] );

		register_rest_route( $ns, '/temples/(?P<slug>[a-z0-9-]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_temple_single' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
				'lat'      => [ 'type' => 'number', 'required' => false ],
				'lon'      => [ 'type' => 'number', 'required' => false ],
			],
		] );

		// ── Articles ──────────────────────────────────────────────
		register_rest_route( $ns, '/articles', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_articles' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'category' => [ 'type' => 'string', 'default' => '' ],
				'per_page' => [ 'type' => 'integer', 'default' => 10, 'maximum' => 50 ],
				'language' => [ 'type' => 'string', 'default' => 'en', 'enum' => [ 'en', 'te', 'hi' ] ],
			],
		] );

		// ── Services ──────────────────────────────────────────────
		register_rest_route( $ns, '/services', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_services' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'per_page' => [ 'type' => 'integer', 'default' => 20, 'maximum' => 50 ],
			],
		] );

		// ── Global Multi-CPT Search ───────────────────────────────
		register_rest_route( $ns, '/search', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'search_all' ],
			'permission_callback' => '__return_true',
			'args'                => [
				's'        => [ 'type' => 'string', 'required' => true ],
				'per_page' => [ 'type' => 'integer', 'default' => 20, 'maximum' => 100 ],
			],
		] );

		// ── Today (aggregated home endpoint) ──────────────────────
		register_rest_route( $ns, '/today', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_today' ],
			'permission_callback' => '__return_true',
			'args'                => self::panchangam_args(),
		] );

		// ── Admin: Precalculate ───────────────────────────────────
		register_rest_route( $ns, '/admin/precalculate', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ __CLASS__, 'admin_precalculate' ],
			'permission_callback' => function() { return current_user_can( 'manage_options' ); },
			'args'                => [
				'days' => [ 'type' => 'integer', 'default' => 30, 'maximum' => 365 ],
			],
		] );
	}

	/**
	 * Common Panchangam args definition.
	 */
	private static function panchangam_args(): array {
		return [
			'date' => [
				'type'              => 'string',
				'default'           => date( 'Y-m-d' ),
				'validate_callback' => function( $v ) {
					return (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v );
				},
				'sanitize_callback' => 'sanitize_text_field',
			],
			'latitude' => [
				'type'    => 'number',
				'default' => 17.3850,
				'minimum' => -90,
				'maximum' => 90,
			],
			'longitude' => [
				'type'    => 'number',
				'default' => 78.4867,
				'minimum' => -180,
				'maximum' => 180,
			],
			'timezone' => [
				'type'              => 'string',
				'default'           => 'Asia/Kolkata',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function( $v ) {
					return in_array( $v, timezone_identifiers_list(), true );
				},
			],
			'region' => [
				'type'              => 'string',
				'default'           => 'telugu',
				'sanitize_callback' => 'sanitize_key',
			],
			'language' => [
				'type'              => 'string',
				'default'           => 'en',
				'enum'              => [ 'en', 'te', 'hi', 'kn', 'ta', 'ml' ],
				'sanitize_callback' => 'sanitize_key',
			],
		];
	}

	/* ─── Endpoint Handlers ──────────────────────────────────── */

	private static function respond( $data, $meta = [] ): WP_REST_Response {
		return new WP_REST_Response( [
			'success' => true,
			'data'    => $data,
			'meta'    => $meta,
		], 200 );
	}

	private static function respond_error( $message, $status = 400, $code = 'error', $details = null ): WP_REST_Response {
		$error_payload = [
			'code'    => $code,
			'message' => $message,
		];
		if ( ! empty( $details ) ) {
			$error_payload['details'] = $details;
		}
		return new WP_REST_Response( [
			'success' => false,
			'error'   => $error_payload,
		], $status );
	}

	private static function validate_panchangam_request( $date, $lat, $lon, $tz ) {
		$errors = [];
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			$errors[] = 'Invalid date format. Expected YYYY-MM-DD.';
		}
		if ( $lat < -90 || $lat > 90 ) {
			$errors[] = 'Latitude must be between -90 and 90.';
		}
		if ( $lon < -180 || $lon > 180 ) {
			$errors[] = 'Longitude must be between -180 and 180.';
		}
		if ( ! in_array( $tz, timezone_identifiers_list(), true ) ) {
			$errors[] = 'Invalid IANA timezone identifier.';
		}
		return empty( $errors ) ? false : implode( ' ', $errors );
	}

	/**
	 * GET /djv/v1/panchangam
	 */
	public static function get_panchangam( WP_REST_Request $req ): WP_REST_Response {
		$date     = sanitize_text_field( $req->get_param( 'date' ) );
		$lat      = floatval( $req->get_param( 'latitude' ) );
		$lon      = floatval( $req->get_param( 'longitude' ) );
		$tz       = sanitize_text_field( $req->get_param( 'timezone' ) );
		$region   = sanitize_key( $req->get_param( 'region' ) );
		$language = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $language, [ 'en', 'te', 'hi' ], true ) ) {
			$language = 'en';
		}

		// Validate inputs
		$errors = self::validate_panchangam_request( $date, $lat, $lon, $tz );
		if ( ! empty( $errors ) ) {
			return self::respond_error( $errors, 400, 'validation_failed' );
		}

		// Calculate via Panchangam engine (handles caching internally)
		$panchangam_engine = new DJV_Panchangam();
		$result = $panchangam_engine->get_panchangam( $date, $lat, $lon, $tz, $region ?: 'telugu', $language ?: 'en' );
		if ( is_wp_error( $result ) ) {
			$err_data = $result->get_error_data();
			$status   = is_array( $err_data ) && isset( $err_data['status'] ) ? (int) $err_data['status'] : 500;
			$details  = is_array( $err_data ) && isset( $err_data['details'] ) ? $err_data['details'] : null;
			return self::respond_error( $result->get_error_message(), $status, $result->get_error_code(), $details );
		}

		$meta = $result['meta'] ?? [];
		$meta['cached'] = isset($result['_cache_hit']);
		unset($result['meta']);
		unset($result['_cache_hit']);
		
		return self::respond( $result, $meta );
	}

	/**
	 * GET /djv/v1/festivals
	 */
	public static function get_festivals( WP_REST_Request $req ): WP_REST_Response {
		$year     = intval( $req->get_param( 'year' ) ?: date( 'Y' ) );
		$month    = intval( $req->get_param( 'month' ) ?: 0 );
		$lat      = floatval( $req->get_param( 'latitude' ) ?: 17.3850 );
		$lon      = floatval( $req->get_param( 'longitude' ) ?: 78.4867 );
		$tz       = sanitize_text_field( $req->get_param( 'timezone' ) ?: 'Asia/Kolkata' );
		$city     = sanitize_text_field( $req->get_param( 'city' ) ?: ( $req->get_param( 'location' ) ?: 'Hyderabad' ) );
		$state    = sanitize_text_field( $req->get_param( 'state' ) ?: 'Telangana' );
		$scope    = sanitize_key( $req->get_param( 'scope' ) ?: 'relevant' );
		$region   = sanitize_text_field( $req->get_param( 'region' ) ?: 'all' );
		$category = sanitize_text_field( $req->get_param( 'category' ) ?: 'all' );
		$deity    = sanitize_text_field( $req->get_param( 'deity' ) ?: 'all' );
		$lang     = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}
		$search   = sanitize_text_field( $req->get_param( 'search' ) ?: '' );

		// Validate year range
		if ( $year < 1900 || $year > 2200 ) {
			return self::respond_error( 'Year out of range (1900–2200)', 400 );
		}

		require_once __DIR__ . '/class-djv-festival-master.php';
		$occurrences = DJV_Festival_Master::get_occurrences( $year, [
			'latitude'  => $lat,
			'longitude' => $lon,
			'timezone'  => $tz,
			'city'      => $city,
			'state'     => $state,
			'scope'     => $scope,
			'region'    => $region,
			'category'  => $category,
			'deity'     => $deity,
			'month'     => $month,
			'language'  => $lang,
			'search'    => $search,
		] );

		return self::respond( $occurrences, [
			'year'      => $year,
			'month'     => $month,
			'language'  => $lang,
			'scope'     => $scope,
			'location'  => [
				'city'      => $city,
				'state'     => $state,
				'latitude'  => $lat,
				'longitude' => $lon,
				'timezone'  => $tz,
			],
			'region'    => $region,
			'state'     => $state,
			'category'  => $category,
			'count'     => count( $occurrences ),
			'engine'    => 'DJV Astronomical Panchangam Engine v2.1',
		] );
	}

	/**
	 * GET /djv/v1/festivals/{slug}
	 */
	public static function get_festival_single( WP_REST_Request $req ): WP_REST_Response {
		$raw_slug = sanitize_key( $req->get_param( 'slug' ) );
		$year     = intval( $req->get_param( 'year' ) ?: ( $req->get_param( 'y' ) ?: 2026 ) );
		$lang     = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$city     = sanitize_text_field( $req->get_param( 'city' ) ?: ( $req->get_param( 'location' ) ?: 'Hyderabad' ) );
		$state    = sanitize_text_field( $req->get_param( 'state' ) ?: 'Telangana' );
		$lat      = floatval( $req->get_param( 'latitude' ) ?: ( $req->get_param( 'lat' ) ?: 17.3850 ) );
		$lon      = floatval( $req->get_param( 'longitude' ) ?: ( $req->get_param( 'lon' ) ?: 78.4867 ) );
		$tz       = sanitize_text_field( $req->get_param( 'timezone' ) ?: ( $req->get_param( 'tz' ) ?: 'Asia/Kolkata' ) );
		$region   = sanitize_text_field( $req->get_param( 'region' ) ?: 'all' );

		require_once __DIR__ . '/class-djv-festival-master.php';
		$slug = DJV_Festival_Master::resolve_slug_alias( $raw_slug );

		$post = get_page_by_path( $slug, OBJECT, 'djv_festival' );
		if ( ! $post ) {
			return self::respond_error( 'Festival not found', 404, 'not_found' );
		}

		// Single festival cache key incorporating all parameters per Section 21
		$cache_key = 'djv_fsing_' . md5( sprintf(
			'%d|%s|%d|%s|%s|%s|%.4f|%.4f|%s|%s|%s|%s',
			$post->ID,
			$slug,
			$year,
			$lang,
			sanitize_key( $city ),
			sanitize_key( $state ),
			$lat,
			$lon,
			sanitize_key( $tz ),
			sanitize_key( $region ),
			DJV_Festival_Master::VERSION,
			'r2.1'
		) );

		$cached = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) ) {
			return self::respond( $cached );
		}

		$data = self::format_festival_post( $post );

		// Add calculated occurrence for requested year and location
		$occurrences = DJV_Festival_Master::get_occurrences( $year, [
			'latitude'  => $lat,
			'longitude' => $lon,
			'timezone'  => $tz,
			'city'      => $city,
			'state'     => $state,
			'scope'     => 'all_india', // When viewing single festival, calculate occurrence regardless of scope
			'language'  => $lang,
		] );

		foreach ( $occurrences as $occ ) {
			if ( $occ['slug'] === $slug || $occ['slug'] === $raw_slug || ( ! empty( $occ['id'] ) && $occ['id'] === $post->ID ) ) {
				$data['year_occurrence'] = $occ;
				$data['date']            = $occ['date'];
				$data['formatted_date']  = $occ['formatted_date'];
				$data['short_formatted'] = $occ['short_formatted'] ?? $occ['formatted_date'];
				$data['day_of_week']     = $occ['day_of_week'];
				$data['scope']           = $occ['scope'] ?? 'pan_india';
				$data['scope_label']     = $occ['scope_label'] ?? 'Pan-India';
				if ( ! empty( $occ['puja_timings'] ) ) {
					$data['puja_timings'] = $occ['puja_timings'];
				}
				break;
			}
		}

		set_transient( $cache_key, $data, 7 * DAY_IN_SECONDS );

		return self::respond( $data );
	}

	/**
	 * GET /djv/v1/muhurtham
	 */
	public static function get_muhurtham( WP_REST_Request $req ): WP_REST_Response {
		$date     = sanitize_text_field( $req->get_param( 'date' ) );
		$lat      = floatval( $req->get_param( 'latitude' ) );
		$lon      = floatval( $req->get_param( 'longitude' ) );
		$tz       = sanitize_text_field( $req->get_param( 'timezone' ) );
		$language = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $language, [ 'en', 'te', 'hi' ], true ) ) {
			$language = 'en';
		}

		$errors = self::validate_panchangam_request( $date, $lat, $lon, $tz );
		if ( ! empty( $errors ) ) {
			return self::respond_error( $errors, 400, 'validation_failed' );
		}

		$panchangam_engine = new DJV_Panchangam();
		$panchangam = $panchangam_engine->get_panchangam( $date, $lat, $lon, $tz, 'telugu', $language );
		if ( is_wp_error( $panchangam ) ) {
			$err_data = $panchangam->get_error_data();
			$status   = is_array( $err_data ) && isset( $err_data['status'] ) ? (int) $err_data['status'] : 500;
			$details  = is_array( $err_data ) && isset( $err_data['details'] ) ? $err_data['details'] : null;
			return self::respond_error( $panchangam->get_error_message(), $status, $panchangam->get_error_code(), $details );
		}

		// Extract muhurtham-relevant timings from Panchangam
		$muhurtham = [
			'date'              => $date,
			'abhijit'           => $panchangam['timings']['abhijitMuhurtham'] ?? null,
			'inauspicious'      => [
				'rahu_kalam'  => $panchangam['timings']['rahuKalam'] ?? null,
				'yamagandam'  => $panchangam['timings']['yamagandam'] ?? null,
				'gulika_kalam'=> $panchangam['timings']['gulikaKalam'] ?? null,
			],
			'disclaimer'        => 'These timings are for informational purposes only. Consult a qualified Jyotishi for ceremonial muhurtham.',
		];

		return self::respond( $muhurtham, $panchangam['meta'] ?? [] );
	}

	/**
	 * GET /djv/v1/pooja
	 */
	public static function get_pooja( WP_REST_Request $req ): WP_REST_Response {
		$lang = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$posts = get_posts( [
			'post_type'      => 'djv_pooja',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		] );

		$pooja_list = array_map( function( $post ) use ( $lang ) {
			$id       = $post->ID;
			$title_en = get_post_meta( $id, '_djv_title_en', true ) ?: get_the_title( $post );
			$title_te = get_post_meta( $id, '_djv_title_te', true );
			$title_hi = get_post_meta( $id, '_djv_title_hi', true );

			$display_title = $title_en;
			if ( $lang === 'te' && ! empty( $title_te ) ) {
				$display_title = $title_te;
			} elseif ( $lang === 'hi' && ! empty( $title_hi ) ) {
				$display_title = $title_hi;
			}

			$intro_en = get_post_meta( $id, '_djv_intro_en', true ) ?: get_the_excerpt( $post );
			$intro_te = get_post_meta( $id, '_djv_intro_te', true );
			$intro_hi = get_post_meta( $id, '_djv_intro_hi', true );

			$display_intro = $intro_en;
			if ( $lang === 'te' && ! empty( $intro_te ) ) {
				$display_intro = $intro_te;
			} elseif ( $lang === 'hi' && ! empty( $intro_hi ) ) {
				$display_intro = $intro_hi;
			}

			return [
				'id'          => $id,
				'slug'        => $post->post_name,
				'title'       => $display_title,
				'title_en'    => $title_en,
				'title_te'    => $title_te,
				'title_hi'    => $title_hi,
				'excerpt'     => $display_intro,
				'intro'       => $display_intro,
				'duration'    => get_post_meta( $id, '_djv_duration', true ),
				'categories'  => wp_get_post_terms( $id, 'djv_pooja_cat', [ 'fields' => 'names' ] ),
				'deity'       => wp_get_post_terms( $id, 'djv_deity', [ 'fields' => 'names' ] ),
				'link'        => get_permalink( $post ),
				'thumbnail'   => get_the_post_thumbnail_url( $id, 'medium' ),
			];
		}, $posts );

		return self::respond( $pooja_list, [ 'count' => count( $pooja_list ), 'language' => $lang ] );
	}

	/**
	 * GET /djv/v1/pooja/{slug}
	 */
	public static function get_pooja_single( WP_REST_Request $req ): WP_REST_Response {
		$slug = sanitize_key( $req->get_param( 'slug' ) );
		$lang = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$post = get_page_by_path( $slug, OBJECT, 'djv_pooja' );

		if ( ! $post || $post->post_status !== 'publish' ) {
			return self::respond_error( 'Pooja guide not found', 404, 'not_found' );
		}

		$id       = $post->ID;
		$title_en = get_post_meta( $id, '_djv_title_en', true ) ?: get_the_title( $post );
		$title_te = get_post_meta( $id, '_djv_title_te', true );
		$title_hi = get_post_meta( $id, '_djv_title_hi', true );

		$display_title = $title_en;
		if ( $lang === 'te' && ! empty( $title_te ) ) {
			$display_title = $title_te;
		} elseif ( $lang === 'hi' && ! empty( $title_hi ) ) {
			$display_title = $title_hi;
		}

		$intro_en = get_post_meta( $id, '_djv_intro_en', true ) ?: apply_filters( 'the_content', $post->post_content );
		$intro_te = get_post_meta( $id, '_djv_intro_te', true );
		$intro_hi = get_post_meta( $id, '_djv_intro_hi', true );

		$display_intro = $intro_en;
		if ( $lang === 'te' && ! empty( $intro_te ) ) {
			$display_intro = $intro_te;
		} elseif ( $lang === 'hi' && ! empty( $intro_hi ) ) {
			$display_intro = $intro_hi;
		}

		return self::respond( [
			'id'               => $id,
			'slug'             => $post->post_name,
			'title'            => $display_title,
			'title_en'         => $title_en,
			'title_te'         => $title_te,
			'title_hi'         => $title_hi,
			'intro'            => $display_intro,
			'intro_en'         => $intro_en,
			'intro_te'         => $intro_te,
			'intro_hi'         => $intro_hi,
			'duration'         => get_post_meta( $id, '_djv_duration', true ),
			'samagri'          => get_post_meta( $id, '_djv_samagri', true ),
			'preparation'      => get_post_meta( $id, '_djv_preparation', true ),
			'sankalpam'        => get_post_meta( $id, '_djv_sankalpam', true ),
			'kalasha_sthapana' => get_post_meta( $id, '_djv_kalasha_sthapana', true ),
			'avahanam'         => get_post_meta( $id, '_djv_avahanam', true ),
			'dhyana'           => get_post_meta( $id, '_djv_dhyana', true ),
			'main_puja'        => get_post_meta( $id, '_djv_main_puja', true ),
			'mantra_japa'      => get_post_meta( $id, '_djv_mantra_japa', true ),
			'naivedyam'        => get_post_meta( $id, '_djv_naivedyam', true ),
			'aarti'            => get_post_meta( $id, '_djv_aarti', true ),
			'prarthana'        => get_post_meta( $id, '_djv_prarthana', true ),
			'prasadam'         => get_post_meta( $id, '_djv_prasadam', true ),
			'visarjan'         => get_post_meta( $id, '_djv_visarjan', true ),
			'vrat_rules'       => get_post_meta( $id, '_djv_vrat_rules', true ),
			'faq'              => get_post_meta( $id, '_djv_faq', true ) ?: [],
			'related_mantras'  => get_post_meta( $id, '_djv_related_mantras', true ) ?: [],
			'related_festivals'=> get_post_meta( $id, '_djv_related_festivals', true ) ?: [],
			'categories'       => wp_get_post_terms( $id, 'djv_pooja_cat', [ 'fields' => 'names' ] ),
			'deity'            => wp_get_post_terms( $id, 'djv_deity', [ 'fields' => 'names' ] ),
			'link'             => get_permalink( $post ),
			'updated'          => $post->post_modified,
		], [ 'language' => $lang ] );
	}

	/**
	 * GET /djv/v1/mantras
	 */
	public static function get_mantras( WP_REST_Request $req ): WP_REST_Response {
		$deity_param    = sanitize_text_field( $req->get_param( 'deity' ) );
		$category_param = sanitize_text_field( $req->get_param( 'category' ) );
		$search_param   = sanitize_text_field( $req->get_param( 'search' ) ?: $req->get_param( 's' ) );
		$featured_param = $req->get_param( 'featured' );
		$popular_param  = $req->get_param( 'popular' );
		$page           = max( 1, intval( $req->get_param( 'page' ) ?: 1 ) );
		$per_page_raw   = $req->get_param( 'per_page' );
		$per_page       = $per_page_raw !== null && $per_page_raw !== '' ? min( max( 1, intval( $per_page_raw ) ), 100 ) : 24;
		$lang           = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$args = [
			'post_type'      => 'djv_mantra',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		];

		if ( $search_param ) {
			$args['s'] = $search_param;
		}

		$tax_query = [];
		if ( $category_param && strtolower( $category_param ) !== 'all' ) {
			$cat_slug = sanitize_title( $category_param );
			$tax_query[] = [
				'relation' => 'OR',
				[
					'taxonomy' => 'djv_mantra_cat',
					'field'    => 'slug',
					'terms'    => [ $cat_slug, strtolower( $category_param ) ],
				],
				[
					'taxonomy' => 'djv_deity',
					'field'    => 'slug',
					'terms'    => [ $cat_slug, strtolower( $category_param ) ],
				],
			];
		}

		if ( $deity_param && strtolower( $deity_param ) !== 'all' ) {
			$tax_query[] = [
				'taxonomy' => 'djv_deity',
				'field'    => 'slug',
				'terms'    => [ sanitize_title( $deity_param ), strtolower( $deity_param ) ],
			];
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		$meta_query = [];
		if ( $featured_param !== null && $featured_param !== '' && $featured_param !== '0' && $featured_param !== 'false' ) {
			$meta_query[] = [
				'key'   => '_djv_is_featured',
				'value' => '1',
			];
		}
		if ( $popular_param !== null && $popular_param !== '' && $popular_param !== '0' && $popular_param !== 'false' ) {
			$meta_query[] = [
				'key'   => '_djv_is_popular',
				'value' => '1',
			];
		}
		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		$query = new WP_Query( $args );
		$posts = $query->posts;

		$mantras = array_map( function( $post ) use ( $lang ) {
			return self::format_mantra_data( $post, false, $lang );
		}, $posts );

		return self::respond( $mantras, [
			'count'       => count( $mantras ),
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => $page,
			'per_page'    => $per_page,
			'language'    => $lang,
		] );
	}

	/**
	 * GET /djv/v1/mantras/{slug}
	 */
	public static function get_mantra_single( WP_REST_Request $req ): WP_REST_Response {
		$slug = sanitize_title( $req->get_param( 'slug' ) );
		$lang = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$post = get_page_by_path( $slug, OBJECT, 'djv_mantra' );

		if ( ! $post || $post->post_status !== 'publish' ) {
			return self::respond_error( __( 'Mantra not found.', 'djv-core' ), 404, 'not_found' );
		}

		return self::respond( self::format_mantra_data( $post, true, $lang ), [ 'language' => $lang ] );
	}

	/**
	 * Format single mantra item with rich metadata.
	 */
	private static function format_mantra_data( WP_Post $post, bool $detailed = false, string $lang = 'en' ): array {
		$id        = $post->ID;
		$deity     = get_post_meta( $id, '_djv_deity', true );
		if ( empty( $deity ) ) {
			$deity_terms = wp_get_post_terms( $id, 'djv_deity', [ 'fields' => 'names' ] );
			$deity = ! empty( $deity_terms ) ? $deity_terms[0] : '';
		}
		$categories = wp_get_post_terms( $id, 'djv_mantra_cat', [ 'fields' => 'names' ] );

		$title_en = get_the_title( $post );
		$title_te = get_post_meta( $id, '_djv_telugu_title', true );
		$title_hi = get_post_meta( $id, '_djv_hindi_title', true );

		$display_title = $title_en;
		if ( $lang === 'te' && ! empty( $title_te ) ) {
			$display_title = $title_te;
		} elseif ( $lang === 'hi' && ! empty( $title_hi ) ) {
			$display_title = $title_hi;
		}

		$sanskrit = get_post_meta( $id, '_djv_sanskrit_text', true ) ?: get_post_meta( $id, '_djv_original_text', true );

		$meaning_en = get_post_meta( $id, '_djv_meaning', true );
		$meaning_te = get_post_meta( $id, '_djv_meaning_te', true );
		$meaning_hi = get_post_meta( $id, '_djv_meaning_hi', true );

		$display_meaning = $meaning_en;
		if ( $lang === 'te' && ! empty( $meaning_te ) ) {
			$display_meaning = $meaning_te;
		} elseif ( $lang === 'hi' && ! empty( $meaning_hi ) ) {
			$display_meaning = $meaning_hi;
		}

		$data = [
			'id'              => $id,
			'slug'            => $post->post_name,
			'title'           => $display_title,
			'title_en'        => $title_en,
			'title_te'        => $title_te,
			'title_hi'        => $title_hi,
			'telugu_title'    => $title_te,
			'deity'           => $deity,
			'categories'      => $categories,
			'excerpt'         => get_the_excerpt( $post ),
			'sanskrit_text'   => $sanskrit,
			'telugu_text'     => get_post_meta( $id, '_djv_telugu_text', true ),
			'transliteration' => get_post_meta( $id, '_djv_transliteration', true ),
			'meaning'         => $display_meaning,
			'chant_count'     => get_post_meta( $id, '_djv_chant_count', true ),
			'best_time'       => get_post_meta( $id, '_djv_best_time', true ),
			'is_featured'     => get_post_meta( $id, '_djv_is_featured', true ) === '1',
			'is_popular'      => get_post_meta( $id, '_djv_is_popular', true ) === '1',
			'featured_image'  => get_the_post_thumbnail_url( $id, 'medium' ) ?: null,
			'audio_url'       => get_post_meta( $id, '_djv_audio_url', true ) ?: get_post_meta( $id, '_djv_audio', true ),
			'link'            => get_permalink( $post ),
		];

		if ( $detailed ) {
			$data['content']        = apply_filters( 'the_content', $post->post_content );
			$data['how_to_chant']   = get_post_meta( $id, '_djv_how_to_chant', true );
			$data['significance']   = get_post_meta( $id, '_djv_significance', true );
			$data['benefits']       = get_post_meta( $id, '_djv_benefits', true );
			$data['faq']            = get_post_meta( $id, '_djv_faq', true ) ?: [];
			$data['seo_title']      = get_post_meta( $id, '_djv_seo_title', true );
			$data['meta_description']= get_post_meta( $id, '_djv_meta_description', true );
			$data['focus_keyword']  = get_post_meta( $id, '_djv_focus_keyword', true );
		}

		return $data;
	}

	/**
	 * GET /djv/v1/temples
	 */
	public static function get_temples( WP_REST_Request $req ): WP_REST_Response {
		$state     = sanitize_text_field( $req->get_param( 'state' ) );
		$district  = sanitize_text_field( $req->get_param( 'district' ) );
		$city      = sanitize_text_field( $req->get_param( 'city' ) );
		$deity     = sanitize_key( $req->get_param( 'deity' ) );
		$category  = sanitize_key( $req->get_param( 'category' ) );
		$tradition = sanitize_key( $req->get_param( 'tradition' ) );
		$search    = sanitize_text_field( $req->get_param( 'search' ) ?: $req->get_param( 's' ) );
		$page      = max( 1, intval( $req->get_param( 'page' ) ?: 1 ) );
		$per_page  = min( max( 1, intval( $req->get_param( 'per_page' ) ?: 20 ) ), 100 );
		$lang      = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$user_lat = $req->get_param( 'lat' ) !== null ? floatval( $req->get_param( 'lat' ) ) : null;
		$user_lon = $req->get_param( 'lon' ) !== null ? floatval( $req->get_param( 'lon' ) ) : null;
		$radius   = $req->get_param( 'radius' ) !== null ? floatval( $req->get_param( 'radius' ) ) : null;

		$args = [
			'post_type'      => 'djv_temple',
			'posts_per_page' => ( $radius !== null || $user_lat !== null ) ? 100 : $per_page,
			'paged'          => ( $radius !== null || $user_lat !== null ) ? 1 : $page,
			'post_status'    => 'publish',
		];

		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		$tax_query = [];
		if ( $state ) {
			$tax_query[] = [
				'relation' => 'OR',
				[ 'taxonomy' => 'djv_state', 'field' => 'slug', 'terms' => sanitize_title( $state ) ],
				[ 'taxonomy' => 'djv_region', 'field' => 'slug', 'terms' => sanitize_title( $state ) ],
			];
		}
		if ( $deity ) {
			$tax_query[] = [ 'taxonomy' => 'djv_deity', 'field' => 'slug', 'terms' => $deity ];
		}
		if ( $category ) {
			$tax_query[] = [ 'taxonomy' => 'djv_temple_category', 'field' => 'slug', 'terms' => $category ];
		}
		if ( $tradition ) {
			$tax_query[] = [ 'taxonomy' => 'djv_tradition', 'field' => 'slug', 'terms' => $tradition ];
		}
		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = array_merge( [ 'relation' => 'AND' ], $tax_query );
		}

		$meta_query = [];
		if ( $district ) {
			$meta_query[] = [ 'key' => '_djv_district', 'value' => $district, 'compare' => 'LIKE' ];
		}
		if ( $city ) {
			$meta_query[] = [ 'key' => '_djv_city', 'value' => $city, 'compare' => 'LIKE' ];
		}
		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = array_merge( [ 'relation' => 'AND' ], $meta_query );
		}

		$query = new WP_Query( $args );
		$temples = [];

		foreach ( $query->posts as $post ) {
			$formatted = self::format_temple_payload( $post, $lang, $user_lat, $user_lon, false );

			if ( $radius !== null && $user_lat !== null && $user_lon !== null ) {
				if ( isset( $formatted['distance_km'] ) && $formatted['distance_km'] > $radius ) {
					continue;
				}
			}

			$temples[] = $formatted;
		}

		// Sort by distance if user coordinates were provided
		if ( $user_lat !== null && $user_lon !== null ) {
			usort( $temples, function( $a, $b ) {
				$da = $a['distance_km'] ?? 999999;
				$db = $b['distance_km'] ?? 999999;
				return $da <=> $db;
			} );

			// Slice if needed for pagination
			if ( count( $temples ) > $per_page ) {
				$temples = array_slice( $temples, ( $page - 1 ) * $per_page, $per_page );
			}
		}

		return self::respond( $temples, [
			'count'        => count( $temples ),
			'total'        => $query->found_posts,
			'total_pages'  => (int) $query->max_num_pages,
			'current_page' => $page,
			'page'         => $page,
			'per_page'     => $per_page,
			'items'        => $temples,
			'language'     => $lang,
			'user_coords'  => ( $user_lat !== null && $user_lon !== null ) ? [ 'lat' => $user_lat, 'lon' => $user_lon ] : null,
		] );
	}

	/**
	 * GET /djv/v1/temples/(?P<slug>[a-z0-9-]+)
	 */
	public static function get_temple_single( WP_REST_Request $req ): WP_REST_Response {
		$slug = sanitize_title( $req->get_param( 'slug' ) );
		$lang = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$user_lat = $req->get_param( 'lat' ) !== null ? floatval( $req->get_param( 'lat' ) ) : null;
		$user_lon = $req->get_param( 'lon' ) !== null ? floatval( $req->get_param( 'lon' ) ) : null;

		$post = get_page_by_path( $slug, OBJECT, 'djv_temple' );
		if ( ! $post ) {
			return self::respond( [ 'message' => 'Temple not found' ], [ 'slug' => $slug ], 404 );
		}

		$payload = self::format_temple_payload( $post, $lang, $user_lat, $user_lon, true );

		return self::respond( $payload, [ 'language' => $lang ] );
	}

	/**
	 * GET /djv/v1/temples/nearby
	 */
	public static function get_temples_nearby( WP_REST_Request $req ): WP_REST_Response {
		$lat    = floatval( $req->get_param( 'lat' ) ?: 17.3850 );
		$lon    = floatval( $req->get_param( 'lon' ) ?: 78.4867 );
		$radius = floatval( $req->get_param( 'radius' ) ?: 150.0 );
		$limit  = min( max( 1, intval( $req->get_param( 'limit' ) ?: 12 ) ), 50 );
		$lang   = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$posts = get_posts( [
			'post_type'      => 'djv_temple',
			'posts_per_page' => 100,
			'post_status'    => 'publish',
		] );

		$list = [];
		foreach ( $posts as $p ) {
			$formatted = self::format_temple_payload( $p, $lang, $lat, $lon, false );
			if ( isset( $formatted['distance_km'] ) && $formatted['distance_km'] <= $radius ) {
				$list[] = $formatted;
			}
		}

		usort( $list, function( $a, $b ) {
			return ( $a['distance_km'] ?? 999999 ) <=> ( $b['distance_km'] ?? 999999 );
		} );

		$results = array_slice( $list, 0, $limit );

		return self::respond( $results, [
			'count'        => count( $results ),
			'origin'       => [ 'lat' => $lat, 'lon' => $lon ],
			'radius_km'    => $radius,
			'language'     => $lang,
		] );
	}

	/**
	 * Comprehensive Formatter for Temple payload adhering to strict language fallback
	 */
	public static function format_temple_payload( WP_Post $post, string $lang = 'en', ?float $user_lat = null, ?float $user_lon = null, bool $detailed = false ): array {
		$id      = $post->ID;
		$name_en = get_the_title( $post );
		$name_te = get_post_meta( $id, '_djv_name_te', true ) ?: get_post_meta( $id, '_djv_title_te', true );
		$name_hi = get_post_meta( $id, '_djv_name_hi', true ) ?: get_post_meta( $id, '_djv_title_hi', true );

		$display_name = $name_en;
		if ( $lang === 'te' && ! empty( $name_te ) ) {
			$display_name = $name_te;
		} elseif ( $lang === 'hi' && ! empty( $name_hi ) ) {
			$display_name = $name_hi;
		}

		$t_lat = get_post_meta( $id, '_djv_lat', true );
		$t_lon = get_post_meta( $id, '_djv_lon', true );
		$lat_val = ( $t_lat !== '' && $t_lat !== false ) ? (float) $t_lat : null;
		$lon_val = ( $t_lon !== '' && $t_lon !== false ) ? (float) $t_lon : null;

		$dist_km = null;
		$bearing = null;
		$compass = null;
		$direction_sentence = null;

		if ( $user_lat !== null && $user_lon !== null && $lat_val !== null && $lon_val !== null ) {
			if ( class_exists( 'DJV_Temple_Master' ) ) {
				$dist_km = DJV_Temple_Master::haversine_distance( $user_lat, $user_lon, $lat_val, $lon_val );
				$bearing = DJV_Temple_Master::calculate_bearing( $user_lat, $user_lon, $lat_val, $lon_val );
				$compass = DJV_Temple_Master::bearing_to_compass( $bearing, $lang );
				$direction_sentence = DJV_Temple_Master::format_direction_sentence( $dist_km, $compass['code'], $lang );
			}
		}

		$item = [
			'id'          => $id,
			'slug'        => $post->post_name,
			'name'        => $display_name,
			'name_en'     => $name_en,
			'name_te'     => $name_te,
			'name_hi'     => $name_hi,
			'deity'       => get_post_meta( $id, '_djv_deity', true ) ?: implode( ', ', wp_get_post_terms( $id, 'djv_deity', [ 'fields' => 'names' ] ) ),
			'category'    => get_post_meta( $id, '_djv_category', true ) ?: implode( ', ', wp_get_post_terms( $id, 'djv_temple_category', [ 'fields' => 'names' ] ) ),
			'tradition'   => get_post_meta( $id, '_djv_tradition', true ) ?: implode( ', ', wp_get_post_terms( $id, 'djv_tradition', [ 'fields' => 'names' ] ) ),
			'state'       => get_post_meta( $id, '_djv_state', true ),
			'district'    => get_post_meta( $id, '_djv_district', true ),
			'city'        => get_post_meta( $id, '_djv_city', true ),
			'address'     => get_post_meta( $id, '_djv_address', true ),
			'pincode'     => get_post_meta( $id, '_djv_pincode', true ),
			'coordinates' => [
				'lat' => $lat_val,
				'lon' => $lon_val,
			],
			'distance_km'          => $dist_km,
			'bearing_deg'          => $bearing,
			'compass_direction'    => $compass ? $compass['code'] : null,
			'compass_label'        => $compass ? $compass['label'] : null,
			'direction_sentence'   => $direction_sentence,
			'timings'              => get_post_meta( $id, '_djv_timings', true ),
			'morning_open'         => get_post_meta( $id, '_djv_morning_open', true ),
			'morning_close'        => get_post_meta( $id, '_djv_morning_close', true ),
			'evening_open'         => get_post_meta( $id, '_djv_evening_open', true ),
			'evening_close'        => get_post_meta( $id, '_djv_evening_close', true ),
			'dress_code'           => get_post_meta( $id, '_djv_dress_code', true ),
			'verification_status'  => get_post_meta( $id, '_djv_verification_status', true ) ?: 'Verified',
			'source_name'          => get_post_meta( $id, '_djv_source_name', true ) ?: 'Dharma Jyothi Vedika',
			'source_url'           => get_post_meta( $id, '_djv_source_url', true ) ?: '',
			'source_type'          => get_post_meta( $id, '_djv_source_type', true ) ?: 'official_directory',
			'link'                 => get_permalink( $post ),
			'thumbnail'            => get_the_post_thumbnail_url( $post, 'medium' ) ?: false,
		];

		if ( $detailed ) {
			// Multilingual content selection with strict fallback
			$about = get_post_meta( $id, '_djv_about_en', true ) ?: $post->post_content;
			if ( $lang === 'te' && get_post_meta( $id, '_djv_about_te', true ) ) {
				$about = get_post_meta( $id, '_djv_about_te', true );
			} elseif ( $lang === 'hi' && get_post_meta( $id, '_djv_about_hi', true ) ) {
				$about = get_post_meta( $id, '_djv_about_hi', true );
			}

			$history = get_post_meta( $id, '_djv_history_en', true );
			if ( $lang === 'te' && get_post_meta( $id, '_djv_history_te', true ) ) {
				$history = get_post_meta( $id, '_djv_history_te', true );
			} elseif ( $lang === 'hi' && get_post_meta( $id, '_djv_history_hi', true ) ) {
				$history = get_post_meta( $id, '_djv_history_hi', true );
			}

			$purana = get_post_meta( $id, '_djv_sthala_purana_en', true );
			if ( $lang === 'te' && get_post_meta( $id, '_djv_sthala_purana_te', true ) ) {
				$purana = get_post_meta( $id, '_djv_sthala_purana_te', true );
			} elseif ( $lang === 'hi' && get_post_meta( $id, '_djv_sthala_purana_hi', true ) ) {
				$purana = get_post_meta( $id, '_djv_sthala_purana_hi', true );
			}

			$item['about']           = $about;
			$item['history']         = $history;
			$item['sthala_purana']   = $purana;
			$item['railway']         = get_post_meta( $id, '_djv_railway', true );
			$item['airport']         = get_post_meta( $id, '_djv_airport', true );
			$item['bus_station']     = get_post_meta( $id, '_djv_bus_station', true );
			$item['highway']         = get_post_meta( $id, '_djv_highway', true );
			$item['website']             = get_post_meta( $id, '_djv_website', true );
			$item['contact']             = get_post_meta( $id, '_djv_contact', true );
			$item['trust_name']          = get_post_meta( $id, '_djv_trust_name', true );
			$item['official_source']     = get_post_meta( $id, '_djv_official_source', true );
			$item['last_verified']       = get_post_meta( $id, '_djv_last_verified', true );
			$item['source_name']         = get_post_meta( $id, '_djv_source_name', true ) ?: 'Dharma Jyothi Vedika';
			$item['source_url']          = get_post_meta( $id, '_djv_source_url', true ) ?: '';
			$item['source_type']         = get_post_meta( $id, '_djv_source_type', true ) ?: 'official_directory';
			$item['architecture']        = get_post_meta( $id, '_djv_architecture', true ) ?: '';
			$item['visiting_guide']      = get_post_meta( $id, '_djv_visiting_guide', true ) ?: '';
			$item['scripture_reference'] = get_post_meta( $id, '_djv_scripture_reference', true ) ?: '';
			$item['maps_url']            = ( $lat_val && $lon_val ) ? "https://www.google.com/maps/dir/?api=1&destination={$lat_val},{$lon_val}" : '';
		}

		return $item;
	}

	/**
	 * GET /djv/v1/articles
	 */
	public static function get_articles( WP_REST_Request $req ): WP_REST_Response {
		$category = sanitize_text_field( $req->get_param( 'category' ) );
		$per_page = min( intval( $req->get_param( 'per_page' ) ?: 10 ), 50 );
		$lang     = sanitize_key( $req->get_param( 'language' ) ?: ( $req->get_param( 'lang' ) ?: 'en' ) );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		$args = [
			'post_type'      => 'post',
			'posts_per_page' => $per_page,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if ( $category ) {
			$args['category_name'] = $category;
		}

		$posts = get_posts( $args );

		$articles = array_map( function( $post ) use ( $lang ) {
			$id       = $post->ID;
			$title_en = get_the_title( $post );
			$title_te = get_post_meta( $id, '_djv_title_te', true );
			$title_hi = get_post_meta( $id, '_djv_title_hi', true );

			$display_title = $title_en;
			if ( $lang === 'te' && ! empty( $title_te ) ) {
				$display_title = $title_te;
			} elseif ( $lang === 'hi' && ! empty( $title_hi ) ) {
				$display_title = $title_hi;
			}

			return [
				'id'           => $id,
				'slug'         => $post->post_name,
				'title'        => $display_title,
				'title_en'     => $title_en,
				'title_te'     => $title_te,
				'title_hi'     => $title_hi,
				'excerpt'      => get_the_excerpt( $post ),
				'author'       => get_the_author_meta( 'display_name', $post->post_author ),
				'published'    => $post->post_date,
				'updated'      => $post->post_modified,
				'reading_time' => ceil( str_word_count( strip_tags( $post->post_content ) ) / 200 ),
				'thumbnail'    => get_the_post_thumbnail_url( $post, 'medium' ),
				'link'         => get_permalink( $post ),
				'categories'   => wp_get_post_categories( $post->ID, [ 'fields' => 'names' ] ),
			];
		}, $posts );

		return self::respond( $articles, [ 'count' => count( $articles ), 'language' => $lang ] );
	}

	/**
	 * GET /djv/v1/services — Vedic Services list
	 */
	public static function get_services( WP_REST_Request $req ): WP_REST_Response {
		$posts = get_posts([
			'post_type'      => 'djv_service',
			'posts_per_page' => (int) ($req->get_param( 'per_page' ) ?: 20),
			'post_status'    => 'publish',
		]);

		$services = array_map( function( WP_Post $p ) {
			return [
				'id'           => $p->ID,
				'slug'         => $p->post_name,
				'title'        => get_the_title( $p ),
				'excerpt'      => get_the_excerpt( $p ),
				'content'      => apply_filters( 'the_content', $p->post_content ),
				'service_type' => get_post_meta( $p->ID, '_djv_service_type', true ),
				'price'        => get_post_meta( $p->ID, '_djv_price', true ),
				'duration'     => get_post_meta( $p->ID, '_djv_duration', true ),
				'contact'      => get_post_meta( $p->ID, '_djv_contact', true ),
				'thumbnail'    => get_the_post_thumbnail_url( $p, 'medium' ),
				'link'         => get_permalink( $p ),
			];
		}, $posts );

		return self::respond( $services, [ 'count' => count( $services ) ] );
	}

	/**
	 * GET /djv/v1/search — Unified Search across all DJV post types
	 */
	public static function search_all( WP_REST_Request $req ): WP_REST_Response {
		$s = sanitize_text_field( $req->get_param( 's' ) );
		if ( empty( $s ) ) {
			return self::respond( [], [ 'count' => 0 ] );
		}

		$query = new WP_Query([
			's'              => $s,
			'post_type'      => [ 'post', 'djv_festival', 'djv_temple', 'djv_pooja', 'djv_mantra', 'djv_muhurtham', 'djv_service' ],
			'posts_per_page' => (int) ($req->get_param( 'per_page' ) ?: 20),
			'post_status'    => 'publish',
		]);

		$results = [];
		foreach ( $query->posts as $p ) {
			$results[] = [
				'id'        => $p->ID,
				'slug'      => $p->post_name,
				'title'     => get_the_title( $p ),
				'excerpt'   => get_the_excerpt( $p ),
				'post_type' => $p->post_type,
				'thumbnail' => get_the_post_thumbnail_url( $p, 'medium' ),
				'link'      => get_permalink( $p ),
			];
		}

		return self::respond( $results, [ 'count' => count( $results ) ] );
	}

	/**
	 * GET /djv/v1/today — Aggregated endpoint for home screen
	 */
	public static function get_today( WP_REST_Request $req ): WP_REST_Response {
		$date = date( 'Y-m-d' );
		$lat  = floatval( $req->get_param( 'latitude' ) );
		$lon  = floatval( $req->get_param( 'longitude' ) );
		$tz   = sanitize_text_field( $req->get_param( 'timezone' ) );

		// Panchangam
		$panchangam_engine = new DJV_Panchangam();
		$panchangam = $panchangam_engine->get_panchangam( $date, $lat, $lon, $tz );
		if ( is_wp_error( $panchangam ) ) {
			$err_data = $panchangam->get_error_data();
			$status   = is_array( $err_data ) && isset( $err_data['status'] ) ? (int) $err_data['status'] : 500;
			$details  = is_array( $err_data ) && isset( $err_data['details'] ) ? $err_data['details'] : null;
			return self::respond_error( $panchangam->get_error_message(), $status, $panchangam->get_error_code(), $details );
		}

		// Today's festivals
		$posts = get_posts( [
			'post_type'      => 'djv_festival',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );
		$festivals = array_map( [ __CLASS__, 'format_festival_post' ], $posts );
		$today_festivals = array_filter( $festivals, function($f) use ($date) {
			return ( $f['date'] ?? '' ) === $date;
		} );

		// Daily mantra (cycle through based on day of year)
		$mantra_count = (int) (wp_count_posts('djv_mantra')->publish ?? 0);
		$daily_mantra = null;
		if ( $mantra_count > 0 ) {
			$day_of_year = (int) date( 'z', strtotime( $date ) );
			$offset = $day_of_year % $mantra_count;
			$mantra_posts = get_posts([
				'post_type'      => 'djv_mantra',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'offset'         => $offset,
			]);
			if ( ! empty( $mantra_posts ) ) {
				$p = $mantra_posts[0];
				$daily_mantra = [
					'id'              => $p->ID,
					'slug'            => $p->post_name,
					'title'           => get_the_title( $p ),
					'original_text'   => get_post_meta( $p->ID, '_djv_original_text', true ),
					'transliteration' => get_post_meta( $p->ID, '_djv_transliteration', true ),
					'meaning'         => get_post_meta( $p->ID, '_djv_meaning', true ),
					'audio_url'       => get_post_meta( $p->ID, '_djv_audio_url', true ),
					'deity'           => wp_get_post_terms( $p->ID, 'djv_deity', [ 'fields' => 'names' ] ),
					'link'            => get_permalink( $p ),
				];
			}
		}

		// Latest articles (3)
		$articles = self::get_articles( new WP_REST_Request() )->get_data()['data'] ?? [];

		$meta = $panchangam['meta'] ?? [];
		unset($panchangam['meta']);

		return self::respond( [
			'date'            => $date,
			'panchangam'      => $panchangam,
			'festivals_today' => array_values( $today_festivals ),
			'daily_mantra'    => $daily_mantra,
			'latest_articles' => array_slice( $articles, 0, 3 ),
		], $meta );
	}

	/**
	 * POST /djv/v1/admin/precalculate
	 */
	public static function admin_precalculate( WP_REST_Request $req ): WP_REST_Response {
		return self::respond_error( 'Precalculation is handled internally by DJV_Panchangam and WP-Cron.', 501, 'not_implemented' );
	}

	/* ─── Helpers ─────────────────────────────────────────────── */

	private static function format_festival_post( WP_Post $post ): array {
		$id = $post->ID;
		return [
			'id'              => $id,
			'slug'            => $post->post_name,
			'title'           => get_the_title( $post ),
			'title_en'        => get_post_meta( $id, '_djv_title_en', true ) ?: get_the_title( $post ),
			'title_te'        => get_post_meta( $id, '_djv_title_te', true ) ?: get_post_meta( $id, '_djv_telugu_name', true ),
			'title_hi'        => get_post_meta( $id, '_djv_title_hi', true ),
			'excerpt'         => get_the_excerpt( $post ),
			'content_en'      => get_post_meta( $id, '_djv_content_en', true ) ?: apply_filters( 'the_content', $post->post_content ),
			'content_te'      => get_post_meta( $id, '_djv_content_te', true ),
			'content_hi'      => get_post_meta( $id, '_djv_content_hi', true ),
			'significance'    => get_post_meta( $id, '_djv_significance', true ),
			'history'         => get_post_meta( $id, '_djv_history', true ),
			'puja_timings'    => get_post_meta( $id, '_djv_puja_timings', true ),
			'samagri'         => get_post_meta( $id, '_djv_samagri', true ),
			'naivedyam'       => get_post_meta( $id, '_djv_naivedyam', true ),
			'vrat_rules'      => get_post_meta( $id, '_djv_vrat_rules', true ),
			'dos'             => get_post_meta( $id, '_djv_dos', true ),
			'donts'           => get_post_meta( $id, '_djv_donts', true ),
			'date'            => get_post_meta( $id, '_djv_festival_date', true ),
			'tithi_rule'      => get_post_meta( $id, '_djv_tithi_rule', true ),
			'month'           => get_post_meta( $id, '_djv_month', true ),
			'is_major'        => (bool) get_post_meta( $id, '_djv_is_major', true ),
			'is_telugu'       => (bool) get_post_meta( $id, '_djv_is_telugu', true ),
			'categories'      => wp_get_post_terms( $id, 'djv_festival_cat', [ 'fields' => 'names' ] ),
			'deity'           => wp_get_post_terms( $id, 'djv_deity', [ 'fields' => 'names' ] ),
			'related_poojas'  => get_post_meta( $id, '_djv_related_poojas', true ) ?: [],
			'related_mantras' => get_post_meta( $id, '_djv_related_mantras', true ) ?: [],
			'thumbnail'       => get_the_post_thumbnail_url( $id, 'large' ),
			'link'            => get_permalink( $post ),
			'updated'         => $post->post_modified,
		];
	}
}
