<?php
/**
 * Dharma Jyothi Vedika — Dynamic SEO & Schema.org JSON-LD Architecture
 *
 * Provides dynamic meta tags, Open Graph, Twitter Cards, canonical links,
 * and comprehensive Schema.org JSON-LD structured data.
 *
 * @package DJV_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Filter Search Query to include all DJV Custom Post Types.
 */
function djv_filter_search_cpts( WP_Query $query ): void {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		$query->set( 'post_type', [
			'post',
			'page',
			'djv_festival',
			'djv_temple',
			'djv_pooja',
			'djv_mantra',
			'djv_muhurtham',
			'djv_service',
		] );
	}
}
add_action( 'pre_get_posts', 'djv_filter_search_cpts' );

/**
 * 2. Generate Dynamic SEO Meta Description.
 */
function djv_get_seo_description(): string {
	if ( is_front_page() || is_home() ) {
		return __( 'Dharma Jyothi Vedika (DJV) — Authentic Daily Hindu Panchangam, Festival Dates, Shubh Muhurtham, Pooja Vidhi, Vedic Mantras, and Sacred Temples across India.', 'djv-theme' );
	}

	if ( is_page( 'today' ) || is_page( 'panchangam' ) || is_page_template( 'page-panchangam-today.php' ) || is_page_template( 'template-panchangam-today.php' ) ) {
		return __( "Today's complete Hindu Panchangam. Accurate Tithi, Nakshatra, Yoga, Karana, Rahu Kalam, Sunrise, Sunset, Moonrise, Moonset and Muhurtham calculated dynamically using Lahiri Ayanamsa for any location in India.", 'djv-theme' );
	}

	if ( is_singular( 'djv_mantra' ) ) {
		$post_id = get_the_ID();
		$custom_meta_desc = get_post_meta( $post_id, '_djv_meta_description', true );
		if ( ! empty( $custom_meta_desc ) ) {
			return $custom_meta_desc;
		}
	}

	if ( is_singular( 'djv_festival' ) ) {
		$post_id = get_the_ID();
		$post    = get_post( $post_id );
		$year    = intval( $_GET['year'] ?? ( $_GET['y'] ?? 2026 ) );
		if ( $year < 1900 || $year > 2200 ) $year = 2026;
		$lang    = sanitize_key( $_GET['lang'] ?? 'en' );

		$custom_rm_desc = get_post_meta( $post_id, 'rank_math_description', true );
		if ( ! empty( $custom_rm_desc ) ) {
			return str_replace( [ '%currentyear%', '%year%' ], (string) $year, $custom_rm_desc );
		}

		$title_en = get_post_meta( $post_id, '_djv_title_en', true ) ?: $post->post_title;
		$title_te = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
		$title_hi = get_post_meta( $post_id, '_djv_title_hi', true );

		$date_str = '';
		if ( class_exists( 'DJV_Festival_Master' ) ) {
			$occs = DJV_Festival_Master::get_occurrences( $year, [ 'scope' => 'all_india' ] );
			foreach ( $occs as $o ) {
				if ( $o['slug'] === $post->post_name || ( ! empty( $o['id'] ) && $o['id'] === $post_id ) ) {
					$date_str = $o['formatted_date'];
					break;
				}
			}
		}

		if ( $lang === 'te' && $title_te ) {
			return "{$title_te} {$year}" . ( $date_str ? " ({$date_str})" : "" ) . " విశిష్టత, ఖచ్చితమైన పూజా సమయం, శుభ ముహూర్తం, తిథి, వ్రత నియమాలు మరియు పూజా విధానం. ధర్మ జ్యోతి వేదిక.";
		} elseif ( $lang === 'hi' && $title_hi ) {
			return "{$title_hi} {$year}" . ( $date_str ? " ({$date_str})" : "" ) . " की सही तिथि, शुभ पूजा मुहूर्त, व्रत नियम, मंत्र, एवं संपूर्ण पूजा विधि। धर्म ज्योति वेदिका पर अपनी लोकेशन अनुसार देखें।";
		} else {
			return "{$title_en} {$year}" . ( $date_str ? " falls on {$date_str}." : "." ) . " Check exact puja timings, auspicious muhurat, tithi, panchang, vidhi, and spiritual significance on Dharma Jyothi Vedika.";
		}
	}

	if ( is_singular() ) {
		$post_id = get_the_ID();
		if ( has_excerpt( $post_id ) ) {
			return wp_strip_all_tags( get_the_excerpt( $post_id ) );
		}
		$content = get_post_field( 'post_content', $post_id );
		if ( $content ) {
			return wp_trim_words( wp_strip_all_tags( $content ), 28, '...' );
		}
	}

	if ( is_post_type_archive( 'djv_festival' ) ) {
		return __( 'Hindu Festivals & Vrats Calendar — Exact tithi dates, rituals, fasting guidelines, and significance according to Vedic Panchangam.', 'djv-theme' );
	}

	if ( is_post_type_archive( 'djv_temple' ) ) {
		return __( 'Sacred Hindu Temples Directory — Darshan timings, sthala purana, history, and pilgrimage guidance for Jyotirlingas, Shakti Peethas, and Divya Desams.', 'djv-theme' );
	}

	if ( is_post_type_archive( 'djv_pooja' ) ) {
		return __( 'Pooja Guides & Vidhi — Authentic step-by-step procedures, required samagri lists, sankalpam, and sacred naivedyam.', 'djv-theme' );
	}

	if ( is_post_type_archive( 'djv_mantra' ) ) {
		return __( 'Mantras & Slokas Library — Sanskrit texts with Telugu script, transliteration, and spiritual meanings for daily worship.', 'djv-theme' );
	}

	if ( is_post_type_archive( 'djv_muhurtham' ) ) {
		return __( 'Shubh Muhurtham Finder — Auspicious dates and timings for Vivaha, Gruhapravesham, Namakaranam, and new beginnings.', 'djv-theme' );
	}

	if ( is_post_type_archive( 'djv_service' ) ) {
		return __( 'Vedic Services & Consultations — Verified Vedic scholars for homams, horoscope readings, and traditional Vastu inspections.', 'djv-theme' );
	}

	return get_bloginfo( 'description' ) ?: __( 'Authentic Vedic Panchangam and Hindu devotional platform.', 'djv-theme' );
}

