<?php
/**
 * Dharma Jyothi Vedika — Theme Functions & Definitions
 *
 * @package DJV_Theme
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

define( 'DJV_THEME_VERSION', '1.0.1' );
define( 'DJV_THEME_DIR', get_template_directory() );
define( 'DJV_THEME_URI', get_template_directory_uri() );

/**
 * Theme Setup.
 */
function djv_theme_setup(): void {
	// Let WordPress manage the document title
	add_theme_support( 'title-tag' );

	// Enable featured images
	add_theme_support( 'post-thumbnails' );

	// Custom logo support
	add_theme_support( 'custom-logo', [
		'height'      => 60,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	// Switch default core markup to HTML5
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	] );

	// Register Navigation Menus
	register_nav_menus( [
		'primary' => __( 'Primary Header Menu', 'djv-theme' ),
		'footer'  => __( 'Footer Quick Links', 'djv-theme' ),
	] );
}
add_action( 'after_setup_theme', 'djv_theme_setup' );

/**
 * Disable WordPress Admin Bar on the Frontend.
 *
 * Ensures that logged-in users (including administrators) see the exact same
 * frontend website as public logged-out users, with zero admin bar markup,
 * zero extra classes on <body>, and zero top spacing shifts.
 *
 * The WordPress admin dashboard (wp-admin) remains unaffected and fully functional.
 */
add_filter( 'show_admin_bar', '__return_false' );
add_action( 'get_header', function() {
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
} );

/**
 * Enqueue Styles and Scripts.
 */
function djv_theme_enqueue_scripts(): void {
	// Google Fonts
	wp_enqueue_style(
		'djv-google-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Noto+Sans+Telugu:wght@300;400;500;600;700&display=swap',
		[],
		null
	);

	// Design System Tokens
	wp_enqueue_style(
		'djv-design-system',
		DJV_THEME_URI . '/assets/css/design-system.css',
		[],
		DJV_THEME_VERSION
	);

	// Main Platform Styles
	wp_enqueue_style(
		'djv-main-styles',
		DJV_THEME_URI . '/assets/css/main.css',
		[ 'djv-design-system' ],
		DJV_THEME_VERSION
	);

	// Theme Root Style
	wp_enqueue_style(
		'djv-theme-style',
		get_stylesheet_uri(),
		[ 'djv-main-styles' ],
		DJV_THEME_VERSION
	);

	// India Locations Database
	wp_enqueue_script(
		'djv-india-locations',
		DJV_THEME_URI . '/assets/js/india-locations.js',
		[],
		DJV_THEME_VERSION,
		true
	);

	// Main Theme Interactions (Header, Drawer, Modals, Search)
	wp_enqueue_script(
		'djv-theme-main',
		DJV_THEME_URI . '/assets/js/theme-main.js',
		[ 'djv-india-locations', 'wp-i18n' ],
		DJV_THEME_VERSION,
		true
	);

	// Panchangam Client Application (REST API Consumer)
	wp_enqueue_script(
		'djv-panchangam-app',
		DJV_THEME_URI . '/assets/js/theme-panchangam.js',
		[ 'djv-india-locations', 'djv-theme-main', 'wp-i18n' ],
		DJV_THEME_VERSION,
		true
	);

	// Localize configuration for the frontend REST API
	wp_localize_script( 'djv-panchangam-app', 'djvConfig', [
		'apiUrl'         => esc_url_raw( rest_url( 'djv/v1/' ) ),
		'homeUrl'        => esc_url_raw( home_url( '/' ) ),
		'themeUrl'       => esc_url_raw( DJV_THEME_URI ),
		'nonce'          => wp_create_nonce( 'wp_rest' ),
		'currentLang'    => function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en',
		'supportedLangs' => function_exists( 'djv_get_supported_languages' ) ? djv_get_supported_languages() : [ 'en', 'te', 'hi' ],
		'defaultLat'     => 17.3850,
		'defaultLon'     => 78.4867,
		'defaultTz'      => 'Asia/Kolkata',
		'defaultCity'    => 'Hyderabad',
		'defaultState'   => 'Telangana',
	] );
}
add_action( 'wp_enqueue_scripts', 'djv_theme_enqueue_scripts' );

/**
 * Server-Side Panchangam Fetch Helper for SSR.
 * Uses DJV_Panchangam class if available.
 *
 * @param string|null $date
 * @param float|null  $lat
 * @param float|null  $lon
 * @param string|null $tz
 * @param string|null $region
 * @param string|null $lang
 * @return array|null
 */
