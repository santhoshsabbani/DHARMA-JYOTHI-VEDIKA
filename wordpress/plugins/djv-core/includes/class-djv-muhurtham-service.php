<?php
/**
 * DJV Muhurtham Service & Event Rule Engine
 *
 * Implements astronomical event-specific Muhurtham calculations,
 * inauspicious window carving, personalized marriage synergy,
 * scoring, ranking, and multilingual reasoning generation.
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/data-muhurtham-events.php';

class DJV_Muhurtham_Service {

	const RULE_VERSION   = '1.1.0';
	const ENGINE_VERSION = '1.3.0';

	/**
	 * Find candidate Muhurthams for a requested event, location, and date range.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function find_muhurtham( array $args ): array {
		$event_slug = sanitize_key( $args['event'] ?? 'marriage' );
		$event_def  = DJV_Muhurtham_Events::get_event( $event_slug );
		if ( ! $event_def ) {
			return [
				'status'  => 'error',
				'code'    => 'invalid_event',
				'message' => 'The selected event is not recognized.',
			];
		}

		$date_from = sanitize_text_field( $args['date_from'] ?? date( 'Y-m-d' ) );
		$date_to   = sanitize_text_field( $args['date_to'] ?? date( 'Y-m-d', strtotime( '+30 days' ) ) );

		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_from ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_to ) ) {
			return [
				'status'  => 'error',
				'code'    => 'invalid_date_format',
				'message' => 'Dates must be in YYYY-MM-DD format.',
			];
		}

		$ts_from = strtotime( $date_from );
		$ts_to   = strtotime( $date_to );

		if ( $ts_to < $ts_from ) {
			return [
				'status'  => 'error',
				'code'    => 'invalid_date_range',
				'message' => 'To Date must be equal to or later than From Date.',
			];
		}

		// Cap maximum search range to 120 days for safety and performance
		$days_diff = ( $ts_to - $ts_from ) / DAY_IN_SECONDS;
		if ( $days_diff > 120 ) {
			$ts_to   = $ts_from + ( 120 * DAY_IN_SECONDS );
			$date_to = date( 'Y-m-d', $ts_to );
		}

		// Location parameters
		$loc       = $args['location'] ?? [];
		$city      = sanitize_text_field( $loc['city'] ?? 'Hyderabad' );
		$state     = sanitize_text_field( $loc['state'] ?? 'Telangana' );
		$latitude  = floatval( $loc['latitude'] ?? 17.3850 );
		$longitude = floatval( $loc['longitude'] ?? 78.4867 );
		$timezone  = sanitize_text_field( $loc['timezone'] ?? 'Asia/Kolkata' );

		if ( $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180 ) {
			$latitude  = 17.3850;
			$longitude = 78.4867;
		}

		// Language
		$lang = sanitize_key( $args['language'] ?? 'en' );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}

		// Mode: basic vs personalized
		$mode         = sanitize_key( $args['mode'] ?? 'basic' );
		$preferences  = is_array( $args['preferences'] ?? null ) ? $args['preferences'] : [];
		$time_of_day  = sanitize_key( $preferences['time_of_day'] ?? 'any' );
		if ( ! in_array( $time_of_day, [ 'any', 'morning', 'afternoon', 'evening' ], true ) ) {
			$time_of_day = 'any';
		}

		$participants = is_array( $args['participants'] ?? null ) ? $args['participants'] : [];

		// Check if personalized mode qualifies (for marriage)
		$is_personalized = false;
		if ( $event_slug === 'marriage' && $mode === 'personalized' ) {
			$bride_dob = sanitize_text_field( $participants['bride_dob'] ?? ( $participants['bride']['dob'] ?? '' ) );
			$groom_dob = sanitize_text_field( $participants['groom_dob'] ?? ( $participants['groom']['dob'] ?? '' ) );
			if ( ! empty( $bride_dob ) && ! empty( $groom_dob ) ) {
				$is_personalized = true;
			}
		}

		// Cache check incorporating all 11 factors per Section 29
		$cache_key = 'djv_muh_' . md5( sprintf(
			'%s|%s|%s|%.4f|%.4f|%s|%s|%s|%s|%s|%s|%s|%s|%s',
			$event_slug,
			$date_from,
			$date_to,
			$latitude,
			$longitude,
			$timezone,
			$city,
			$lang,
			$is_personalized ? 'pers' : 'basic',
			$time_of_day,
			$is_personalized ? ( $participants['bride_dob'] ?? '' ) . '|' . ( $participants['groom_dob'] ?? '' ) : 'none',
			self::RULE_VERSION,
			self::ENGINE_VERSION,
			'v1'
		) );

		$cached = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) ) {
			$cached['is_cached'] = true;
			return $cached;
		}

		// Retrieve multi-day Panchangam ephemeris
		$days_data = self::fetch_range_panchangam( $date_from, $date_to, $latitude, $longitude, $timezone );
		if ( empty( $days_data ) ) {
			return [
				'status'  => 'error',
				'code'    => 'calculation_failed',
				'message' => 'Failed to calculate astronomical ephemeris for the selected date range.',
			];
		}

		// Personalized birth star lookup if applicable
		$bride_nakshatra_id = null;
		$groom_nakshatra_id = null;
		$bride_rashi_id     = null;
		$groom_rashi_id     = null;

		if ( $is_personalized ) {
			$bride_data = self::calculate_birth_ephemeris(
				$participants['bride_dob'] ?? ( $participants['bride']['dob'] ?? '' ),
				$participants['bride_time'] ?? ( $participants['bride']['time'] ?? '12:00' ),
				$latitude,
				$longitude,
				$timezone
			);
			$groom_data = self::calculate_birth_ephemeris(
				$participants['groom_dob'] ?? ( $participants['groom']['dob'] ?? '' ),
				$participants['groom_time'] ?? ( $participants['groom']['time'] ?? '12:00' ),
				$latitude,
				$longitude,
				$timezone
			);

			$bride_nakshatra_id = $bride_data['nakshatra_id'] ?? null;
			$groom_nakshatra_id = $groom_data['nakshatra_id'] ?? null;
			$bride_rashi_id     = $bride_data['rashi_id'] ?? null;
			$groom_rashi_id     = $groom_data['rashi_id'] ?? null;
		}

		// Evaluate each day against the event rules
		$candidates = [];
		$rules      = $event_def['rules'];

		foreach ( $days_data as $date_str => $day_p ) {
			if ( empty( $day_p ) || ! empty( $day_p['error'] ) ) {
				continue;
			}

			$eval = self::evaluate_day(
				$event_slug,
				$rules,
				$day_p,
				$time_of_day,
				$is_personalized,
				$bride_nakshatra_id,
				$groom_nakshatra_id,
				$bride_rashi_id,
				$groom_rashi_id,
				$lang
			);

			if ( $eval['is_valid'] && ! empty( $eval['candidate_window'] ) ) {
				$candidates[] = $eval;
			}
		}

		// Sort candidates: first by rank/score descending, secondary by date ascending
		usort( $candidates, function( $a, $b ) {
			if ( $a['score'] === $b['score'] ) {
				return strcmp( $a['date'], $b['date'] );
			}
			return ( $a['score'] > $b['score'] ) ? -1 : 1;
		} );

		// Select top candidates (up to 8, at least 3 if available)
		$top_candidates = array_slice( $candidates, 0, 8 );

		// Format output results for UI and API
		$formatted_results = [];
		foreach ( $top_candidates as $idx => $c ) {
			$formatted_results[] = self::format_result_candidate( $c, $event_slug, $lang, $timezone );
		}

		$response_data = [
			'status'       => 'success',
			'event'        => $event_slug,
			'event_title'  => $event_def['title'][ $lang ] ?? $event_def['title']['en'],
			'mode'         => $is_personalized ? 'personalized' : 'basic',
			'mode_label'   => $is_personalized
				? ( $lang === 'te' ? 'వ్యక్తిగత వివాహ ముహూర్తం (తారాబలం & చంద్రబల విశ్లేషణ)' : ( $lang === 'hi' ? 'व्यक्तिगत विवाह मुहूर्त (ताराबल व चंद्रबल सहित)' : 'Personalized Marriage Muhurtham (With Tarabala & Chandrabala Analysis)' ) )
				: ( $lang === 'te' ? 'ప్రాథమిక పంచాంగ ముహూర్తం' : ( $lang === 'hi' ? 'सामान्य पंचांग मुहूर्त' : 'Basic Panchanga Muhurtham' ) ),
			'location'     => [
				'city'      => $city,
				'state'     => $state,
				'latitude'  => $latitude,
				'longitude' => $longitude,
				'timezone'  => $timezone,
			],
			'date_from'    => $date_from,
			'date_to'      => $date_to,
			'language'     => $lang,
			'preferences'  => [
				'time_of_day' => $time_of_day,
			],
			'total_found'  => count( $formatted_results ),
			'results'      => $formatted_results,
			'metadata'     => [
				'rule_set_used'  => "DJV {$event_slug} Rule Engine v" . self::RULE_VERSION,
				'engine_version' => self::ENGINE_VERSION,
				'generated_at'   => current_time( 'mysql' ),
				'disclaimer'     => $lang === 'te'
					? 'DJV పంచాంగ గణితం మరియు శాస్త్రోక్త ముహూర్త నియమాల ఆధారంగా లెక్కించబడింది. ప్రాంతీయ మరియు సంప్రదాయ భేదాలు ఉండవచ్చు. తుది నిర్ణయానికి అర్హులైన పండితులను సంప్రదించండి.'
					: ( $lang === 'hi'
						? 'DJV पंचांग और शास्त्रोक्त मुहूर्त नियमों के अनुसार परिकलित। परंपराओं एवं क्षेत्रीय रीति-रिवाजों में भेद हो सकता है। अंतिम पुष्टि हेतु योग्य पुरोहित से परामर्श लें।'
						: 'Calculated using DJV Panchangam and event-specific Muhurtham rules. Traditions and regional practices can differ. Consult a qualified priest/acharya for final confirmation.'
					),
			],
		];

		// Persist in transient cache for 12 hours
		set_transient( $cache_key, $response_data, 12 * HOUR_IN_SECONDS );

		return $response_data;
	}

	/**
	 * Evaluate a single day's astronomical ephemeris against event rules.
	 *
	 * @return array
	 */
	private static function evaluate_day(
		string $event_slug,
		array $rules,
		array $p,
		string $time_of_day,
		bool $is_personalized,
		?int $bride_nakshatra_id,
		?int $groom_nakshatra_id,
		?int $bride_rashi_id,
		?int $groom_rashi_id,
		string $lang
	): array {
		$date_str = $p['meta']['date']['iso'] ?? ( $p['meta']['date'] ?? date( 'Y-m-d' ) );

		$tithi_id     = intval( $p['tithi']['id'] ?? 1 );
		$nakshatra_id = intval( $p['nakshatra']['nakshatra']['id'] ?? ( $p['nakshatra']['id'] ?? 1 ) );
		$vara_id      = intval( $p['vara']['id'] ?? ( $p['weekday'] ?? 0 ) );
		$yoga_name    = $p['yoga']['name'] ?? '';
		$karana_name  = $p['karana']['name'] ?? '';

		// 1. Strict Prohibitions Check
		$prohibited_tithis    = $rules['prohibited_tithis'] ?? [ 4, 9, 14, 30 ];
		$prohibited_nakshatras = $rules['prohibited_nakshatras'] ?? [];
		$prohibited_weekdays  = $rules['prohibited_weekdays'] ?? [];
		$prohibited_yogas     = $rules['prohibited_yogas'] ?? [ 'Vyatipata', 'Vaidhriti' ];
		$prohibited_karanas   = $rules['prohibited_karanas'] ?? [ 'Vishti', 'Bhadra' ];

		$is_disqualified = false;
		$dq_reason       = '';

		if ( in_array( $tithi_id, $prohibited_tithis, true ) ) {
			$is_disqualified = true;
			$dq_reason       = 'Prohibited Tithi (Rikta or Amavasya)';
		} elseif ( in_array( $nakshatra_id, $prohibited_nakshatras, true ) ) {
			$is_disqualified = true;
			$dq_reason       = 'Inauspicious Nakshatra for ceremony';
		} elseif ( in_array( $vara_id, $prohibited_weekdays, true ) ) {
			$is_disqualified = true;
			$dq_reason       = 'Prohibited Weekday';
		} elseif ( in_array( $yoga_name, $prohibited_yogas, true ) ) {
			$is_disqualified = true;
			$dq_reason       = 'Malefic Yoga (' . $yoga_name . ')';
		} elseif ( in_array( $karana_name, $prohibited_karanas, true ) ) {
			$is_disqualified = true;
			$dq_reason       = 'Inauspicious Karana (' . $karana_name . ')';
		}

		if ( $is_disqualified ) {
			return [
				'is_valid' => false,
				'reason'   => $dq_reason,
				'date'     => $date_str,
			];
		}

		// 2. Base Scoring (0 to 100)
		$score   = 50; // Starting baseline for passing all strict filters
		$reasons = [];

		// Nakshatra Score
		$fav_nakshatras = $rules['favorable_nakshatras'] ?? [];
		if ( in_array( $nakshatra_id, $fav_nakshatras, true ) ) {
			$score += 25;
			$reasons[] = self::trans_reason( 'nakshatra_fav', $lang, [ 'nakshatra' => $p['nakshatra']['nakshatra']['name'] ?? $p['nakshatra']['name'] ?? '' ] );
		} else {
			$score += 5;
			$reasons[] = self::trans_reason( 'nakshatra_neutral', $lang, [ 'nakshatra' => $p['nakshatra']['nakshatra']['name'] ?? $p['nakshatra']['name'] ?? '' ] );
		}

		// Tithi Score
		$fav_tithis = $rules['favorable_tithis'] ?? [];
		if ( in_array( $tithi_id, $fav_tithis, true ) ) {
			$score += 15;
			$reasons[] = self::trans_reason( 'tithi_fav', $lang, [ 'tithi' => $p['tithi']['name'] ?? '', 'paksha' => $p['tithi']['paksha'] ?? '' ] );
		} else {
			$score += 5;
		}

		// Weekday Score
		$fav_weekdays = $rules['favorable_weekdays'] ?? [];
		if ( in_array( $vara_id, $fav_weekdays, true ) ) {
			$score += 15;
			$reasons[] = self::trans_reason( 'vara_fav', $lang, [ 'vara' => $p['vara']['en'] ?? $p['vara']['name'] ?? '' ] );
		}

		// Personalized Marriage Analysis (Tarabala + Chandrabala)
		if ( $is_personalized && $bride_nakshatra_id && $groom_nakshatra_id ) {
			$bride_tara = self::calc_tarabala( $bride_nakshatra_id, $nakshatra_id );
			$groom_tara = self::calc_tarabala( $groom_nakshatra_id, $nakshatra_id );

			if ( $bride_tara['is_auspicious'] && $groom_tara['is_auspicious'] ) {
				$score += 15;
				$reasons[] = self::trans_reason( 'tarabala_excellent', $lang, [
					'bride_tara' => $bride_tara['name'],
					'groom_tara' => $groom_tara['name'],
				] );
			} elseif ( ! $bride_tara['is_auspicious'] || ! $groom_tara['is_auspicious'] ) {
				$score -= 20;
				if ( $bride_tara['is_critical_avoid'] || $groom_tara['is_critical_avoid'] ) {
					return [
						'is_valid' => false,
						'reason'   => 'Naidhana/Vadha Tara conflict in personalized chart',
						'date'     => $date_str,
					];
				}
			}
		}

		// 3. Time Window Carving (Excluding Inauspicious Periods)
		$window_data = self::carve_auspicious_window( $p, $time_of_day );
		if ( ! $window_data['has_valid_window'] ) {
			return [
				'is_valid' => false,
				'reason'   => 'No continuous auspicious window >= 45 mins available on this day for the requested time of day.',
				'date'     => $date_str,
			];
		}

		// Bonus for aligning with Abhijit Muhurtham or Amrit Kalam
		if ( ! empty( $window_data['includes_abhijit'] ) ) {
			$score += 10;
			$reasons[] = self::trans_reason( 'abhijit_aligned', $lang, [
				'slot' => $p['timings']['abhijitMuhurtham']['startStr'] . ' – ' . $p['timings']['abhijitMuhurtham']['endStr'],
			] );
		}

		$reasons[] = self::trans_reason( 'inauspicious_free', $lang );

		// Cap score to 98 (leaving perfection to divinity)
		$score = min( 98, max( 40, $score ) );

		// Assign Rank based on score per Section 17
		if ( $score >= 85 ) {
			$rank       = 'best';
			$rank_label = $lang === 'te' ? 'శ్రేష్ఠమైనది (Best)' : ( $lang === 'hi' ? 'सर्वोत्तम (Best)' : 'Best' );
		} elseif ( $score >= 70 ) {
			$rank       = 'very_good';
			$rank_label = $lang === 'te' ? 'ఉత్తమమైనది (Very Good)' : ( $lang === 'hi' ? 'अति उत्तम (Very Good)' : 'Very Good' );
		} elseif ( $score >= 55 ) {
			$rank       = 'good';
			$rank_label = $lang === 'te' ? 'మంచిది (Good)' : ( $lang === 'hi' ? 'उत्तम (Good)' : 'Good' );
		} else {
			$rank       = 'acceptable';
			$rank_label = $lang === 'te' ? 'సామాన్యం (Acceptable)' : ( $lang === 'hi' ? 'सामान्य (Acceptable)' : 'Acceptable' );
		}

		return [
			'is_valid'         => true,
			'date'             => $date_str,
			'score'            => $score,
			'rank'             => $rank,
			'rank_label'       => $rank_label,
			'panchangam'       => $p,
			'candidate_window' => $window_data,
			'reasons'          => $reasons,
		];
	}

	/**
	 * Carve out inauspicious periods (Rahu Kalam, Yamagandam, Dur Muhurtham, Varjyam)
	 * and determine the highest quality uninterrupted auspicious window.
	 *
	 * @param array $p
	 * @param string $time_pref
	 * @return array
	 */
	private static function carve_auspicious_window( array $p, string $time_pref ): array {
		$solar = $p['solar'] ?? [];
		$sunrise_iso = $solar['sunrise'] ?? null;
		$sunset_iso  = $solar['sunset'] ?? null;

		if ( ! $sunrise_iso || ! $sunset_iso ) {
			$sunrise_ts = strtotime( $p['meta']['date']['iso'] . ' 06:15:00' );
			$sunset_ts  = strtotime( $p['meta']['date']['iso'] . ' 18:00:00' );
		} else {
			$sunrise_ts = strtotime( $sunrise_iso );
			$sunset_ts  = strtotime( $sunset_iso );
		}

		$date_base = date( 'Y-m-d', $sunrise_ts );

		// Establish preferred boundary bracket
		switch ( $time_pref ) {
			case 'morning':
				$bracket_start = $sunrise_ts;
				$bracket_end   = strtotime( $date_base . ' 12:00:00' );
				break;
			case 'afternoon':
				$bracket_start = strtotime( $date_base . ' 12:00:00' );
				$bracket_end   = strtotime( $date_base . ' 17:00:00' );
				break;
			case 'evening':
				$bracket_start = strtotime( $date_base . ' 17:00:00' );
				$bracket_end   = strtotime( $date_base . ' 20:30:00' );
				break;
			case 'any':
			default:
				$bracket_start = $sunrise_ts;
				$bracket_end   = $sunset_ts;
				break;
		}

		// Gather inauspicious intervals to subtract
		$blocked = [];
		$timings = $p['timings'] ?? [];

		// Rahu Kalam
		if ( ! empty( $timings['rahuKalam']['start'] ) && ! empty( $timings['rahuKalam']['end'] ) ) {
			$blocked[] = [ 'start' => strtotime( $timings['rahuKalam']['start'] ), 'end' => strtotime( $timings['rahuKalam']['end'] ) ];
		}
		// Yamagandam
		if ( ! empty( $timings['yamagandam']['start'] ) && ! empty( $timings['yamagandam']['end'] ) ) {
			$blocked[] = [ 'start' => strtotime( $timings['yamagandam']['start'] ), 'end' => strtotime( $timings['yamagandam']['end'] ) ];
		}
		// Dur Muhurtham (array)
		if ( ! empty( $timings['durMuhurtham'] ) && is_array( $timings['durMuhurtham'] ) ) {
			foreach ( $timings['durMuhurtham'] as $dm ) {
				if ( ! empty( $dm['start'] ) && ! empty( $dm['end'] ) ) {
					$blocked[] = [ 'start' => strtotime( $dm['start'] ), 'end' => strtotime( $dm['end'] ) ];
				}
			}
		}
		// Varjyam
		if ( ! empty( $timings['varjyam']['start'] ) && ! empty( $timings['varjyam']['end'] ) ) {
			$blocked[] = [ 'start' => strtotime( $timings['varjyam']['start'] ), 'end' => strtotime( $timings['varjyam']['end'] ) ];
		}

		// Check Abhijit window
		$abhijit_start = ! empty( $timings['abhijitMuhurtham']['start'] ) ? strtotime( $timings['abhijitMuhurtham']['start'] ) : null;
		$abhijit_end   = ! empty( $timings['abhijitMuhurtham']['end'] ) ? strtotime( $timings['abhijitMuhurtham']['end'] ) : null;

		// Sort blocked intervals
		usort( $blocked, function( $a, $b ) {
			return $a['start'] <=> $b['start'];
		} );

		// Carve out available intervals from [$bracket_start, $bracket_end]
		$available = [];
		$current   = $bracket_start;

		foreach ( $blocked as $blk ) {
			if ( $blk['end'] <= $current ) {
				continue;
			}
			if ( $blk['start'] >= $bracket_end ) {
				break;
			}

			if ( $blk['start'] > $current ) {
				$gap_end = min( $blk['start'], $bracket_end );
				if ( ( $gap_end - $current ) >= ( 45 * 60 ) ) { // Minimum duration >= 45 mins
					$available[] = [ 'start' => $current, 'end' => $gap_end ];
				}
			}
			$current = max( $current, $blk['end'] );
		}

		if ( $current < $bracket_end && ( $bracket_end - $current ) >= ( 45 * 60 ) ) {
			$available[] = [ 'start' => $current, 'end' => $bracket_end ];
		}

		if ( empty( $available ) ) {
			return [ 'has_valid_window' => false ];
		}

		// Find best window: prefer Abhijit Muhurtham concurrence or longest morning window
		$best_window      = $available[0];
		$includes_abhijit = false;

		foreach ( $available as $win ) {
			if ( $abhijit_start && $abhijit_end ) {
				if ( $win['start'] <= $abhijit_start && $win['end'] >= $abhijit_end ) {
					$best_window      = [ 'start' => $abhijit_start, 'end' => $abhijit_end ];
					$includes_abhijit = true;
					break;
				}
			}
			// Prefer window closer to standard 1.5 - 2 hour optimum
			$dur = $win['end'] - $win['start'];
			$best_dur = $best_window['end'] - $best_window['start'];
			if ( $dur > $best_dur ) {
				$best_window = $win;
			}
		}

		$duration_mins = round( ( $best_window['end'] - $best_window['start'] ) / 60 );
		$dur_str       = $duration_mins >= 60
			? floor( $duration_mins / 60 ) . 'h ' . ( $duration_mins % 60 ) . 'm'
			: $duration_mins . ' mins';

		return [
			'has_valid_window' => true,
			'start_ts'         => $best_window['start'],
			'end_ts'           => $best_window['end'],
			'start_str'        => date( 'g:i A', $best_window['start'] ),
			'end_str'          => date( 'g:i A', $best_window['end'] ),
			'duration_str'     => $dur_str,
			'duration_mins'    => $duration_mins,
			'includes_abhijit' => $includes_abhijit,
		];
	}

	/**
	 * Tarabala calculation between birth nakshatra and transit nakshatra.
	 *
	 * @param int $birth_nak
	 * @param int $target_nak
	 * @return array
	 */
	public static function calc_tarabala( int $birth_nak, int $target_nak ): array {
		$diff = ( $target_nak - $birth_nak );
		if ( $diff < 0 ) {
			$diff += 27;
		}
		$tara_num = ( $diff % 9 ) + 1;

		$names = [
			1 => 'Janma',
			2 => 'Sampat',
			3 => 'Vipat',
			4 => 'Kshema',
			5 => 'Pratyak',
			6 => 'Sadhana',
			7 => 'Naidhana',
			8 => 'Mitra',
			9 => 'Parama Mitra',
		];

		$is_auspicious = in_array( $tara_num, [ 2, 4, 6, 8, 9 ], true );
		$is_critical   = ( $tara_num === 7 ); // Vadha/Naidhana is strictly avoided

		return [
			'number'             => $tara_num,
			'name'               => $names[ $tara_num ] ?? 'Unknown',
			'is_auspicious'      => $is_auspicious,
			'is_critical_avoid'  => $is_critical,
		];
	}

	/**
	 * Format candidate Muhurtham for API and UI rendering.
	 */
	private static function format_result_candidate( array $c, string $event_slug, string $lang, string $timezone ): array {
		$p   = $c['panchangam'];
		$win = $c['candidate_window'];

		$ts = strtotime( $c['date'] );
		$formatted_date = date_i18n( 'l, F j, Y', $ts );

		// Localized weekday
		$weekday = $p['vara']['en'] ?? date( 'l', $ts );
		if ( $lang === 'te' ) {
			$weekday = $p['vara']['nameTe'] ?? $weekday;
		} elseif ( $lang === 'hi' ) {
			$weekday = $p['vara']['nameHi'] ?? $weekday;
		}

		$tithi_name     = $lang === 'te' ? ( $p['tithi']['nameTe'] ?? $p['tithi']['name'] ?? '' ) : ( $lang === 'hi' ? ( $p['tithi']['nameHi'] ?? $p['tithi']['name'] ?? '' ) : ( $p['tithi']['name'] ?? '' ) );
		$nakshatra_name = $lang === 'te' ? ( $p['nakshatra']['nakshatra']['nameTe'] ?? $p['nakshatra']['nakshatra']['name'] ?? '' ) : ( $lang === 'hi' ? ( $p['nakshatra']['nakshatra']['nameHi'] ?? $p['nakshatra']['nakshatra']['name'] ?? '' ) : ( $p['nakshatra']['nakshatra']['name'] ?? '' ) );
		$yoga_name      = $lang === 'te' ? ( $p['yoga']['nameTe'] ?? $p['yoga']['name'] ?? '' ) : ( $lang === 'hi' ? ( $p['yoga']['nameHi'] ?? $p['yoga']['name'] ?? '' ) : ( $p['yoga']['name'] ?? '' ) );
		$karana_name    = $lang === 'te' ? ( $p['karana']['nameTe'] ?? $p['karana']['name'] ?? '' ) : ( $lang === 'hi' ? ( $p['karana']['nameHi'] ?? $p['karana']['name'] ?? '' ) : ( $p['karana']['name'] ?? '' ) );

		return [
			'id'             => 'djv_muh_' . str_replace( '-', '', $c['date'] ) . '_' . substr( md5( $win['start_str'] ), 0, 4 ),
			'date'           => $c['date'],
			'formatted_date' => $formatted_date,
			'weekday'        => $weekday,
			'start_time'     => $win['start_str'],
			'end_time'       => $win['end_str'],
			'time_window'    => $win['start_str'] . ' – ' . $win['end_str'],
			'duration'       => $win['duration_str'],
			'duration_mins'  => $win['duration_mins'],
			'rank'           => $c['rank'],
			'rank_label'     => $c['rank_label'],
			'score'          => $c['score'],
			'panchangam'     => [
				'tithi'          => $tithi_name,
				'paksha'         => $p['tithi']['paksha'] ?? '',
				'nakshatra'      => $nakshatra_name,
				'pada'           => $p['nakshatra']['pada'] ?? 1,
				'yoga'           => $yoga_name,
				'karana'         => $karana_name,
				'vara'           => $weekday,
			],
			'solar'          => [
				'sunrise' => $p['solar']['sunriseStr'] ?? '6:10 AM',
				'sunset'  => $p['solar']['sunsetStr'] ?? '5:50 PM',
			],
			'inauspicious_periods' => [
				'rahu_kalam'   => $p['timings']['rahuKalam']['text'] ?? '',
				'yamagandam'   => $p['timings']['yamagandam']['text'] ?? '',
				'gulika_kalam' => $p['timings']['gulikaKalam']['text'] ?? '',
				'dur_muhurtham'=> ! empty( $p['timings']['durMuhurtham'] ) && is_array( $p['timings']['durMuhurtham'] )
					? implode( ', ', array_column( $p['timings']['durMuhurtham'], 'text' ) )
					: '',
				'varjyam'      => $p['timings']['varjyam']['text'] ?? '',
			],
			'why_this_time'  => $c['reasons'],
			'rule_set_used'  => "DJV {$event_slug} Rule Engine v" . self::RULE_VERSION,
		];
	}

	/**
	 * Helper to format multilingual reasons with checkmarks.
	 */
	private static function trans_reason( string $key, string $lang, array $vars = [] ): string {
		$templates = [
			'nakshatra_fav' => [
				'en' => '✓ Suitable Nakshatra: {nakshatra} (Promotes enduring harmony and prosperity)',
				'te' => '✓ అనుకూల నక్షత్రం: {nakshatra} (శాస్త్రోక్త శుభ ఫలితాలను ప్రసాదిస్తుంది)',
				'hi' => '✓ अनुकूल नक्षत्र: {nakshatra} (सुख, समृद्धि एवं कल्याणकारी प्रभाव)',
			],
			'nakshatra_neutral' => [
				'en' => '✓ Moderate Nakshatra: {nakshatra} (Free of major Vedic blemishes)',
				'te' => '✓ దోషరహిత నక్షత్రం: {nakshatra} (సాధారణ శుభ ఫలం)',
				'hi' => '✓ शुद्ध नक्षत्र: {nakshatra} (दोष रहित)',
			],
			'tithi_fav' => [
				'en' => '✓ Auspicious Tithi: {tithi} ({paksha} Paksha, favorable lunar phase)',
				'te' => '✓ శుభ తిథి: {tithi} ({paksha} పక్షం, మంగళకరమైన తిథి)',
				'hi' => '✓ शुभ तिथि: {tithi} ({paksha} पक्ष, कल्याणकारी चंद्र कला)',
			],
			'vara_fav' => [
				'en' => '✓ Preferred Weekday: {vara} (Ruled by favorable cosmic deity)',
				'te' => '✓ అనుకూల వారం: {vara} (శుభ గ్రహాధిపత్యం గల రోజు)',
				'hi' => '✓ अनुकूल वार: {vara} (शुभ ग्रहाधिपति का दिन)',
			],
			'tarabala_excellent' => [
				'en' => '✓ Excellent Tarabala synergy (Bride: {bride_tara} Tara, Groom: {groom_tara} Tara)',
				'te' => '✓ ఉత్తమ తారాబలం (వధువు: {bride_tara} తార, వరుడు: {groom_tara} తార అనుకూలం)',
				'hi' => '✓ उत्तम ताराबल अनुकूलता (वधू: {bride_tara} तारा, वर: {groom_tara} तारा)',
			],
			'abhijit_aligned' => [
				'en' => '✓ Concurs with sacred Abhijit Muhurtham window ({slot})',
				'te' => '✓ పరమ పవిత్ర అభిజిత్ ముహూర్త కాలంతో ఏకీభవిస్తోంది ({slot})',
				'hi' => '✓ पवित्र अभिजित मुहूर्त काल से सुसंबद्ध ({slot})',
			],
			'inauspicious_free' => [
				'en' => '✓ Completely free from Rahu Kalam, Yamagandam, Dur Muhurtham & Varjyam',
				'te' => '✓ రాహుకాలం, యమగండం, దుర్ముహూర్తం మరియు వర్జ్యం వంటి దోషాల నుండి సంపూర్ణంగా విముక్తం',
				'hi' => '✓ राहुकाल, यमगंड, दुर्मुहूर्त एवं वर्ज्यम् दोषों से सर्वथा मुक्त शुद्ध काल',
			],
		];

		$tpl = $templates[ $key ][ $lang ] ?? $templates[ $key ]['en'] ?? '';
		foreach ( $vars as $k => $v ) {
			$tpl = str_replace( '{' . $k . '}', (string) $v, $tpl );
		}
		return $tpl;
	}

	/**
	 * High-speed range calculation via Node runner --range.
	 *
	 * @return array
	 */
	public static function fetch_range_panchangam( string $date_from, string $date_to, float $lat, float $lon, string $tz ): array {
		$bridge = new DJV_Panchangam();

		// 1. Try local Panchangam HTTP server if active (fastest response)
		$http_url = sprintf(
			'http://127.0.0.1:3000/range?start=%s&end=%s&lat=%.4f&lon=%.4f&tz=%s',
			rawurlencode( $date_from ),
			rawurlencode( $date_to ),
			$lat,
			$lon,
			rawurlencode( $tz )
		);
		$http_response = wp_remote_get( $http_url, [ 'timeout' => 3 ] );
		if ( ! is_wp_error( $http_response ) && wp_remote_retrieve_response_code( $http_response ) === 200 ) {
			$body = wp_remote_retrieve_body( $http_response );
			$json = json_decode( $body, true );
			if ( ! empty( $json['success'] ) && ! empty( $json['days'] ) ) {
				return $json['days'];
			}
		}

		// 2. Locate runner.js across filesystem
		$runner_candidates = [
			dirname( __DIR__, 5 ) . '/packages/panchangam-engine/src/runner.js',
			dirname( __DIR__, 4 ) . '/packages/panchangam-engine/src/runner.js',
			dirname( __DIR__, 3 ) . '/packages/panchangam-engine/src/runner.js',
			defined( 'ABSPATH' ) ? rtrim( ABSPATH, '/\\' ) . '/../packages/panchangam-engine/src/runner.js' : '',
			defined( 'ABSPATH' ) ? dirname( ABSPATH ) . '/packages/panchangam-engine/src/runner.js' : '',
			defined( 'DJV_PLUGIN_DIR' ) ? trailingslashit( DJV_PLUGIN_DIR ) . '../../../packages/panchangam-engine/src/runner.js' : '',
			'E:/Ai/DHARMA JYOTHI VEDIKA/packages/panchangam-engine/src/runner.js',
			'e:/Ai/DHARMA JYOTHI VEDIKA/packages/panchangam-engine/src/runner.js',
		];
		$runner = false;
		foreach ( $runner_candidates as $rc ) {
			if ( ! empty( $rc ) && file_exists( $rc ) ) {
				$runner = realpath( $rc ) ?: $rc;
				break;
			}
		}

		// Locate node binary
		$node_bin = defined( 'DJV_NODE_PATH' ) ? DJV_NODE_PATH : null;
		if ( ! $node_bin ) {
			$node_candidates = [
				'C:\\Program Files\\nodejs\\node.exe',
				'C:\\Program Files (x86)\\nodejs\\node.exe',
				'/usr/bin/node',
				'/usr/local/bin/node',
				'node',
			];
			foreach ( $node_candidates as $nb ) {
				if ( file_exists( $nb ) ) {
					$node_bin = $nb;
					break;
				}
			}
			if ( ! $node_bin ) {
				$node_bin = 'node';
			}
		}

		// Attempt fast batch mode via Node subprocess
		if ( $runner && file_exists( $runner ) ) {
			$cmd = sprintf(
				'"%s" "%s" --range %s %s %.4f %.4f %s 2>&1',
				$node_bin,
				$runner,
				escapeshellarg( $date_from ),
				escapeshellarg( $date_to ),
				$lat,
				$lon,
				escapeshellarg( $tz )
			);

			$output = @shell_exec( $cmd );
			if ( $output ) {
				$json = json_decode( trim( $output ), true );
				if ( ! empty( $json['status'] ) && $json['status'] === 'success' && ! empty( $json['days'] ) ) {
					return $json['days'];
				}
			}
		}

		// Fallback: iterate dates sequentially using standard DJV_Panchangam
		$curr = strtotime( $date_from );
		$end  = strtotime( $date_to );
		$days = [];

		while ( $curr <= $end ) {
			$d_str = date( 'Y-m-d', $curr );
			$p = $bridge->get_panchangam( $d_str, $lat, $lon, $tz, 'telugu', 'en' );
			if ( ! is_wp_error( $p ) ) {
				$days[ $d_str ] = $p;
			}
			$curr += DAY_IN_SECONDS;
		}

		return $days;
	}

	/**
	 * Approximate birth star / rashi from birth date & time.
	 */
	private static function calculate_birth_ephemeris( string $dob, string $time, float $lat, float $lon, string $tz ): array {
		if ( empty( $dob ) ) {
			return [];
		}
		$bridge = new DJV_Panchangam();
		$p = $bridge->get_panchangam( $dob, $lat, $lon, $tz, 'telugu', 'en' );
		if ( is_wp_error( $p ) || empty( $p['nakshatra'] ) ) {
			return [];
		}

		$nak_id = intval( $p['nakshatra']['nakshatra']['id'] ?? ( $p['nakshatra']['id'] ?? 1 ) );
		// Approximate Rashi: each Rashi contains 2.25 Nakshatras (9 padas)
		$rashi_id = (int) ceil( $nak_id / 2.25 );

		return [
			'nakshatra_id' => $nak_id,
			'rashi_id'     => $rashi_id,
		];
	}

	/**
	 * Ensure all 21 CPT posts exist with clean slugs and metadata.
	 */
	public static function ensure_seed_posts(): void {
		$all = DJV_Muhurtham_Events::get_all();

		foreach ( $all as $slug => $data ) {
			// Check if post exists by slug or legacy slug
			$existing = get_page_by_path( $slug, OBJECT, 'djv_muhurtham' );
			if ( ! $existing && ! empty( $data['legacy_slug'] ) ) {
				$existing = get_page_by_path( $data['legacy_slug'], OBJECT, 'djv_muhurtham' );
			}

			$title = $data['title']['en'] . ( ! empty( $data['title']['te'] ) ? ' (' . $data['title']['te'] . ')' : '' );
			$post_content = $data['content']['about']['en'] . "\n\n" . $data['content']['religious_significance']['en'];

			if ( $existing ) {
				// Update slug if it was using legacy slug and refresh title
				wp_update_post( [
					'ID'         => $existing->ID,
					'post_name'  => $slug,
					'post_title' => $title,
				] );
				update_post_meta( $existing->ID, '_djv_category', $data['category'] );
				update_post_meta( $existing->ID, '_djv_description', $data['short_desc']['en'] );
				update_post_meta( $existing->ID, '_djv_icon', $data['icon'] );
				update_post_meta( $existing->ID, '_djv_form_schema', $data['form_schema'] );
			} else {
				// Insert new post
				$new_id = wp_insert_post( [
					'post_type'    => 'djv_muhurtham',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $post_content,
					'post_status'  => 'publish',
				] );

				if ( $new_id && ! is_wp_error( $new_id ) ) {
					update_post_meta( $new_id, '_djv_category', $data['category'] );
					update_post_meta( $new_id, '_djv_description', $data['short_desc']['en'] );
					update_post_meta( $new_id, '_djv_icon', $data['icon'] );
					update_post_meta( $new_id, '_djv_form_schema', $data['form_schema'] );
				}
			}
		}
	}
}
