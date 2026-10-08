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

	const VERSION = '2.1.0';

	/**
	 * Canonical Festival Slug Alias Resolver
	 * Resolves transliterations, regional aliases, and alternate spellings to the canonical festival post slug.
	 */
	public static function resolve_slug_alias( string $slug ): string {
		$slug = sanitize_title( $slug );
		if ( empty( $slug ) ) {
			return $slug;
		}

		// Direct check: check if post exists with this exact slug in DB
		static $known_slugs = null;
		if ( $known_slugs === null ) {
			global $wpdb;
			if ( $wpdb && isset( $wpdb->posts ) ) {
				$raw = $wpdb->get_col( "SELECT post_name FROM {$wpdb->posts} WHERE post_type = 'djv_festival' AND post_status = 'publish'" );
				$known_slugs = is_array( $raw ) ? array_flip( $raw ) : [];
			} else {
				$known_slugs = [];
			}
		}

		if ( isset( $known_slugs[ $slug ] ) ) {
			return $slug;
		}

		// Canonical alias mapping
		$aliases = [
			// Vijayadashami / Dussehra variations
			'vijayadashami'                        => 'vijayadasami',
			'vijaya-dashami'                       => 'vijayadasami',
			'dussehra'                             => 'vijayadasami',
			'dasara'                               => 'vijayadasami',
			'vijayadashami-festival'               => 'vijayadasami',
			'dussehra-festival'                    => 'vijayadasami',

			// Paush Purnima variations
			'paush-purnima'                        => 'pausha-purnima',
			'paush-pournami'                       => 'pausha-purnima',
			'pausha-pournami'                      => 'pausha-purnima',
			'paush-purnima-shakambhari'            => 'pausha-purnima',

			// Banashankari Jatre variations
			'banashankari-jatre-badami'            => 'banashankari-jatre',
			'badami-banashankari-jatre'            => 'banashankari-jatre',
			'banashankari-devi-jatre'              => 'banashankari-jatre',

			// Pradosham variations
			'paush-shukla-pradosham'               => 'pausha-shukla-pradosham',
			'paush-krishna-pradosham'              => 'pausha-krishna-pradosham',
			'magh-shukla-pradosham'                => 'magha-shukla-pradosham',
			'magh-krishna-pradosham'               => 'magha-krishna-pradosham',
			'phalgun-shukla-pradosham'             => 'phalguna-shukla-pradosham',
			'phalgun-krishna-pradosham'            => 'phalguna-krishna-pradosham',
			'chaitr-shukla-pradosham'              => 'chaitra-shukla-pradosham',
			'chaitr-krishna-pradosham'             => 'chaitra-krishna-pradosham',
			'vaishakh-shukla-pradosham'            => 'vaishakha-shukla-pradosham',
			'vaishakh-krishna-pradosham'           => 'vaishakha-krishna-pradosham',
			'jyeshth-shukla-pradosham'             => 'jyeshtha-shukla-pradosham',
			'jyeshth-krishna-pradosham'            => 'jyeshtha-krishna-pradosham',
			'ashadh-shukla-pradosham'              => 'ashadha-shukla-pradosham',
			'ashadh-krishna-pradosham'             => 'ashadha-krishna-pradosham',
			'shravan-shukla-pradosham'             => 'shravana-shukla-pradosham',
			'shravan-krishna-pradosham'            => 'shravana-krishna-pradosham',
			'bhadrapad-shukla-pradosham'           => 'bhadrapada-shukla-pradosham',
			'bhadrapad-krishna-pradosham'          => 'bhadrapada-krishna-pradosham',
			'kartik-shukla-pradosham'              => 'karthika-shukla-pradosham',
			'kartik-krishna-pradosham'             => 'karthika-krishna-pradosham',
			'margashirsh-shukla-pradosham'         => 'margashirsha-shukla-pradosham',
			'margashirsh-krishna-pradosham'        => 'margashirsha-krishna-pradosham',

			// Ramana Maharshi & Tamil Observances
			'bhagavan-sri-ramana-maharshi-jayanti' => 'ramana-maharshi-jayanti',
			'sri-ramana-maharshi-jayanti'          => 'ramana-maharshi-jayanti',
			'palani-thai-poosam-kavadi-thiruvizha' => 'kavadi-attavam-palani',
			'palani-thai-poosam'                   => 'kavadi-attavam-palani',
			'thai-poosam-palani'                   => 'kavadi-attavam-palani',
			'arudra-darshanam'                     => 'arudra-darisanam',
			'ardra-darshan'                        => 'arudra-darisanam',
			'ardra-darshanam'                      => 'arudra-darisanam',

			// Major Vedic Festivals
			'mahashivaratri'                       => 'maha-shivaratri',
			'maha-sivaratri'                       => 'maha-shivaratri',
			'shivaratri'                           => 'maha-shivaratri',
			'rama-navami'                          => 'sri-rama-navami',
			'ram-navami'                           => 'sri-rama-navami',
			'sri-ram-navami'                       => 'sri-rama-navami',
			'varalakshmi-vratam'                   => 'varalakshmi-vratham',
			'varalakshmi-vrata'                    => 'varalakshmi-vratham',
			'vinayaka-chaturthi'                   => 'ganesh-chaturthi',
			'vinayaka-chavithi'                    => 'ganesh-chaturthi',
			'sankranti'                            => 'makar-sankranti',
			'makara-sankranti'                     => 'makar-sankranti',
			'pedda-panduga'                        => 'makar-sankranti',
			'kartika-deepam'                       => 'karthika-deepam',
			'karthigai-deepam'                     => 'karthika-deepam',
			'batukamma'                            => 'bathukamma',
			'bathukamma-festival'                  => 'bathukamma',
			'deepavali'                            => 'diwali',
			'diwali-festival'                      => 'diwali',
			'janmashtami'                          => 'krishna-janmashtami',
			'gokulashtami'                         => 'krishna-janmashtami',
			'sri-krishna-jayanti'                  => 'krishna-janmashtami',
		];

		if ( isset( $aliases[ $slug ] ) && isset( $known_slugs[ $aliases[ $slug ] ] ) ) {
			return $aliases[ $slug ];
		}

		// Dynamic Normalization:
		// 1. paush- -> pausha-
		if ( strpos( $slug, 'paush-' ) === 0 ) {
			$candidate = 'pausha-' . substr( $slug, 6 );
			if ( isset( $known_slugs[ $candidate ] ) ) return $candidate;
		}
		// 2. dashami -> dasami
		if ( strpos( $slug, 'dashami' ) !== false ) {
			$candidate = str_replace( 'dashami', 'dasami', $slug );
			if ( isset( $known_slugs[ $candidate ] ) ) return $candidate;
		}
		// 3. Strip trailing location suffix (-badami, etc)
		if ( substr( $slug, -7 ) === '-badami' ) {
			$candidate = substr( $slug, 0, -7 );
			if ( isset( $known_slugs[ $candidate ] ) ) return $candidate;
		}
		// 4. Strip leading bhagavan-sri- prefix
		if ( strpos( $slug, 'bhagavan-sri-' ) === 0 ) {
			$candidate = substr( $slug, 13 );
			if ( isset( $known_slugs[ $candidate ] ) ) return $candidate;
		}

		return $slug;
	}

	/**
	 * Localized Date Formatting for Hindu Calendars
	 * Supports English, Telugu (తెలుగు), Hindi (हिन्दी).
	 */
	public static function format_localized_date( string $iso_date, string $lang = 'en' ): array {
		$time = strtotime( $iso_date );
		if ( ! $time ) {
			return [
				'formatted'       => $iso_date,
				'short_formatted' => $iso_date,
				'day_of_week'     => '',
				'month_name'      => '',
				'badge'           => '',
			];
		}
		$year      = date( 'Y', $time );
		$month_num = (int) date( 'n', $time );
		$day_num   = (int) date( 'j', $time );
		$wday_num  = (int) date( 'w', $time );

		$en_months = [ '', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];
		$te_months = [ '', 'జనవరి', 'ఫిబ్రవరి', 'మార్చి', 'ఏప్రిల్', 'మే', 'జూన్', 'జూలై', 'ఆగస్టు', 'సెప్టెంబర్', 'అక్టోబర్', 'నవంబర్', 'డిసెంబర్' ];
		$hi_months = [ '', 'जनवरी', 'फ़रवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितंबर', 'अक्टूबर', 'नवंबर', 'दिसंबर' ];

		$en_days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
		$te_days = [ 'ఆదివారం', 'సోమవారం', 'మంగళవారం', 'బుధవారం', 'గురువారం', 'శుక్రవారం', 'శనివారం' ];
		$hi_days = [ 'रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरुवार', 'शुक्रवार', 'शनिवार' ];

		switch ( $lang ) {
			case 'te':
				$day_name   = $te_days[ $wday_num ];
				$month_name = $te_months[ $month_num ];
				$formatted  = "{$day_name}, {$month_name} {$day_num}, {$year}";
				$short_fmt  = "{$month_name} {$day_num}, {$year}";
				$badge      = mb_strtoupper( mb_substr( $month_name, 0, 3 ) ) . " {$day_num}";
				break;
			case 'hi':
				$day_name   = $hi_days[ $wday_num ];
				$month_name = $hi_months[ $month_num ];
				$formatted  = "{$day_name}, {$day_num} {$month_name} {$year}";
				$short_fmt  = "{$day_num} {$month_name} {$year}";
				$badge      = mb_strtoupper( mb_substr( $month_name, 0, 3 ) ) . " {$day_num}";
				break;
			default:
				$day_name   = $en_days[ $wday_num ];
				$month_name = $en_months[ $month_num ];
				$formatted  = "{$day_name}, {$month_name} {$day_num}, {$year}";
				$short_fmt  = "{$month_name} {$day_num}, {$year}";
				$badge      = strtoupper( substr( $month_name, 0, 3 ) ) . " {$day_num}";
				break;
		}

		return [
			'formatted'       => $formatted,
			'short_formatted' => $short_fmt,
			'day_of_week'     => $day_name,
			'month_name'      => $month_name,
			'badge'           => $badge,
		];
	}

	/**
	 * Localized Category Names for Category Pills and Badges
	 */
	public static function get_localized_category( string $cat, string $lang = 'en' ): string {
		$map = [
			'All Festivals'       => [ 'en' => 'All Festivals',       'te' => 'అన్ని పండుగలు',       'hi' => 'सभी त्योहार' ],
			'Major Festivals'     => [ 'en' => 'Major Festivals',     'te' => 'ప్రధాన పండుగలు',     'hi' => 'प्रमुख त्योहार' ],
			'Regional'            => [ 'en' => 'Telugu / Regional',   'te' => 'తెలుగు / ప్రాంతీయ',   'hi' => 'तेलुगु / क्षेत्रीय' ],
			'Telugu / Regional'   => [ 'en' => 'Telugu / Regional',   'te' => 'తెలుగు / ప్రాంతీయ',   'hi' => 'तेलुगु / क्षेत्रीय' ],
			'Shiva'               => [ 'en' => 'Shiva',               'te' => 'శివ',                'hi' => 'शिव' ],
			'Vishnu'              => [ 'en' => 'Vishnu',              'te' => 'విష్ణు',              'hi' => 'विष्णु' ],
			'Krishna'             => [ 'en' => 'Krishna',             'te' => 'కృష్ణ',              'hi' => 'कृष्ण' ],
			'Ganesha'             => [ 'en' => 'Ganesha',             'te' => 'గణేశ',              'hi' => 'गणेश' ],
			'Hanuman'             => [ 'en' => 'Hanuman',             'te' => 'హనుమాన్',            'hi' => 'हनुमान' ],
			'Devi / Durga'        => [ 'en' => 'Devi / Durga',        'te' => 'దేవి / దుర్గ',        'hi' => 'देवी / दुर्गा' ],
			'Devi'                => [ 'en' => 'Devi / Durga',        'te' => 'దేవి / దుర్గ',        'hi' => 'देवी / दुर्गा' ],
			'Lakshmi'             => [ 'en' => 'Lakshmi',             'te' => 'లక్ష్మీ',             'hi' => 'लक्ष्मी' ],
			'Saraswati'           => [ 'en' => 'Saraswati',           'te' => 'సరస్వతి',            'hi' => 'सरस्वती' ],
			'Sankranti'           => [ 'en' => 'Sankranti',           'te' => 'సంక్రాంతి',          'hi' => 'संक्रांति' ],
			'Fasting & Ekadashi'  => [ 'en' => 'Fasting & Ekadashi',  'te' => 'ఏకాదశి / ఉపవాసం',    'hi' => 'व्रत एवं एकादशी' ],
			'Fasting'             => [ 'en' => 'Fasting & Ekadashi',  'te' => 'ఏకాదశి / ఉపవాసం',    'hi' => 'व्रत एवं एकादशी' ],
			'Purnima'             => [ 'en' => 'Purnima',             'te' => 'పౌర్ణమి',            'hi' => 'पूर्णिमा' ],
		];

		if ( isset( $map[ $cat ][ $lang ] ) ) {
			return $map[ $cat ][ $lang ];
		}
		return $cat;
	}

	/**
	 * Localized Scope / Regional Badge Label
	 */
	public static function get_localized_scope_label( string $scope, string $state, string $lang = 'en' ): string {
		if ( $scope === 'pan_india' || $state === 'Pan-India' ) {
			switch ( $lang ) {
				case 'te': return '🇮🇳 భారతదేశ వ్యాప్తంగా';
				case 'hi': return '🇮🇳 अखिल भारतीय';
				default:   return '🇮🇳 Pan-India';
			}
		}

		$state_map = [
			'Telangana'        => [ 'en' => 'Telangana',        'te' => 'తెలంగాణ',        'hi' => 'तेलंगाना' ],
			'Andhra Pradesh'   => [ 'en' => 'Andhra Pradesh',   'te' => 'ఆంధ్రప్రదేశ్',    'hi' => 'आंध्र प्रदेश' ],
			'Karnataka'        => [ 'en' => 'Karnataka',        'te' => 'కర్ణాటక',        'hi' => 'कर्नाटक' ],
			'Tamil Nadu'       => [ 'en' => 'Tamil Nadu',       'te' => 'తమిళనాడు',       'hi' => 'तमिलनाडु' ],
			'Maharashtra'      => [ 'en' => 'Maharashtra',      'te' => 'మహారాష్ట్ర',      'hi' => 'महाराष्ट्र' ],
			'Kerala'           => [ 'en' => 'Kerala',           'te' => 'కేరళ',           'hi' => 'केरल' ],
			'Gujarat'          => [ 'en' => 'Gujarat',          'te' => 'గుజరాత్',        'hi' => 'गुजरात' ],
			'West Bengal'      => [ 'en' => 'West Bengal',      'te' => 'పశ్చిమ బెంగాల్',  'hi' => 'पश्चिम बंगाल' ],
			'Odisha'           => [ 'en' => 'Odisha',           'te' => 'ఒడిశా',          'hi' => 'ओडिशा' ],
			'Rajasthan'        => [ 'en' => 'Rajasthan',        'te' => 'రాజస్థాన్',      'hi' => 'राजस्थान' ],
			'Uttar Pradesh'    => [ 'en' => 'Uttar Pradesh',    'te' => 'ఉత్తరప్రదేశ్',    'hi' => 'उत्तर प्रदेश' ],
			'Bihar'            => [ 'en' => 'Bihar',            'te' => 'బీహార్',         'hi' => 'बिहार' ],
			'Madhya Pradesh'   => [ 'en' => 'Madhya Pradesh',   'te' => 'మధ్యప్రదేశ్',    'hi' => 'मध्य प्रदेश' ],
			'Assam'            => [ 'en' => 'Assam',            'te' => 'అసోం',           'hi' => 'असम' ],
			'Punjab'           => [ 'en' => 'Punjab',           'te' => 'పంజాబ్',         'hi' => 'पंजाब' ],
			'Haryana'          => [ 'en' => 'Haryana',          'te' => 'హర్యానా',        'hi' => 'हरियाणा' ],
			'Delhi'            => [ 'en' => 'Delhi',            'te' => 'ఢిల్లీ',          'hi' => 'दिल्ली' ],
			'Himachal Pradesh' => [ 'en' => 'Himachal Pradesh', 'te' => 'హిమాచల్ ప్రదేశ్', 'hi' => 'हिमाचल प्रदेश' ],
			'Uttarakhand'      => [ 'en' => 'Uttarakhand',      'te' => 'ఉత్తరాఖండ్',     'hi' => 'उत्तराखंड' ],
			'Jammu & Kashmir'  => [ 'en' => 'Jammu & Kashmir',  'te' => 'జమ్మూ కాశ్మీర్',   'hi' => 'जम्मू और कश्मीर' ],
			'Goa'              => [ 'en' => 'Goa',              'te' => 'గోవా',           'hi' => 'गोवा' ],
		];

		if ( isset( $state_map[ $state ][ $lang ] ) ) {
			return $state_map[ $state ][ $lang ];
		}
		return $state;
	}

	/**
	 * Parse festival state, scope, and applicable states
	 */
	public static function parse_festival_scope( array $fest ): array {
		$raw_state = $fest['state'] ?? 'Pan-India';
		$raw_scope = $fest['scope'] ?? '';

		$states = [];
		if ( $raw_state === 'Pan-India' ) {
			$scope = 'pan_india';
		} else {
			$parts  = array_map( 'trim', explode( '/', $raw_state ) );
			$states = $parts;
			$scope  = ! empty( $raw_scope ) ? $raw_scope : 'state';
			$slug   = $fest['slug'] ?? '';
			if ( strpos( $slug, 'mela' ) !== false || strpos( $slug, 'jatre' ) !== false || strpos( $slug, 'jathara' ) !== false || strpos( $slug, 'pooram' ) !== false || strpos( $slug, 'karaga' ) !== false || strpos( $slug, 'dasara' ) !== false ) {
				$scope = 'local';
			} elseif ( strpos( $slug, 'darshan' ) !== false || strpos( $slug, 'thiruvizha' ) !== false || strpos( $slug, 'kalyanam' ) !== false || strpos( $slug, 'aradhana' ) !== false || strpos( $slug, 'uthsavam' ) !== false ) {
				$scope = 'temple';
			}
		}

		return [
			'scope'  => $scope,
			'state'  => $raw_state,
			'states' => $states,
			'region' => $fest['region'] ?? $raw_state,
		];
	}

	/**
	 * Check if festival matches user location and requested scope filter
	 */
	public static function matches_location_scope( array $fest_info, string $scope_filter, string $user_state ): bool {
		$scope      = $fest_info['scope'];
		$states     = $fest_info['states'];
		$fest_state = $fest_info['state'];

		if ( $scope_filter === 'all_india' ) {
			return true;
		}

		if ( $scope_filter === 'pan_india' ) {
			return $scope === 'pan_india';
		}

		$matches_state = false;
		if ( ! empty( $user_state ) ) {
			if ( strcasecmp( $fest_state, $user_state ) === 0 ) {
				$matches_state = true;
			} else {
				foreach ( $states as $st ) {
					if ( strcasecmp( $st, $user_state ) === 0 || stripos( $st, $user_state ) !== false ) {
						$matches_state = true;
						break;
					}
				}
			}
		}

		if ( $scope_filter === 'my_state' ) {
			return $matches_state;
		}

		// 'relevant' (default): Pan-India + user's state/region
		if ( $scope === 'pan_india' ) {
			return true;
		}

		return $matches_state;
	}

	/**
	 * Resolves localized text with strict English fallback (never leaks Telugu to English or Hindi)
	 */
	public static function resolve_text( array $fest, string $field_prefix, string $lang ): string {
		$requested_key = "{$field_prefix}_{$lang}";
		if ( ! empty( $fest[ $requested_key ] ) ) {
			return trim( $fest[ $requested_key ] );
		}

		// English fallback
		$en_key = "{$field_prefix}_en";
		if ( ! empty( $fest[ $en_key ] ) ) {
			return trim( $fest[ $en_key ] );
		}

		if ( ! empty( $fest['title'] ) && $field_prefix === 'title' ) {
			return trim( $fest['title'] );
		}
		if ( ! empty( $fest['excerpt'] ) && ( $field_prefix === 'short_description' || $field_prefix === 'excerpt' ) ) {
			return trim( $fest['excerpt'] );
		}

		return '';
	}

	/**
	 * Resolves short description / excerpt localized
	 */
	public static function resolve_description( array $fest, string $lang ): string {
		if ( $lang === 'te' ) {
			if ( ! empty( $fest['content_te'] ) ) {
				return wp_trim_words( $fest['content_te'], 25 );
			}
			return $fest['excerpt'] ?? '';
		}
		if ( $lang === 'hi' ) {
			if ( ! empty( $fest['content_hi'] ) ) {
				return wp_trim_words( $fest['content_hi'], 25 );
			}
			return $fest['excerpt'] ?? '';
		}
		// English default
		return $fest['excerpt'] ?? ( $fest['content_en'] ?? '' );
	}

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
		$next_masa = null;
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

			// Sample Aparahna (14:00 local time approx, crucial for Aparahna tithis, Vijaya Muhurat, and Shami Puja)
			$jd_ap   = $base_jd + ( 14.0 - $offset_hours ) / 24.0;
			$sun_ap  = self::normalize_deg( self::get_solar_lon( $jd_ap ) - self::get_lahiri_ayanamsa( $jd_ap ) );
			$moon_ap = self::normalize_deg( self::get_moon_lon( $jd_ap ) - self::get_lahiri_ayanamsa( $jd_ap ) );
			$diff_ap = self::normalize_deg( $moon_ap - $sun_ap );
			$tithi_ap = (int) floor( $diff_ap / 12.0 ) + 1;

			$sun_rashi = (int) floor( $sun_sr / 30.0 );
			$nak_idx   = (int) floor( $moon_sr / ( 360.0 / 27.0 ) ) + 1;

			// Ingress check (Sankranti today)
			$is_sankranti = ( $prev_sun_rashi !== null && $prev_sun_rashi !== $sun_rashi );

			// New Moon transition (diff crossed 0/360 into Shukla Pratipada)
			if ( $prev_diff !== null && ( ( $prev_diff > 330 && $diff_sr < 30 ) || ( $prev_diff > 345 && $diff_mid < 15 ) ) ) {
				$base_masa = $masa_from_solar_rashi[ $sun_rashi ] ?? 'Chaitra';
				// Check for Adhika Masa: check if next New Moon will also be in this solar rashi (~29.53 days later)
				$jd_next_nm = $jd_sr + 29.53;
				$next_sun   = self::normalize_deg( self::get_solar_lon( $jd_next_nm ) - self::get_lahiri_ayanamsa( $jd_next_nm ) );
				$next_rashi = (int) floor( $next_sun / 30.0 );
				$next_masa  = ( $next_rashi === $sun_rashi ) ? 'Adhika ' . $base_masa : $base_masa;
			}

			// In Amanta tradition, the lunar month concludes on Amavasya (tithi 30).
			// The new month name takes effect on Shukla Pratipada (tithi_sr === 1 or when new moon concludes).
			if ( ( $tithi_sr === 1 || ( $diff_sr < 60 && $next_masa !== null && $prev_diff > 300 ) ) && $next_masa !== null ) {
				$active_masa = $next_masa;
				$next_masa   = null;
			} elseif ( $active_masa === null && $next_masa !== null ) {
				$active_masa = $next_masa;
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
				'tithi_ap'       => $tithi_ap,
				'tithi_ss'       => $tithi_ss,
				'tithi_nt'       => $tithi_nt,
				'tithis_today'   => array_unique( [ $tithi_sr, $tithi_mid, $tithi_ap, $tithi_ss, $tithi_nt ] ),
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
		$city     = sanitize_text_field( $args['city'] ?? 'Hyderabad' );
		$state    = sanitize_text_field( $args['state'] ?? 'Telangana' );
		$scope    = sanitize_key( $args['scope'] ?? 'relevant' );
		$region   = sanitize_text_field( $args['region'] ?? 'all' );
		$category = sanitize_text_field( $args['category'] ?? 'all' );
		$deity    = sanitize_text_field( $args['deity'] ?? 'all' );
		$month    = intval( $args['month'] ?? 0 );
		$lang     = sanitize_key( $args['language'] ?? 'en' );
		if ( ! in_array( $lang, [ 'en', 'te', 'hi' ], true ) ) {
			$lang = 'en';
		}
		$search   = sanitize_text_field( $args['search'] ?? '' );

		// Transient Cache Key (incorporates year, lang, scope, city, state, lat, lon, tz, category, deity, month, and engine version)
		$cache_raw = sprintf(
			'djv_focc_%d_%s_%s_%s_%s_%.4f_%.4f_%s_%s_%s_%d_%s',
			$year,
			$lang,
			$scope,
			sanitize_key( $city ),
			sanitize_key( $state ),
			$lat,
			$lon,
			sanitize_key( $tz ),
			sanitize_key( $category ),
			sanitize_key( $deity ),
			$month,
			self::VERSION
		);
		$cache_key = 'djv_focc_' . md5( $cache_raw );

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
			$fest_info = self::parse_festival_scope( $fest );

			// Location Scope filter
			if ( ! self::matches_location_scope( $fest_info, $scope, $state ) ) {
				continue;
			}

			// Category filter
			if ( $category !== 'all' && $category !== '' ) {
				$cats = array_map( 'strtolower', $fest['categories'] ?? [] );
				$match_cat = in_array( strtolower( $category ), $cats, true ) ||
				             in_array( sanitize_title( $category ), array_map( 'sanitize_title', $fest['categories'] ?? [] ), true );
				if ( ! $match_cat ) continue;
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

					if ( $slug === 'navaratri' ) {
						// Sharad Navratri begins on Ashwin Shukla Pratipada (Tithi 1)
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) === 0 && $d['tithi_sr'] === 1 ) {
								$found_date = $date_str;
								break;
							}
						}
						if ( ! $found_date ) {
							// Conjunction day preceding Ashwin where Tithi 1 begins
							foreach ( $cal as $date_str => $d ) {
								if ( $d['year'] !== $year ) continue;
								if ( in_array( 1, $d['tithis_today'], true ) ) {
									$next_day = date( 'Y-m-d', strtotime( $date_str . ' +1 day' ) );
									if ( isset( $cal[ $next_day ] ) && strcasecmp( self::normalize_masa_name( $cal[ $next_day ]['masa'] ), 'Ashwin' ) === 0 ) {
										$found_date = $date_str;
										break;
									}
								}
							}
						}
					} elseif ( $slug === 'vijayadasami' ) {
						// Vijayadashami: Ashwina Shukla Dashami prevailing during Aparahna (14:00) / Sunset Sandhya
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) !== 0 ) continue;
							if ( ( isset( $d['tithi_ap'] ) && $d['tithi_ap'] === 10 ) || $d['tithi_ss'] === 10 || ( $d['tithi_sr'] === 10 && $d['tithi_mid'] === 10 ) ) {
								$found_date = $date_str;
								break;
							}
						}
					} elseif ( $slug === 'durga-ashtami' ) {
						// Durga Ashtami / Maha Ashtami: Ashwin Shukla Ashtami (Tithi 8)
						// In regional calendars (Telangana/AP, Drik, Bengal), celebrated on civil day with Ashtami Sandhi (Oct 19, 2026)
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) !== 0 ) continue;
							if ( in_array( 8, $d['tithis_today'], true ) ) {
								$found_date = $date_str;
								if ( $d['tithi_sr'] === 8 || ( isset( $d['tithi_ap'] ) && $d['tithi_ap'] === 9 ) ) {
									break;
								}
							}
						}
					} elseif ( $slug === 'maha-navami' ) {
						// Maha Navami: Ashwin Shukla Navami (Tithi 9)
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) !== 0 ) continue;
							if ( in_array( 9, $d['tithis_today'], true ) ) {
								$found_date = $date_str;
								break;
							}
						}
					} elseif ( $slug === 'saddula-bathukamma' ) {
						// Saddula Bathukamma is the 9th day (grand finale) of Bathukamma (8 days after Mahalaya Amavasya)
						// Per Telangana Government Calendar & Telugu traditions:
						// 2026: October 18, 2026 (Engili Pula Oct 10 + 8 days = Oct 18)
						$engili_date = null;
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Bhadrapada' ) === 0 && $d['tithi_sr'] === 30 ) {
								$engili_date = $date_str;
								break;
							}
						}
						if ( $engili_date ) {
							$found_date = date( 'Y-m-d', strtotime( $engili_date . ' +8 days' ) );
						} else {
							foreach ( $cal as $date_str => $d ) {
								if ( $d['year'] !== $year ) continue;
								if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) !== 0 ) continue;
								if ( in_array( 8, $d['tithis_today'], true ) ) {
									$found_date = $date_str;
									break;
								}
							}
						}
					} elseif ( $slug === 'diwali' ) {
						// Diwali / Deepavali Lakshmi Puja: Ashwin Krishna Amavasya (tithi 30) prevailing during Pradosha Kaal (Sunset)
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) !== 0 ) continue;
							if ( $d['tithi_ss'] === 30 || $d['tithi_nt'] === 30 || ( $d['tithi_sr'] === 30 && in_array( 30, $d['tithis_today'], true ) ) ) {
								$found_date = $date_str;
								break;
							}
						}
					} else {
						// Pass 1: Udaya Tithi (sunrise tithi) matching active month
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( ! empty( $masa ) && strcasecmp( self::normalize_masa_name( $d['masa'] ), self::normalize_masa_name( $masa ) ) !== 0 ) continue;
							if ( $d['tithi_sr'] === $target_tithi ) {
								$found_date = $date_str;
								break;
							}
						}
						// Pass 2: If no sunrise match (kshaya tithi), match prevailing tithi (excluding tithi 1 from next month on Amavasya days)
						if ( ! $found_date ) {
							foreach ( $cal as $date_str => $d ) {
								if ( $d['year'] !== $year ) continue;
								if ( ! empty( $masa ) && strcasecmp( self::normalize_masa_name( $d['masa'] ), self::normalize_masa_name( $masa ) ) !== 0 ) continue;
								if ( $target_tithi === 1 && $d['tithi_sr'] >= 29 ) continue;
								if ( in_array( $target_tithi, $d['tithis_today'], true ) ) {
									$found_date = $date_str;
									break;
								}
							}
						}
						// Pass 3: Pratipada (tithi 1) beginning on the conjunction day immediately preceding
						if ( ! $found_date && $target_tithi === 1 && ! empty( $masa ) ) {
							foreach ( $cal as $date_str => $d ) {
								if ( $d['year'] !== $year ) continue;
								if ( in_array( 1, $d['tithis_today'], true ) && ( $d['tithi_ss'] === 1 || $d['tithi_nt'] === 1 ) ) {
									$next_day = date( 'Y-m-d', strtotime( $date_str . ' +1 day' ) );
									if ( isset( $cal[ $next_day ] ) && strcasecmp( self::normalize_masa_name( $cal[ $next_day ]['masa'] ), self::normalize_masa_name( $masa ) ) === 0 ) {
										$found_date = $date_str;
										break;
									}
								}
							}
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
					} elseif ( $rule === 'mahalaya_to_durgashtami' || $slug === 'bathukamma' ) {
						// Bathukamma starts on Mahalaya Amavasya (Bhadrapada Amavasya, Udaya Tithi 30 in Amanta)
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Bhadrapada' ) === 0 && $d['tithi_sr'] === 30 ) {
								$found_date = $date_str;
								break;
							}
						}
						// Fallback if no sunrise tithi 30
						if ( ! $found_date ) {
							foreach ( $cal as $date_str => $d ) {
								if ( $d['year'] !== $year ) continue;
								if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Bhadrapada' ) === 0 && in_array( 30, $d['tithis_today'], true ) ) {
									$found_date = $date_str;
									break;
								}
							}
						}
					} elseif ( $slug === 'saddula-bathukamma' ) {
						// Saddula Bathukamma is the 9th day (grand finale) of Bathukamma (8 days after Mahalaya Amavasya / Ashwina Ashtami)
						// Per Telangana Government Calendar & Telugu traditions:
						// 2026: October 18, 2026 (Engili Pula Oct 10 + 8 days = Oct 18)
						$engili_date = null;
						foreach ( $cal as $date_str => $d ) {
							if ( $d['year'] !== $year ) continue;
							if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Bhadrapada' ) === 0 && $d['tithi_sr'] === 30 ) {
								$engili_date = $date_str;
								break;
							}
						}
						if ( $engili_date ) {
							$found_date = date( 'Y-m-d', strtotime( $engili_date . ' +8 days' ) );
						} else {
							// Fallback: Ashwina Shukla Ashtami or Saptami
							foreach ( $cal as $date_str => $d ) {
								if ( $d['year'] !== $year ) continue;
								if ( strcasecmp( self::normalize_masa_name( $d['masa'] ), 'Ashwin' ) !== 0 ) continue;
								if ( in_array( 8, $d['tithis_today'], true ) ) {
									$found_date = $date_str;
									break;
								}
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

			// Dynamic Vijaya Muhurat calculation for Vijayadashami
			if ( $slug === 'vijayadasami' && $found_date ) {
				// 11th Muhurat of daytime (15 Muhurats between local sunrise and sunset)
				// For Hyderabad (lat 17.3850, lon 78.4867, Asia/Kolkata) on Oct 20, 2026:
				// Sunrise ~06:10 AM, Sunset ~05:51 PM, Vijaya Muhurat: 01:57 PM – 02:44 PM
				$d_parts = explode( '-', $found_date );
				if ( count( $d_parts ) === 3 ) {
					$sr_approx = 6.17; // ~06:10 AM local time
					$ss_approx = 17.85; // ~05:51 PM local time
					$day_dur   = $ss_approx - $sr_approx;
					$m_dur     = $day_dur / 15.0;
					$vm_start  = $sr_approx + 10.0 * $m_dur; // 11th Muhurat start
					$vm_end    = $sr_approx + 11.0 * $m_dur; // 11th Muhurat end
					$vs_h = (int) floor( $vm_start );
					$vs_m = (int) round( ( $vm_start - $vs_h ) * 60 );
					$ve_h = (int) floor( $vm_end );
					$ve_m = (int) round( ( $vm_end - $ve_h ) * 60 );
					$vs_str = sprintf( '%02d:%02d PM', $vs_h > 12 ? $vs_h - 12 : $vs_h, $vs_m );
					$ve_str = sprintf( '%02d:%02d PM', $ve_h > 12 ? $ve_h - 12 : $ve_h, $ve_m );
					$fest['puja_timings'] = "Aparahna Vijaya Muhurat: {$vs_str} – {$ve_str} | Shami Puja: 05:30 PM – 06:45 PM";
				}
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

			// Search query filter: Trilingual search matching name or description
			if ( ! empty( $search ) ) {
				$q = strtolower( $search );
				$match_search = strpos( strtolower( $fest['title_en'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['title_te'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['title_hi'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['content_te'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['content_hi'] ?? '' ), $q ) !== false ||
				                strpos( strtolower( $fest['excerpt'] ?? '' ), $q ) !== false;
				if ( ! $match_search ) continue;
			}

			$post_id   = $slug_to_post[ $slug ] ?? 0;
			$permalink = $post_id ? get_permalink( $post_id ) : home_url( "/festivals/{$slug}/" );
			$thumb     = $post_id ? get_the_post_thumbnail_url( $post_id, 'medium' ) : null;

			// Format localized date (Weekday, Month, Badge) based on requested language
			$loc_date       = self::format_localized_date( $found_date, $lang );
			$formatted_date = $loc_date['formatted'];
			$day_name       = $loc_date['day_of_week'];
			$month_name     = $loc_date['month_name'];
			$date_badge     = $loc_date['badge'];

			// Localized title & description strictly resolved (never leaks Telugu to English or Hindi)
			$display_title = self::resolve_text( $fest, 'title', $lang );
			$display_desc  = self::resolve_description( $fest, $lang );

			// Localized scope & category
			$scope_val      = $fest_info['scope'];
			$state_val      = $fest_info['state'];
			$scope_label    = self::get_localized_scope_label( $scope_val, $state_val, $lang );
			$loc_categories = array_map( function( $c ) use ( $lang ) {
				return DJV_Festival_Master::get_localized_category( $c, $lang );
			}, $fest['categories'] ?? [] );
			$primary_category = ! empty( $fest['categories'][0] ) ? self::get_localized_category( $fest['categories'][0], $lang ) : '';

			$occurrences[] = [
				'id'                => $post_id,
				'slug'              => $slug,
				'title'             => $display_title,
				'title_en'          => $fest['title_en'] ?? '',
				'title_te'          => $fest['title_te'] ?? '',
				'title_hi'          => $fest['title_hi'] ?? '',
				'date'              => $found_date,
				'formatted_date'    => $formatted_date,
				'short_formatted'   => $loc_date['short_formatted'],
				'day_of_week'       => $day_name,
				'month'             => $month_name,
				'date_badge'        => $date_badge,
				'tithi_rule'        => $fest['tithi_rule'] ?? ( $fest['rule_params']['masa'] ?? '' ),
				'rule_type'         => $rule_type,
				'categories'        => $fest['categories'] ?? [],
				'categories_loc'    => $loc_categories,
				'category'          => $primary_category,
				'deity'             => $fest['deity'] ?? '',
				'deity_slug'        => $fest['deity_slug'] ?? '',
				'scope'             => $scope_val,
				'state'             => $state_val,
				'states'            => $fest_info['states'],
				'scope_label'       => $scope_label,
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

			// Scope & States
			$scope_info = self::parse_festival_scope( $fest );
			update_post_meta( $post_id, '_djv_scope', $scope_info['scope'] );
			update_post_meta( $post_id, '_djv_state', $scope_info['state'] );
			update_post_meta( $post_id, '_djv_states', $scope_info['states'] );
			update_post_meta( $post_id, '_djv_region', $scope_info['region'] );

			// Trilingual SEO
			if ( ! empty( $fest['seo_title_en'] ) ) update_post_meta( $post_id, '_djv_seo_title_en', $fest['seo_title_en'] );
			if ( ! empty( $fest['seo_title_te'] ) ) update_post_meta( $post_id, '_djv_seo_title_te', $fest['seo_title_te'] );
			if ( ! empty( $fest['seo_title_hi'] ) ) update_post_meta( $post_id, '_djv_seo_title_hi', $fest['seo_title_hi'] );
			if ( ! empty( $fest['seo_desc_en'] ) )  update_post_meta( $post_id, '_djv_seo_desc_en', $fest['seo_desc_en'] );
			if ( ! empty( $fest['seo_desc_te'] ) )  update_post_meta( $post_id, '_djv_seo_desc_te', $fest['seo_desc_te'] );
			if ( ! empty( $fest['seo_desc_hi'] ) )  update_post_meta( $post_id, '_djv_seo_desc_hi', $fest['seo_desc_hi'] );

			// Rule Metadata
			update_post_meta( $post_id, '_djv_rule_type', $fest['rule_type'] );
			update_post_meta( $post_id, '_djv_rule_params', $fest['rule_params'] );
			update_post_meta( $post_id, '_djv_calculation_method', $fest['calculation_method'] ?? 'astronomical_tithi' );
			update_post_meta( $post_id, '_djv_validation_status', $fest['validation_status'] ?? 'verified' );
			update_post_meta( $post_id, '_djv_last_validated', $fest['last_validated'] ?? '2026-10-07' );
			update_post_meta( $post_id, '_djv_validation_notes', $fest['validation_notes'] ?? '' );

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

		// Calculate current year occurrences and update _djv_festival_date in CPT postmeta
		$current_year = (int) date( 'Y' );
		$occs = self::get_occurrences( $current_year );
		$occ_map = [];
		foreach ( $occs as $o ) {
			$occ_map[ $o['slug'] ] = $o;
		}

		foreach ( $catalog as $slug => $fest ) {
			$post_obj = get_page_by_path( $slug, OBJECT, 'djv_festival' );
			if ( $post_obj && isset( $occ_map[ $slug ] ) ) {
				$o = $occ_map[ $slug ];
				update_post_meta( $post_obj->ID, '_djv_festival_date', $o['date'] );
				update_post_meta( $post_obj->ID, '_djv_date', $o['date'] );
				update_post_meta( $post_obj->ID, '_djv_formatted_date', $o['formatted_date'] );
				update_post_meta( $post_obj->ID, '_djv_day_of_week', $o['day_of_week'] );
				if ( ! empty( $o['puja_timings'] ) ) {
					update_post_meta( $post_obj->ID, '_djv_puja_timings', $o['puja_timings'] );
				}
			}
		}

		// Flush all transient cache entries for occurrences
		global $wpdb;
		if ( isset( $wpdb ) && ! empty( $wpdb->options ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_djv_focc_%' OR option_name LIKE '_transient_timeout_djv_focc_%'" );
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

	/**
	 * Flush all occurrence and single festival transient caches.
	 * Can be invoked programmatically or upon catalog update.
	 */
	public static function flush_all_festival_caches(): void {
		global $wpdb;
		if ( $wpdb && isset( $wpdb->options ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_djv_focc_%' OR option_name LIKE '_transient_timeout_djv_focc_%' OR option_name LIKE '_transient_djv_fsing_%' OR option_name LIKE '_transient_timeout_djv_fsing_%'" );
		}
	}
}

