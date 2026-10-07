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
				'year'  => [ 'type' => 'integer', 'default' => date('Y') ],
				'month' => [ 'type' => 'integer', 'default' => 0 ],
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
		] );

		register_rest_route( $ns, '/pooja/(?P<slug>[a-z0-9-]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_pooja_single' ],
			'permission_callback' => '__return_true',
		] );

		// ── Mantras ───────────────────────────────────────────────
		register_rest_route( $ns, '/mantras', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_mantras' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'deity'    => [ 'type' => 'string', 'default' => '' ],
				'language' => [ 'type' => 'string', 'default' => 'en' ],
				'per_page' => [ 'type' => 'integer', 'default' => 20, 'maximum' => 100 ],
			],
		] );

		// ── Temples ───────────────────────────────────────────────
		register_rest_route( $ns, '/temples', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_temples' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'state'   => [ 'type' => 'string', 'default' => '' ],
				'deity'   => [ 'type' => 'string', 'default' => '' ],
				'per_page'=> [ 'type' => 'integer', 'default' => 20, 'maximum' => 100 ],
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
		$language = sanitize_key( $req->get_param( 'language' ) );

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
		$year  = intval( $req->get_param( 'year' ) );
		$month = intval( $req->get_param( 'month' ) );

		// Validate year range
		if ( $year < 2020 || $year > 2100 ) {
			return self::respond_error( 'Year out of range (2020–2100)', 400 );
		}

		// Fetch from djv_festival CPT
		$posts = get_posts( [
			'post_type'      => 'djv_festival',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );

		$festivals = array_map( [ __CLASS__, 'format_festival_post' ], $posts );

		return self::respond( $festivals, [
			'year'  => $year,
			'month' => $month,
			'count' => count( $festivals )
		] );
	}

	/**
	 * GET /djv/v1/festivals/{slug}
	 */
	public static function get_festival_single( WP_REST_Request $req ): WP_REST_Response {
		$slug = sanitize_key( $req->get_param( 'slug' ) );

		$post = get_page_by_path( $slug, OBJECT, 'djv_festival' );
		if ( ! $post ) {
			return self::respond_error( 'Festival not found', 404, 'not_found' );
		}

		return self::respond( self::format_festival_post( $post ) );
	}

	/**
	 * GET /djv/v1/muhurtham
	 */
	public static function get_muhurtham( WP_REST_Request $req ): WP_REST_Response {
		$date = sanitize_text_field( $req->get_param( 'date' ) );
		$lat  = floatval( $req->get_param( 'latitude' ) );
		$lon  = floatval( $req->get_param( 'longitude' ) );
		$tz   = sanitize_text_field( $req->get_param( 'timezone' ) );

		$errors = self::validate_panchangam_request( $date, $lat, $lon, $tz );
		if ( ! empty( $errors ) ) {
			return self::respond_error( $errors, 400, 'validation_failed' );
		}

		$panchangam_engine = new DJV_Panchangam();
		$panchangam = $panchangam_engine->get_panchangam( $date, $lat, $lon, $tz );
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
		$posts = get_posts( [
			'post_type'      => 'djv_pooja',
			'posts_per_page' => 20,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		] );

		$pooja_list = array_map( function( $post ) {
			return [
				'id'      => $post->ID,
				'slug'    => $post->post_name,
				'title'   => get_the_title( $post ),
				'excerpt' => get_the_excerpt( $post ),
				'link'    => get_permalink( $post ),
				'icon'    => get_post_meta( $post->ID, '_djv_icon', true ),
				'deity'   => wp_get_post_terms( $post->ID, 'djv_deity', [ 'fields' => 'names' ] ),
			];
		}, $posts );

		return self::respond( $pooja_list, [ 'count' => count( $pooja_list ) ] );
	}

	/**
	 * GET /djv/v1/pooja/{slug}
	 */
	public static function get_pooja_single( WP_REST_Request $req ): WP_REST_Response {
		$slug = sanitize_key( $req->get_param( 'slug' ) );
		$post = get_page_by_path( $slug, OBJECT, 'djv_pooja' );

		if ( ! $post ) {
			return self::respond_error( 'Pooja guide not found', 404, 'not_found' );
		}

		return self::respond( [
			'id'          => $post->ID,
			'slug'        => $post->post_name,
			'title'       => get_the_title( $post ),
			'content'     => apply_filters( 'the_content', $post->post_content ),
			'samagri'     => get_post_meta( $post->ID, '_djv_samagri', true ),
			'mantras'     => get_post_meta( $post->ID, '_djv_mantras', true ),
			'naivedyam'   => get_post_meta( $post->ID, '_djv_naivedyam', true ),
			'deity'       => wp_get_post_terms( $post->ID, 'djv_deity', [ 'fields' => 'names' ] ),
			'updated'     => $post->post_modified,
		] );
	}

	/**
	 * GET /djv/v1/mantras
	 */
	public static function get_mantras( WP_REST_Request $req ): WP_REST_Response {
		$deity    = sanitize_key( $req->get_param( 'deity' ) );
		$language = sanitize_key( $req->get_param( 'language' ) );
		$per_page = min( intval( $req->get_param( 'per_page' ) ), 100 );

		$args = [
			'post_type'      => 'djv_mantra',
			'posts_per_page' => $per_page,
			'post_status'    => 'publish',
		];

		if ( $deity ) {
			$args['tax_query'] = [[
				'taxonomy' => 'djv_deity',
				'field'    => 'slug',
				'terms'    => $deity,
			]];
		}

		$posts = get_posts( $args );

		$mantras = array_map( function( $post ) use ( $language ) {
			return [
				'id'              => $post->ID,
				'slug'            => $post->post_name,
				'title'           => get_the_title( $post ),
				'original_text'   => get_post_meta( $post->ID, '_djv_original_text', true ),
				'transliteration' => get_post_meta( $post->ID, '_djv_transliteration', true ),
				'meaning'         => get_post_meta( $post->ID, '_djv_meaning', true ),
				'audio_url'       => get_post_meta( $post->ID, '_djv_audio_url', true ),
				'deity'           => wp_get_post_terms( $post->ID, 'djv_deity', [ 'fields' => 'names' ] ),
				'link'            => get_permalink( $post ),
			];
		}, $posts );

		return self::respond( $mantras, [ 'count' => count( $mantras ) ] );
	}

	/**
	 * GET /djv/v1/temples
	 */
	public static function get_temples( WP_REST_Request $req ): WP_REST_Response {
		$state    = sanitize_text_field( $req->get_param( 'state' ) );
		$deity    = sanitize_key( $req->get_param( 'deity' ) );
		$per_page = min( intval( $req->get_param( 'per_page' ) ), 100 );

		$args = [
			'post_type'      => 'djv_temple',
			'posts_per_page' => $per_page,
			'post_status'    => 'publish',
		];

		$tax_query = [];
		if ( $state ) {
			$tax_query[] = [ 'taxonomy' => 'djv_region', 'field' => 'slug', 'terms' => sanitize_key( $state ) ];
		}
		if ( $deity ) {
			$tax_query[] = [ 'taxonomy' => 'djv_deity', 'field' => 'slug', 'terms' => $deity ];
		}
		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = array_merge( [ 'relation' => 'AND' ], $tax_query );
		}

		$posts = get_posts( $args );

		$temples = array_map( function( $post ) {
			return [
				'id'          => $post->ID,
				'slug'        => $post->post_name,
				'name'        => get_the_title( $post ),
				'deity'       => wp_get_post_terms( $post->ID, 'djv_deity', [ 'fields' => 'names' ] ),
				'address'     => get_post_meta( $post->ID, '_djv_address', true ),
				'coordinates' => [
					'lat' => get_post_meta( $post->ID, '_djv_lat', true ),
					'lon' => get_post_meta( $post->ID, '_djv_lon', true ),
				],
				'timings'     => get_post_meta( $post->ID, '_djv_timings', true ),
				'link'        => get_permalink( $post ),
				'thumbnail'   => get_the_post_thumbnail_url( $post, 'medium' ),
			];
		}, $posts );

		return self::respond( $temples, [ 'count' => count( $temples ) ] );
	}

	/**
	 * GET /djv/v1/articles
	 */
	public static function get_articles( WP_REST_Request $req ): WP_REST_Response {
		$category = sanitize_text_field( $req->get_param( 'category' ) );
		$per_page = min( intval( $req->get_param( 'per_page' ) ), 50 );

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

		$articles = array_map( function( $post ) {
			return [
				'id'           => $post->ID,
				'slug'         => $post->post_name,
				'title'        => get_the_title( $post ),
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

		return self::respond( $articles, [ 'count' => count( $articles ) ] );
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
		return [
			'id'          => $post->ID,
			'slug'        => $post->post_name,
			'name'        => get_the_title( $post ),
			'content'     => apply_filters( 'the_content', $post->post_content ),
			'significance'=> get_post_meta( $post->ID, '_djv_significance', true ),
			'puja_timings'=> get_post_meta( $post->ID, '_djv_puja_timings', true ),
			'samagri'     => get_post_meta( $post->ID, '_djv_samagri', true ),
			'naivedyam'   => get_post_meta( $post->ID, '_djv_naivedyam', true ),
			'mantras'     => get_post_meta( $post->ID, '_djv_mantras', true ),
			'date_rule'   => get_post_meta( $post->ID, '_djv_date_rule', true ),
			'thumbnail'   => get_the_post_thumbnail_url( $post, 'large' ),
			'link'        => get_permalink( $post ),
			'updated'     => $post->post_modified,
		];
	}
}