/**
 * 3. Output Dynamic SEO Meta Tags, Open Graph, and Canonical into <head>.
 */
function djv_render_seo_meta(): void {
	static $already_rendered = false;
	if ( $already_rendered ) {
		return;
	}
	$already_rendered = true;

	$desc      = djv_get_seo_description();
	$site_name = get_bloginfo( 'name' );
	$title     = wp_get_document_title();
	$url       = is_singular() ? get_permalink() : home_url( add_query_arg( [], $GLOBALS['wp']->request ) );
	$image     = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}
	if ( ! $image ) {
		$image = DJV_THEME_URI . '/assets/images/og-default.jpg';
	}
	?>
  <!-- DJV Dynamic SEO -->
  <meta name="description" content="<?php echo esc_attr( $desc ); ?>" />
  <link rel="canonical" href="<?php echo esc_url( $url ); ?>" />

  <!-- Open Graph / Facebook -->
  <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
  <meta property="og:type" content="<?php echo is_singular( 'post' ) ? 'article' : 'website'; ?>" />
  <meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
  <meta property="og:description" content="<?php echo esc_attr( $desc ); ?>" />
  <meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
  <meta property="og:image" content="<?php echo esc_url( $image ); ?>" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
  <meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>" />
  <meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
	<?php
}
add_action( 'wp_head', 'djv_render_seo_meta', 1 );

/**
 * 4. Structured Data (Schema.org JSON-LD).
 */
