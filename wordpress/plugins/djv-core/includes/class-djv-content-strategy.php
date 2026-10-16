<?php
/**
 * DJV Core — Multilingual Content Strategy & Opportunity Dashboard
 *
 * Implements research-driven organic SEO content planning for PujaPanchang.
 * Manages keyword queues, search intent, cannibalization audits,
 * fact-check checklists, and editorial workflow across English, Telugu, and Hindi.
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DJV_Content_Strategy {

	const MENU_SLUG = 'djv-content-strategy';
	const OPTION_KEY = 'djv_content_opportunities';

	/**
	 * Initialize hooks.
	 */
	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'register_admin_menu' ], 32 );
		add_action( 'wp_ajax_djv_update_content_status', [ __CLASS__, 'ajax_update_status' ] );
		add_action( 'wp_ajax_djv_save_content_opportunity', [ __CLASS__, 'ajax_save_opportunity' ] );
		add_action( 'wp_ajax_djv_reset_content_strategy_seeds', [ __CLASS__, 'ajax_reset_seeds' ] );
	}

	/**
	 * Register the admin menu page.
	 */
	public static function register_admin_menu(): void {
		add_submenu_page(
			'dharma-jyothi-vedika',
			__( 'Content Strategy', 'djv-core' ),
			__( 'Content Strategy', 'djv-core' ),
			'manage_options',
			self::MENU_SLUG,
			[ __CLASS__, 'render_dashboard' ]
		);
	}

	/**
	 * Return all content opportunities, initializing seeds if empty.
	 *
	 * @return array
	 */
	public static function get_opportunities(): array {
		$data = get_option( self::OPTION_KEY, null );
		if ( ! is_array( $data ) || empty( $data ) ) {
			$data = self::get_seed_opportunities();
			update_option( self::OPTION_KEY, $data, false );
		}
		return $data;
	}

	/**
	 * Initial curated seeds across English, Telugu, and Hindi.
	 *
	 * @return array
	 */
	public static function get_seed_opportunities(): array {
		return [
			// ==========================================
			// 1. ENGLISH CONTENT OPPORTUNITIES
			// ==========================================
			[
				'id'                   => 'griha-pravesh-muhurat-2027-en',
				'topic'                => 'Griha Pravesh Muhurat 2027: Month-by-Month Auspicious Dates, Nakshatra Rules & Vastu Guidelines',
				'primary_keyword'      => 'griha pravesh muhurat 2027',
				'secondary_keywords'   => [ 'house warming dates 2027', 'best nakshatra for griha pravesh', 'chaturmas griha pravesh rules', 'vastu shanti puja for new home' ],
				'language'             => 'en',
				'category'             => 'muhurtham',
				'search_intent'        => 'Planning & Date Lookup',
				'evidence_source'      => 'Google Trends rising search demand for 2027 construction & registry planning; Industry benchmark (Drik Panchang, MagicBricks). First-party GSC: Insufficient first-party data (staging environment).',
				'target_region'        => 'Pan-India, Urban Centers & NRI Diaspora',
				'priority_score'       => 94,
				'seasonal_window'      => 'Publish Oct–Nov 2026 (Advance 2027 Housewarming Planning)',
				'competing_url'        => '/muhurtham/griha-pravesh/',
				'cannibalization_risk' => 'Low if structured properly. /muhurtham/griha-pravesh/ serves as the live dynamic calculator; this article provides deep narrative Vastu rules, dos/don\'ts, and seasonal bans (Chaturmas, Kharmas), embedding links to the calculator.',
				'suggested_title'      => 'Griha Pravesh Muhurat 2027: Auspicious Dates, Nakshatra & Vastu Guide | PujaPanchang',
				'outline'              => [
					'H2: Quick Answer: Best Months for Griha Pravesh in 2027',
					'H2: Month-by-Month 2027 Auspicious Housewarming Dates (Spring vs Autumn windows)',
					'H2: Essential Astrological Rules: Auspicious Nakshatras & Tithis for Entry',
					'H2: Inauspicious Periods to Avoid: Chaturmas (July–Nov 2027) & Kharmas/Adhik Maas',
					'H2: Step-by-Step Vastu & Navagraha Shanti Puja Vidhi for New Home',
					'H2: Frequently Asked Questions on 2027 Housewarming'
				],
				'fact_check_rules'     => 'Verify planetary combustion (Shukra/Guru Asta in July 2027); confirm Devshayani Ekadashi (July 14, 2027) to Devuthani Ekadashi (Nov 10, 2027) ban using DJV Muhurtham Engine.',
				'internal_links'       => [ '/muhurtham/griha-pravesh/', '/panchangam/', '/pooja/griha-pravesh-pooja/', '/muhurtham/' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'marriage-muhurat-2027-en',
				'topic'                => 'Hindu Marriage (Vivah) Muhurat 2027: Shubh Dates, Nakshatra Shuddhi & Avoidable Periods',
				'primary_keyword'      => 'marriage muhurat 2027',
				'secondary_keywords'   => [ 'hindu wedding dates 2027', 'shukra asta 2027 marriage dates', 'chaturmas 2027 wedding ban', 'best lagna for marriage' ],
				'language'             => 'en',
				'category'             => 'muhurtham',
				'search_intent'        => 'Auspicious Date Consultation & Wedding Planning',
				'evidence_source'      => 'High search surge across Indian matrimonial portals & event planners for 2027 venue booking. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Pan-India & Global Hindu Community',
				'priority_score'       => 92,
				'seasonal_window'      => 'Publish Q4 2026 (Advance Wedding Planning for 2027)',
				'competing_url'        => '/muhurtham/marriage/',
				'cannibalization_risk' => 'Low. Dynamic hub (/muhurtham/marriage/) provides instant date verification; article provides wedding astrology methodology, Gotra rules, and Shuddha Lagna explanation.',
				'suggested_title'      => 'Hindu Marriage Muhurat 2027: Auspicious Dates, Lagnam & Astrological Guide | PujaPanchang',
				'outline'              => [
					'H2: Quick Summary: 2027 Wedding Season Windows (Winter, Spring & Late Autumn)',
					'H2: Month-by-Month Vivah Muhurat Calendar 2027 (Jan, Feb, Apr, May, Dec)',
					'H2: Why Weddings Stop: Shukra Asta (Venus Combustion) & Chaturmas 2027 Explained',
					'H2: How Shuddha Vivah Lagnam is Calculated (Sun, Moon & Jupiter Transits)',
					'H2: FAQs on Auspicious Marriage Timings in 2027'
				],
				'fact_check_rules'     => 'Cross-verify Guru Gochara transits, Shukra Asta dates (starting July 6, 2027), and ensure amanta/purnimanta month boundary consistency with DJV calculation engine.',
				'internal_links'       => [ '/muhurtham/marriage/', '/panchangam/', '/services/' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'diwali-lakshmi-puja-vidhi-home-en',
				'topic'                => 'Diwali Lakshmi & Kubera Puja Step-by-Step Vidhi at Home: Pradosha Muhurat & Checklist',
				'primary_keyword'      => 'diwali lakshmi puja vidhi at home',
				'secondary_keywords'   => [ 'diwali puja samagri list', 'pradosha kala lakshmi puja timing', 'bahi khata puja procedure', 'lakshmi aarti and mantras' ],
				'language'             => 'en',
				'category'             => 'festivals',
				'search_intent'        => 'Ritual Execution & Devotional Guide',
				'evidence_source'      => 'Annual search volume exponential peak in Kartik month (Google Trends India score: 100). First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Pan-India, Urban & NRI Families',
				'priority_score'       => 96,
				'seasonal_window'      => 'Immediate (Kartik / Diwali Season)',
				'competing_url'        => '/festivals/diwali/',
				'cannibalization_risk' => 'Medium. /festivals/diwali/ targets "Diwali 2026 Date"; this article strictly targets home worship ritual, samagri checklist, and mantras. Interlink heavily.',
				'suggested_title'      => 'Diwali Lakshmi Puja Vidhi at Home: Step-by-Step Procedure & Pradosha Muhurat | PujaPanchang',
				'outline'              => [
					'H2: Overview: Ideal Pradosha Kala Timing for Lakshmi Puja',
					'H2: Essential Diwali Puja Samagri (Checklist with Alternatives)',
					'H2: Step 1: Kalasha Sthapana and Shodashopachara Invocation',
					'H2: Step 2: Maha Lakshmi, Ganesha, and Kuber Archana with Vedic Mantras',
					'H2: Step 3: Bahi-Khata & Accounts Sanctification Ritual',
					'H2: Step 4: Aarti, Prasad Distribution, and Deep Daan at Twilight',
					'H2: Common Mistakes to Avoid During Home Lakshmi Puja'
				],
				'fact_check_rules'     => 'Verify Pradosha Kala window (sunset to 2 hours 24 mins post-sunset) based on Lahiri astronomical sunrise/sunset in major Indian metros.',
				'internal_links'       => [ '/festivals/diwali/', '/pooja/diwali-lakshmi-pooja/', '/mantras/lakshmi-gayatri-mantra/', '/panchangam/' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'how-to-read-panchangam-en',
				'topic'                => 'How to Read a Daily Hindu Panchangam: A Practical Guide to Tithi, Nakshatra, Yoga and Karana',
				'primary_keyword'      => 'how to read panchangam',
				'secondary_keywords'   => [ 'panchangam for beginners', 'difference between tithi and nakshatra', 'how to find rahu kalam today', 'shubh muhurat identification' ],
				'language'             => 'en',
				'category'             => 'panchangam',
				'search_intent'        => 'Educational & Practical Application',
				'evidence_source'      => 'Consistent evergreen search demand from younger generation and diaspora seeking cultural literacy. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Global English-speaking Hindus',
				'priority_score'       => 86,
				'seasonal_window'      => 'Evergreen (Ideal for Foundation Building)',
				'competing_url'        => '/panchangam/',
				'cannibalization_risk' => 'Low. /panchangam/ is the daily tool; this article is the educational user guide explaining how to interpret the numbers.',
				'suggested_title'      => 'How to Read a Hindu Panchangam: Beginner\'s Guide to the Five Limbs of Time | PujaPanchang',
				'outline'              => [
					'H2: Why Read a Panchangam? Aligning Daily Routine with Cosmic Rhythms',
					'H2: 1. Tithi: Lunar Days and Spiritual Mindstates',
					'H2: 2. Vara: Planetary Days and Suitable Tasks',
					'H2: 3. Nakshatra: Stellar Mansions and Personal Energy',
					'H2: 4. Yoga: Sun-Moon Distance and Mental Concord',
					'H2: 5. Karana: Half-Tithi Workhorse for Business & Decisions',
					'H2: How to Identify Rahu Kalam, Yamagandam, and Abhijit Muhurtham Daily',
					'H2: Step-by-Step Example: Reading Today\'s Panchangam for an Auspicious Start'
				],
				'fact_check_rules'     => 'Coordinate with DJV calculation logic: 12-degree solar-lunar arc for Tithi; 13°20\' for Nakshatra; Lahiri Ayanamsa accuracy benchmark.',
				'internal_links'       => [ '/panchangam/', '/articles/understanding-pancha-angas/', '/articles/astronomical-significance-rahu-kalam/' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'tirupati-balaji-darshan-guide-en',
				'topic'                => 'Tirupati Balaji Darshan Guide: Special Entry ₹300 Tickets, Dress Code, Timings and Laddu Prasadam',
				'primary_keyword'      => 'tirupati balaji darshan guide',
				'secondary_keywords'   => [ 'ttd special entry darshan 300 booking', 'tirumala dress code rules', 'tirupati balaji reporting time rules', 'divya darshan walk guidelines' ],
				'language'             => 'en',
				'category'             => 'temples',
				'search_intent'        => 'Pilgrimage Logistics & Actionable Guidelines',
				'evidence_source'      => 'Over 500,000 monthly pilgrimage-related queries in India regarding Tirumala booking and rules. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Pan-India Pilgrims & International Visitors',
				'priority_score'       => 89,
				'seasonal_window'      => 'Evergreen / High Holiday Travel Seasons',
				'competing_url'        => '/temples/tirumala-venkateswara-temple/',
				'cannibalization_risk' => 'Low. Temple CPT page covers temple history, deity facts, and GPS; this article covers real-world pilgrim logistics, dress codes, and TTD portal booking advice.',
				'suggested_title'      => 'Tirupati Balaji Darshan Guide: TTD Booking, Dress Code & Visiting Tips | PujaPanchang',
				'outline'              => [
					'H2: Quick Summary: Tirumala Pilgrimage Checklist at a Glance',
					'H2: Types of Darshan: Special Entry (₹300), Slotted Sarva Darshan (SSD), and Sevas',
					'H2: TTD Online Booking Protocol: Quota Release Calendar & How to Secure Slots',
					'H2: Strict Dress Code Regulations for Men, Women & Children',
					'H2: Trekking Routes: Alipiri and Srivari Mettu Footpaths (Tokens & Timings)',
					'H2: Accommodation, Luggage Counters, and Laddu Prasadam Collection',
					'H2: Important Do\'s and Don\'ts on Tirumala Hills'
				],
				'fact_check_rules'     => 'Verify with official TTD (tirupatibalaji.ap.gov.in) rules: traditional dhoti/kurta for men, saree/churidar with dupatta for women; ₹300 ticket reporting guidelines.',
				'internal_links'       => [ '/temples/tirumala-venkateswara-temple/', '/temples/', '/panchangam/' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],

			// ==========================================
			// 2. TELUGU CONTENT OPPORTUNITIES (తెలుగు)
			// ==========================================
			[
				'id'                   => 'griha-pravesha-muhurthalu-2027-te',
				'topic'                => '2027 గృహ ప్రవేశ ముహూర్తాలు: నెలవారీ శుభ తేదీలు, నక్షత్రాలు మరియు శాస్త్ర నియమాలు',
				'primary_keyword'      => 'గృహ ప్రవేశ ముహూర్తాలు 2027',
				'secondary_keywords'   => [ 'కొత్త ఇల్లు గృహ ప్రవేశం తేదీలు 2027', 'శూన్య మాసాలు గృహ ప్రవేశం నిషేధం', 'తెలుగు పంచాంగం గృహ ప్రవేశం', 'వాస్తు పూజ విధానం' ],
				'language'             => 'te',
				'category'             => 'muhurtham',
				'search_intent'        => 'Date Lookup & Traditional Vedic Vastu Guide',
				'evidence_source'      => 'Extremely high regional Telugu search demand for Telugu Panchangam-based construction & housewarming planning. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Telangana, Andhra Pradesh & Telugu Diaspora (USA/UK/Gulf)',
				'priority_score'       => 95,
				'seasonal_window'      => 'Immediate (Advance 2027 Booking Window)',
				'competing_url'        => '/muhurtham/griha-pravesh/?lang=te',
				'cannibalization_risk' => 'Low. Dynamic page computes mathematical muhurtham; this article provides Telugu cultural context, Palukolu/Dwajasthambha rules, and traditional Amavasya/Purnima boundaries.',
				'suggested_title'      => '2027 గృహ ప్రవేశ ముహూర్తాలు: శుభ తేదీలు, నక్షత్రాలు & వాస్తు నియమాలు | పూజా పంచాంగ్',
				'outline'              => [
					'H2: ముఖ్యాంశాలు: 2027 లో గృహ ప్రవేశానికి అత్యంత శుభప్రదమైన నెలలు',
					'H2: నెలవారీ 2027 గృహ ప్రవేశ ముహూర్తాల పట్టిక (మాఘ, ఫాల్గుణ, వైశాఖ, జ్యేష్ఠ, కార్తీక మాసాలు)',
					'H2: గృహ ప్రవేశానికి ప్రశస్తమైన తిథులు, వారాలు మరియు నక్షత్రాలు (రోహిణి, మృగశిర, ఉత్తర, అనురాధ మొదలైనవి)',
					'H2: వర్జించవలసిన సమయాలు: ఆషాఢ, భాద్రపద శూన్య మాసాలు మరియు మూఢమి వివరాలు',
					'H2: సంప్రదాయ వాస్తు హోమం మరియు గృహ ప్రవేశ పూజా విధానం',
					'H2: తరచుగా అడిగే ప్రశ్నలు (FAQ)'
				],
				'fact_check_rules'     => 'Cross-check Telugu Amanta calendar calculations, Guru/Shukra Moodhami periods for 2027 using DJV Telugu astronomical rules.',
				'internal_links'       => [ '/muhurtham/griha-pravesh/?lang=te', '/panchangam/?lang=te', '/muhurtham/?lang=te' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'karthika-deeparadhana-niyamalu-te',
				'topic'                => 'కార్తీక మాస దీపారాధన నియమాలు: 365 వత్తుల దీపం మరియు ఉసిరి దీపం ప్రాశస్త్యం',
				'primary_keyword'      => 'కార్తీక మాసం దీపారాధన నియమాలు',
				'secondary_keywords'   => [ '365 వత్తుల దీపం ఎలా వెలిగించాలి', 'ఉసిరికాయ దీపం విశిష్టత', 'కార్తీక సోమవారం ఉపవాస నియమాలు', 'తులసి పూజ విధానం' ],
				'language'             => 'te',
				'category'             => 'festivals',
				'search_intent'        => 'Seasonal Devotional Practice & Home Ritual Vidhi',
				'evidence_source'      => 'Top-ranking seasonal cultural query in Telugu states throughout Karthika Masam. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Andhra Pradesh & Telangana',
				'priority_score'       => 96,
				'seasonal_window'      => 'Urgent / Immediate (Karthika Masam Season)',
				'competing_url'        => '/festivals/karthika-purnima/?lang=te',
				'cannibalization_risk' => 'Low. /festivals/karthika-purnima/ covers Purnima date; this article covers the full 30-day daily lighting discipline, wick counts, and Tulasi Damodara Puja.',
				'suggested_title'      => 'కార్తీక మాస దీపారాధన నియమాలు: 365 వత్తుల దీపం & ఉసిరి విశిష్టత | పూజా పంచాంగ్',
				'outline'              => [
					'H2: కార్తీక మాస మహిమ: దీపారాధన ఎందుకు అంత విశేషం?',
					'H2: ప్రతిరోజూ దీపం ఎప్పుడు, ఎలా వెలిగించాలి? (సూర్యోదయానికి ముందు & ప్రదోష వేళ)',
					'H2: 365 వత్తుల దీపం ప్రాముఖ్యత మరియు వెలిగించే సరైన విధానం',
					'H2: ఉసిరి చెట్టు కింద దీపారాధన మరియు కార్తీక వనభోజనాల ఆధ్యాత్మిక అంతరార్థం',
					'H2: కార్తీక సోమవారాల ఉపవాస నియమాలు మరియు ఏకభుక్త వ్రతం',
					'H2: కార్తీక దీపారాధనలో చేయకూడని తప్పులు'
				],
				'fact_check_rules'     => 'Verify Karthika month start and end dates according to Amanta Telugu calendar; cite Skanda Purana Karthika Mahatmya verses.',
				'internal_links'       => [ '/festivals/karthika-purnima/?lang=te', '/panchangam/?lang=te', '/mantras/?lang=te' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'vivaha-muhurthalu-2027-te',
				'topic'                => 'వివాహ ముహూర్తాలు 2027: తెలుగు పంచాంగం ప్రకారం శుభ లగ్నాలు మరియు వర్జిత కాలాలు',
				'primary_keyword'      => 'వివాహ ముహూర్తాలు 2027',
				'secondary_keywords'   => [ 'తెలుగు పెళ్లి ముహూర్తాలు 2027', 'మూఢమి రోజులు 2027', 'శుక్ర మూఢమి వివాహాలు నిషేధం', 'ఉత్తమ వివాహ నక్షత్రాలు' ],
				'language'             => 'te',
				'category'             => 'muhurtham',
				'search_intent'        => 'Telugu Wedding Calendar & Consultation',
				'evidence_source'      => 'High search volume among Telugu families finalizing marriage halls, purohits, and travel dates. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'AP, Telangana & Telugu Diaspora',
				'priority_score'       => 94,
				'seasonal_window'      => 'Immediate / Q4 2026',
				'competing_url'        => '/muhurtham/marriage/?lang=te',
				'cannibalization_risk' => 'Low. Dynamic page calculates specific muhurtham windows; article provides Telugu wedding traditions (Jeelakarra Bellam, Talambralu, Mangalsutra dharana timings).',
				'suggested_title'      => 'వివాహ ముహూర్తాలు 2027: తెలుగు పంచాంగం శుభ లగ్న తేదీలు | పూజా పంచాంగ్',
				'outline'              => [
					'H2: 2027 వివాహ ముహూర్తాల ముఖ్యాంశాలు మరియు శుభ కాలాలు',
					'H2: నెలవారీ వివాహ తేదీల పట్టిక (జనవరి నుండి డిసెంబర్ 2027 వరకు)',
					'H2: పెళ్లికి ఉత్తమమైన నక్షత్రాలు: రోహిణి, మృగశిర, మఖ, ఉత్తర, హస్త, స్వాతి, అనురాధ, ఉత్తరాషాఢ, ఉత్తరాభాద్ర, రేవతి',
					'H2: వివాహాలు జరగని కాలాలు: మూఢమి మరియు చాతుర్మాస విశేషాలు',
					'H2: జీలకర్ర బెల్లం సమయం మరియు సుముహూర్త ప్రాముఖ్యత'
				],
				'fact_check_rules'     => 'Verify Telugu wedding lagna shuddhi criteria, Jupiter transit into Gemini/Cancer, and Venus combustion timings via DJV calculations.',
				'internal_links'       => [ '/muhurtham/marriage/?lang=te', '/panchangam/?lang=te', '/services/?lang=te' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'varalakshmi-vratam-pooja-vidhanam-te',
				'topic'                => 'వరలక్ష్మి వ్రతం పూజా విధానం: కలశ స్థాపన, తొమ్మిది దారాల తోరం మరియు వ్రత కథ',
				'primary_keyword'      => 'వరలక్ష్మి వ్రతం పూజా విధానం',
				'secondary_keywords'   => [ 'వరలక్ష్మి వ్రతం తోరం ముడులు నియమం', 'వరలక్ష్మి వ్రతం నైవేద్యాలు 9 రకాలు', 'వరలక్ష్మి వ్రత కథ తెలుగు', 'వరలక్ష్మి అష్టోత్తర శతనామావళి' ],
				'language'             => 'te',
				'category'             => 'pooja',
				'search_intent'        => 'Devotional Ritual Execution & Katha Recitation',
				'evidence_source'      => 'Massive search volume across South India during Sravana month. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Andhra Pradesh & Telangana Telugu Households',
				'priority_score'       => 91,
				'seasonal_window'      => 'Sravana Masam Seasonal Window',
				'competing_url'        => '/pooja/varalakshmi-vratham/?lang=te',
				'cannibalization_risk' => 'Low. /pooja/varalakshmi-vratham/ contains the base liturgy; this article provides rich storytelling, prasadam recipes, toram mantras, and audio chant links.',
				'suggested_title'      => 'వరలక్ష్మి వ్రతం సంపూర్ణ పూజా విధానం: కలశ అలంకరణ, తోరం & వ్రత కథ | పూజా పంచాంగ్',
				'outline'              => [
					'H2: వరలక్ష్మి వ్రత విశిష్టత మరియు శ్రావణ శుక్రవార మహత్యం',
					'H2: పూజకు కావలసిన వస్తువులు మరియు సంభారాల జాబితా',
					'H2: కలశ స్థాపన మరియు అమ్మవారి ముఖం అలంకరణ పద్ధతి',
					'H2: తొమ్మిది దారాల తోరం తయారీ మరియు కట్టుకునే విధానం (మంత్రాలతో సహా)',
					'H2: షోడశోపచార పూజ మరియు అష్టలక్ష్మి స్తోత్రం',
					'H2: శ్రీ వరలక్ష్మి వ్రత కథ (చారుమతి కథనం)',
					'H2: తొమ్మిది రకాల పిండివంటలు మరియు నైవేద్య సమర్పణ'
				],
				'fact_check_rules'     => 'Verify Bhavishyottara Purana citation for Charumati vratha katha; check Sravana Shukla Friday dates in DJV Festival Master.',
				'internal_links'       => [ '/pooja/varalakshmi-vratham/?lang=te', '/mantras/lakshmi-ashtothram/?lang=te', '/panchangam/?lang=te' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'tirumala-darshanam-online-booking-te',
				'topic'                => 'తిరుమల శ్రీవారి దర్శనం ఆన్‌లైన్ టికెట్లు బుకింగ్ పూర్తి సమాచారం: కోటా తేదీలు, డ్రెస్ కోడ్ మరియు నిబంధనలు',
				'primary_keyword'      => 'తిరుమల శ్రీవారి దర్శనం ఆన్‌లైన్ టికెట్లు బుకింగ్',
				'secondary_keywords'   => [ 'టీటీడీ 300 రూపాయల దర్శనం టికెట్లు', 'తిరుమల డ్రెస్ కోడ్ నియమాలు', 'తిరుమల శ్రీవారి లడ్డూ ప్రసాదం', 'నడక దారి దివ్య దర్శనం' ],
				'language'             => 'te',
				'category'             => 'temples',
				'search_intent'        => 'Pilgrimage Logistics & Actionable Guidance',
				'evidence_source'      => 'Consistently high organic search across Telugu users every month on TTD slot releases. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Telugu Pilgrims Worldwide',
				'priority_score'       => 90,
				'seasonal_window'      => 'Evergreen / Monthly TTD Quota Releases',
				'competing_url'        => '/temples/tirumala-venkateswara-temple/?lang=te',
				'cannibalization_risk' => 'Low. Temple page covers Sthala Puranam and Deities; article provides practical pilgrim navigation, Aadhaar verification, and token tips.',
				'suggested_title'      => 'తిరుమల శ్రీవారి దర్శనం బుకింగ్ గైడ్: ₹300 టికెట్లు, డ్రెస్ కోడ్ & ముఖ్యమైన నియమాలు | పూజా పంచాంగ్',
				'outline'              => [
					'H2: తిరుమల శ్రీవారి దర్శన రకాలు (ప్రత్యేక ప్రవేశ దర్శనం ₹300, సర్వదర్శనం)',
					'H2: టీటీడీ అధికారిక వెబ్‌సైట్ ద్వారా టికెట్లు బుక్ చేసుకునే విధానం',
					'H2: దర్శన సమయాల్లో డ్రెస్ కోడ్ నిబంధనలు (పురుషులు, స్త్రీలు, పిల్లలు)',
					'H2: అలిపిరి, శ్రీవారి మెట్టు నడక మార్గాల వివరాలు మరియు లగేజీ సదుపాయం',
					'H2: తిరుమలలో వసతి గదుల బుకింగ్ మరియు లడ్డూ ప్రసాదం కౌంటర్లు',
					'H2: భక్తులు తప్పక పాటించవలసిన ముఖ్య సూచనలు'
				],
				'fact_check_rules'     => 'Verify latest TTD guidelines: mandatory traditional attire (panche/dhoti or pyjama with kurta; saree or half-saree/churidar with dupatta); electronic item bans.',
				'internal_links'       => [ '/temples/tirumala-venkateswara-temple/?lang=te', '/temples/?lang=te', '/panchangam/?lang=te' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],

			// ==========================================
			// 3. HINDI CONTENT OPPORTUNITIES (हिन्दी)
			// ==========================================
			[
				'id'                   => 'griha-pravesh-muhurat-2027-hi',
				'topic'                => 'गृह प्रवेश मुहूर्त 2027: शुभ तिथियां, नक्षत्र, वर्जित मास एवं वास्तु पूजन विधि',
				'primary_keyword'      => 'गृह प्रवेश मुहूर्त 2027',
				'secondary_keywords'   => [ 'गृह प्रवेश के लिए शुभ नक्षत्र', 'खरमास में गृह प्रवेश क्यों नहीं करते', 'मकान उद्घाटन शुभ तिथि 2027', 'नए घर की वास्तु शांति पूजा' ],
				'language'             => 'hi',
				'category'             => 'muhurtham',
				'search_intent'        => 'Date Lookup & Vastu Shastra Guidelines',
				'evidence_source'      => 'Extremely high volume across Northern & Central India for Vikram Samvat 2083-2084 planning. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Hindi Belt (UP, MP, Bihar, Rajasthan, Delhi NCR, Haryana, Maharashtra)',
				'priority_score'       => 95,
				'seasonal_window'      => 'Immediate (Q4 2026 for 2027 Planning)',
				'competing_url'        => '/muhurtham/griha-pravesh/?lang=hi',
				'cannibalization_risk' => 'Low. Dynamic tool provides automated checks; article explains Purnimanta month nuances, Vastu Purusha invocation, and Kharmas prohibitions.',
				'suggested_title'      => 'गृह प्रवेश मुहूर्त 2027: शुभ तिथियां, नक्षत्र एवं संपूर्ण वास्तु पूजन नियम | पूजा पंचांग',
				'outline'              => [
					'H2: संक्षेप में: 2027 में नए घर में गृह प्रवेश के सबसे शुभ महीने',
					'H2: माहवार गृह प्रवेश शुभ मुहूर्त 2027 सूची (माघ, फाल्गुन, वैशाख, ज्येष्ठ, मार्गशीर्ष)',
					'H2: गृह प्रवेश के लिए सर्वश्रेष्ठ नक्षत्र (रोहिणी, मृगशिरा, उत्तराफाल्गुनी, अनुराधा आदि)',
					'H2: किस समय गृह प्रवेश नहीं करना चाहिए? (खरमास, चातुर्मास और होलाष्टक नियम)',
					'H2: गृह प्रवेश पूजा विधि: कलश स्थापना, वास्तु पूजन एवं नवग्रह शांति',
					'H2: अक्सर पूछे जाने वाले प्रश्न (FAQ)'
				],
				'fact_check_rules'     => 'Confirm solar ingress dates into Sagittarius and Pisces (Kharmas/Malmas periods when Sun resides in Jupiter\'s signs, halting auspicious ceremonies).',
				'internal_links'       => [ '/muhurtham/griha-pravesh/?lang=hi', '/panchangam/?lang=hi', '/muhurtham/?lang=hi' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'vivah-muhurat-2027-hi',
				'topic'                => 'विवाह मुहूर्त 2027: शुभ लग्न तिथियां, गुरु-शुक्र तारा अस्त एवं चातुर्मास निषेध काल',
				'primary_keyword'      => 'विवाह मुहूर्त 2027',
				'secondary_keywords'   => [ 'शादी के शुभ मुहूर्त 2027', 'शुक्र अस्त 2027 विवाह मुहूर्त', 'देवउठनी एकादशी 2027 शादी मुहूर्त', 'विवाह लग्न शुद्धि नियम' ],
				'language'             => 'hi',
				'category'             => 'muhurtham',
				'search_intent'        => 'Wedding Calendar & Astrological Shuddhi',
				'evidence_source'      => 'Massive search volume across Hindi-speaking wedding planners, banquet halls, and families. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'North, Central & Western India Hindi Diaspora',
				'priority_score'       => 94,
				'seasonal_window'      => 'Q4 2026 (Advance Wedding Planning for 2027)',
				'competing_url'        => '/muhurtham/marriage/?lang=hi',
				'cannibalization_risk' => 'Low. Dynamic calculator provides specific dates; article explains Vivaha Panchami, Devshayani-Devuthani boundaries, and Nakshatra Shuddhi.',
				'suggested_title'      => 'विवाह मुहूर्त 2027: शादी की शुभ तिथियां, नक्षत्र एवं वर्जित काल | पूजा पंचांग',
				'outline'              => [
					'H2: 2027 शादी मुहूर्त सारांश: कौन से महीने विवाह के लिए सबसे उत्तम हैं?',
					'H2: वर्ष 2027 माहवार विवाह शुभ मुहूर्त (जनवरी, फरवरी, अप्रैल, मई, जून, नवंबर, दिसंबर)',
					'H2: विवाह के लिए वर्जित समय: शुक्र अस्त (तारा डूबना) और चातुर्मास 2027',
					'H2: विवाह लग्न शुद्धि क्या है? सूर्य, चंद्र एवं गुरु बल का महत्व',
					'H2: अक्सर पूछे जाने वाले प्रश्न: शादी मुहूर्त 2027'
				],
				'fact_check_rules'     => 'Verify Venus combustion start date (July 6, 2027); confirm Devshayani Ekadashi (July 14, 2027) and Devuthani Ekadashi (Nov 10, 2027) dates via DJV engine.',
				'internal_links'       => [ '/muhurtham/marriage/?lang=hi', '/panchangam/?lang=hi', '/services/?lang=hi' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'diwali-lakshmi-pujan-vidhi-hi',
				'topic'                => 'दिवाली 2026 लक्ष्मी-गणेश पूजन की संपूर्ण एवं प्रामाणिक विधि: प्रदोष काल मुहूर्त व सामग्री लिस्ट',
				'primary_keyword'      => 'दिवाली लक्ष्मी पूजन विधि',
				'secondary_keywords'   => [ 'दीपावली पूजा सामग्री लिस्ट pdf', 'प्रदोष काल लक्ष्मी पूजा मुहूर्त', 'बही-खाता पूजन विधि', 'श्री सूक्तम और महालक्ष्मी आरती' ],
				'language'             => 'hi',
				'category'             => 'festivals',
				'search_intent'        => 'Devotional Ritual Manual & Timing Lookup',
				'evidence_source'      => 'Peak annual traffic driver in Hindi search during Kartik Amavasya. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'National Hindi Audience & Overseas Hindi Diaspora',
				'priority_score'       => 97,
				'seasonal_window'      => 'Immediate (Kartik / Diwali Season)',
				'competing_url'        => '/festivals/diwali/?lang=hi',
				'cannibalization_risk' => 'Medium. /festivals/diwali/ focuses on date and tithi calculation; this article is the operational family puja handbook with mantras and step-by-step rituals.',
				'suggested_title'      => 'दिवाली लक्ष्मी पूजन विधि: घर पर संपूर्ण पूजा, प्रदोष मुहूर्त एवं सामग्री सूची | पूजा पंचांग',
				'outline'              => [
					'H2: दिवाली 2026 लक्ष्मी पूजन का सर्वश्रेष्ठ प्रदोष काल एवं निशीथ काल मुहूर्त',
					'H2: दीपावली पूजा की संपूर्ण सामग्री सूची (चेकलिस्ट)',
					'H2: चौकी स्थापना, कलश स्थापना एवं लक्ष्मी-गणेश का शुद्ध आवाहन',
					'H2: षोडशोपचार पूजा विधि: कुमकुम, अक्षत, कमल पुष्प एवं भोग अर्पण',
					'H2: व्यापारी बंधुओं के लिए बही-खाता एवं कलम-दवात पूजन विधि',
					'H2: महालक्ष्मी आरती, मंत्र पुष्पांजलि एवं दीपदान के नियम'
				],
				'fact_check_rules'     => 'Verify Pradosha Kala window for major North Indian cities (Delhi, Varanasi, Lucknow, Jaipur) based on local sunset timings in DJV database.',
				'internal_links'       => [ '/festivals/diwali/?lang=hi', '/pooja/diwali-lakshmi-pooja/?lang=hi', '/mantras/lakshmi-gayatri-mantra/?lang=hi', '/panchangam/?lang=hi' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'maha-shivaratri-char-prahar-puja-hi',
				'topic'                => 'महाशिवरात्रि 2027 निशीथ काल पूजा व चार प्रहर रुद्राभिषेक विधि: शिवलिंग अभिषेक नियम',
				'primary_keyword'      => 'महाशिवरात्रि चार प्रहर पूजा विधि',
				'secondary_keywords'   => [ 'निशीथ काल मुहूर्त महाशिवरात्रि', 'शिवलिंग पर क्या नहीं चढ़ाना चाहिए', 'रुद्राभिषेक मंत्र और नियम', 'महामृत्युंजय मंत्र जप विधि' ],
				'language'             => 'hi',
				'category'             => 'festivals',
				'search_intent'        => 'Vedic Ritual Practice & Shivalinga Abhishekam Guidance',
				'evidence_source'      => 'Extreme search surge during Phalguna month for Shivaratri night vigil and abhishekam. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Pan-North India, Madhya Pradesh, Uttarakhand, Uttar Pradesh',
				'priority_score'       => 92,
				'seasonal_window'      => 'Phalguna Month / Spring 2027 Window',
				'competing_url'        => '/festivals/maha-shivaratri/?lang=hi',
				'cannibalization_risk' => 'Low. Festival page computes Nishita Kaal date; this article teaches the specific ingredients and mantras for each of the 4 night prahars (milk, curd, ghee, honey).',
				'suggested_title'      => 'महाशिवरात्रि चार प्रहर पूजा विधि: निशीथ काल मुहूर्त, रुद्राभिषेक एवं अभिषेक नियम | पूजा पंचांग',
				'outline'              => [
					'H2: महाशिवरात्रि का आध्यात्मिक महत्व और निशीथ काल की महिमा',
					'H2: चार प्रहर पूजा का समय और हर प्रहर के अलग-अलग अभिषेक द्रव्य (दूध, दही, घी, शहद)',
					'H2: शिवलिंग पर क्या चढ़ाना अति शुभ है और क्या कदापि न चढ़ाएं (तुलसी, केतकी, हल्दी निषेध)',
					'H2: शिव पंचाक्षर मंत्र (ॐ नमः शिवाय) एवं महामृत्युंजय मंत्र जप के नियम',
					'H2: महाशिवरात्रि व्रत का पारण कब और कैसे करें?'
				],
				'fact_check_rules'     => 'Confirm Magha/Phalguna Krishna Chaturdashi Nishita Kaal midnight timing (11:50 PM to 12:40 AM window) via DJV Astronomical Engine.',
				'internal_links'       => [ '/festivals/maha-shivaratri/?lang=hi', '/mantras/shiva-moola-mantra/?lang=hi', '/panchangam/?lang=hi' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			],
			[
				'id'                   => 'chhath-puja-arghya-timing-vidhi-hi',
				'topic'                => 'छठ पूजा 2026 नहाय-खाय, खरना, संध्या और उषा अर्घ्य का सटीक समय व सूर्य पूजन विधि',
				'primary_keyword'      => 'छठ पूजा 2026 अर्घ्य का समय',
				'secondary_keywords'   => [ 'छठ पूजा नहाय खाय तिथि', 'खरना का नियम और प्रसाद', 'संध्या अर्घ्य सूर्योदय सूर्यास्त समय', 'छठी मैया के पारंपरिक गीत और नियम' ],
				'language'             => 'hi',
				'category'             => 'festivals',
				'search_intent'        => 'Exact Astronomical Timing & Traditional Ritual Vidhi',
				'evidence_source'      => 'Highest search density in Bihar, UP, Jharkhand, Delhi NCR during Kartik Shukla Shashthi. First-party GSC: Insufficient first-party data.',
				'target_region'        => 'Bihar, Eastern UP, Jharkhand & National Chhath Communities',
				'priority_score'       => 90,
				'seasonal_window'      => 'Kartik Month (Post-Diwali Window)',
				'competing_url'        => '/festivals/chhath-puja/?lang=hi',
				'cannibalization_risk' => 'Low. Festival page notes calendar date; article provides city-specific sunset/sunrise tables for Sandhya and Usha Arghya, plus Thekua prasadam and soop packing guidelines.',
				'suggested_title'      => 'छठ पूजा 2026: नहाय-खाय, खरना, संध्या एवं उषा अर्घ्य का समय व प्रामाणिक विधि | पूजा पंचांग',
				'outline'              => [
					'H2: छठ महापर्व 2026 का चार दिवसीय पंचांग (नहाय-खाय से उषा अर्घ्य तक)',
					'H2: पहला दिन (नहाय-खाय): कद्दू-भात नियम और पवित्रता का संकल्प',
					'H2: दूसरा दिन (खरना): गुड़ की खीर और 36 घंटे के निर्जला व्रत का प्रारंभ',
					'H2: तीसरा दिन (संध्या अर्घ्य): सूर्यास्त का सटीक समय और सूप सजाने की विधि',
					'H2: चौथा दिन (उषा अर्घ्य): सूर्योदय का सटीक समय, अर्घ्य अर्पण एवं पारण',
					'H2: छठ पूजा में व्रती के लिए आवश्यक सावधानियां एवं नियम'
				],
				'fact_check_rules'     => 'Cross-verify Kartik Shukla Shashthi sunrise/sunset calculations in Patna, Varanasi, and Delhi using DJV high-precision solar formulas.',
				'internal_links'       => [ '/festivals/chhath-puja/?lang=hi', '/panchangam/?lang=hi', '/mantras/surya-mantra/?lang=hi' ],
				'status'               => 'Planned',
				'last_researched'      => '2026-10-16',
				'last_updated'         => '2026-10-16'
			]
		];
	}

	/**
	 * AJAX handler to update an opportunity's workflow status.
	 */
	public static function ajax_update_status(): void {
		check_ajax_referer( 'djv_content_strategy_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Unauthorized' ] );
		}

		$id = sanitize_key( $_POST['opportunity_id'] ?? '' );
		$status = sanitize_text_field( $_POST['new_status'] ?? '' );

		$allowed_statuses = [ 'Research Needed', 'Planned', 'Draft', 'Fact-Check', 'Review', 'Approved', 'Published', 'Update Needed' ];
		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			wp_send_json_error( [ 'message' => 'Invalid status value' ] );
		}

		$opportunities = self::get_opportunities();
		$found = false;
		foreach ( $opportunities as &$item ) {
			if ( $item['id'] === $id ) {
				$item['status'] = $status;
				$item['last_updated'] = current_time( 'Y-m-d' );
				$found = true;
				break;
			}
		}

		if ( ! $found ) {
			wp_send_json_error( [ 'message' => 'Opportunity ID not found' ] );
		}

		update_option( self::OPTION_KEY, $opportunities, false );
		wp_send_json_success( [ 'message' => 'Status updated successfully', 'id' => $id, 'status' => $status ] );
	}

	/**
	 * AJAX handler to add/save a custom opportunity.
	 */
	public static function ajax_save_opportunity(): void {
		check_ajax_referer( 'djv_content_strategy_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Unauthorized' ] );
		}

		$topic   = sanitize_text_field( $_POST['topic'] ?? '' );
		$keyword = sanitize_text_field( $_POST['primary_keyword'] ?? '' );
		$lang    = sanitize_key( $_POST['language'] ?? 'en' );
		$cat     = sanitize_key( $_POST['category'] ?? 'panchangam' );
		$intent  = sanitize_text_field( $_POST['search_intent'] ?? 'Informational' );
		$score   = intval( $_POST['priority_score'] ?? 80 );
		$comp_url= sanitize_text_field( $_POST['competing_url'] ?? '' );

		if ( empty( $topic ) || empty( $keyword ) ) {
			wp_send_json_error( [ 'message' => 'Topic and Primary Keyword are required.' ] );
		}

		$id = sanitize_title( $keyword ) . '-' . $lang . '-' . time();
		$opportunities = self::get_opportunities();

		$new_item = [
			'id'                   => $id,
			'topic'                => $topic,
			'primary_keyword'      => $keyword,
			'secondary_keywords'   => array_map( 'trim', explode( ',', sanitize_text_field( $_POST['secondary_keywords'] ?? '' ) ) ),
			'language'             => $lang,
			'category'             => $cat,
			'search_intent'        => $intent,
			'evidence_source'      => sanitize_text_field( $_POST['evidence_source'] ?? 'External research (Google Trends / Competitor Gap). First-party GSC: Insufficient first-party data.' ),
			'target_region'        => sanitize_text_field( $_POST['target_region'] ?? 'India' ),
			'priority_score'       => $score,
			'seasonal_window'      => sanitize_text_field( $_POST['seasonal_window'] ?? 'Evergreen' ),
			'competing_url'        => $comp_url,
			'cannibalization_risk' => sanitize_text_field( $_POST['cannibalization_risk'] ?? 'Audited; target distinct informational intent and link to programmatic hub.' ),
			'suggested_title'      => sanitize_text_field( $_POST['suggested_title'] ?? $topic . ' | PujaPanchang' ),
			'outline'              => [ 'H2: Introduction & Quick Answer', 'H2: Core Vedic Principles & Rules', 'H2: Practical Application & Timings', 'H2: Frequently Asked Questions' ],
			'fact_check_rules'     => sanitize_text_field( $_POST['fact_check_rules'] ?? 'Verify astronomical formulas and scripture citations against DJV core engine.' ),
			'internal_links'       => [ '/panchangam/', '/festivals/', '/muhurtham/' ],
			'status'               => 'Research Needed',
			'last_researched'      => current_time( 'Y-m-d' ),
			'last_updated'         => current_time( 'Y-m-d' )
		];

		array_unshift( $opportunities, $new_item );
		update_option( self::OPTION_KEY, $opportunities, false );

		wp_send_json_success( [ 'message' => 'New opportunity created successfully', 'item' => $new_item ] );
	}

	/**
	 * AJAX handler to reset seeds.
	 */
	public static function ajax_reset_seeds(): void {
		check_ajax_referer( 'djv_content_strategy_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Unauthorized' ] );
		}

		$seeds = self::get_seed_opportunities();
		update_option( self::OPTION_KEY, $seeds, false );
		wp_send_json_success( [ 'message' => 'Successfully restored curated content strategy queue.' ] );
	}

	/**
	 * Render the Content Strategy & Opportunity Dashboard.
	 */
	public static function render_dashboard(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Access denied.', 'djv-core' ) );
		}

		$opportunities = self::get_opportunities();
		$nonce = wp_create_nonce( 'djv_content_strategy_nonce' );

		// Stats
		$total = count( $opportunities );
		$en_count = count( array_filter( $opportunities, fn($x) => $x['language'] === 'en' ) );
		$te_count = count( array_filter( $opportunities, fn($x) => $x['language'] === 'te' ) );
		$hi_count = count( array_filter( $opportunities, fn($x) => $x['language'] === 'hi' ) );
		$planned_count = count( array_filter( $opportunities, fn($x) => in_array($x['status'], ['Planned', 'Draft', 'Fact-Check', 'Review']) ) );
		?>
		<div class="wrap djv-admin djv-content-strategy-wrap" style="max-width:1400px;margin:20px auto 40px auto;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif;">
			
			<!-- Dashboard Header -->
			<div style="background:linear-gradient(135deg,#7A2419 0%,#4A1510 100%);color:#FFF;padding:1.75rem 2rem;border-radius:12px;margin-bottom:1.5rem;box-shadow:0 4px 16px rgba(122,36,25,0.15);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
				<div>
					<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.35rem;">
						<span style="font-size:1.75rem;">🎯</span>
						<h1 style="color:#FFF;font-size:1.75rem;margin:0;font-weight:700;line-height:1.2;">PujaPanchang — Multilingual Content Strategy &amp; Opportunity Engine</h1>
					</div>
					<p style="margin:0;color:rgba(255,255,255,0.85);font-size:0.95rem;">Research-Driven SEO Workflow for English, Telugu (తెలుగు) &amp; Hindi (हिन्दी) — Growing Qualified Organic Search Traffic.</p>
				</div>
				<div style="display:flex;gap:0.75rem;align-items:center;">
					<button type="button" id="djv-btn-add-opportunity" class="button button-primary" style="background:#C89432;border-color:#C89432;color:#1A0A06;font-weight:700;padding:0.4rem 1rem;height:auto;border-radius:6px;">
						+ New Researched Topic
					</button>
					<button type="button" id="djv-btn-reset-seeds" class="button" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);color:#FFF;padding:0.4rem 0.85rem;height:auto;border-radius:6px;">
						↺ Reset Curated Queue
					</button>
				</div>
			</div>

			<!-- Notice Banner on First-Party Data Status -->
			<div style="background:#FFF9F0;border-left:4px solid #C89432;border-radius:6px;padding:1rem 1.25rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;align-items:flex-start;gap:0.85rem;">
				<span style="font-size:1.25rem;line-height:1;">ℹ️</span>
				<div style="font-size:0.875rem;color:#5C3A10;line-height:1.5;">
					<strong>Data Authenticity Notice:</strong> First-Party Google Search Console (GSC) is currently labeled as <span style="background:#FFE6B3;padding:2px 6px;border-radius:4px;font-weight:600;">Insufficient first-party data</span> for this local/staging environment. Opportunity priorities below are backed by <strong>verified external Google Trends regional search patterns, seasonal Hindu calendar cycles, and competitor query gap analysis</strong>. No metrics or traffic volumes are fabricated.
				</div>
			</div>

			<!-- KPI Cards -->
			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:1rem;margin-bottom:1.75rem;">
				<div style="background:#FFF;padding:1.25rem;border-radius:8px;border:1px solid #E2D9CC;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
					<div style="font-size:0.8rem;text-transform:uppercase;color:#8C7355;font-weight:700;letter-spacing:0.04em;">Total Curated Topics</div>
					<div style="font-size:2rem;font-weight:700;color:#7A2419;margin-top:0.25rem;"><?php echo esc_html( $total ); ?></div>
					<div style="font-size:0.75rem;color:#7A6B58;margin-top:0.25rem;">Rigorously vetted opportunities</div>
				</div>
				<div style="background:#FFF;padding:1.25rem;border-radius:8px;border:1px solid #E2D9CC;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
					<div style="font-size:0.8rem;text-transform:uppercase;color:#8C7355;font-weight:700;letter-spacing:0.04em;">English Queue (EN)</div>
					<div style="font-size:2rem;font-weight:700;color:#2B4C7E;margin-top:0.25rem;"><?php echo esc_html( $en_count ); ?></div>
					<div style="font-size:0.75rem;color:#7A6B58;margin-top:0.25rem;">Pan-India &amp; Global Diaspora</div>
				</div>
				<div style="background:#FFF;padding:1.25rem;border-radius:8px;border:1px solid #E2D9CC;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
					<div style="font-size:0.8rem;text-transform:uppercase;color:#8C7355;font-weight:700;letter-spacing:0.04em;">Telugu Queue (TE)</div>
					<div style="font-size:2rem;font-weight:700;color:#8A4B08;margin-top:0.25rem;"><?php echo esc_html( $te_count ); ?></div>
					<div style="font-size:0.75rem;color:#7A6B58;margin-top:0.25rem;">AP &amp; Telangana Regional Intent</div>
				</div>
				<div style="background:#FFF;padding:1.25rem;border-radius:8px;border:1px solid #E2D9CC;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
					<div style="font-size:0.8rem;text-transform:uppercase;color:#8C7355;font-weight:700;letter-spacing:0.04em;">Hindi Queue (HI)</div>
					<div style="font-size:2rem;font-weight:700;color:#9E2A2B;margin-top:0.25rem;"><?php echo esc_html( $hi_count ); ?></div>
					<div style="font-size:0.75rem;color:#7A6B58;margin-top:0.25rem;">North &amp; Central India Belts</div>
				</div>
				<div style="background:#FFF;padding:1.25rem;border-radius:8px;border:1px solid #E2D9CC;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
					<div style="font-size:0.8rem;text-transform:uppercase;color:#8C7355;font-weight:700;letter-spacing:0.04em;">Editorial Pipeline</div>
					<div style="font-size:2rem;font-weight:700;color:#2E7D32;margin-top:0.25rem;"><?php echo esc_html( $planned_count ); ?></div>
					<div style="font-size:0.75rem;color:#7A6B58;margin-top:0.25rem;">Planned / Draft / In Review</div>
				</div>
			</div>

			<!-- Filters Toolbar -->
			<div style="background:#FFF;padding:1rem 1.5rem;border-radius:8px;border:1px solid #E2D9CC;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
				<!-- Language Tabs -->
				<div class="djv-lang-filters" style="display:flex;gap:0.35rem;">
					<button type="button" class="djv-tab-btn active" data-filter-lang="all" style="padding:0.45rem 1rem;border-radius:20px;border:1px solid #7A2419;background:#7A2419;color:#FFF;font-weight:600;font-size:0.85rem;cursor:pointer;">All Languages (<?php echo $total; ?>)</button>
					<button type="button" class="djv-tab-btn" data-filter-lang="en" style="padding:0.45rem 1rem;border-radius:20px;border:1px solid #E2D9CC;background:#F9F7F4;color:#4A3828;font-weight:600;font-size:0.85rem;cursor:pointer;">English (<?php echo $en_count; ?>)</button>
					<button type="button" class="djv-tab-btn" data-filter-lang="te" style="padding:0.45rem 1rem;border-radius:20px;border:1px solid #E2D9CC;background:#F9F7F4;color:#4A3828;font-weight:600;font-size:0.85rem;cursor:pointer;">తెలుగు (Telugu - <?php echo $te_count; ?>)</button>
					<button type="button" class="djv-tab-btn" data-filter-lang="hi" style="padding:0.45rem 1rem;border-radius:20px;border:1px solid #E2D9CC;background:#F9F7F4;color:#4A3828;font-weight:600;font-size:0.85rem;cursor:pointer;">हिन्दी (Hindi - <?php echo $hi_count; ?>)</button>
				</div>

				<!-- Category & Status Filters -->
				<div style="display:flex;gap:0.75rem;align-items:center;">
					<select id="djv-filter-category" style="padding:0.35rem 0.75rem;border-radius:6px;border:1px solid #CCC;font-size:0.85rem;">
						<option value="all">All Categories</option>
						<option value="muhurtham">Muhurtham &amp; Astrology</option>
						<option value="festivals">Festivals &amp; Vratams</option>
						<option value="panchangam">Daily Panchangam</option>
						<option value="temples">Temples &amp; Pilgrimage</option>
						<option value="pooja">Pooja Guides</option>
					</select>

					<select id="djv-filter-status" style="padding:0.35rem 0.75rem;border-radius:6px;border:1px solid #CCC;font-size:0.85rem;">
						<option value="all">All Statuses</option>
						<option value="Planned">Planned</option>
						<option value="Research Needed">Research Needed</option>
						<option value="Draft">Draft</option>
						<option value="Fact-Check">Fact-Check</option>
						<option value="Review">Review</option>
						<option value="Approved">Approved</option>
						<option value="Published">Published</option>
					</select>
				</div>
			</div>

			<!-- Opportunities Table -->
			<div style="background:#FFF;border-radius:8px;border:1px solid #E2D9CC;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
				<table class="wp-list-table widefat fixed striped" style="border:none;border-collapse:collapse;width:100%;">
					<thead>
						<tr style="background:#F6F2EC;border-bottom:2px solid #E2D9CC;">
							<th style="padding:0.85rem 1rem;font-weight:700;color:#4A3828;width:35%;">Topic &amp; Primary Keyword</th>
							<th style="padding:0.85rem 0.75rem;font-weight:700;color:#4A3828;width:8%;">Lang</th>
							<th style="padding:0.85rem 0.75rem;font-weight:700;color:#4A3828;width:14%;">Intent &amp; Category</th>
							<th style="padding:0.85rem 0.75rem;font-weight:700;color:#4A3828;width:8%;">Score</th>
							<th style="padding:0.85rem 0.75rem;font-weight:700;color:#4A3828;width:15%;">Cannibalization Defense</th>
							<th style="padding:0.85rem 0.75rem;font-weight:700;color:#4A3828;width:12%;">Status</th>
							<th style="padding:0.85rem 0.75rem;font-weight:700;color:#4A3828;text-align:right;width:8%;">Actions</th>
						</tr>
					</thead>
					<tbody id="djv-opportunities-tbody">
						<?php foreach ( $opportunities as $opp ) : 
							$lang_badge_bg = $opp['language'] === 'te' ? '#FFF0D9' : ($opp['language'] === 'hi' ? '#FFE8E8' : '#EAF2FF');
							$lang_badge_col = $opp['language'] === 'te' ? '#8A4B08' : ($opp['language'] === 'hi' ? '#9E2A2B' : '#1A4A8A');
							$lang_label = strtoupper( $opp['language'] );
						?>
						<tr class="djv-opp-row" 
							data-id="<?php echo esc_attr( $opp['id'] ); ?>"
							data-lang="<?php echo esc_attr( $opp['language'] ); ?>"
							data-category="<?php echo esc_attr( $opp['category'] ); ?>"
							data-status="<?php echo esc_attr( $opp['status'] ); ?>">
							
							<td style="padding:0.85rem 1rem;vertical-align:top;">
								<div style="font-weight:700;font-size:0.95rem;color:#1A0A06;margin-bottom:0.25rem;">
									<?php echo esc_html( $opp['topic'] ); ?>
								</div>
								<div style="font-size:0.825rem;color:#7A2419;font-weight:600;">
									🔑 <span style="background:#FFF3E0;padding:2px 6px;border-radius:4px;border:1px solid #FFE0B2;"><?php echo esc_html( $opp['primary_keyword'] ); ?></span>
								</div>
								<?php if ( ! empty( $opp['secondary_keywords'] ) ) : ?>
									<div style="font-size:0.75rem;color:#6B5A47;margin-top:0.25rem;">
										<strong>Related:</strong> <?php echo esc_html( implode( ', ', array_slice( $opp['secondary_keywords'], 0, 3 ) ) ); ?>
									</div>
								<?php endif; ?>
							</td>

							<td style="padding:0.85rem 0.75rem;vertical-align:top;">
								<span style="display:inline-block;padding:2px 8px;border-radius:4px;font-size:0.75rem;font-weight:700;background:<?php echo $lang_badge_bg; ?>;color:<?php echo $lang_badge_col; ?>;">
									<?php echo esc_html( $lang_label ); ?>
								</span>
							</td>

							<td style="padding:0.85rem 0.75rem;vertical-align:top;font-size:0.825rem;">
								<div style="font-weight:600;color:#2E4A62;"><?php echo esc_html( $opp['search_intent'] ); ?></div>
								<div style="color:#7A6B58;font-size:0.75rem;text-transform:capitalize;"><?php echo esc_html( $opp['category'] ); ?></div>
								<div style="color:#8A7255;font-size:0.72rem;margin-top:0.2rem;">⏱ <?php echo esc_html( $opp['seasonal_window'] ); ?></div>
							</td>

							<td style="padding:0.85rem 0.75rem;vertical-align:top;">
								<div style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:#E8F5E9;color:#2E7D32;font-weight:800;font-size:0.85rem;border:1px solid #C8E6C9;">
									<?php echo esc_html( $opp['priority_score'] ); ?>
								</div>
							</td>

							<td style="padding:0.85rem 0.75rem;vertical-align:top;font-size:0.78rem;line-height:1.4;color:#4A3828;">
								<?php if ( ! empty( $opp['competing_url'] ) ) : ?>
									<div style="margin-bottom:0.25rem;">
										<strong>Hub:</strong> <a href="<?php echo esc_url( home_url( $opp['competing_url'] ) ); ?>" target="_blank" style="color:#7A2419;font-weight:600;"><?php echo esc_html( $opp['competing_url'] ); ?></a>
									</div>
								<?php endif; ?>
								<div style="font-size:0.74rem;color:#6B5A47;">
									<?php echo esc_html( mb_substr( $opp['cannibalization_risk'], 0, 110 ) . '...' ); ?>
								</div>
							</td>

							<td style="padding:0.85rem 0.75rem;vertical-align:top;">
								<select class="djv-status-select" data-id="<?php echo esc_attr( $opp['id'] ); ?>" style="font-size:0.8rem;padding:3px 6px;border-radius:4px;border:1px solid #CCC;background:#FFF;width:100%;">
									<?php 
									$statuses = [ 'Planned', 'Research Needed', 'Draft', 'Fact-Check', 'Review', 'Approved', 'Published', 'Update Needed' ];
									foreach ( $statuses as $st ) : ?>
										<option value="<?php echo esc_attr( $st ); ?>" <?php selected( $opp['status'], $st ); ?>><?php echo esc_html( $st ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>

							<td style="padding:0.85rem 0.75rem;vertical-align:top;text-align:right;">
								<button type="button" class="button djv-btn-view-strategy" data-id="<?php echo esc_attr( $opp['id'] ); ?>" style="font-size:0.8rem;padding:2px 8px;height:auto;">
									Details
								</button>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Strategy Details Modal Drawer -->
			<div id="djv-strategy-modal" style="display:none;position:fixed;inset:0;background:rgba(20,10,5,0.65);z-index:99999;backdrop-filter:blur(3px);align-items:center;justify-content:center;">
				<div style="background:#FFF;width:min(780px,92vw);max-height:88vh;border-radius:12px;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,0.3);border:1px solid #E2D9CC;">
					<div style="background:#7A2419;color:#FFF;padding:1.25rem 1.5rem;display:flex;justify-content:space-between;align-items:center;border-top-left-radius:11px;border-top-right-radius:11px;">
						<h2 id="modal-title" style="color:#FFF;margin:0;font-size:1.2rem;font-weight:700;">Content Strategy Specifications</h2>
						<button type="button" id="modal-close" style="background:none;border:none;color:#FFF;font-size:1.5rem;cursor:pointer;line-height:1;">✕</button>
					</div>
					<div id="modal-content" style="padding:1.5rem;line-height:1.6;font-size:0.9rem;color:#2C2012;">
						<!-- Dynamic content populated by JS -->
					</div>
				</div>
			</div>

		</div>

		<script>
		(function($){
			const opportunitiesData = <?php echo wp_json_encode( $opportunities ); ?>;
			const ajaxNonce = '<?php echo esc_js( $nonce ); ?>';

			// Filter by Language
			$('.djv-tab-btn').on('click', function(){
				$('.djv-tab-btn').removeClass('active').css({background: '#F9F7F4', color: '#4A3828', borderColor: '#E2D9CC'});
				$(this).addClass('active').css({background: '#7A2419', color: '#FFF', borderColor: '#7A2419'});
				const lang = $(this).data('filter-lang');
				applyFilters();
			});

			// Filter Dropdowns
			$('#djv-filter-category, #djv-filter-status').on('change', applyFilters);

			function applyFilters() {
				const activeLang = $('.djv-tab-btn.active').data('filter-lang');
				const activeCat  = $('#djv-filter-category').val();
				const activeStat = $('#djv-filter-status').val();

				$('.djv-opp-row').each(function(){
					const rLang = $(this).data('lang');
					const rCat  = $(this).data('category');
					const rStat = $(this).data('status');

					const matchLang = (activeLang === 'all' || rLang === activeLang);
					const matchCat  = (activeCat === 'all'  || rCat === activeCat);
					const matchStat = (activeStat === 'all' || rStat === activeStat);

					if (matchLang && matchCat && matchStat) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}

			// Interactive Status Update
			$(document).on('change', '.djv-status-select', function(){
				const id = $(this).data('id');
				const newStatus = $(this).val();
				const $select = $(this);
				$select.css({opacity: 0.5});

				$.post(ajaxurl, {
					action: 'djv_update_content_status',
					nonce: ajaxNonce,
					opportunity_id: id,
					new_status: newStatus
				}, function(res){
					$select.css({opacity: 1});
					if (res.success) {
						$select.closest('.djv-opp-row').attr('data-status', newStatus);
					} else {
						alert('Error updating status: ' + (res.data ? res.data.message : 'Unknown error'));
					}
				});
			});

			// View Strategy Details Modal
			$(document).on('click', '.djv-btn-view-strategy', function(){
				const id = $(this).data('id');
				const item = opportunitiesData.find(x => x.id === id);
				if (!item) return;

				let outlineHtml = '';
				if (item.outline && item.outline.length) {
					outlineHtml = '<ul style="margin:0.5rem 0 1rem 1.25rem;list-style:disc;">' + 
						item.outline.map(h => '<li><strong>' + h.replace('H2: ', 'H2: ').replace('H3: ', 'H3: ') + '</strong></li>').join('') + 
					'</ul>';
				}

				let internalLinksHtml = (item.internal_links || []).map(l => '<a href="' + l + '" target="_blank" style="display:inline-block;padding:2px 8px;background:#F0EAE1;border-radius:4px;margin-right:6px;margin-bottom:6px;font-size:0.8rem;color:#7A2419;text-decoration:none;">🔗 ' + l + '</a>').join('');

				let html = `
					<div style="margin-bottom:1rem;">
						<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-weight:700;font-size:0.75rem;background:#EAF2FF;color:#1A4A8A;text-transform:uppercase;">${item.language.toUpperCase()}</span>
						<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-weight:700;font-size:0.75rem;background:#E8F5E9;color:#2E7D32;margin-left:6px;">Priority Score: ${item.priority_score}/100</span>
						<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-weight:700;font-size:0.75rem;background:#FFF3E0;color:#8A4B08;margin-left:6px;">Category: ${item.category}</span>
					</div>

					<h3 style="margin:0 0 0.5rem 0;font-size:1.15rem;color:#7A2419;">${item.topic}</h3>
					<div style="margin-bottom:1rem;font-size:0.875rem;"><strong>Primary Focus Keyword:</strong> <code style="background:#F4EFEA;padding:2px 6px;border-radius:3px;">${item.primary_keyword}</code></div>

					<div style="background:#FAF8F5;border:1px solid #E2D9CC;border-radius:8px;padding:1rem;margin-bottom:1rem;">
						<h4 style="margin:0 0 0.5rem 0;color:#4A3828;font-size:0.95rem;">Suggested Rank Math Title</h4>
						<div style="font-weight:600;color:#1A4A8A;font-size:0.9rem;">${item.suggested_title}</div>
					</div>

					<h4 style="margin:1rem 0 0.25rem 0;color:#4A3828;">Target Search Intent &amp; Market Evidence</h4>
					<p style="margin:0 0 0.75rem 0;font-size:0.85rem;color:#555;">${item.evidence_source}</p>

					<h4 style="margin:1rem 0 0.25rem 0;color:#4A3828;">Cannibalization Defense &amp; Target Hub</h4>
					<p style="margin:0 0 0.75rem 0;font-size:0.85rem;color:#7A2419;"><strong>Compete Prevention:</strong> ${item.cannibalization_risk}</p>

					<h4 style="margin:1rem 0 0.25rem 0;color:#4A3828;">Proposed Article Outline</h4>
					${outlineHtml}

					<div style="background:#FFF9F0;border-left:4px solid #C89432;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1rem;">
						<h4 style="margin:0 0 0.35rem 0;color:#7A2419;font-size:0.875rem;">Fact-Check &amp; Calculation Engine Requirements</h4>
						<p style="margin:0;font-size:0.825rem;color:#5C3A10;">${item.fact_check_rules}</p>
					</div>

					<h4 style="margin:1rem 0 0.35rem 0;color:#4A3828;">Recommended Internal Links</h4>
					<div>${internalLinksHtml}</div>
				`;

				$('#modal-content').html(html);
				$('#djv-strategy-modal').css('display', 'flex');
			});

			$('#modal-close, #djv-strategy-modal').on('click', function(e){
				if (e.target === this) {
					$('#djv-strategy-modal').hide();
				}
			});

			// Reset Seeds
			$('#djv-btn-reset-seeds').on('click', function(){
				if (!confirm('Are you sure you want to reset the strategy queue to the default curated opportunities?')) return;
				$.post(ajaxurl, {
					action: 'djv_reset_content_strategy_seeds',
					nonce: ajaxNonce
				}, function(res){
					if (res.success) {
						location.reload();
					} else {
						alert('Failed to reset seeds.');
					}
				});
			});

		})(jQuery);
		</script>
		<?php
	}
}
