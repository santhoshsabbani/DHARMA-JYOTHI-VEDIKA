<?php
/**
 * Dharma Jyothi Vedika — Global Language Architecture
 *
 * Implements centralized trilingual support (English default, Telugu, Hindi).
 * Provides single source of truth for language state across WordPress, PHP rendering,
 * URL handling (?lang=en|te|hi), persistence (cookie + localStorage),
 * gettext internationalization, and content localization.
 *
 * @package DJV_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Supported Languages
 */
function djv_get_supported_languages(): array {
	return [ 'en', 'te', 'hi' ];
}

/**
 * 2. Global Language Source of Truth
 *
 * Priority:
 * 1. Valid ?lang= parameter in URL
 * 2. Valid ?language= parameter in URL
 * 3. Valid djv_language cookie
 * 4. Valid djv_lang cookie (legacy fallback)
 * 5. Default strictly to 'en' (NEVER default to Telugu)
 *
 * @return string 'en' | 'te' | 'hi'
 */
function djv_get_current_language(): string {
	static $current_lang = null;
	if ( $current_lang !== null ) {
		return $current_lang;
	}

	$supported = djv_get_supported_languages();

	// 1. Read ?lang=
	if ( isset( $_GET['lang'] ) ) {
		$lang = sanitize_key( $_GET['lang'] );
		if ( in_array( $lang, $supported, true ) ) {
			$current_lang = $lang;
			return $current_lang;
		}
	}

	// 2. Read ?language=
	if ( isset( $_GET['language'] ) ) {
		$lang = sanitize_key( $_GET['language'] );
		if ( in_array( $lang, $supported, true ) ) {
			$current_lang = $lang;
			return $current_lang;
		}
	}

	// 3. Read cookie djv_language
	if ( isset( $_COOKIE['djv_language'] ) ) {
		$cookie_lang = sanitize_key( $_COOKIE['djv_language'] );
		if ( in_array( $cookie_lang, $supported, true ) ) {
			$current_lang = $cookie_lang;
			return $current_lang;
		}
	}

	// 4. Read legacy cookie djv_lang
	if ( isset( $_COOKIE['djv_lang'] ) ) {
		$cookie_lang = sanitize_key( $_COOKIE['djv_lang'] );
		if ( in_array( $cookie_lang, $supported, true ) ) {
			$current_lang = $cookie_lang;
			return $current_lang;
		}
	}

	// 5. Default strictly to English
	$current_lang = 'en';
	return $current_lang;
}

/**
 * 3. Cookie Persistence for PHP Server-Rendered Content
 *
 * When a user visits with ?lang=te or ?lang=hi, ensure the cookie is set
 * so subsequent navigation without explicit query params maintains the language.
 */
function djv_set_language_cookie_on_request(): void {
	if ( headers_sent() ) {
		return;
	}

	$supported = djv_get_supported_languages();
	$requested = null;

	if ( isset( $_GET['lang'] ) && in_array( sanitize_key( $_GET['lang'] ), $supported, true ) ) {
		$requested = sanitize_key( $_GET['lang'] );
	} elseif ( isset( $_GET['language'] ) && in_array( sanitize_key( $_GET['language'] ), $supported, true ) ) {
		$requested = sanitize_key( $_GET['language'] );
	}

	if ( $requested !== null ) {
		if ( ! isset( $_COOKIE['djv_language'] ) || $_COOKIE['djv_language'] !== $requested ) {
			setcookie( 'djv_language', $requested, [
				'expires'  => time() + 31536000, // 1 year
				'path'     => '/',
				'secure'   => is_ssl(),
				'httponly' => false,
				'samesite' => 'Lax',
			] );
			$_COOKIE['djv_language'] = $requested;
		}
	}
}
add_action( 'init', 'djv_set_language_cookie_on_request', 1 );

/**
 * 4. Locale & Document HTML Lang Attribute
 *
 * Output <html lang="en">, <html lang="te">, or <html lang="hi">.
 */
function djv_filter_language_attributes( string $output ): string {
	$lang = djv_get_current_language();
	return 'lang="' . esc_attr( $lang ) . '"';
}
add_filter( 'language_attributes', 'djv_filter_language_attributes', 999 );

function djv_filter_locale( string $locale ): string {
	$lang = djv_get_current_language();
	switch ( $lang ) {
		case 'te':
			return 'te';
		case 'hi':
			return 'hi_IN';
		case 'en':
		default:
			return 'en_US';
	}
}
add_filter( 'locale', 'djv_filter_locale', 999 );
add_filter( 'determine_locale', 'djv_filter_locale', 999 );

/**
 * 5. URL Language Persistence Helper & Filters
 *
 * When current language is non-default ('te' or 'hi'), append ?lang= to internal links
 * to ensure clicking any link preserves the language state.
 */
function djv_append_lang_to_url( string $url ): string {
	if ( empty( $url ) || is_admin() || wp_doing_ajax() ) {
		return $url;
	}

	$lang = djv_get_current_language();
	if ( $lang === 'en' ) {
		return $url;
	}

	// Only append to internal URLs
	$home = home_url();
	if ( strpos( $url, $home ) !== 0 && strpos( $url, '/' ) !== 0 ) {
		return $url;
	}

	// Do not append to assets, wp-json, wp-admin
	if ( preg_match( '#\.(css|js|png|jpg|jpeg|gif|svg|webp|woff|woff2|ttf|ico)(\?.*)?$#i', $url ) ) {
		return $url;
	}
	if ( strpos( $url, 'wp-json' ) !== false || strpos( $url, 'wp-admin' ) !== false ) {
		return $url;
	}

	return add_query_arg( 'lang', $lang, $url );
}
add_filter( 'page_link', 'djv_append_lang_to_url', 10 );
add_filter( 'post_link', 'djv_append_lang_to_url', 10 );
add_filter( 'post_type_link', 'djv_append_lang_to_url', 10 );
add_filter( 'term_link', 'djv_append_lang_to_url', 10 );

