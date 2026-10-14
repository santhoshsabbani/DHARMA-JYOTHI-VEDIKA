<?php
/**
 * Template Part: Muhurtham Home (Shubh Muhurtham Finder)
 *
 * Implements the complete DJV Event-Based Muhurtham Home structure:
 * Hero -> What is Muhurtham? -> Choose Your Event (21 Cards) ->
 * How DJV Finds Muhurtham -> Important Notes -> FAQ.
 *
 * @package DJV_Theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once DJV_PLUGIN_DIR . 'includes/data-muhurtham-events.php';

$all_events  = DJV_Muhurtham_Events::get_all();
$categories  = DJV_Muhurtham_Events::get_categories();
$total_count = count( $all_events );

// Compute category counts
$cat_counts = [ 'all' => $total_count ];
foreach ( $all_events as $ev ) {
	$c = $ev['category'] ?? 'other';
	$cat_counts[ $c ] = ( $cat_counts[ $c ] ?? 0 ) + 1;
}

// Language resolution
$current_lang = 'en';
if ( ! empty( $_GET['lang'] ) && in_array( $_GET['lang'], [ 'en', 'te', 'hi' ], true ) ) {
	$current_lang = sanitize_key( $_GET['lang'] );
} elseif ( ! empty( $_COOKIE['djv_language'] ) && in_array( $_COOKIE['djv_language'], [ 'en', 'te', 'hi' ], true ) ) {
	$current_lang = sanitize_key( $_COOKIE['djv_language'] );
}

// Multilingual UI strings
$i18n = [
	'hero_title' => [
		'en' => 'DJV Shubh Muhurtham Finder',
		'te' => 'డి.జె.వి శుభ ముహూర్త దర్శిని',
		'hi' => 'डी.जे.वी शुभ मुहूर्त दर्शक',
	],
	'hero_sub' => [
		'en' => 'Event-based Vedic auspicious timing finder. Calculate precise, personalized Muhurtham windows for sacred ceremonies, house warming, marriages, and new beginnings grounded in classical Jyotish Shastra.',
		'te' => 'శాస్త్రోక్త తిథి, నక్షత్ర, లగ్న గణితంతో కూడిన ఈవెంట్ ఆధారిత శుభ ముహూర్తాల అన్వేషణ. వివాహం, గృహప్రవేశం, నామకరణం మరియు శుభకార్యాలకు అత్యంత అనుకూలమైన సమయాలను తెలుసుకోండి.',
		'hi' => 'शास्त्रोक्त तिथि, नक्षत्र एवं लग्न गणना पर आधारित शुभ मुहूर्त दर्शक। विवाह, गृह प्रवेश, नामकरण एवं नवीन कार्यों के लिए सर्वोत्तम वैदिक काल का चयन करें।',
	],
	'what_title' => [
		'en' => 'What is Muhurtham in Vedic Jyotish?',
		'te' => 'వైదిక జ్యోతిషంలో ముహూర్తం అంటే ఏమిటి?',
		'hi' => 'वैदिक ज्योतिष में मुहूर्त क्या है?',
	],
	'what_p1' => [
		'en' => 'In Vedic astrology, "Muhurtham" is the precise cosmic moment when planetary vibrations and universal energies align favorably for the commencement of an auspicious deed. A single Hindu solar day contains thirty Muhurthams (each lasting approximately 48 minutes). Just as seeds planted in fertile soil yield bountiful harvests, actions initiated in an unblemished, auspicious Muhurtham flourish with enduring success, prosperity, and spiritual harmony.',
		'te' => 'వైదిక జ్యోతిష శాస్త్రంలో ముహూర్తం అంటే ఏదైనా శుభకార్యాన్ని ప్రారంభించడానికి సృష్టిలోని గ్రహశక్తులు అత్యంత అనుకూలంగా ఉండే పవిత్ర కాలం. ఒక రోజులో 30 ముహూర్తాలు (సుమారు 48 నిమిషాల వ్యవధి గలవి) ఉంటాయి. మంచి నేలలో నాటిన విత్తనం పుష్కలంగా ఫలితాన్ని ఇచ్చినట్లే, శుభ ముహూర్తంలో ప్రారంభించిన కార్యాలు నిర్విఘ్నంగా, శుభప్రదంగా పూర్తవుతాయి.',
		'hi' => 'वैदिक ज्योतिष में मुहूर्त वह विशिष्ट कालखंड है जब ब्रह्मांडीय ग्रह ऊर्जाएं किसी कार्य के शुभारंभ हेतु अत्यंत अनुकूल होती हैं। एक अहोरात्र (दिन-रात) में 30 मुहूर्त (प्रत्येक लगभग 48 मिनट) होते हैं। शुभ मुहूर्त में आरंभ किया गया कार्य विघ्नरहित होकर चिरस्थायी सुख और समृद्धि प्रदान करता है।',
	],
	'what_p2' => [
		'en' => 'Unlike static calendar date lists that generalize entire days, DJV calculates exact time windows for your precise geographical coordinates by evaluating the five limbs of Panchangam (Tithi, Vara, Nakshatra, Yoga, Karana), carving out malefic periods like Rahu Kalam and Varjyam, and respecting regional tradition differences.',
		'te' => 'సాధారణ క్యాలెండర్ల మాదిరిగా రోజంతా శుభప్రదం అని భావించకుండా, మీ ప్రాంతపు ఖచ్చితమైన అక్షాంశ, రేఖాంశాల ఆధారంగా రాహుకాలం, యమగండం, వర్జ్యం వంటి దోషాలను తొలగించి ఖచ్చితమైన ముహూర్త సమయాన్ని DJV గణిస్తుంది.',
		'hi' => 'सामान्य कैलेंडरों की भांति केवल तिथि बताने के बजाय, DJV आपके नगर के वास्तविक अक्षांश-देशांतर के आधार पर राहुकाल, यमगंड एवं वर्ज्यम् दोषों का त्याग कर सटीक समय अवधि प्रदान करता है।',
	],
	'choose_title' => [
		'en' => 'Choose Your Event',
		'te' => 'మీ శుభకార్యాన్ని ఎంచుకోండి',
		'hi' => 'अपने मांगलिक कार्य का चयन करें',
	],
	'choose_sub' => [
		'en' => "Select from {$total_count} traditional Hindu ceremonies across 7 major Vedic categories. Each event features dedicated Vedic rules, educational guidelines, and an interactive calculation form.",
		'te' => "7 ప్రధాన వర్గాలలోని {$total_count} సాంప్రదాయ వైదిక శుభకార్యాలలో ఒకదానిని ఎంచుకోండి. ప్రతి ఈవెంట్‌కు ప్రత్యేక శాస్త్ర నియమాలు మరియు ఖచ్చితమైన ముహూర్త గణన ఫారమ్ అందుబాటులో ఉంది.",
		'hi' => "7 प्रमुख वैदिक श्रेणियों में से {$total_count} पारंपरिक संस्कारों में से चुनें। प्रत्येक कार्य हेतु स्वतंत्र शास्त्रोक्त नियम एवं गणना प्रपत्र उपलब्ध है।",
	],
	'how_title' => [
		'en' => 'How DJV Finds Your Auspicious Muhurtham',
		'te' => 'DJV ముహూర్తాన్ని ఎలా గణిస్తుంది?',
		'hi' => 'DJV शुभ मुहूर्त की गणना कैसे करता है?',
	],
	'notes_title' => [
		'en' => 'Important Vedic Principles & Advisories',
		'te' => 'ముఖ్యమైన శాస్త్ర నియమాలు & సూచనలు',
		'hi' => 'महत्वपूर्ण शास्त्रोक्त नियम एवं परामर्श',
	],
	'faq_title' => [
		'en' => 'Frequently Asked Questions',
		'te' => 'తరచుగా అడిగే ప్రశ్నలు (FAQ)',
		'hi' => 'अक्सर पूछे जाने वाले प्रश्न (FAQ)',
	],
	'filter_all' => [ 'en' => 'All Ceremonies', 'te' => 'అన్ని శుభకార్యాలు', 'hi' => 'सभी संस्कार' ],
	'search_placeholder' => [
		'en' => 'Search 58 ceremonies (e.g. Marriage, Griha Pravesh, Vehicle, Business, Namakaran)...',
		'te' => 'శుభకార్యాన్ని శోధించండి (ఉదా. వివాహం, గృహప్రవేశం, వాహనం, వ్యాపారం, నామకరణం)...',
		'hi' => 'मांगलिक कार्य खोजें (उदा. विवाह, गृह प्रवेश, वाहन, व्यापार, नामकरण)...',
	],
	'find_btn' => [ 'en' => 'Find Muhurtham', 'te' => 'ముహూర్తం కనుగొనండి', 'hi' => 'मुहूर्त खोजें' ],
	'read_more' => [ 'en' => 'Read Principles & Calculate', 'te' => 'నియమాలు చదవండి & లెక్కించండి', 'hi' => 'नियम पढ़ें एवं गणना करें' ],
];

function djv_get_i18n( array $i18n, string $key, string $lang ): string {
	return $i18n[ $key ][ $lang ] ?? $i18n[ $key ]['en'] ?? '';
}
?>

<div class="muhurtham-hub-wrapper" style="background: var(--clr-bg, #FFF9F0); padding-bottom: 5rem;">

	<!-- ─── Hero Section ─────────────────────────────────────────── -->
	<section class="muhurtham-hero" style="background: linear-gradient(135deg, #241914 0%, #3D2A22 60%, #5C1A11 100%); color: #FFF; padding-top: calc(var(--header-h) + 2rem); padding-bottom: 3.5rem; position: relative; overflow: hidden;">
		<div style="position: absolute; inset: 0; background: radial-gradient(ellipse 70% 50% at 50% 20%, rgba(200, 148, 50, 0.18) 0%, transparent 70%); pointer-events: none;"></div>

		<div class="container" style="position: relative; z-index: 1; max-width: 1140px;">
			<nav aria-label="Breadcrumb" style="font-size: 0.8125rem; color: rgba(255,255,255,0.65); margin-bottom: 1.25rem;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
				<span style="color: var(--clr-accent, #E7B75A); font-weight: 600;"><?php esc_html_e( 'Shubh Muhurtham', 'djv-theme' ); ?></span>
			</nav>

			<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 2rem;">
				<div style="max-width: 760px;">
					<div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(231, 183, 90, 0.15); border: 1px solid rgba(231, 183, 90, 0.35); padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; color: var(--clr-accent, #E7B75A); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
						<span>🕉</span> <?php esc_html_e( 'Classical Vedic Jyotish Engine', 'djv-theme' ); ?>
					</div>
					<h1 style="font-family: var(--font-heading, serif); font-size: clamp(2.2rem, 4.5vw, 3.4rem); line-height: 1.2; color: #FFF; margin: 0 0 1rem 0;">
						<?php echo esc_html( djv_get_i18n( $i18n, 'hero_title', $current_lang ) ); ?>
					</h1>
					<p style="font-size: clamp(1rem, 1.8vw, 1.15rem); line-height: 1.7; color: rgba(255, 255, 255, 0.85); margin: 0 0 1.5rem 0;">
						<?php echo esc_html( djv_get_i18n( $i18n, 'hero_sub', $current_lang ) ); ?>
					</p>

					<!-- Location indicator -->
					<div style="display: inline-flex; align-items: center; gap: 0.75rem; background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.15); border-radius: 9999px; padding: 0.45rem 1.1rem; font-size: 0.875rem;">
						<span style="color: var(--clr-accent);">📍</span>
						<span><?php esc_html_e( 'Active Calculation Location:', 'djv-theme' ); ?> <strong class="global-location-name" id="hero-loc-name">Hyderabad, Telangana</strong></span>
						<button type="button" class="global-location-pill" style="background: none; border: none; color: var(--clr-accent); text-decoration: underline; font-weight: 600; cursor: pointer; padding: 0; font-size: inherit;">
							<?php esc_html_e( 'Change', 'djv-theme' ); ?>
						</button>
					</div>
				</div>

				<!-- Quick stats or visual badge -->
				<div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(231, 183, 90, 0.25); border-radius: 1.25rem; padding: 1.5rem 2rem; backdrop-filter: blur(10px); min-width: 250px; text-align: center;">
					<div style="font-size: 2.5rem; margin-bottom: 0.25rem;">✨</div>
					<div style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 700; color: var(--clr-accent);"><?php echo esc_html( $total_count ); ?></div>
					<div style="font-size: 0.85rem; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 0.05em;">
						<?php esc_html_e( 'Sacred Ceremonies', 'djv-theme' ); ?>
					</div>
					<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.8125rem; color: rgba(255,255,255,0.65);">
						✓ Drik Ganita Ephemeris<br>
						✓ Rahu Kalam Carved Out<br>
						✓ Multiple Ranked Options
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="container" style="max-width: 1140px; margin-top: -1.5rem; position: relative; z-index: 2;">

		<!-- ─── What is Muhurtham Section ────────────────────────────── -->
		<section class="muhurtham-intro-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8D5C4); border-radius: 1.25rem; padding: 2.5rem; box-shadow: var(--shadow-md, 0 4px 20px rgba(0,0,0,0.06)); margin-bottom: 3.5rem;">
			<div style="max-width: 900px;">
				<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-primary, #7A2419); text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.35rem;">
					<span>📖</span> <?php esc_html_e( 'Foundations of Vedic Timing', 'djv-theme' ); ?>
				</span>
				<h2 style="font-family: var(--font-heading); font-size: clamp(1.6rem, 2.8vw, 2.2rem); color: var(--clr-primary, #7A2419); margin: 0.5rem 0 1.25rem 0;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'what_title', $current_lang ) ); ?>
				</h2>
				<p style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text, #2C1A14); margin-bottom: 1.25rem;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'what_p1', $current_lang ) ); ?>
				</p>
				<p style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text-secondary, #6B4C3B); margin: 0;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'what_p2', $current_lang ) ); ?>
				</p>
			</div>

			<!-- Core Panchanga Pillars Grid -->
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #E8D5C4);">
				<div style="background: var(--clr-bg, #FFF9F0); border-radius: 0.75rem; padding: 1rem; text-align: center;">
					<div style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem;">1. తిథి (Tithi)</div>
					<div style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-top: 0.25rem;">Lunar Phase Energy</div>
				</div>
				<div style="background: var(--clr-bg, #FFF9F0); border-radius: 0.75rem; padding: 1rem; text-align: center;">
					<div style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem;">2. వారం (Vara)</div>
					<div style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-top: 0.25rem;">Solar Day Ruler</div>
				</div>
				<div style="background: var(--clr-bg, #FFF9F0); border-radius: 0.75rem; padding: 1rem; text-align: center;">
					<div style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem;">3. నక్షత్రం (Nakshatra)</div>
					<div style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-top: 0.25rem;">Cosmic Lunar Mansion</div>
				</div>
				<div style="background: var(--clr-bg, #FFF9F0); border-radius: 0.75rem; padding: 1rem; text-align: center;">
					<div style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem;">4. యోగం (Yoga)</div>
					<div style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-top: 0.25rem;">Sun-Moon Synergy</div>
				</div>
				<div style="background: var(--clr-bg, #FFF9F0); border-radius: 0.75rem; padding: 1rem; text-align: center;">
					<div style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem;">5. కరణం (Karana)</div>
					<div style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-top: 0.25rem;">Half-Tithi Vitality</div>
				</div>
			</div>
		</section>

		<!-- ─── Choose Your Event Section ────────────────────────────── -->
		<section id="choose-event-section" style="margin-bottom: 4rem;">
			<div style="text-align: center; max-width: 760px; margin: 0 auto 2.5rem auto;">
				<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary, #C89432); text-transform: uppercase; letter-spacing: 0.05em;">
					✨ <?php esc_html_e( 'Event-Based Astrological Schemas', 'djv-theme' ); ?>
				</span>
				<h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 3.5vw, 2.6rem); color: var(--clr-primary); margin: 0.4rem 0 0.8rem 0;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'choose_title', $current_lang ) ); ?>
				</h2>
				<p style="font-size: 1.05rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'choose_sub', $current_lang ) ); ?>
				</p>

				<!-- Category Filter Pills (7 Categories + All) -->
				<div class="event-filter-pills" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.6rem; margin-top: 1.75rem;">
					<button type="button" class="ev-filter-btn active" data-category="all" style="padding: 0.5rem 1.1rem; border-radius: 9999px; border: 1px solid var(--clr-primary); background: var(--clr-primary); color: #FFF; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
						<?php echo esc_html( djv_get_i18n( $i18n, 'filter_all', $current_lang ) ); ?> (<?php echo esc_html( $total_count ); ?>)
					</button>
					<?php foreach ( $categories as $cat_key => $cat_info ) :
						$c_title = $cat_info['title'][ $current_lang ] ?? $cat_info['title']['en'];
						$c_count = $cat_counts[ $cat_key ] ?? 0;
					?>
						<button type="button" class="ev-filter-btn" data-category="<?php echo esc_attr( $cat_key ); ?>" style="padding: 0.5rem 1.1rem; border-radius: 9999px; border: 1px solid var(--clr-border); background: #FFF; color: var(--clr-dark); font-weight: 500; font-size: 0.85rem; cursor: pointer;">
							<?php echo esc_html( ( $cat_info['icon'] ?? '✨' ) . ' ' . $c_title ); ?> (<?php echo esc_html( $c_count ); ?>)
						</button>
					<?php endforeach; ?>
				</div>

				<!-- Live Search Bar -->
				<div style="max-width: 560px; margin: 1.75rem auto 0 auto; position: relative;">
					<input type="text" id="muhurtham-search-input" placeholder="<?php echo esc_attr( djv_get_i18n( $i18n, 'search_placeholder', $current_lang ) ); ?>" style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 2.5rem; font-size: 0.95rem; border: 1.5px solid var(--clr-border); border-radius: 9999px; outline: none; background: #FFF; box-shadow: var(--shadow-sm); transition: border-color 0.2s ease;">
					<span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1rem; color: var(--clr-text-muted); pointer-events: none;">🔍</span>
					<button type="button" id="muhurtham-search-clear" style="display: none; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.2rem; color: var(--clr-text-muted); cursor: pointer; padding: 0; line-height: 1;">&times;</button>
				</div>
			</div>

			<!-- 58 Event Cards Grid -->
			<div class="muhurtham-events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
				<?php foreach ( $all_events as $slug => $data ) :
					$title_display = $data['title'][ $current_lang ] ?? $data['title']['en'];
					$desc_display  = $data['short_desc'][ $current_lang ] ?? $data['short_desc']['en'];
					$target_url    = home_url( "/muhurtham/{$slug}/" );
					$cat_obj       = $categories[ $data['category'] ] ?? null;
					$cat_name      = $cat_obj ? ( $cat_obj['title'][ $current_lang ] ?? $cat_obj['title']['en'] ) : $data['category'];
					$search_blob   = strtolower( $slug . ' ' . ( $data['title']['en'] ?? '' ) . ' ' . ( $data['title']['te'] ?? '' ) . ' ' . ( $data['title']['hi'] ?? '' ) . ' ' . ( $data['short_desc']['en'] ?? '' ) . ' ' . ( $data['short_desc']['te'] ?? '' ) . ' ' . ( $data['short_desc']['hi'] ?? '' ) );
				?>
					<article class="muhurtham-event-card" data-category="<?php echo esc_attr( $data['category'] ); ?>" data-search="<?php echo esc_attr( $search_blob ); ?>" style="background: #FFF; border: 1px solid var(--clr-border, #E8D5C4); border-radius: 1.1rem; padding: 1.75rem; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; transition: transform 0.25s ease, box-shadow 0.25s ease; position: relative;">
						<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
							<div style="font-size: 2.2rem; line-height: 1;"><?php echo esc_html( $data['icon'] ?? '✨' ); ?></div>
							<span style="font-size: 0.725rem; font-weight: 700; color: var(--clr-secondary); background: var(--clr-secondary-faint, #FDF5E3); padding: 0.25rem 0.65rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">
								<?php echo esc_html( $cat_name ); ?>
							</span>
						</div>

						<h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-primary); margin: 0 0 0.6rem 0; line-height: 1.3;">
							<a href="<?php echo esc_url( $target_url ); ?>" style="color: inherit; text-decoration: none;">
								<?php echo esc_html( $title_display ); ?>
							</a>
						</h3>

						<p style="font-size: 0.925rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1.25rem 0; flex: 1;">
							<?php echo esc_html( $desc_display ); ?>
						</p>

						<div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: space-between;">
							<span style="font-size: 0.75rem; color: var(--clr-text-muted);">
								✓ <?php echo esc_html( ucfirst( str_replace( '_', ' ', $data['form_schema'] ?? 'Standard' ) ) ); ?>
							</span>
							<a href="<?php echo esc_url( $target_url ); ?>" class="btn-event-calc" style="text-decoration: none; font-size: 0.875rem; font-weight: 700; color: var(--clr-primary); display: inline-flex; align-items: center; gap: 0.4rem; transition: color 0.2s ease;">
								<span><?php echo esc_html( djv_get_i18n( $i18n, 'read_more', $current_lang ) ); ?></span> →
							</a>
						</div>
					</article>
				<?php endforeach; ?>

				<!-- No Search Results Fallback Container -->
				<div id="muhurtham-search-empty" style="display: none; grid-column: 1 / -1; background: #FFF; border: 1px dashed var(--clr-border); border-radius: 1rem; padding: 3rem 1.5rem; text-align: center;">
					<div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
					<h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">No Ceremonies Found</h3>
					<p style="color: var(--clr-text-secondary); font-size: 0.95rem; margin: 0;">Try adjusting your search terms or switch the category filter to "All Ceremonies".</p>
				</div>
			</div>
		</section>

		<!-- ─── How DJV Finds Muhurtham Section ──────────────────────── -->
		<section class="how-djv-finds-section" style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1.25rem; padding: 3rem 2.5rem; margin-bottom: 3.5rem;">
			<div style="text-align: center; max-width: 760px; margin: 0 auto 2.5rem auto;">
				<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
					⚙️ <?php esc_html_e( 'Calculation Architecture', 'djv-theme' ); ?>
				</span>
				<h2 style="font-family: var(--font-heading); font-size: 2.2rem; color: var(--clr-primary); margin: 0.5rem 0 0.75rem 0;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'how_title', $current_lang ) ); ?>
				</h2>
				<p style="font-size: 1rem; color: var(--clr-text-secondary); margin: 0;">
					<?php esc_html_e( 'How our multi-layer astronomical engine computes and ranks auspicious time windows with zero hardcoding.', 'djv-theme' ); ?>
				</p>
			</div>

			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
				<div style="background: var(--clr-bg); border-radius: 1rem; padding: 1.75rem; border: 1px solid var(--clr-border);">
					<div style="font-size: 2rem; margin-bottom: 0.5rem;">🔭</div>
					<h3 style="font-size: 1.1rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">1. Local Drik Ephemeris</h3>
					<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0;">
						Calculates exact sunrise, sunset, Tithi transitions, and planetary positions tailored to your city coordinates and IANA timezone.
					</p>
				</div>
				<div style="background: var(--clr-bg); border-radius: 1rem; padding: 1.75rem; border: 1px solid var(--clr-border);">
					<div style="font-size: 2rem; margin-bottom: 0.5rem;">📜</div>
					<h3 style="font-size: 1.1rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">2. Event Rule Engine</h3>
					<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0;">
						Applies event-specific Vedic rules: evaluates favorable & prohibited Tithis, Nakshatras, Weekdays, Yogas, and Karanas.
					</p>
				</div>
				<div style="background: var(--clr-bg); border-radius: 1rem; padding: 1.75rem; border: 1px solid var(--clr-border);">
					<div style="font-size: 2rem; margin-bottom: 0.5rem;">✂️</div>
					<h3 style="font-size: 1.1rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">3. Inauspicious Carving</h3>
					<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0;">
						Removes Rahu Kalam, Yamagandam, Gulika, Dur Muhurtham, and Varjyam to isolate continuous auspicious periods >= 45 minutes.
					</p>
				</div>
				<div style="background: var(--clr-bg); border-radius: 1rem; padding: 1.75rem; border: 1px solid var(--clr-border);">
					<div style="font-size: 2rem; margin-bottom: 0.5rem;">🥇</div>
					<h3 style="font-size: 1.1rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">4. Multi-Option Ranking</h3>
					<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0;">
						Provides multiple ranked Muhurthams (Best, Very Good, Good) complete with transparent, verified rule-based reasoning.
					</p>
				</div>
			</div>
		</section>

		<!-- ─── Important Notes Section ──────────────────────────────── -->
		<section class="muhurtham-notes-section" style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF 100%); border: 1px solid var(--clr-border); border-left: 5px solid var(--clr-primary); border-radius: 1.25rem; padding: 2.5rem; margin-bottom: 3.5rem;">
			<h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin: 0 0 1rem 0;">
				<?php echo esc_html( djv_get_i18n( $i18n, 'notes_title', $current_lang ) ); ?>
			</h2>
			<ul style="margin: 0; padding-left: 1.25rem; color: var(--clr-text); font-size: 0.95rem; line-height: 1.8;">
				<li><strong><?php esc_html_e( 'Guru & Shukra Tara Asta:', 'djv-theme' ); ?></strong> <?php esc_html_e( 'Major samskaras such as Vivaha (Marriage) and Griha Pravesha are traditionally deferred when Jupiter or Venus is combust (Maudhyami / Asta).', 'djv-theme' ); ?></li>
				<li><strong><?php esc_html_e( 'Rikta Tithis Avoidance:', 'djv-theme' ); ?></strong> <?php esc_html_e( 'Chaturthi (4th), Navami (9th), and Chaturdashi (14th) lunar tithis are considered Rikta (empty) and excluded for auspicious beginnings.', 'djv-theme' ); ?></li>
				<li><strong><?php esc_html_e( 'Solar Sankranti & Shunya Masa:', 'djv-theme' ); ?></strong> <?php esc_html_e( 'Dhanu Sankranti (Dhanurmasa) and Meena Sankranti are traditionally avoided for marriage and new house warming in South Indian traditions.', 'djv-theme' ); ?></li>
				<li><strong><?php esc_html_e( 'Priest Consultation Advisory:', 'djv-theme' ); ?></strong> <?php esc_html_e( 'Calculated using DJV Panchangam and event-specific Muhurtham rules. Regional and sampradaya practices may vary. Consult your family priest/acharya for final confirmation.', 'djv-theme' ); ?></li>
			</ul>
		</section>

		<!-- ─── Comprehensive FAQ Section ────────────────────────────── -->
		<section class="muhurtham-faq-section" style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1.25rem; padding: 3rem 2.5rem; margin-bottom: 3.5rem;">
			<div style="text-align: center; max-width: 760px; margin: 0 auto 2rem auto;">
				<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
					❓ <?php esc_html_e( 'Knowledge & Clarity', 'djv-theme' ); ?>
				</span>
				<h2 style="font-family: var(--font-heading); font-size: 2.2rem; color: var(--clr-primary); margin: 0.4rem 0 0.5rem 0;">
					<?php echo esc_html( djv_get_i18n( $i18n, 'faq_title', $current_lang ) ); ?>
				</h2>
			</div>

			<div class="faq-accordion" style="display: flex; flex-direction: column; gap: 1rem; max-width: 900px; margin: 0 auto;">
				<details style="background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.75rem; padding: 1.25rem; cursor: pointer;">
					<summary style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
						<span><?php esc_html_e( 'Why does DJV provide multiple Muhurtham options instead of just one?', 'djv-theme' ); ?></span>
						<span style="font-size: 1.2rem; color: var(--clr-secondary);">+</span>
					</summary>
					<p style="margin-top: 0.75rem; color: var(--clr-text-secondary); font-size: 0.95rem; line-height: 1.7;">
						<?php esc_html_e( 'Vedic astrology recognizes varying degrees of auspiciousness based on planetary strengths, weekdays, lunar tithis, and individual star synergies. Providing ranked options (Best, Very Good, Good) empowers families to coordinate venue availability, priest schedules, and family logistics without compromising astrological sanctity.', 'djv-theme' ); ?>
					</p>
				</details>

				<details style="background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.75rem; padding: 1.25rem; cursor: pointer;">
					<summary style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
						<span><?php esc_html_e( 'How does city location affect Muhurtham timings?', 'djv-theme' ); ?></span>
						<span style="font-size: 1.2rem; color: var(--clr-secondary);">+</span>
					</summary>
					<p style="margin-top: 0.75rem; color: var(--clr-text-secondary); font-size: 0.95rem; line-height: 1.7;">
						<?php esc_html_e( 'Panchanga and inauspicious intervals (such as Rahu Kalam, Yamagandam, and Dur Muhurtham) are strictly calculated from local sunrise and sunset. Because sunrise varies significantly between cities (e.g. Hyderabad vs Delhi vs Chennai), Muhurtham lagna windows shift by minutes or hours based on location.', 'djv-theme' ); ?>
					</p>
				</details>

				<details style="background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.75rem; padding: 1.25rem; cursor: pointer;">
					<summary style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
						<span><?php esc_html_e( 'What is the difference between Basic and Personalized Muhurtham?', 'djv-theme' ); ?></span>
						<span style="font-size: 1.2rem; color: var(--clr-secondary);">+</span>
					</summary>
					<p style="margin-top: 0.75rem; color: var(--clr-text-secondary); font-size: 0.95rem; line-height: 1.7;">
						<?php esc_html_e( 'Basic calculation evaluates universal Panchanga suitability (Tithi, Vara, Nakshatra, Yoga, Karana). Personalized calculation additionally incorporates the individual Janma Kundali (birth star and moon sign) of participants—such as Bride and Groom in Marriage—evaluating Tarabala and Chandrabala to avoid critical personal doshas like Naidhana Tara.', 'djv-theme' ); ?>
					</p>
				</details>

				<details style="background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.75rem; padding: 1.25rem; cursor: pointer;">
					<summary style="font-weight: 700; color: var(--clr-primary); font-size: 1.05rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
						<span><?php esc_html_e( 'What should I do if no Muhurtham is found in my chosen date range?', 'djv-theme' ); ?></span>
						<span style="font-size: 1.2rem; color: var(--clr-secondary);">+</span>
					</summary>
					<p style="margin-top: 0.75rem; color: var(--clr-text-secondary); font-size: 0.95rem; line-height: 1.7;">
						<?php esc_html_e( 'DJV strictly adheres to Vedic rules and never fabricates invalid dates. If no candidate passes all criteria in a short date range (for instance, during a month of inauspicious transits or combustions), simply use the "Search Next 30 Days" or "Search Next 60 Days" quick expanders to examine the upcoming cycle.', 'djv-theme' ); ?>
					</p>
				</details>
			</div>
		</section>

		<!-- ─── Live Daily Panchangam Cross-Reference Banner ─────────── -->
		<div style="background: linear-gradient(135deg, #241914 0%, #7A2419 100%); color: #FFF; border-radius: 1.25rem; padding: 2.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
			<div style="max-width: 650px;">
				<span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; letter-spacing: 0.05em;">
					📅 <?php esc_html_e( 'Live Ephemeris Integration', 'djv-theme' ); ?>
				</span>
				<h3 style="font-family: var(--font-heading); font-size: 1.75rem; color: #FFF; margin: 0.5rem 0 0.5rem 0;">
					<?php esc_html_e( "Verify Against Today's Complete Panchangam", 'djv-theme' ); ?>
				</h3>
				<p style="margin: 0; color: rgba(255,255,255,0.85); font-size: 0.95rem; line-height: 1.6;">
					<?php esc_html_e( 'Cross-reference daily Tithi, Nakshatra padas, Brahma Muhurtham, and planetary hours for over 350+ cities across India.', 'djv-theme' ); ?>
				</p>
			</div>
			<div>
				<a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="btn-hero-primary" style="text-decoration: none; padding: 0.85rem 1.85rem; border-radius: 9999px; background: linear-gradient(135deg, var(--clr-secondary), var(--clr-accent)); color: var(--clr-dark); font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
					<span>🗓</span> <?php esc_html_e( 'Open Daily Panchangam', 'djv-theme' ); ?>
				</a>
			</div>
		</div>

	</div>
</div>

<script>
// Category Filter Pills & Live Search interactivity
document.addEventListener('DOMContentLoaded', function() {
	var filterBtns = document.querySelectorAll('.ev-filter-btn');
	var eventCards = document.querySelectorAll('.muhurtham-event-card');
	var searchInput = document.getElementById('muhurtham-search-input');
	var searchClear = document.getElementById('muhurtham-search-clear');
	var emptyNotice = document.getElementById('muhurtham-search-empty');

	var activeCategory = 'all';
	var activeSearchQuery = '';

	function applyFilters() {
		var visibleCount = 0;
		var q = activeSearchQuery.toLowerCase().trim();

		eventCards.forEach(function(card) {
			var cardCat = card.getAttribute('data-category');
			var cardSearch = (card.getAttribute('data-search') || '').toLowerCase();

			var matchesCat = (activeCategory === 'all' || cardCat === activeCategory);
			var matchesSearch = (!q || cardSearch.indexOf(q) !== -1);

			if (matchesCat && matchesSearch) {
				card.style.display = 'flex';
				visibleCount++;
			} else {
				card.style.display = 'none';
			}
		});

		if (emptyNotice) {
			emptyNotice.style.display = (visibleCount === 0) ? 'block' : 'none';
		}

		if (searchClear) {
			searchClear.style.display = q ? 'block' : 'none';
		}
	}

	filterBtns.forEach(function(btn) {
		btn.addEventListener('click', function() {
			filterBtns.forEach(function(b) {
				b.classList.remove('active');
				b.style.background = '#FFF';
				b.style.color = 'var(--clr-dark)';
				b.style.borderColor = 'var(--clr-border)';
			});
			this.classList.add('active');
			this.style.background = 'var(--clr-primary)';
			this.style.color = '#FFF';
			this.style.borderColor = 'var(--clr-primary)';

			activeCategory = this.getAttribute('data-category') || 'all';
			applyFilters();
		});
	});

	if (searchInput) {
		searchInput.addEventListener('input', function() {
			activeSearchQuery = this.value;
			applyFilters();
		});
	}

	if (searchClear) {
		searchClear.addEventListener('click', function() {
			if (searchInput) searchInput.value = '';
			activeSearchQuery = '';
			applyFilters();
			if (searchInput) searchInput.focus();
		});
	}

	// Sync active location name from localStorage if available
	if (window.DJVLocation && typeof window.DJVLocation.getSelected === 'function') {
		var activeLoc = window.DJVLocation.getSelected();
		if (activeLoc && activeLoc.name) {
			var locNameEl = document.getElementById('hero-loc-name');
			if (locNameEl) {
				locNameEl.textContent = activeLoc.name + (activeLoc.state ? ', ' + activeLoc.state : '');
			}
		}
	}
});
</script>