function djv_get_ssr_panchangam( ?string $date = null, ?float $lat = null, ?float $lon = null, ?string $tz = null, ?string $region = null, ?string $lang = null ): ?array {
	if ( ! class_exists( 'DJV_Panchangam' ) ) {
		return null;
	}

	$tz   = $tz ?: 'Asia/Kolkata';
	$lat  = $lat ?: 17.3850;
	$lon  = $lon ?: 78.4867;
	$lang = $lang ?: ( function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en' );

	if ( ! $date ) {
		try {
			$now  = new DateTimeImmutable( 'now', new DateTimeZone( $tz ) );
			$date = $now->format( 'Y-m-d' );
		} catch ( Exception $e ) {
			$date = date( 'Y-m-d' );
		}
	}

	$engine = new DJV_Panchangam();
	$res    = $engine->get_panchangam( $date, $lat, $lon, $tz, $region ?: 'telugu', $lang );

	if ( is_wp_error( $res ) ) {
		return null;
	}

	return $res;
}

/**
 * Safely format an ISO time or timestamp for SSR templates in the given timezone.
 *
 * @param string|int|null $time
 * @param string          $tz
 * @param string          $format
 * @return string
 */
function djv_format_ssr_time( $time, string $tz = 'Asia/Kolkata', string $format = 'g:i A' ): string {
	if ( empty( $time ) || $time === '—' ) {
		return '—';
	}
	if ( is_string( $time ) && ! preg_match( '/\d{4}-\d{2}-\d{2}|T|\d{2}:\d{2}/', $time ) ) {
		return $time;
	}
	try {
		$tz_obj = new DateTimeZone( $tz );
		if ( is_numeric( $time ) ) {
			$dt = new DateTimeImmutable( "@{$time}" );
			return $dt->setTimezone( $tz_obj )->format( $format );
		}
		$dt = new DateTimeImmutable( $time );
		return $dt->setTimezone( $tz_obj )->format( $format );
	} catch ( Exception $e ) {
		return (string) $time;
	}
}

/**
 * Safely format a timing period (e.g. Rahu Kalam) for SSR templates.
 *
 * @param array|string|null $period
 * @param string            $tz
 * @return string
 */
function djv_format_ssr_period( $period, string $tz = 'Asia/Kolkata' ): string {
	if ( empty( $period ) ) {
		return '—';
	}
	if ( is_string( $period ) ) {
		return $period;
	}
	if ( is_array( $period ) ) {
		if ( isset( $period[0] ) ) {
			return djv_format_ssr_period( $period[0], $tz );
		}
		if ( ! empty( $period['text'] ) ) {
			return $period['text'];
		}
		if ( ! empty( $period['startStr'] ) && ! empty( $period['endStr'] ) ) {
			return $period['startStr'] . ' – ' . $period['endStr'];
		}
		if ( ! empty( $period['start'] ) && ! empty( $period['end'] ) ) {
			$s = djv_format_ssr_time( $period['start'], $tz );
			$e = djv_format_ssr_time( $period['end'], $tz );
			if ( $s !== '—' && $e !== '—' ) {
				return $s . ' – ' . $e;
			}
		}
	}
	return '—';
}

/**
 * Safely format an array or single timing period into HTML lines.
 *
 * @param array|string|null $periods
 * @param string            $tz
 * @return string
 */
function djv_format_ssr_period_list( $periods, string $tz = 'Asia/Kolkata' ): string {
	if ( empty( $periods ) ) {
		return '—';
	}
	if ( is_string( $periods ) ) {
		return esc_html( $periods );
	}
	if ( is_array( $periods ) ) {
		if ( isset( $periods[0] ) ) {
			$slots = [];
			foreach ( $periods as $p ) {
				$formatted = djv_format_ssr_period( $p, $tz );
				if ( $formatted && $formatted !== '—' ) {
					$slots[] = $formatted;
				}
			}
			if ( empty( $slots ) ) {
				return '—';
			}
			if ( count( $slots ) === 1 ) {
				return esc_html( $slots[0] );
			}
			$html = '';
			foreach ( $slots as $slot ) {
				$html .= '<span class="timing-period-slot">' . esc_html( $slot ) . '</span>';
			}
			return $html;
		} else {
			return esc_html( djv_format_ssr_period( $periods, $tz ) );
		}
	}
	return '—';
}

/**
 * Load Global Trilingual Language Architecture (Section 2 & 6)
 */
require_once DJV_THEME_DIR . '/inc/language.php';

/**
 * Load Dynamic SEO & Schema.org Definitions
 */
require_once DJV_THEME_DIR . '/inc/seo-schema.php';

/**
 * Bulletproof routing fallback for Panchangam, Today, and Articles pages.
 * Ensures /panchangam/, /panchangam/today/, and /articles/ load their dedicated templates
 * even on fresh servers or environments where pages have not yet been manually created.
 */
function djv_theme_custom_route_fallback(): void {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$request_uri = trim( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?: '', '/' );
	$home_path   = trim( parse_url( home_url(), PHP_URL_PATH ) ?: '', '/' );

	if ( ! empty( $home_path ) && strpos( $request_uri, $home_path ) === 0 ) {
		$request_uri = trim( substr( $request_uri, strlen( $home_path ) ), '/' );
	}

	if ( $request_uri === 'panchangam' || $request_uri === 'panchangam/today' || $request_uri === 'today' ) {
		if ( is_404() || ! is_page() ) {
			status_header( 200 );
			$template = locate_template( [ 'page-panchangam-today.php', 'page-panchangam.php' ] );
			if ( $template ) {
				include $template;
				exit;
			}
		}
	}

	if ( $request_uri === 'articles' ) {
		if ( is_404() || ! is_page() ) {
			status_header( 200 );
			$template = locate_template( [ 'page-articles.php' ] );
			if ( $template ) {
				include $template;
				exit;
			}
		}
	}

	if ( $request_uri === 'mantras/mahamrityunjaya-mantra' ) {
		wp_safe_redirect( home_url( '/mantras/maha-mrityunjaya-mantra/' ), 301 );
		exit;
	}

	// Robust single festival fallback for canonical and alias URLs
	if ( preg_match( '#^festivals/([^/]+)/?$#', $request_uri, $matches ) ) {
		$raw_slug = sanitize_title( $matches[1] );
		if ( class_exists( 'DJV_Festival_Master' ) ) {
			$resolved_slug = DJV_Festival_Master::resolve_slug_alias( $raw_slug );
			$fest_post = get_page_by_path( $resolved_slug, OBJECT, 'djv_festival' );
			if ( $fest_post ) {
				global $wp_query, $post;
				$wp_query->is_404            = false;
				$wp_query->is_single         = true;
				$wp_query->is_singular       = true;
				$wp_query->post              = $fest_post;
				$wp_query->posts             = [ $fest_post ];
				$wp_query->post_count        = 1;
				$wp_query->queried_object    = $fest_post;
				$wp_query->queried_object_id = $fest_post->ID;
				$post = $fest_post;
				setup_postdata( $post );
				status_header( 200 );

				$template = locate_template( [ 'single-djv_festival.php', 'single-festival.php' ] );
				if ( $template ) {
					include $template;
					exit;
				}
			}
		}
	}
}
add_action( 'template_redirect', 'djv_theme_custom_route_fallback', 5 );

/**
 * Canonical Festival Slug Request Resolver
 * Ensures /festivals/vijayadashami/, /festivals/paush-purnima/, /festivals/banashankari-jatre-badami/, etc.
 * natively resolve to canonical djv_festival CPT posts with HTTP 200 without duplicate posts or redirect loops.
 */
function djv_theme_festival_request_filter( array $query_vars ): array {
	if ( ! empty( $query_vars['djv_festival'] ) && class_exists( 'DJV_Festival_Master' ) ) {
		$raw_slug = $query_vars['djv_festival'];
		$resolved = DJV_Festival_Master::resolve_slug_alias( $raw_slug );
		if ( $resolved !== $raw_slug ) {
			$query_vars['djv_festival'] = $resolved;
		}
	}
	return $query_vars;
}
add_filter( 'request', 'djv_theme_festival_request_filter', 1 );

/**
 * Single Festival Template Hierarchy Filter
 * Ensures WordPress resolves single-festival.php as the canonical single template for djv_festival CPT.
 */
function djv_theme_festival_single_template( string $template ): string {
	global $post;
	if ( $post && $post->post_type === 'djv_festival' ) {
		$festival_tpl = locate_template( [ 'single-festival.php', 'single-djv_festival.php' ] );
		if ( $festival_tpl ) {
			return $festival_tpl;
		}
	}
	return $template;
}
add_filter( 'single_template', 'djv_theme_festival_single_template' );

/**
 * Global Pagination: Set archive queries to 18 posts per view.
 */
function djv_theme_archive_posts_per_page( $query ): void {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->is_post_type_archive( [ 'djv_festival', 'djv_mantra', 'djv_temple', 'djv_service', 'djv_muhurtham', 'djv_pooja' ] ) || $query->is_tax( [ 'djv_festival_cat', 'djv_festival_type', 'djv_deity' ] ) ) {
			$query->set( 'posts_per_page', 18 );
		}
	}
}
add_action( 'pre_get_posts', 'djv_theme_archive_posts_per_page' );