function djv_render_schema_jsonld(): void {
	$home_url  = home_url( '/' );
	$site_name = get_bloginfo( 'name' );

	// 1. WebSite Schema with SearchAction
	$website_schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'name'            => $site_name,
		'url'             => $home_url,
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => $home_url . '?s={search_term_string}',
			'query-input' => 'required name=search_term_string',
		],
	];

	echo "\n<!-- DJV Structured Data: WebSite -->\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";

	// 2. BreadcrumbList Schema
	if ( ! is_front_page() ) {
		$crumbs = [
			[
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Home', 'djv-theme' ),
				'item'     => $home_url,
			],
		];

		if ( is_singular() ) {
			$post_type = get_post_type();
			$pt_obj    = get_post_type_object( $post_type );
			if ( $pt_obj && $pt_obj->has_archive ) {
				$archive_link = get_post_type_archive_link( $post_type );
				if ( $archive_link ) {
					$crumbs[] = [
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => $pt_obj->labels->name,
						'item'     => $archive_link,
					];
				}
			}
			$crumbs[] = [
				'@type'    => 'ListItem',
				'position' => count( $crumbs ) + 1,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			];
		} elseif ( is_archive() ) {
			$crumbs[] = [
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => post_type_archive_title( '', false ) ?: __( 'Archive', 'djv-theme' ),
				'item'     => home_url( add_query_arg( [], $GLOBALS['wp']->request ) ),
			];
		}

		$breadcrumb_schema = [
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $crumbs,
		];

		echo "<!-- DJV Structured Data: Breadcrumb -->\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}

	// 2b. WebPage Schema for Panchangam
	if ( is_page( 'today' ) || is_page( 'panchangam' ) || is_page_template( 'page-panchangam-today.php' ) || is_page_template( 'template-panchangam-today.php' ) ) {
		$webpage_schema = [
			'@context'    => 'https://schema.org',
			'@type'       => 'WebPage',
			'name'        => wp_get_document_title(),
			'url'         => home_url( '/panchangam/today/' ),
			'description' => djv_get_seo_description(),
			'inLanguage'  => [ 'en', 'te' ],
			'isPartOf'    => [
				'@type' => 'WebSite',
				'name'  => $site_name,
				'url'   => $home_url,
			],
		];
		echo "<!-- DJV Structured Data: WebPage -->\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $webpage_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}

	// 3. Single Specific Schemas
	if ( is_singular( 'post' ) ) {
		$post_id        = get_the_ID();
		$article_schema = [
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => get_the_title( $post_id ),
			'datePublished'    => get_the_date( 'c', $post_id ),
			'dateModified'     => get_the_modified_date( 'c', $post_id ),
			'author'           => [
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
			],
			'publisher'        => [
				'@type' => 'Organization',
				'name'  => $site_name,
				'url'   => $home_url,
			],
			'description'      => djv_get_seo_description(),
			'mainEntityOfPage' => get_permalink( $post_id ),
		];
		if ( has_post_thumbnail( $post_id ) ) {
			$article_schema['image'] = get_the_post_thumbnail_url( $post_id, 'large' );
		}

		echo "<!-- DJV Structured Data: Article -->\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}

	// 4. Festival Schema (Event)
	if ( is_singular( 'djv_festival' ) ) {
		$post_id   = get_the_ID();
		$date_meta = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
		if ( $date_meta ) {
			$event_schema = [
				'@context'    => 'https://schema.org',
				'@type'       => 'Event',
				'name'        => get_the_title( $post_id ),
				'startDate'   => date( 'Y-m-d', strtotime( $date_meta ) ),
				'description' => djv_get_seo_description(),
				'eventStatus' => 'https://schema.org/EventScheduled',
				'organizer'   => [
					'@type' => 'Organization',
					'name'  => $site_name,
					'url'   => $home_url,
				],
			];
			if ( has_post_thumbnail( $post_id ) ) {
				$event_schema['image'] = get_the_post_thumbnail_url( $post_id, 'large' );
			}

			echo "<!-- DJV Structured Data: Festival Event -->\n";
			echo '<script type="application/ld+json">' . wp_json_encode( $event_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
		}
	}

	// 5. Temple Schema (Place / HinduTemple)
	if ( is_singular( 'djv_temple' ) ) {
		$post_id = get_the_ID();
		$address = get_post_meta( $post_id, '_djv_address', true );
		$lat     = get_post_meta( $post_id, '_djv_lat', true );
		$lon     = get_post_meta( $post_id, '_djv_lon', true );

		$temple_schema = [
			'@context'    => 'https://schema.org',
			'@type'       => 'HinduTemple',
			'name'        => get_the_title( $post_id ),
			'description' => djv_get_seo_description(),
			'url'         => get_permalink( $post_id ),
		];
		if ( $address ) {
			$temple_schema['address'] = [
				'@type'          => 'PostalAddress',
				'streetAddress'  => $address,
				'addressCountry' => 'IN',
			];
		}
		if ( $lat && $lon ) {
			$temple_schema['geo'] = [
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $lat,
				'longitude' => (float) $lon,
			];
		}
		if ( has_post_thumbnail( $post_id ) ) {
			$temple_schema['image'] = get_the_post_thumbnail_url( $post_id, 'large' );
		}

		echo "<!-- DJV Structured Data: Hindu Temple -->\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $temple_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}
add_action( 'wp_head', 'djv_render_schema_jsonld', 2 );

/**
 * Custom SEO Document Title for Mantras.
 */
function djv_filter_mantra_document_title( string $title ): string {
	if ( is_singular( 'djv_mantra' ) ) {
		$seo_title = get_post_meta( get_the_ID(), '_djv_seo_title', true );
		if ( ! empty( $seo_title ) ) {
			return $seo_title;
		}
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'djv_filter_mantra_document_title', 20 );

/**
 * Dynamic SEO Document Title for Festivals (Adapts to selected year and language)
 * Structure: [FESTIVAL] [YEAR] – Date, Puja Time & Muhurat | Dharma Jyothi Vedika
 */
function djv_filter_festival_document_title( string $title ): string {
	if ( is_singular( 'djv_festival' ) ) {
		$post_id = get_the_ID();
		$post    = get_post( $post_id );
		$year    = intval( $_GET['year'] ?? ( $_GET['y'] ?? 2026 ) );
		if ( $year < 1900 || $year > 2200 ) $year = 2026;
		$lang    = sanitize_key( $_GET['lang'] ?? 'en' );

		// Custom postmeta override if explicitly set
		$custom_rm_title = get_post_meta( $post_id, 'rank_math_title', true );
		if ( ! empty( $custom_rm_title ) ) {
			return str_replace( [ '%currentyear%', '%year%' ], (string) $year, $custom_rm_title );
		}

		$title_en = get_post_meta( $post_id, '_djv_title_en', true ) ?: $post->post_title;
		$title_te = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
		$title_hi = get_post_meta( $post_id, '_djv_title_hi', true );

		if ( $lang === 'te' && $title_te ) {
			return "{$title_te} {$year} – పండుగ తేదీ, పూజా సమయం & ముహూర్తం | ధర్మ జ్యోతి వేదిక";
		} elseif ( $lang === 'hi' && $title_hi ) {
			return "{$title_hi} {$year} – तिथि, पूजा का शुभ मुहूर्त एवं विधि | धर्म ज्योति वेदिका";
		} else {
			return "{$title_en} {$year} – Date, Puja Time & Muhurat | Dharma Jyothi Vedika";
		}
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'djv_filter_festival_document_title', 20 );
add_filter( 'rank_math/frontend/title', 'djv_filter_festival_document_title', 15 );

add_filter( 'rank_math/frontend/description', function( $desc ) {
	if ( is_singular( 'djv_festival' ) ) {
		$post_id = get_the_ID();
		$post    = get_post( $post_id );
		$year    = intval( $_GET['year'] ?? ( $_GET['y'] ?? 2026 ) );
		if ( $year < 1900 || $year > 2200 ) $year = 2026;
		$lang    = sanitize_key( $_GET['lang'] ?? 'en' );

		$custom_rm_desc = get_post_meta( $post_id, 'rank_math_description', true );
		if ( ! empty( $custom_rm_desc ) ) {
			return str_replace( [ '%currentyear%', '%year%' ], (string) $year, $custom_rm_desc );
		}

		$title_en = get_post_meta( $post_id, '_djv_title_en', true ) ?: $post->post_title;
		$title_te = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
		$title_hi = get_post_meta( $post_id, '_djv_title_hi', true );

		$date_str = '';
		if ( class_exists( 'DJV_Festival_Master' ) ) {
			$occs = DJV_Festival_Master::get_occurrences( $year, [ 'scope' => 'all_india' ] );
			foreach ( $occs as $o ) {
				if ( $o['slug'] === $post->post_name || ( ! empty( $o['id'] ) && $o['id'] === $post_id ) ) {
					$date_str = $o['formatted_date'];
					break;
				}
			}
		}

		if ( $lang === 'te' && $title_te ) {
			return "{$title_te} {$year}" . ( $date_str ? " ({$date_str})" : "" ) . " విశిష్టత, ఖచ్చితమైన పూజా సమయం, శుభ ముహూర్తం, తిథి, వ్రత నియమాలు మరియు పూజా విధానం. ధర్మ జ్యోతి వేదిక.";
		} elseif ( $lang === 'hi' && $title_hi ) {
			return "{$title_hi} {$year}" . ( $date_str ? " ({$date_str})" : "" ) . " की सही तिथि, शुभ पूजा मुहूर्त, व्रत नियम, मंत्र, एवं संपूर्ण पूजा विधि। धर्म ज्योति वेदिका पर अपनी लोकेशन अनुसार देखें।";
		} else {
			return "{$title_en} {$year}" . ( $date_str ? " falls on {$date_str}." : "." ) . " Check exact puja timings, auspicious muhurat, tithi, panchang, vidhi, and spiritual significance on Dharma Jyothi Vedika.";
		}
	}
	return $desc;
}, 15 );

add_filter( 'rank_math/canonical_url', function( $canonical ) {
	if ( is_singular( 'djv_festival' ) ) {
		$post = get_post();
		if ( $post ) {
			return home_url( '/festivals/' . $post->post_name . '/' );
		}
	}
	return $canonical;
}, 15 );