/**
 * Helper to build language switch URL while preserving existing query parameters.
 *
 * @param string $target_lang 'en' | 'te' | 'hi'
 * @return string
 */
function djv_get_language_switch_url( string $target_lang ): string {
	$current_url = home_url( add_query_arg( [] ) );
	return add_query_arg( 'lang', $target_lang, $current_url );
}

/**
 * 6. Central Trilingual Translation Dictionary
 *
 * Maps English source strings to authentic Telugu and Hindi translations.
 * Completely covers all static strings specified in Section 8 & Section 10.
 */
function djv_get_translation_dictionary(): array {
	static $dict = null;
	if ( $dict !== null ) {
		return $dict;
	}

	$dict = [
		// Brand & Site Identity (Section 8)
		'DHARMA JYOTHI VEDIKA' => [
			'te' => 'ధర్మ జ్యోతి వేదిక',
			'hi' => 'धर्म ज्योति वेदिका',
		],
		'Dharma Jyothi Vedika' => [
			'te' => 'ధర్మ జ్యోతి వేదిక',
			'hi' => 'धर्म ज्योति वेदिका',
		],
		'Hindu Panchangam & Devotional Platform' => [
			'te' => 'హిందూ పంచాంగం & భక్తి వేదిక',
			'hi' => 'हिंदू पंचांग एवं भक्ति मंच',
		],
		'Vedic Panchangam & Devotional Platform' => [
			'te' => 'వేద పంచాంగం & భక్తి వేదిక',
			'hi' => 'वैदिक पंचांग एवं भक्ति मंच',
		],
		'Accurate daily Panchangam, festivals, Muhurtham and devotional guidance for devotees across India. Grounded in tradition, built for modern life.' => [
			'te' => 'భారతదేశ వ్యాప్తంగా భక్తుల కోసం ఖచ్చితమైన దిన పంచాంగం, పండుగలు, శుభ ముహూర్తాలు మరియు ఆధ్యాత్మిక మార్గదర్శకత్వం.',
			'hi' => 'समस्त भारत के श्रद्धालुओं हेतु सटीक दैनिक पंचांग, प्रमुख त्योहार, शुभ मुहूर्त एवं प्रामाणिक वैदिक मार्गदर्शन।',
		],

		// Navigation & Core Sections (Section 8)
		'Home' => [
			'te' => 'హోమ్',
			'hi' => 'होम',
		],
		'Panchangam' => [
			'te' => 'పంచాంగం',
			'hi' => 'पंचांग',
		],
		'Festivals' => [
			'te' => 'పండుగలు',
			'hi' => 'त्योहार',
		],
		'Muhurtham' => [
			'te' => 'ముహూర్తం',
			'hi' => 'मुहूर्त',
		],
		'Pooja' => [
			'te' => 'పూజ',
			'hi' => 'पूजा',
		],
		'Mantras' => [
			'te' => 'మంత్రాలు',
			'hi' => 'मंत्र',
		],
		'Temples' => [
			'te' => 'దేవాలయాలు',
			'hi' => 'मंदिर',
		],
		'Articles' => [
			'te' => 'వ్యాసాలు',
			'hi' => 'लेख',
		],
		'Services' => [
			'te' => 'సేవలు',
			'hi' => 'सेवाएं',
		],
		'Calendar' => [
			'te' => 'క్యాలెండర్',
			'hi' => 'कैलेंडर',
		],
		"Today's Panchangam" => [
			'te' => 'నేటి పంచాంగం',
			'hi' => 'आज का पंचांग',
		],
		"Today's Hindu Panchangam" => [
			'te' => 'నేటి హిందూ పంచాంగం',
			'hi' => 'आज का हिंदू पंचांग',
		],

		// Section 10 Static Theme Strings
		'Search' => [
			'te' => 'వెతకండి',
			'hi' => 'खोजें',
		],
		'Year' => [
			'te' => 'సంవత్సరం',
			'hi' => 'वर्ष',
		],
		'Location' => [
			'te' => 'ప్రదేశం',
			'hi' => 'स्थान',
		],
		'Read More' => [
			'te' => 'మరింత చదవండి',
			'hi' => 'और पढ़ें',
		],
		'View Details' => [
			'te' => 'వివరాలు చూడండి',
			'hi' => 'विवरण देखें',
		],
		'Previous' => [
			'te' => 'మునుపటి',
			'hi' => 'पिछला',
		],
		'Next' => [
			'te' => 'తరువాత',
			'hi' => 'अगला',
		],
		'Today' => [
			'te' => 'నేడు',
			'hi' => 'आज',
		],
		'Submit' => [
			'te' => 'సమర్పించండి',
			'hi' => 'जमा करें',
		],
		'Find Muhurtham' => [
			'te' => 'ముహూర్తం కనుగొనండి',
			'hi' => 'मुहूर्त खोजें',
		],
		'No results' => [
			'te' => 'ఫలితాలు లేవు',
			'hi' => 'कोई परिणाम नहीं',
		],
		'No results found' => [
			'te' => 'ఫలితాలు కనుగొనబడలేదు',
			'hi' => 'कोई परिणाम नहीं मिला',
		],
		'Loading' => [
			'te' => 'లోడ్ అవుతోంది…',
			'hi' => 'लोड हो रहा है…',
		],
		'Loading...' => [
			'te' => 'లోడ్ అవుతోంది…',
			'hi' => 'लोड हो रहा है…',
		],
		'Error' => [
			'te' => 'లోపం',
			'hi' => 'त्रुटि',
		],
		'Back' => [
			'te' => 'వెనుకకు',
			'hi' => 'वापस',
		],
		'Back to Festivals' => [
			'te' => 'పండుగల జాబితాకు వెళ్లండి',
			'hi' => 'त्योहारों की सूची पर वापस जाएं',
		],
		'Back to Pooja' => [
			'te' => 'పూజా జాబితాకు వెళ్లండి',
			'hi' => 'पूजा सूची पर वापस जाएं',
		],
		'Back to Mantras' => [
			'te' => 'మంత్రాల జాబితాకు వెళ్లండి',
			'hi' => 'मंत्रों की सूची पर वापस जाएं',
		],
		'Share' => [
			'te' => 'భాగస్వామ్యం చేయండి',
			'hi' => 'साझा करें',
		],
		'Print' => [
			'te' => 'ప్రింట్ చేయండి',
			'hi' => 'प्रिंट करें',
		],

		// Common Headings & Subtitles
		'View Full Panchangam' => [
			'te' => 'పూర్తి పంచాంగం చూడండి',
			'hi' => 'संपूर्ण पंचांग देखें',
		],
		'Change Location' => [
			'te' => 'ప్రదేశం మార్చండి',
			'hi' => 'स्थान बदलें',
		],
		'Upcoming' => [
			'te' => 'రాబోయే పండుగలు',
			'hi' => 'आगामी',
		],
		'Hindu Festivals & Holy Days' => [
			'te' => 'హిందూ పండుగలు & పుణ్య దినాలు',
			'hi' => 'हिंदू त्योहार एवं पर्व',
		],
		'View All Festivals & Vrats' => [
			'te' => 'అన్ని పండుగలు & వ్రతాలు చూడండి',
			'hi' => 'सभी त्योहार एवं व्रत देखें',
		],
		'Shubh Timings' => [
			'te' => 'శుభ సమయాలు',
			'hi' => 'शुभ समय',
		],
		'Auspicious Muhurtham Finder' => [
			'te' => 'శుభ ముహూర్తములు & కాల నిర్ణయం',
			'hi' => 'शुभ मुहूर्त खोज',
		],
		'Shubh Muhurtham Finder' => [
			'te' => 'శుభ ముహూర్తములు & కాల నిర్ణయం',
			'hi' => 'शुभ मुहूर्त खोज',
		],
		'Shubh Muhurtham' => [
			'te' => 'శుభ ముహూర్తం',
			'hi' => 'शुभ मुहूर्त',
		],
		'Explore All Muhurtham Categories' => [
			'te' => 'అన్ని ముహూర్తాల విభాగాలు చూడండి',
			'hi' => 'सभी मुहूर्त श्रेणियां देखें',
		],
		'Vedic Pooja Guides & Vidhi' => [
			'te' => 'వేద పూజా విధానం & క్రతువులు',
			'hi' => 'वैदिक पूजा विधि एवं अनुष्ठान',
		],
		'Pooja Guides' => [
			'te' => 'పూజా విధానం',
			'hi' => 'पूजा विधि',
		],
		'View All Pooja Guides' => [
			'te' => 'అన్ని పూజా విధానాలు చూడండి',
			'hi' => 'सभी पूजा विधियां देखें',
		],
		'Sacred Mantras & Slokas' => [
			'te' => 'పవిత్ర మంత్రాలు & స్తోత్రాలు',
			'hi' => 'पवित्र मंत्र एवं स्तोत्र',
		],
		'Sacred Mantras' => [
			'te' => 'పవిత్ర మంత్రాలు',
			'hi' => 'पवित्र मंत्र',
		],
		'Explore All Mantras & Stotrams' => [
			'te' => 'అన్ని మంత్రాలు & స్తోత్రాలు చూడండి',
			'hi' => 'सभी मंत्र एवं स्तोत्र देखें',
		],
		'Temples of Bharat' => [
			'te' => 'భారతీయ పుణ్యక్షేత్రాలు',
			'hi' => 'भारत के पावन मंदिर',
		],
		'Discover All Temples' => [
			'te' => 'అన్ని దేవాలయాలను చూడండి',
			'hi' => 'सभी मंदिर देखें',
		],
		'Vedic Services & Seva' => [
			'te' => 'వేద సేవలు & సంప్రదింపులు',
			'hi' => 'वैदिक सेवाएं एवं परामर्श',
		],
		'View All Vedic Services' => [
			'te' => 'అన్ని వేద సేవలు చూడండి',
			'hi' => 'सभी वैदिक सेवाएं देखें',
		],
		'Sacred Knowledge & Articles' => [
			'te' => 'వేద జ్ఞానం & వ్యాసాలు',
			'hi' => 'वैदिक ज्ञान एवं लेख',
		],
		'Browse All Articles & Guides' => [
			'te' => 'అన్ని వ్యాసాలు చూడండి',
			'hi' => 'सभी लेख देखें',
		],
		'Live Calculation' => [
			'te' => 'ఖగోళ గణన',
			'hi' => 'प्रत्यक्ष गणना',
		],
		"Today's Complete Panchangam" => [
			'te' => 'నేటి సంపూర్ణ పంచాంగం',
			'hi' => 'आज का संपूर्ण पंचांग',
		],
		'Search panchangam, festivals, mantras, temples...' => [
			'te' => 'పంచాంగం, పండుగలు, మంత్రాలు, దేవాలయాలు వెతకండి...',
			'hi' => 'पंचांग, त्योहार, मंत्र, मंदिर खोजें...',
		],
		'Quick Access' => [
			'te' => 'త్వరిత దర్శనం',
			'hi' => 'त्वरित पहुँच',
		],

		// Panchangam Elements
		'Tithi' => [
			'te' => 'తిథి',
			'hi' => 'तिथि',
		],
		'Nakshatra' => [
			'te' => 'నక్షత్రం',
			'hi' => 'नक्षत्र',
		],
		'Yoga' => [
			'te' => 'యోగం',
			'hi' => 'योग',
		],
		'Karana' => [
			'te' => 'కరణం',
			'hi' => 'करण',
		],
		'Vara' => [
			'te' => 'వారం',
			'hi' => 'वार',
		],
		'Vara (Weekday)' => [
			'te' => 'వారం (దినం)',
			'hi' => 'वार (दिन)',
		],
		'Paksha' => [
			'te' => 'పక్షం',
			'hi' => 'पक्ष',
		],
		'Ayanam' => [
			'te' => 'అయనం',
			'hi' => 'अयन',
		],
		'Rutu' => [
			'te' => 'ఋతువు',
			'hi' => 'ऋतु',
		],
		'Masa' => [
			'te' => 'మాసం',
			'hi' => 'मास',
		],
		'Samvatsara' => [
			'te' => 'సంవత్సరం',
			'hi' => 'संवत्सर',
		],
		'Sunrise' => [
			'te' => 'సూర్యోదయం',
			'hi' => 'सूर्योदय',
		],
		'Sunset' => [
			'te' => 'సూర్యాస్తమయం',
			'hi' => 'सूर्यास्त',
		],
		'Moonrise' => [
			'te' => 'చంద్రోదయం',
			'hi' => 'चंद्रोदय',
		],
		'Moonset' => [
			'te' => 'చంద్రాస్తమయం',
			'hi' => 'चंद्रास्त',
		],
		'Rahu Kalam' => [
			'te' => 'రాహు కాలం',
			'hi' => 'राहु काल',
		],
		'Yamagandam' => [
			'te' => 'యమగండం',
			'hi' => 'यमगंड',
		],
		'Gulika Kalam' => [
			'te' => 'గుళిక కాలం',
			'hi' => 'गुलिक काल',
		],
		'Abhijit Muhurtham' => [
			'te' => 'అభిజిత్ ముహూర్తం',
			'hi' => 'अभिजित मुहूर्त',
		],
		'Amrit Kalam' => [
			'te' => 'అమృత కాలం',
			'hi' => 'अमृत काल',
		],
		'Durmuhurtham' => [
			'te' => 'దుర్ముహూర్తం',
			'hi' => 'दुर्मुहूर्त',
		],
		'Varjyam' => [
			'te' => 'వర్జ్యం',
			'hi' => 'वर्ज्य',
		],
		'Brahma Muhurtham' => [
			'te' => 'బ్రహ్మ ముహూర్తం',
			'hi' => 'ब्रह्म मुहूर्त',
		],
		'Pancha Angas' => [
			'te' => 'పంచాంగాలు',
			'hi' => 'पंचांग अंग',
		],
		'Auspicious Timings' => [
			'te' => 'శుభ సమయాలు',
			'hi' => 'शुभ समय',
		],
		'Inauspicious Timings' => [
			'te' => 'అశుభ సమయాలు',
			'hi' => 'अशुभ समय',
		],
		'Solar & Lunar Timings' => [
			'te' => 'సూర్య & చంద్ర సమయాలు',
			'hi' => 'सूर्य एवं चंद्र समय',
		],
		'Dur Muhurtam' => [
			'te' => 'దుర్ముహూర్తం',
			'hi' => 'दुर्मुहूर्त',
		],
		'Varjyam (Tyajyam)' => [
			'te' => 'వర్జ్యం (త్యాజ్యం)',
			'hi' => 'वर्ज्य (त्याज्य)',
		],
		'Pancha Angas Summary' => [
			'te' => 'పంచాంగ సంక్షేపం',
			'hi' => 'पंचांग सारांश',
		],
		'Solar Noon' => [
			'te' => 'మధ్యాహ్నం',
			'hi' => 'मध्याह्न',
		],
		'Day Length' => [
			'te' => 'పగటి కాలం',
			'hi' => 'दिन की अवधि',
		],
		'Moon Phase' => [
			'te' => 'చంద్ర దశ',
			'hi' => 'चंद्र चरण',
		],
		'Lunar Description' => [
			'te' => 'చంద్ర వివరణ',
			'hi' => 'चंद्र विवरण',
		],
		'Illumination %' => [
			'te' => 'ప్రకాశం %',
			'hi' => 'प्रकाश %',
		],
		'Solar Day Details' => [
			'te' => 'సౌర వివరాలు',
			'hi' => 'सौर दिन का विवरण',
		],
		'Lunar Phase & Illumination' => [
			'te' => 'చంద్ర కళ & ప్రకాశం',
			'hi' => 'चंद्र कला एवं प्रकाश',
		],
		'Calculation Convention' => [
			'te' => 'గణన పద్ధతి',
			'hi' => 'गणना पद्धति',
		],
		'Engine Verification Metadata' => [
			'te' => 'ఖగోళ గణన సమాచారం',
			'hi' => 'इंजन सत्यापन मेटाडेटा',
		],
		'Important Note' => [
			'te' => 'ముఖ్య గమనిక',
			'hi' => 'महत्वपूर्ण सूचना',
		],
		'Jump Date:' => [
			'te' => 'తేదీకి వెళ్లండి:',
			'hi' => 'तारीख चुनें:',
		],
		'Select Date' => [
			'te' => 'తేదీని ఎంచుకోండి',
			'hi' => 'तारीख चुनें',
		],
		'Previous Day' => [
			'te' => 'మునుపటి రోజు',
			'hi' => 'पिछला दिन',
		],
		'Next Day' => [
			'te' => 'తర్వాతి రోజు',
			'hi' => 'अगला दिन',
		],
		'Tomorrow' => [
			'te' => 'రేపు',
			'hi' => 'कल',
		],
		'Yesterday' => [
			'te' => 'నిన్న',
			'hi' => 'बीता कल',
		],
		'Weekday & Planetary Ruler' => [
			'te' => 'వారం & అధిపతి గ్రహం',
			'hi' => 'वार एवं ग्रह अधिपति',
		],
		'Lunar Day & Ending Time' => [
			'te' => 'చాంద్రమాన తిథి & సమాప్తి సమయం',
			'hi' => 'चंद्र तिथि एवं समाप्ति काल',
		],
		'Lunar Mansion, Pada & End Time' => [
			'te' => 'నక్షత్రం, పాదం & సమాప్తి సమయం',
			'hi' => 'नक्षत्र, चरण एवं समाप्ति काल',
		],
		'Solar-Lunar Combination' => [
			'te' => 'సూర్య-చంద్ర యోగం',
			'hi' => 'सूर्य-चंद्र योग',
		],
		'Half Lunar Day' => [
			'te' => 'కరణం (అర్ధ తిథి)',
			'hi' => 'करण (अर्ध तिथि)',
		],
		'8th Muhurtham (Highly Auspicious)' => [
			'te' => '8వ ముహూర్తం (విజయప్రదం)',
			'hi' => '8वां मुहूर्त (अत्यंत शुभ)',
		],
		'Auspicious & Favorable Time' => [
			'te' => 'అమృత కాలం · శుభ ప్రదం',
			'hi' => 'अमृत काल · शुभ फलदायी',
		],
		'Pre-Dawn Sacred Muhurtham' => [
			'te' => 'సూర్యోదయానికి పూర్వం',
			'hi' => 'सूर्योदय से पूर्व पवित्र समय',
		],
		'Inauspicious Segment ruled by Rahu' => [
			'te' => 'రాహువు అధిపతి కాలం',
			'hi' => 'राहु द्वारा शासित अशुभ काल',
		],
		'Inauspicious Segment ruled by Yama' => [
			'te' => 'యముని అధిపత్య కాలం',
			'hi' => 'यम द्वारा शासित अशुभ काल',
		],
		'Segment ruled by Gulika (Son of Shani)' => [
			'te' => 'గుళిక కాలం · శని పుత్ర గుళిక',
			'hi' => 'गुलिक काल · शनि पुत्र',
		],
		'Inauspicious Time (Forbidden)' => [
			'te' => 'నిషిద్ధ సమయం',
			'hi' => 'अशुभ समय (वर्जित)',
		],
		'Tyajya Kalam (Inauspicious / Avoidable)' => [
			'te' => 'వర్జ్యం / త్యాజ్య కాలం',
			'hi' => 'त्याज्य काल · वर्जित',
		],
		'Change Location Across India' => [
			'te' => 'భారతదేశం అంతటా స్థానాన్ని మార్చండి',
			'hi' => 'भारत भर में स्थान बदलें',
		],
		'Detect Current GPS Location' => [
			'te' => 'ప్రస్తుత GPS స్థానాన్ని గుర్తించండి',
			'hi' => 'वर्तमान GPS स्थान का पता लगाएं',
		],
		'Real-Time Astronomical Engine · Drik Ganita (Lahiri Ayanamsa)' => [
			'te' => 'ప్రత్యక్ష ఖగోళ గణన యంత్రం · దృక్ సిద్ధాంతం (లాహిరి అయనాంశ)',
			'hi' => 'वास्तविक समय खगोलीय गणना · दृक गणित (लाहिड़ी अयनांश)',
		],
		"Loading today's panchangam…" => [
			'te' => 'నేటి పంచాంగం లోడ్ అవుతోంది…',
			'hi' => 'आज का पंचांग लोड हो रहा है…',
		],
		'Calculating Panchangam…' => [
			'te' => 'పంచాంగం లెక్కిస్తోంది…',
			'hi' => 'पंचांग की गणना की जा रही है…',
		],
		'Running Drik Ganita planetary equations via DJV Core engine.' => [
			'te' => 'డిజెవి కోర్ ఇంజిన్ ద్వారా దృక్ గణిత గ్రహ సమీకరణాలను లెక్కిస్తోంది.',
			'hi' => 'डीजेवी कोर इंजन के माध्यम से दृक गणितीय समीकरणों की गणना जारी है।',
		],
		'Ayanamsa:' => [
			'te' => 'అయనాంశ:',
			'hi' => 'अयनांश:',
		],
		'Lahiri (Chitrapaksha) — Government of India Standard Calendar Reform Committee recommendation.' => [
			'te' => 'లాహిరి (చిత్రపక్ష) — భారత ప్రభుత్వ పంచాంగ సంస్కరణ కమిటీ సిఫార్సు.',
			'hi' => 'लाहिड़ी (चित्रापक्ष) — भारत सरकार पंचांग सुधार समिति की अनुशंसा।',
		],
		'Coordinate System:' => [
			'te' => 'కోఆర్డినేట్ వ్యవస్థ:',
			'hi' => 'निर्देशांक प्रणाली:',
		],
		'Topocentric coordinates accounting for lunar horizontal parallax, semi-diameter, and atmospheric refraction.' => [
			'te' => 'చంద్ర సమాంతర పారలాక్స్, వ్యాసార్థం మరియు వాతావరణ వక్రీభవనాన్ని పరిగణనలోకి తీసుకునే స్థానిక సమన్వయాలు.',
			'hi' => 'चंद्र क्षितिज लंबन, अर्ध-व्यास एवं वायुमंडलीय अपवर्तन को समाहित करते भू-केंद्रीय निर्देशांक।',
		],
		'Convention:' => [
			'te' => 'సిద్ధాంతం:',
			'hi' => 'मान्यता:',
		],
		'Astronomical Udaya Tithi (Tithi at local sunrise) and civil date attribution.' => [
			'te' => 'ఖగోళ ఉదయ తిథి (స్థానిక సూర్యోదయ తిథి) మరియు సివిల్ తేదీ వర్తింపు.',
			'hi' => 'खगोलीय उदय तिथि (स्थानीय सूर्योदय कालीन तिथि) एवं सौर दिन गणना।',
		],
		'Timezone:' => [
			'te' => 'సమయ మండలం:',
			'hi' => 'समय क्षेत्र:',
		],
		'Indian Standard Time (IST, UTC+05:30) calculated from exact observer latitude & longitude.' => [
			'te' => 'ఖచ్చితమైన పరిశీలకుల అక్షాంశ & రేఖాంశాల ఆధారంగా భారతీయ ప్రామాణిక సమయం (IST, UTC+05:30).',
			'hi' => 'सटीक अक्षांश एवं देशांतर के आधार पर भारतीय मानक समय (IST, UTC+05:30)।',
		],
		'Calculated in real-time from verified astronomical coordinates:' => [
			'te' => 'ధృవీకరించబడిన ఖగోళ సమన్వయాల నుండి నిజ సమయంలో లెక్కించబడింది:',
			'hi' => 'सत्यापित खगोलीय निर्देशांकों से वास्तविक समय में गणना की गई:',
		],
		'Loading metadata…' => [
			'te' => 'మెటాడేటా లోడ్ అవుతోంది…',
			'hi' => 'मेटाडेटा लोड हो रहा है…',
		],
		'Panchangam calculations are provided for devotional and informational purposes based on Drik Ganita ephemeris. For ceremonial Muhurtham decisions, please consult an authoritative Jyotishi.' => [
			'te' => 'పంచాంగ గణనలు దృక్ సిద్ధాంతం ఆధారంగా భక్తి మరియు సమాచార ప్రయోజనాల కోసం అందించబడ్డాయి. శుభ ముహూర్త నిర్ణయాల కోసం ప్రామాణిక జ్యోతిష్కులను సంప్రదించండి.',
			'hi' => 'पंचांग गणना दृक गणित पर आधारित भक्ति एवं जानकारी के उद्देश्य से प्रदान की गई है। विशिष्ट शुभ मुहूर्त निर्णयों हेतु कृपया किसी प्रामाणिक ज्योतिषी से परामर्श लें।',
		],
		'Change' => [
			'te' => 'మార్చండి',
			'hi' => 'बदलें',
		],
		'Date Presets' => [
			'te' => 'తేదీ ఎంపికలు',
			'hi' => 'तारीख विकल्प',
		],
		'Breadcrumb' => [
			'te' => 'మార్గం',
			'hi' => 'मार्ग',
		],
		'Solar and lunar astronomical timings' => [
			'te' => 'సూర్య మరియు చంద్ర ఖగోళ సమయాలు',
			'hi' => 'सूर्य एवं चंद्र खगोलीय समय',
		],
		'Pancha Angas Summary Table' => [
			'te' => 'పంచాంగ సంక్షేప పట్టిక',
			'hi' => 'पंचांग सारांश तालिका',
		],

		// Common Actions & Form Controls
		'Search Mantras...' => [
			'te' => 'మంత్రాలను వెతకండి...',
			'hi' => 'मंत्र खोजें...',
		],
		'Search festivals...' => [
			'te' => 'పండుగలను వెతకండి...',
			'hi' => 'त्योहार खोजें...',
		],
		'Search Festivals' => [
			'te' => 'పండుగలను వెతకండి',
			'hi' => 'त्योहार खोजें',
		],
		'Clear' => [
			'te' => 'క్లియర్',
			'hi' => 'साफ करें',
		],
		'Retry' => [
			'te' => 'మళ్ళీ ప్రయత్నించండి',
			'hi' => 'पुनः प्रयास करें',
		],
		'Use My Location' => [
			'te' => 'నా స్థానాన్ని ఉపయోగించు',
			'hi' => 'मेरा स्थान उपयोग करें',
		],
		'All' => [
			'te' => 'అన్నీ',
			'hi' => 'सभी',
		],
		'Filter by Category' => [
			'te' => 'వర్గం ప్రకారం ఫిల్టర్ చేయండి',
			'hi' => 'श्रेणी अनुसार फिल्टर करें',
		],
		'Filter by Year' => [
			'te' => 'సంవత్సరం ప్రకారం ఫిల్టర్ చేయండి',
			'hi' => 'वर्ष अनुसार फिल्टर करें',
		],
		'Read Mantra' => [
			'te' => 'మంత్రం చదవండి',
			'hi' => 'मंत्र पढ़ें',
		],
		'Copy Verse' => [
			'te' => 'శ్లోకం కాపీ చేయండి',
			'hi' => 'श्लोक कॉपी करें',
		],
		'Copied!' => [
			'te' => 'కాపీ అయింది!',
			'hi' => 'कॉपी हो गया!',
		],
		'Chant Count' => [
			'te' => 'జప సంఖ్య',
			'hi' => 'जप संख्या',
		],
		'Best Time' => [
			'te' => 'ఉత్తమ సమయం',
			'hi' => 'उत्तम समय',
		],
		'Puja Vidhi & Muhurat' => [
			'te' => 'పూజా విధానం & ముహూర్తం',
			'hi' => 'पूजा विधि एवं मुहूर्त',
		],
		'Puja Vidhi & Muhurat →' => [
			'te' => 'పూజా విధానం & ముహూర్తం →',
			'hi' => 'पूजा विधि एवं मुहूर्त →',
		],
		'Samagri' => [
			'te' => 'సామగ్రి',
			'hi' => 'सामग्री',
		],
		'12-Step Vidhi' => [
			'te' => '12 విధానాలు',
			'hi' => '12 चरण विधि',
		],
		'Subscribe' => [
			'te' => 'సబ్స్క్రైబ్',
			'hi' => 'सदस्यता लें',
		],
		'All rights reserved.' => [
			'te' => 'అన్ని హక్కులు ప్రత్యేకించబడ్డాయి.',
			'hi' => 'सर्वाधिकार सुरक्षित।',
		],
		'Privacy Policy' => [
			'te' => 'గోప్యతా విధానం',
			'hi' => 'गोपनीयता नीति',
		],
		'Terms of Service' => [
			'te' => 'నిబంధనలు & షరతులు',
			'hi' => 'सेवा की शर्तें',
		],
		'Astronomical Disclaimer' => [
			'te' => 'ఖగోళ విజ్ఞాపన',
			'hi' => 'खगोलीय अस्वीकरण',
		],
	];

	return $dict;
}

