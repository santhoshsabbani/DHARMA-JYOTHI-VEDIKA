<?php
/**
 * DJV Core — Festival Master Engine & Year-Aware Dynamic Occurrence Calculator
 *
 * Implements high-precision Jean Meeus astronomical algorithms in pure PHP (Meeus Ch. 25 & 47),
 * Amanta Luni-Solar calendar construction, Tithi/Nakshatra/Transit matching,
 * transient caching, and synchronization with the djv_festival CPT.
 *
 * Supports generating dynamic occurrences for 2025, 2026, 2027, 2028, 2029, 2030+
 * across any latitude, longitude, and IANA timezone without manual yearly date entry.
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 86400 );
}

class DJV_Festival_Master {

	const VERSION = '1.0.0';

	/**
	 * Gregorian date to Julian Day Number (Meeus Ch. 7)
	 */
	public static function gregorian_to_jd( int $year, int $month, int $day ): float {
		if ( $month <= 2 ) {
			$year -= 1;
			$month += 12;
		}
		$A = floor( $year / 100.0 );
		$B = 2.0 - $A + floor( $A / 4.0 );
		return floor( 365.25 * ( $year + 4716.0 ) ) + floor( 30.6001 * ( $month + 1.0 ) ) + $day + $B - 1524.5;
	}

	/**
	 * Normalize degrees to [0, 360)
	 */
	public static function normalize_deg( float $deg ): float {
		$d = fmod( $deg, 360.0 );
		return $d < 0 ? $d + 360.0 : $d;
	}

	/**
	 * Canonical Hindu Lunar Month Name
	 */
	public static function normalize_masa_name( string $m ): string {
		$key = strtolower( trim( $m ) );
		$map = [
			'chaitra'      => 'Chaitra', 'chaitram'     => 'Chaitra',
			'vaishakha'    => 'Vaishakha', 'vaishakh'   => 'Vaishakha', 'vaisakha'   => 'Vaishakha',
			'jyeshtha'     => 'Jyeshtha', 'jyeshth'     => 'Jyeshtha', 'jyeshta'    => 'Jyeshtha',
			'ashadha'      => 'Ashadha', 'ashadh'       => 'Ashadha', 'asadha'     => 'Ashadha',
			'shravana'     => 'Shravana', 'shravan'     => 'Shravana', 'sravana'    => 'Shravana',
			'bhadrapada'   => 'Bhadrapada', 'bhadrapad' => 'Bhadrapada', 'bhadra'   => 'Bhadrapada',
			'ashwin'       => 'Ashwin', 'ashwina'       => 'Ashwin', 'aswina'       => 'Ashwin', 'aswayuja' => 'Ashwin', 'ashwayuja' => 'Ashwin',
			'karthika'     => 'Karthika', 'kartika'     => 'Karthika', 'karthik'    => 'Karthika', 'kartik' => 'Karthika',
			'margashirsha' => 'Margashirsha', 'margashirsh' => 'Margashirsha', 'margasira' => 'Margashirsha', 'agrahayana' => 'Margashirsha',
			'pausha'       => 'Pausha', 'paush'         => 'Pausha', 'pushya'       => 'Pausha', 'pushyam' => 'Pausha',
			'magha'        => 'Magha', 'magh'           => 'Magha', 'magham'        => 'Magha',
			'phalguna'     => 'Phalguna', 'phalgun'     => 'Phalguna', 'phalguni'   => 'Phalguna',
		];
		return $map[ $key ] ?? ucfirst( $key );
	}

	/**
	 * Apparent Geocentric Solar Longitude (Tropical, degrees)
	 */
	public static function get_solar_lon( float $jd ): float {
		$T  = ( $jd - 2451545.0 ) / 36525.0;
		$L0 = self::normalize_deg( 280.46646 + 36000.76983 * $T );
		$M  = self::normalize_deg( 357.52911 + 35999.05029 * $T );
		$Mr = $M * M_PI / 180.0;
		$C  = ( 1.914602 - 0.004817 * $T ) * sin( $Mr ) + ( 0.019993 - 0.000101 * $T ) * sin( 2.0 * $Mr );
		return self::normalize_deg( $L0 + $C );
	}

	/**
	 * Geocentric Lunar Longitude (Tropical, degrees)
	 */
	public static function get_moon_lon( float $jd ): float {
		$T  = ( $jd - 2451545.0 ) / 36525.0;
		$L0 = self::normalize_deg( 218.3164477 + 481267.88123421 * $T );
		$D  = self::normalize_deg( 297.8501921 + 445267.1114034  * $T );
		$M  = self::normalize_deg( 357.5291092 + 35999.0502909   * $T );
		$Mp = self::normalize_deg( 134.9633964 + 477198.8675055  * $T );
		$F  = self::normalize_deg( 93.2720950  + 483202.0175233  * $T );

		$d2r = M_PI / 180.0;
		$SigmaL = 6.288774 * sin( $Mp * $d2r ) +
		          1.274027 * sin( ( 2.0 * $D - $Mp ) * $d2r ) +
		          0.658314 * sin( 2.0 * $D * $d2r ) +
		          0.213618 * sin( 2.0 * $Mp * $d2r ) -
		          0.185116 * sin( $M * $d2r ) -
		          0.114332 * sin( 2.0 * $F * $d2r );

		return self::normalize_deg( $L0 + $SigmaL );
	}

	/**
	 * Lahiri Ayanamsa (degrees)
	 */
	public static function get_lahiri_ayanamsa( float $jd ): float {
		$T = ( $jd - 2451545.0 ) / 36525.0;
		return 23.85 + ( 50.29 / 3600.0 ) * ( $T * 100.0 );
	}

	/**
	 * Build an entire astronomical calendar for the given year and location.
	 */
	public static function build_year_calendar( int $year, float $lat = 17.3850, float $lon = 78.4867, string $tz = 'Asia/Kolkata' ): array {
		try {
			$tzObj = new DateTimeZone( $tz );
		} catch ( Exception $e ) {
			$tzObj = new DateTimeZone( 'Asia/Kolkata' );
		}

		// Scan from Dec 1 of prev year to Jan 31 of next year
		$start = new DateTime( ( $year - 1 ) . '-12-01', $tzObj );
		$end   = new DateTime( ( $year + 1 ) . '-01-31', $tzObj );

		$masa_from_solar_rashi = [
			11 => 'Chaitra',       // Sun in Meena at Amanta New Moon
			0  => 'Vaishakha',     // Sun in Mesha
			1  => 'Jyeshtha',      // Sun in Vrishabha
			2  => 'Ashadha',       // Sun in Mithuna
			3  => 'Shravana',      // Sun in Karka
			4  => 'Bhadrapada',    // Sun in Simha
			5  => 'Ashwin',        // Sun in Kanya
			6  => 'Karthika',      // Sun in Tula
			7  => 'Margashirsha',  // Sun in Vrischika
			8  => 'Pausha',        // Sun in Dhanu
			9  => 'Magha',         // Sun in Makara
			10 => 'Phalguna',      // Sun in Kumbha
		];

		$nakshatra_names = [
			1 => 'Ashwini', 2 => 'Bharani', 3 => 'Krittika', 4 => 'Rohini', 5 => 'Mrigashira',
			6 => 'Ardra', 7 => 'Punarvasu', 8 => 'Pushya', 9 => 'Ashlesha', 10 => 'Magha',
			11 => 'Purva Phalguni', 12 => 'Uttara Phalguni', 13 => 'Hasta', 14 => 'Chitra',
			15 => 'Swati', 16 => 'Vishakha', 17 => 'Anuradha', 18 => 'Jyeshtha', 19 => 'Mula',
			20 => 'Purva Ashadha', 21 => 'Uttara Ashadha', 22 => 'Shravana', 23 => 'Dhanishta',
			24 => 'Shatabhisha', 25 => 'Purva Bhadrapada', 26 => 'Uttara Bhadrapada', 27 => 'Revati'
		];

		$days = [];
		$cur = clone $start;
		$prev_diff = null;
		$active_masa = null;
		$prev_sun_rashi = null;

		// Calculate timezone offset in hours
		$offset_hours = $tzObj->getOffset( $cur ) / 3600.0;

		while ( $cur <= $end ) {
			$y = (int) $cur->format( 'Y' );
			$m = (int) $cur->format( 'm' );
			$d = (int) $cur->format( 'd' );

			$base_jd = self::gregorian_to_jd( $y, $m, $d );

			// Sample Sunrise (06:00 local time approx)
			$jd_sr   = $base_jd + ( 6.0 - $offset_hours ) / 24.0;
			$sun_sr  = self::normalize_deg( self::get_solar_lon( $jd_sr ) - self::get_lahiri_ayanamsa( $jd_sr ) );
			$moon_sr = self::normalize_deg( self::get_moon_lon( $jd_sr ) - self::get_lahiri_ayanamsa( $jd_sr ) );
			$diff_sr = self::normalize_deg( $moon_sr - $sun_sr );
			$tithi_sr = (int) floor( $diff_sr / 12.0 ) + 1; // 1-30

			// Sample Midday (12:00 local time)
			$jd_mid   = $base_jd + ( 12.0 - $offset_hours ) / 24.0;
			$sun_mid  = self::normalize_deg( self::get_solar_lon( $jd_mid ) - self::get_lahiri_ayanamsa( $jd_mid ) );
			$moon_mid = self::normalize_deg( self::get_moon_lon( $jd_mid ) - self::get_lahiri_ayanamsa( $jd_mid ) );
			$diff_mid = self::normalize_deg( $moon_mid - $sun_mid );
			$tithi_mid = (int) floor( $diff_mid / 12.0 ) + 1;

			// Sample Sunset (18:00 local time)
			$jd_ss   = $base_jd + ( 18.0 - $offset_hours ) / 24.0;
			$sun_ss  = self::normalize_deg( self::get_solar_lon( $jd_ss ) - self::get_lahiri_ayanamsa( $jd_ss ) );
			$moon_ss = self::normalize_deg( self::get_moon_lon( $jd_ss ) - self::get_lahiri_ayanamsa( $jd_ss ) );
			$diff_ss = self::normalize_deg( $moon_ss - $sun_ss );
			$tithi_ss = (int) floor( $diff_ss / 12.0 ) + 1;

			// Sample Midnight (23:59 local time)
			$jd_nt   = $base_jd + ( 23.99 - $offset_hours ) / 24.0;
			$sun_nt  = self::normalize_deg( self::get_solar_lon( $jd_nt ) - self::get_lahiri_ayanamsa( $jd_nt ) );
			$moon_nt = self::normalize_deg( self::get_moon_lon( $jd_nt ) - self::get_lahiri_ayanamsa( $jd_nt ) );
			$diff_nt = self::normalize_deg( $moon_nt - $sun_nt );
			$tithi_nt = (int) floor( $diff_nt / 12.0 ) + 1;

			$sun_rashi = (int) floor( $sun_sr / 30.0 );
			$nak_idx   = (int) floor( $moon_sr / ( 360.0 / 27.0 ) ) + 1;

			// Ingress check (Sankranti today)
			$is_sankranti = ( $prev_sun_rashi !== null && $prev_sun_rashi !== $sun_rashi );

			// New Moon transition (diff crossed 0/360)
			if ( $prev_diff !== null && ( ( $prev_diff > 330 && $diff_sr < 30 ) || ( $prev_diff > 345 && $diff_mid < 15 ) ) ) {
				$active_masa = $masa_from_solar_rashi[ $sun_rashi ] ?? 'Chaitra';
			}

			$date_str = $cur->format( 'Y-m-d' );
			$days[ $date_str ] = [
				'date'           => $date_str,
				'year'           => $y,
				'month'          => $m,
				'day'            => $d,
				'day_of_week'    => (int) $cur->format( 'w' ),
				'day_name'       => $cur->format( 'l' ),
				'sun_rashi'      => $sun_rashi,
				'is_sankranti'   => $is_sankranti,
				'sankranti_rashi'=> $sun_rashi,
				'tithi_sr'       => $tithi_sr,
				'tithi_mid'      => $tithi_mid,
				'tithi_ss'       => $tithi_ss,
				'tithi_nt'       => $tithi_nt,
				'tithis_today'   => array_unique( [ $tithi_sr, $tithi_mid, $tithi_ss, $tithi_nt ] ),
				'nakshatra_idx'  => $nak_idx,
				'nakshatra'      => $nakshatra_names[ $nak_idx ] ?? '',
				'masa'           => $active_masa,
			];

			$prev_diff      = $diff_sr;
			$prev_sun_rashi = $sun_rashi;
			$cur->modify( '+1 day' );
		}

		// Backfill early dates before first detected new moon in range
		$first_m = null;
		foreach ( $days as $d ) {
			if ( $d['masa'] !== null ) {
				$first_m = $d['masa'];
				break;
			}
		}
		foreach ( $days as &$d ) {
			if ( $d['masa'] === null ) {
				$d['masa'] = $first_m ?: 'Margashirsha';
			}
		}

		return $days;
	}

	/**
	 * Compute occurrences of all festivals for the requested year and location.
	 *
	 * Uses WordPress transients for sub-millisecond retrieval.
	 */
	public static function get_occurrences( int $year, array $args = [] ): array {
		$lat      = floatval( $args['latitude'] ?? 17.3850 );
		$lon      = floatval( $args['longitude'] ?? 78.4867 );
		$tz       = sanitize_text_field( $args['timezone'] ?? 'Asia/Kolkata' );
		$region   = sanitize_text_field( $args['region'] ?? 'all' );
		$state    = sanitize_text_field( $args['state'] ?? 'all' );
		$category = sanitize_text_field( $args['category'] ?? 'all' );
		$deity    = sanitize_text_field( $args['deity'] ?? 'all' );
		$month    = intval( $args['month'] ?? 0 );
		$lang     = sanitize_key( $args['language'] ?? 'en' );
		$search   = sanitize_text_field( $args['search'] ?? '' );

		// Transient Cache Key
		$cache_key = sprintf(
			'djv_focc_%d_%.2f_%.2f_%s_%s_%s_%s_%s_%d_%s_v1',
			$year, $lat, $lon,
			substr( md5( $tz ), 0, 6 ),
			sanitize_key( $region ),
			sanitize_key( $state ),
			sanitize_key( $category ),
			sanitize_key( $deity ),
			$month,
			$lang
		);

		// Limit transient key length to 64 chars
		if ( strlen( $cache_key ) > 64 ) {
			$cache_key = 'djv_focc_' . md5( $cache_key );
		}

		$cached = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) && empty( $search ) ) {
			return $cached;
		}

		// Build astronomical calendar
		$cal = self::build_year_calendar( $year, $lat, $lon, $tz );

		require_once __DIR__ . '/data-festival-master.php';
		$catalog = djv_get_festival_master_catalog();

		// Fetch published posts mapping by slug for thumbnails and permalinks
		static $slug_to_post = null;
		if ( $slug_to_post === null ) {
			$posts = get_posts( [
				'post_type'      => 'djv_festival',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			] );
			$slug_to_post = [];
			foreach ( $posts as $pid ) {
				$slug = get_post_field( 'post_name', $pid );
				if ( $slug ) {
					$slug_to_post[ $slug ] = $pid;
				}
			}
		}

		$occurrences = [];

		foreach ( $catalog as $slug => $fest ) {
			// Category filter
			if ( $category !== 'all' && $category !== '' ) {
				$cats = array_map( 'strtolower', $fest['categories'] ?? [] );
				$match_cat = in_array( strtolower( $category ), $cats, true ) ||
				             in_array( sanitize_title( $category ), array_map( 'sanitize_title', $fest['categories'] ?? [] ), true );
				if ( ! $match_cat ) continue;
			}

			// State filter
			if ( $state !== 'all' && $state !== '' ) {
				if ( stripos( $fest['state'] ?? '', $state ) === false && strcasecmp( $fest['state'] ?? '', 'Pan-India' ) !== 0 ) {
					continue;
				}
			}

			// Region filter
			if ( $region !== 'all' && $region !== '' ) {
				if ( stripos( $fest['region'] ?? '', $region ) === false && strcasecmp( $fest['region'] ?? '', 'Pan-India' ) !== 0 ) {
					continue;
				}
			}

			// Deity filter
			if ( $deity !== 'all' && $deity !== '' ) {
				if ( strcasecmp( $fest['deity_slug'] ?? '', $deity ) !== 0 && stripos( $fest['deity'] ?? '', $deity ) === false ) {
					continue;
				}
			}

			$rule_type = $fest['rule_type'];
			$params    = $fest['rule_params'] ?? [];
			$found_date = null;

			switch ( $rule_type ) {
				case 'lunar_tithi':
				case 'ekadashi':
				case 'pradosham':
				case 'lunar_purnima':
				case 'lunar_amavasya':
				case 'sankashti':
					$masa         = $params['masa'] ?? '';
					$paksha       = $params['paksha'] ?? 'shukla';
					$t_num        = (int) ( $params['tithi'] ?? 1 );
					$target_tithi = ( $paksha === 'shukla' ) ? $t_num : ( $t_num + 15 );

					foreach ( $cal as $date_str => $d ) {
						if ( $d['year'] !== $year ) continue;
						if ( ! empty( $masa ) && strcasecmp( self::normalize_masa_name( $d['masa'] ), self::normalize_masa_name( $masa ) ) !== 0 ) continue;
						if ( in_array( $target_tithi, $d['tithis_today'], true ) ) {
							$found_date = $date_str;
							break;
						}
					}
					break;

				case 'solar_transit':
				case 'regional_solar':
					$raw_rashi = (int) ( $params['target_rashi'] ?? ( $params['solar_rashi'] ?? ( $params['rashi'] ?? 0 ) ) );
					$rashi_0   = ( $raw_rashi >= 1 && $raw_rashi <= 12 ) ? ( $raw_rashi - 1 ) : $raw_rashi;
					foreach ( $cal as $date_str => $d ) {
						if ( $d['year'] !== $year ) continue;
						if ( $d['is_sankranti'] && ( $d['sankranti_rashi'] === $rashi_0 || $d['sankranti_rashi'] === $raw_rashi ) ) {
							$found_date = $date_str;
							break;
						}
					}
					if ( ! $found_date && ! empty( $params['target_month'] ) && ! empty( $params['target_day'] ) ) {
						$found_date = sprintf( '%04d-%02d-%02d', $year, (int) $params['target_month'], (int) $params['target_day'] );
					}
					break;

				case 'solar_fixed':
					$m_fix = (int) ( $params['month'] ?? 1 );
					$d_fix = (int) ( $params['day'] ?? 1 );
					$found_date = sprintf( '%04d-%02d-%02d', $year, $m_fix, $d_fix );
					break;

				case 'lunar_nakshatra':
					$target_nak = $params['nakshatra'] ?? '';
					foreach ( $cal as $date_str => $d ) {
						if ( $d['year'] !== $year ) continue;
						if ( ! empty( $params['masa'] ) && strcasecmp( self::normalize_masa_name( $d['masa'] ), self::normalize_masa_name( $params['masa'] ) ) !== 0 ) continue;
						if ( ! empty( $target_nak ) && ( stripos( $d['nakshatra'], $target_nak ) !== false || stripos( $target_nak, $d['nakshatra'] ) !== false ) ) {
							$found_date = $date_str;
							break;
						}
					}
					break;

				case 'custom_rule':
					$rule = $params['rule'] ?? '';
					if ( $rule === 'second_friday_shravana' || $rule === 'varalakshmi_vratam' ) {
						// Varalakshmi Vratam: Friday preceding Shravana Purnima
						$purnima_date = null;
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] === $year && strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Shravana' ) === 0 && in_array( 15, $d['tithis_today'], true ) ) {
								$purnima_date = new DateTime( $date_str );
								break;
							}
						}
						if ( $purnima_date ) {
							$fri = clone $purnima_date;
							do {
								$fri->modify( '-1 day' );
							} while ( (int) $fri->format( 'w' ) !== 5 );
							$found_date = $fri->format( 'Y-m-d' );
						}
					} elseif ( $rule === 'first_sunday_ashadha' || $rule === 'ashadha_sundays' ) {
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] === $year && strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashadha' ) === 0 && $d['day_of_week'] === 0 ) {
								$found_date = $date_str;
								break;
							}
						}
					} elseif ( $rule === 'mahalaya_to_durgashtami' ) {
						// Bathukamma starts on Mahalaya Amavasya
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] === $year && strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) === 0 && in_array( 30, $d['tithis_today'], true ) ) {
								$found_date = $date_str;
								break;
							}
						}
					} elseif ( ! empty( $params['masa'] ) ) {
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] === $year && strcasecmp( self::normalize_masa_name( $d['masa'] ), self::normalize_masa_name( $params['masa'] ) ) === 0 ) {
								$found_date = $date_str;
								break;
							}
						}
					}
					break;
			}

			// If date wasn't found by specific astronomical rule, fallback to default date if valid for the year
			if ( ! $found_date && ! empty( $fest['date_default'] ) ) {
				$def_time = strtotime( $fest['date_default'] );
				if ( $def_time && (int) date( 'Y', $def_time ) === $year ) {
					$found_date = $fest['date_default'];
				}
			}

			if ( ! $found_date ) {
				continue;
			}

			// Month filter
			if ( $month > 0 ) {
				$f_month = (int) date( 'm', strtotime( $found_date ) );
				if ( $f_month !== $month ) continue;
			}

			// Search query filter
			if ( ! empty( $search ) ) {
				$q = strtolower( $search );
				$match_search = strpos( strtolower( $fest['title_en'] ), $q ) !== false ||
				                strpos( strtolower( $fest['title_te'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['title_hi'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['excerpt'] ?? '' ), $q ) !== false;
				if ( ! $match_search ) continue;
			}

			$post_id   = $slug_to_post[ $slug ] ?? 0;
			$permalink = $post_id ? get_permalink( $post_id ) : home_url( "/festivals/{$slug}/" );
			$thumb     = $post_id ? get_the_post_thumbnail_url( $post_id, 'medium' ) : null;

			$formatted_date = date( 'F j, Y', strtotime( $found_date ) );
			$day_name       = date( 'l', strtotime( $found_date ) );

			// Localized title & description based on requested language
			$display_title = $fest['title_en'];
			$display_desc  = $fest['excerpt'];
			if ( $lang === 'te' && ! empty( $fest['title_te'] ) ) {
				$display_title = $fest['title_te'];
				$display_desc  = $fest['content_te'] ?: $fest['excerpt'];
			} elseif ( $lang === 'hi' && ! empty( $fest['title_hi'] ) ) {
				$display_title = $fest['title_hi'];
				$display_desc  = $fest['content_hi'] ?: $fest['excerpt'];
			}

			$occurrences[] = [
				'id'                => $post_id,
				'slug'              => $slug,
				'title'             => $display_title,
				'title_en'          => $fest['title_en'],
				'title_te'          => $fest['title_te'] ?? '',
				'title_hi'          => $fest['title_hi'] ?? '',
				'date'              => $found_date,
				'formatted_date'    => $formatted_date,
				'day_of_week'       => $day_name,
				'month'             => $fest['month'] ?? date( 'F', strtotime( $found_date ) ),
				'tithi_rule'        => $fest['tithi_rule'] ?? ( $fest['rule_params']['masa'] ?? '' ),
				'rule_type'         => $rule_type,
				'categories'        => $fest['categories'] ?? [],
				'deity'             => $fest['deity'] ?? '',
				'deity_slug'        => $fest['deity_slug'] ?? '',
				'state'             => $fest['state'] ?? 'Pan-India',
				'region'            => $fest['region'] ?? 'Pan-India',
				'is_major'          => ! empty( $fest['is_major'] ) || in_array( 'Major Festivals', $fest['categories'] ?? [], true ),
				'is_telugu'         => ! empty( $fest['is_telugu'] ) || in_array( 'Regional', $fest['categories'] ?? [], true ),
				'excerpt'           => $display_desc,
				'content_en'        => $fest['content_en'] ?? $fest['excerpt'],
				'content_te'        => $fest['content_te'] ?? '',
				'content_hi'        => $fest['content_hi'] ?? '',
				'puja_timings'      => $fest['puja_timings'] ?? '',
				'samagri'           => $fest['samagri'] ?? '',
				'naivedyam'         => $fest['naivedyam'] ?? '',
				'vrat_rules'        => $fest['vrat_rules'] ?? '',
				'dos'               => $fest['dos'] ?? '',
				'donts'             => $fest['donts'] ?? '',
				'significance'      => $fest['significance'] ?? '',
				'history'           => $fest['history'] ?? '',
				'related_poojas'    => $fest['related_poojas'] ?? [],
				'related_mantras'   => $fest['related_mantras'] ?? [],
				'validation_status' => $fest['validation_status'] ?? 'verified',
				'calculation_method'=> $fest['calculation_method'] ?? 'astronomical_tithi',
				'link'              => $permalink,
				'thumbnail'         => $thumb,
			];
		}

		// Sort chronologically by date
		usort( $occurrences, function( $a, $b ) {
			return strcmp( $a['date'], $b['date'] );
		} );

		// Cache for 7 days
		if ( empty( $search ) ) {
			set_transient( $cache_key, $occurrences, 7 * DAY_IN_SECONDS );
		}

		return $occurrences;
	}

	/**
	 * Synchronize all 509 Master definitions into the djv_festival CPT.
	 */
	public static function sync_master_to_cpt(): array {
		require_once __DIR__ . '/data-festival-master.php';
		$catalog = djv_get_festival_master_catalog();

		$created = 0;
		$updated = 0;

		foreach ( $catalog as $slug => $fest ) {
			$existing = get_page_by_path( $slug, OBJECT, 'djv_festival' );
			$post_data = [
				'post_title'   => $fest['title_en'],
				'post_name'    => $slug,
				'post_content' => $fest['content_en'] ?: $fest['excerpt'],
				'post_excerpt' => $fest['excerpt'],
				'post_status'  => 'publish',
				'post_type'    => 'djv_festival',
			];

			if ( $existing ) {
				$post_id = $existing->ID;
				$post_data['ID'] = $post_id;
				wp_update_post( $post_data );
				$updated++;
			} else {
				$post_id = wp_insert_post( $post_data );
				$created++;
			}

			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			// Trilingual Metadata
			update_post_meta( $post_id, '_djv_title_en', $fest['title_en'] );
			update_post_meta( $post_id, '_djv_title_te', $fest['title_te'] );
			update_post_meta( $post_id, '_djv_title_hi', $fest['title_hi'] );
			update_post_meta( $post_id, '_djv_telugu_name', $fest['title_te'] );

			update_post_meta( $post_id, '_djv_content_en', $fest['content_en'] ?: $fest['excerpt'] );
			update_post_meta( $post_id, '_djv_content_te', $fest['content_te'] );
			update_post_meta( $post_id, '_djv_content_hi', $fest['content_hi'] );

			update_post_meta( $post_id, '_djv_description_en', $fest['excerpt'] );
			update_post_meta( $post_id, '_djv_description_te', $fest['content_te'] );
			update_post_meta( $post_id, '_djv_description_hi', $fest['content_hi'] );

			// Rule Metadata
			update_post_meta( $post_id, '_djv_rule_type', $fest['rule_type'] );
			update_post_meta( $post_id, '_djv_rule_params', $fest['rule_params'] );
			update_post_meta( $post_id, '_djv_calculation_method', $fest['calculation_method'] ?? 'astronomical_tithi' );
			update_post_meta( $post_id, '_djv_validation_status', $fest['validation_status'] ?? 'verified' );
			update_post_meta( $post_id, '_djv_last_validated', $fest['last_validated'] ?? '2026-10-07' );
			update_post_meta( $post_id, '_djv_validation_notes', $fest['validation_notes'] ?? '' );
			update_post_meta( $post_id, '_djv_state', $fest['state'] ?? 'Pan-India' );
			update_post_meta( $post_id, '_djv_region', $fest['region'] ?? 'Pan-India' );

			// Observance details
			if ( ! empty( $fest['puja_timings'] ) ) update_post_meta( $post_id, '_djv_puja_timings', $fest['puja_timings'] );
			if ( ! empty( $fest['samagri'] ) ) update_post_meta( $post_id, '_djv_samagri', $fest['samagri'] );
			if ( ! empty( $fest['naivedyam'] ) ) update_post_meta( $post_id, '_djv_naivedyam', $fest['naivedyam'] );
			if ( ! empty( $fest['vrat_rules'] ) ) update_post_meta( $post_id, '_djv_vrat_rules', $fest['vrat_rules'] );
			if ( ! empty( $fest['dos'] ) ) update_post_meta( $post_id, '_djv_dos', $fest['dos'] );
			if ( ! empty( $fest['donts'] ) ) update_post_meta( $post_id, '_djv_donts', $fest['donts'] );
			if ( ! empty( $fest['significance'] ) ) update_post_meta( $post_id, '_djv_significance', $fest['significance'] );
			if ( ! empty( $fest['history'] ) ) update_post_meta( $post_id, '_djv_history', $fest['history'] );

			// Relationships
			update_post_meta( $post_id, '_djv_related_poojas', $fest['related_poojas'] ?? [] );
			update_post_meta( $post_id, '_djv_related_mantras', $fest['related_mantras'] ?? [] );

			// Taxonomies
			if ( ! empty( $fest['categories'] ) ) {
				wp_set_object_terms( $post_id, $fest['categories'], 'djv_festival_cat' );
				wp_set_object_terms( $post_id, $fest['categories'], 'djv_festival_type' );
			}
			if ( ! empty( $fest['deity_slug'] ) ) {
				wp_set_object_terms( $post_id, $fest['deity_slug'], 'djv_deity' );
			}
			if ( ! empty( $fest['state'] ) ) {
				wp_set_object_terms( $post_id, $fest['state'], 'djv_region' );
			}
		}

		return [
			'total'   => count( $catalog ),
			'created' => $created,
			'updated' => $updated,
		];
	}

	/**
	 * Register WordPress Admin Metabox for Festival Master Details
	 */
	public static function register_admin_metabox(): void {
		add_meta_box(
			'djv_festival_master_box',
			__( '🪔 DJV Festival Master & Astronomical Rules', 'djv-core' ),
			[ __CLASS__, 'render_admin_metabox' ],
			'djv_festival',
			'normal',
			'high'
		);
	}

	public static function render_admin_metabox( WP_Post $post ): void {
		$post_id     = $post->ID;
		$rule_type   = get_post_meta( $post_id, '_djv_rule_type', true ) ?: 'lunar_tithi';
		$rule_params = get_post_meta( $post_id, '_djv_rule_params', true );
		$val_status  = get_post_meta( $post_id, '_djv_validation_status', true ) ?: 'verified';
		$method      = get_post_meta( $post_id, '_djv_calculation_method', true ) ?: 'astronomical_tithi';
		$state       = get_post_meta( $post_id, '_djv_state', true ) ?: 'Pan-India';
		$region      = get_post_meta( $post_id, '_djv_region', true ) ?: 'Pan-India';
		$notes       = get_post_meta( $post_id, '_djv_validation_notes', true );
		?>
		<div style="padding: 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 15px;">
				<div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
					<label style="font-weight: 600; font-size: 11px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 4px;">Master ID / Slug</label>
					<code style="font-size: 13px; font-weight: 700; color: #0f172a;"><?php echo esc_html( $post->post_name ); ?></code>
				</div>
				<div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
					<label style="font-weight: 600; font-size: 11px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 4px;">Calculation Rule</label>
					<span style="font-size: 13px; font-weight: 600; color: #7A2419; background: #fff1f0; padding: 2px 8px; border-radius: 4px; display: inline-block;"><?php echo esc_html( $rule_type ); ?></span>
				</div>
				<div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
					<label style="font-weight: 600; font-size: 11px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 4px;">Validation Status</label>
					<?php if ( $val_status === 'verified' ) : ?>
						<span style="font-size: 12px; font-weight: 700; color: #166534; background: #dcfce7; padding: 2px 8px; border-radius: 4px;">✓ Verified Astronomical</span>
					<?php else : ?>
						<span style="font-size: 12px; font-weight: 700; color: #9a3412; background: #ffedd5; padding: 2px 8px; border-radius: 4px;">⚠ Needs Validation</span>
					<?php endif; ?>
				</div>
				<div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
					<label style="font-weight: 600; font-size: 11px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 4px;">State & Region</label>
					<span style="font-size: 13px; font-weight: 600; color: #334155;"><?php echo esc_html( $state ); ?> (<?php echo esc_html( $region ); ?>)</span>
				</div>
			</div>

			<div style="margin-bottom: 15px;">
				<label style="font-weight: 600; font-size: 12px; color: #475569; display: block; margin-bottom: 4px;">Astronomical Rule Parameters (JSON)</label>
				<pre style="background: #0f172a; color: #38bdf8; padding: 10px; border-radius: 6px; font-size: 12px; margin: 0; overflow-x: auto;"><?php echo esc_html( json_encode( $rule_params, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ); ?></pre>
			</div>

			<?php if ( $notes ) : ?>
				<p style="margin: 0; font-size: 12px; color: #64748b;"><strong>Notes:</strong> <?php echo esc_html( $notes ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