/**
 * Prevent Canonical Date Redirect on DJV Custom Routes and Archives
 * Prevents WordPress from treating ?year=2026 as an empty blog date archive and redirecting to home.
 */
function djv_prevent_canonical_year_redirect( $redirect_url, $requested_url ) {
	if ( isset( $_GET['year'] ) ) {
		$path = parse_url( $requested_url, PHP_URL_PATH ) ?: '';
		if ( strpos( $path, 'festivals' ) !== false || strpos( $path, 'panchangam' ) !== false || strpos( $path, 'muhurtham' ) !== false ) {
			return false;
		}
	}
	return $redirect_url;
}
add_filter( 'redirect_canonical', 'djv_prevent_canonical_year_redirect', 10, 2 );

/**
 * Ensure ?year=2026 on /festivals/ queries djv_festival CPT rather than standard posts
 */
function djv_fix_year_query_on_archives( $query ): void {
	if ( ! is_admin() && $query->is_main_query() && isset( $_GET['year'] ) ) {
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $request_uri, '/festivals' ) !== false ) {
			$query->set( 'post_type', 'djv_festival' );
			$query->is_year = false;
			$query->is_date = false;
			$query->is_archive = true;
			$query->is_post_type_archive = true;
		}
	}
}
add_action( 'pre_get_posts', 'djv_fix_year_query_on_archives', 1 );