/**
 * 7. Hook WordPress Gettext Filter
 *
 * Ensures all standard __( 'Home', 'djv' ), esc_html__( 'Home', 'djv-theme' ),
 * _e(), esc_html_e(), and _x() calls automatically return the translated string!
 */
function djv_translate_gettext_filter( string $translation, string $text, string $domain ): string {
	// Apply to djv, djv-theme, djv-core, or default domains
	if ( in_array( $domain, [ 'djv', 'djv-theme', 'djv-core', 'default' ], true ) || empty( $domain ) ) {
		$lang = djv_get_current_language();
		if ( $lang === 'en' ) {
			return $text;
		}

		$dict = djv_get_translation_dictionary();
		if ( isset( $dict[ $text ][ $lang ] ) ) {
			return $dict[ $text ][ $lang ];
		}
	}

	return $translation;
}
add_filter( 'gettext', 'djv_translate_gettext_filter', 20, 3 );
add_filter( 'ngettext', function( $translation, $single, $plural, $number, $domain ) {
	return djv_translate_gettext_filter( $translation, $single, $domain );
}, 20, 5 );

/**
 * Direct Translation Helper
 *
 * @param string      $text
 * @param string|null $lang
 * @return string
 */
function djv__( string $text, ?string $lang = null ): string {
	$lang = $lang ?: djv_get_current_language();
	if ( $lang === 'en' ) {
		return $text;
	}
	$dict = djv_get_translation_dictionary();
	if ( isset( $dict[ $text ][ $lang ] ) ) {
		return $dict[ $text ][ $lang ];
	}
	return $text;
}

/**
 * 8. Content Field Fallback (Section 15)
 *
 * Fallback order:
 * requested language -> English -> empty/not available.
 * NEVER silently fall back to Telugu when English or Hindi is requested!
 *
 * @param int         $post_id
 * @param string      $field_key e.g. 'title', 'content', 'description', 'intro'
 * @param string|null $lang
 * @return string
 */
function djv_get_localized_post_meta( int $post_id, string $field_key, ?string $lang = null ): string {
	$lang = $lang ?: djv_get_current_language();

	// 1. Check requested language specific meta
	$val_lang = get_post_meta( $post_id, "_djv_{$field_key}_{$lang}", true );
	if ( ! empty( $val_lang ) ) {
		return $val_lang;
	}

	// Legacy meta keys check for Telugu
	if ( $lang === 'te' ) {
		$legacy_te = get_post_meta( $post_id, "_djv_{$field_key}_te", true ) ?: get_post_meta( $post_id, "_djv_telugu_{$field_key}", true );
		if ( ! empty( $legacy_te ) ) {
			return $legacy_te;
		}
	}

	// 2. Fall back to English
	$val_en = get_post_meta( $post_id, "_djv_{$field_key}_en", true );
	if ( ! empty( $val_en ) ) {
		return $val_en;
	}

	// Base fallback (post title or default meta key)
	if ( $field_key === 'title' ) {
		return get_the_title( $post_id );
	}
	if ( $field_key === 'content' ) {
		$post = get_post( $post_id );
		return $post ? $post->post_content : '';
	}

	$val_base = get_post_meta( $post_id, "_djv_{$field_key}", true );
	if ( ! empty( $val_base ) ) {
		return $val_base;
	}

	// 3. Empty string
	return '';
}

/**
 * 9. Document Titles Localization (Rank Math + WordPress)
 */
function djv_filter_global_document_title( string $title ): string {
	$lang = djv_get_current_language();
	if ( $lang === 'en' ) {
		return $title;
	}

	// Front page title
	if ( is_front_page() || is_home() ) {
		if ( $lang === 'te' ) {
			return 'ధర్మ జ్యోతి వేదిక – హిందూ పంచాంగం & భక్తి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'धर्म ज्योति वेदिका – हिंदू पंचांग एवं भक्ति मंच';
		}
	}

	// Panchangam page
	if ( is_page( 'panchangam' ) || is_page( 'panchangam/today' ) || is_page( 'today' ) ) {
		if ( $lang === 'te' ) {
			return 'నేటి హిందూ పంచాంగం | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'आज का हिंदू पंचांग | धर्म ज्योति वेदिका';
		}
	}

	// Festivals Archive
	if ( is_post_type_archive( 'djv_festival' ) || is_page( 'festivals' ) ) {
		if ( $lang === 'te' ) {
			return 'హిందూ పండుగలు & వ్రతాలు | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'हिंदू त्योहार एवं व्रत | धर्म ज्योति वेदिका';
		}
	}

	// Muhurtham Archive
	if ( is_post_type_archive( 'djv_muhurtham' ) || is_page( 'muhurtham' ) ) {
		if ( $lang === 'te' ) {
			return 'శుభ ముహూర్తములు & కాల నిర్ణయం | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'शुभ मुहूर्त खोज | धर्म ज्योति वेदिका';
		}
	}

	// Pooja Archive
	if ( is_post_type_archive( 'djv_pooja' ) || is_page( 'pooja' ) ) {
		if ( $lang === 'te' ) {
			return 'పూజా విధానం, సంకల్పం & సామగ్రి | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'पूजा विधि, संकल्प एवं सामग्री | धर्म ज्योति वेदिका';
		}
	}

	// Mantras Archive
	if ( is_post_type_archive( 'djv_mantra' ) || is_page( 'mantras' ) ) {
		if ( $lang === 'te' ) {
			return 'పవిత్ర మంత్రాలు & స్తోత్రాలు | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'पवित्र मंत्र एवं स्तोत्र | धर्म ज्योति वेदिका';
		}
	}

	// Temples Archive
	if ( is_post_type_archive( 'djv_temple' ) || is_page( 'temples' ) ) {
		if ( $lang === 'te' ) {
			return 'పుణ్యక్షేత్రాలు & దివ్యాలయాలు | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'भारत के पावन मंदिर | धर्म ज्योति वेदिका';
		}
	}

	// Articles Archive
	if ( is_page( 'articles' ) ) {
		if ( $lang === 'te' ) {
			return 'వేద వ్యాసాలు & ఆధ్యాత్మిక జ్ఞానం | ధర్మ జ్యోతి వేదిక';
		} elseif ( $lang === 'hi' ) {
			return 'वैदिक लेख एवं आध्यात्मिक ज्ञान | धर्म ज्योति वेदिका';
		}
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'djv_filter_global_document_title', 25 );
add_filter( 'rank_math/frontend/title', 'djv_filter_global_document_title', 25 );

/**
 * 10. Centralized JavaScript Configuration Output (Section 7)
 *
 * Exposes window.DJV_LANGUAGE in <head> so all scripts can immediately access it.
 */
function djv_output_head_language_config(): void {
	$lang = djv_get_current_language();
	$supported = djv_get_supported_languages();
	?>
<script id="djv-language-config">
window.DJV_LANGUAGE = {
  current: <?php echo wp_json_encode( $lang ); ?>,
  supported: <?php echo wp_json_encode( $supported ); ?>,
  switch: function(targetLang) {
    var supported = <?php echo wp_json_encode( $supported ); ?>;
    if (!supported.includes(targetLang)) return;
    try {
      localStorage.setItem('djv_language', targetLang);
      localStorage.setItem('djv_lang', targetLang);
      document.cookie = 'djv_language=' + encodeURIComponent(targetLang) + '; path=/; max-age=31536000; SameSite=Lax';
    } catch(e) {}
    try {
      var u = new URL(window.location.href);
      u.searchParams.set('lang', targetLang);
      window.location.href = u.toString();
    } catch(e) {
      window.location.search = '?lang=' + encodeURIComponent(targetLang);
    }
  }
};
</script>
<style id="djv-language-display-rules">
/* Instant CSS-level trilingual field enforcement */
html[lang="en"] .djv-lang-field[data-lang="te"],
html[lang="en"] .djv-lang-field[data-lang="hi"] { display: none !important; }
html[lang="en"] .djv-lang-field[data-lang="en"] { display: inline !important; }
html[lang="en"] div.djv-lang-field[data-lang="en"],
html[lang="en"] p.djv-lang-field[data-lang="en"],
html[lang="en"] section.djv-lang-field[data-lang="en"] { display: block !important; }

html[lang="te"] .djv-lang-field[data-lang="en"],
html[lang="te"] .djv-lang-field[data-lang="hi"] { display: none !important; }
html[lang="te"] .djv-lang-field[data-lang="te"] { display: inline !important; }
html[lang="te"] div.djv-lang-field[data-lang="te"],
html[lang="te"] p.djv-lang-field[data-lang="te"],
html[lang="te"] section.djv-lang-field[data-lang="te"] { display: block !important; }

html[lang="hi"] .djv-lang-field[data-lang="en"],
html[lang="hi"] .djv-lang-field[data-lang="te"] { display: none !important; }
html[lang="hi"] .djv-lang-field[data-lang="hi"] { display: inline !important; }
html[lang="hi"] div.djv-lang-field[data-lang="hi"],
html[lang="hi"] p.djv-lang-field[data-lang="hi"],
html[lang="hi"] section.djv-lang-field[data-lang="hi"] { display: block !important; }
</style>
	<?php
}
add_action( 'wp_head', 'djv_output_head_language_config', 2 );
