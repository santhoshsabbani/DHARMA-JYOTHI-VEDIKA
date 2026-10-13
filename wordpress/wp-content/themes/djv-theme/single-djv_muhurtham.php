<?php
/**
 * Single Muhurtham Event Template (djv_muhurtham)
 *
 * Implements the complete DJV Event-Based Muhurtham Finder page:
 * 1. Event title & metadata
 * 2. About the event, Religious significance, Why Muhurtham is considered,
 *    Selection principles, Required details (CONTENT BEFORE FORM per Section 3)
 * 3. Dynamic event-specific form (Marriage, Griha Pravesh, Bhoomi Pujan, Vehicle, Namakaran, etc.)
 * 4. Location integration with DJV location system (Lat, Lon, Timezone, City)
 * 5. Date range picker (From - To) & presets
 * 6. Calculation architecture via POST /wp-json/djv/v1/muhurtham
 * 7. Multiple ranked results (Best, Very Good, Good, Acceptable) with transparent reasons
 * 8. Compare 2–3 Muhurthams side-by-side
 * 9. Print & Share actions
 * 10. No result fallback with range expansion
 * 11. Event FAQ, Related Poojas, Mantras, Panchangam & Categories
 *
 * @package DJV_Theme
 */

get_header();

require_once DJV_PLUGIN_DIR . 'includes/data-muhurtham-events.php';
require_once get_template_directory() . '/template-parts/muhurtham/form-fields.php';

$current_slug = get_query_var( 'djv_muhurtham' );
if ( empty( $current_slug ) ) {
	$current_slug = get_query_var( 'muhurtham_event' );
}
if ( empty( $current_slug ) ) {
	$current_slug = get_post_field( 'post_name', get_the_ID() );
}
if ( empty( $current_slug ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
	$req_path = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	if ( preg_match( '#muhurtham/([^/?#]+)#', $req_path, $matches ) ) {
		$current_slug = sanitize_title( $matches[1] );
	}
}

$event_def = DJV_Muhurtham_Events::get_event( $current_slug );

// Fallback search by legacy slug or fallback to marriage
if ( ! $event_def ) {
	$all = DJV_Muhurtham_Events::get_all();
	foreach ( $all as $slug => $data ) {
		if ( ! empty( $data['legacy_slug'] ) && $data['legacy_slug'] === $current_slug ) {
			$event_def    = $data;
			$current_slug = $slug;
			break;
		}
	}
	if ( ! $event_def ) {
		$event_def    = DJV_Muhurtham_Events::get_event( 'marriage' );
		$current_slug = 'marriage';
	}
}

// Language resolution
$current_lang = 'en';
if ( ! empty( $_GET['lang'] ) && in_array( $_GET['lang'], [ 'en', 'te', 'hi' ], true ) ) {
	$current_lang = sanitize_key( $_GET['lang'] );
} elseif ( ! empty( $_COOKIE['djv_language'] ) && in_array( $_COOKIE['djv_language'], [ 'en', 'te', 'hi' ], true ) ) {
	$current_lang = sanitize_key( $_COOKIE['djv_language'] );
}

$title_display = $event_def['title'][ $current_lang ] ?? $event_def['title']['en'];
$short_desc    = $event_def['short_desc'][ $current_lang ] ?? $event_def['short_desc']['en'];
$content_data  = $event_def['content'] ?? [];
$faqs_data     = $event_def['faqs'] ?? [];
$form_schema   = $event_def['form_schema'] ?? 'general_karya';
$category      = $event_def['category'] ?? 'samskara';
$icon          = $event_def['icon'] ?? '✨';

// Labels
$labels = [
	'about_title' => [
		'en' => "What is {$title_display}?",
		'te' => "{$title_display} అంటే ఏమిటి?",
		'hi' => "{$title_display} क्या है?",
	],
	'religious_title' => [
		'en' => 'Religious & Vedic Significance',
		'te' => 'ధార్మిక & వైదిక ప్రాముఖ్యత',
		'hi' => 'धार्मिक एवं वैदिक महत्व',
	],
	'why_title' => [
		'en' => 'Why is Muhurtham Considered for this Event?',
		'te' => 'ఈ కార్యానికి ముహూర్తం ఎందుకు చూడాలి?',
		'hi' => 'इस कार्य हेतु मुहूर्त विचार क्यों आवश्यक है?',
	],
	'principles_title' => [
		'en' => 'General Vedic Selection Principles & Doshas Avoided',
		'te' => 'సాధారణ ముహూర్త నియమాలు & వర్జిత దోషాలు',
		'hi' => 'सामान्य शास्त्रोक्त नियम एवं वर्जित दोष',
	],
	'required_title' => [
		'en' => 'What Information is Required for Calculation?',
		'te' => 'ముహూర్త గణనకు అవసరమైన వివరాలు',
		'hi' => 'मुहूर्त गणना हेतु आवश्यक विवरण',
	],
	'form_title' => [
		'en' => "Find {$title_display}",
		'te' => "{$title_display} కనుగొనండి",
		'hi' => "{$title_display} खोजें",
	],
	'form_sub' => [
		'en' => 'Configure your ceremony preferences to calculate verified auspicious time windows.',
		'te' => 'శాస్త్రోక్త శుభ సమయాలను లెక్కించడానికి మీ వివరాలను పూరించండి.',
		'hi' => 'सटीक शास्त्रोक्त मुहूर्त जानने हेतु अपनी प्राथमिकताएं दर्ज करें।',
	],
	'location_label' => [ 'en' => 'Ceremony Location', 'te' => 'కార్యక్రమ ప్రాంతం', 'hi' => 'कार्यक्रम स्थल' ],
	'date_range_label' => [ 'en' => 'Preferred Date Range', 'te' => 'కోరుకున్న తేదీల పరిధి', 'hi' => 'वांछित दिनांक सीमा' ],
	'from_date' => [ 'en' => 'From Date', 'te' => 'ప్రారంభ తేదీ', 'hi' => 'प्रारंभिक तिथि' ],
	'to_date' => [ 'en' => 'To Date', 'te' => 'ముగింపు తేదీ', 'hi' => 'अंतिम तिथि' ],
	'time_pref_label' => [ 'en' => 'Preferred Time of Day', 'te' => 'ప్రాధాన్యత సమయం', 'hi' => 'समय प्राथमिकता' ],
	'time_any' => [ 'en' => 'Any Time (Full Day)', 'te' => 'రోజంతా (ఏ సమయమైనా)', 'hi' => 'संपूर्ण दिन' ],
	'time_morning' => [ 'en' => 'Morning (Sunrise to 12 PM)', 'te' => 'ఉదయం (సూర్యోదయం - 12 PM)', 'hi' => 'प्रातःकाल (सूर्योदय से 12 बजे)' ],
	'time_afternoon' => [ 'en' => 'Afternoon (12 PM to 5 PM)', 'te' => 'మధ్యాహ్నం (12 PM - 5 PM)', 'hi' => 'मध्याह्न (12 से 5 बजे)' ],
	'time_evening' => [ 'en' => 'Evening (5 PM to 8:30 PM)', 'te' => 'సాయంత్రం (5 PM - 8:30 PM)', 'hi' => 'सायंकाल (5 से 8:30 बजे)' ],
	'submit_btn' => [ 'en' => 'Calculate Auspicious Muhurthams', 'te' => 'శుభ ముహూర్తాలను లెక్కించండి', 'hi' => 'शुभ मुहूर्त की गणना करें' ],
];

function djv_loc( array $labels, string $key, string $lang ): string {
	return $labels[ $key ][ $lang ] ?? $labels[ $key ]['en'] ?? '';
}
?>

<!-- Structured SEO Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "<?php echo esc_attr( $title_display ); ?> 2026 — Auspicious Vedic Timings",
  "description": "<?php echo esc_attr( $short_desc ); ?>",
  "url": "<?php echo esc_url( home_url( "/muhurtham/{$current_slug}/" ) ); ?>",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      { "@type": "ListItem", "position": 1, "name": "Home", "item": "<?php echo esc_url( home_url( '/' ) ); ?>" },
      { "@type": "ListItem", "position": 2, "name": "Muhurtham", "item": "<?php echo esc_url( home_url( '/muhurtham/' ) ); ?>" },
      { "@type": "ListItem", "position": 3, "name": "<?php echo esc_attr( $title_display ); ?>", "item": "<?php echo esc_url( home_url( "/muhurtham/{$current_slug}/" ) ); ?>" }
    ]
  }
}
</script>

<div class="single-muhurtham-page" style="background: var(--clr-bg, #FFF9F0); padding-bottom: 5rem;">

	<!-- ─── Header & Breadcrumb ─────────────────────────────────── -->
	<header class="event-hero" style="background: linear-gradient(135deg, #241914 0%, #3D2A22 60%, #5C1A11 100%); color: #FFF; padding: 3.5rem 0 3rem 0; position: relative;">
		<div class="container" style="max-width: 1040px; position: relative; z-index: 1;">
			<nav aria-label="Breadcrumb" style="font-size: 0.8125rem; color: rgba(255,255,255,0.65); margin-bottom: 1.25rem;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
				<a href="<?php echo esc_url( home_url( '/muhurtham/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Muhurtham', 'djv-theme' ); ?></a> ›
				<span style="color: var(--clr-accent, #E7B75A); font-weight: 600;"><?php echo esc_html( $title_display ); ?></span>
			</nav>

			<div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
				<span style="font-size: 2.8rem; line-height: 1;"><?php echo esc_html( $icon ); ?></span>
				<div>
					<span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-accent); background: rgba(231,183,90,0.15); border: 1px solid rgba(231,183,90,0.3); padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">
						<?php echo esc_html( $category ); ?> Muhurtham
					</span>
				</div>
			</div>

			<h1 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: #FFF; margin: 0 0 0.75rem 0; line-height: 1.2;">
				<?php echo esc_html( $title_display ); ?>
			</h1>
			<p style="font-size: clamp(1rem, 1.8vw, 1.15rem); color: rgba(255,255,255,0.85); line-height: 1.6; max-width: 820px; margin: 0;">
				<?php echo esc_html( $short_desc ); ?>
			</p>
		</div>
	</header>

	<div class="container" style="max-width: 1040px; margin-top: -1.5rem; position: relative; z-index: 2;">

		<!-- ─── MANDATORY EVENT CONTENT BEFORE THE FORM (Section 3) ───── -->
		<section class="event-educational-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8D5C4); border-radius: 1.25rem; padding: 2.5rem; box-shadow: var(--shadow-md, 0 4px 20px rgba(0,0,0,0.06)); margin-bottom: 3rem;">

			<!-- 1. What is the Event? -->
			<?php if ( ! empty( $content_data['about'] ) ) : ?>
				<div class="content-block" style="margin-bottom: 2rem;">
					<h2 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0 0 0.75rem 0;">
						<?php echo esc_html( djv_loc( $labels, 'about_title', $current_lang ) ); ?>
					</h2>
					<p style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text); margin: 0;">
						<?php echo esc_html( $content_data['about'][ $current_lang ] ?? $content_data['about']['en'] ); ?>
					</p>
				</div>
			<?php endif; ?>

			<!-- 2. Religious Significance -->
			<?php if ( ! empty( $content_data['religious_significance'] ) ) : ?>
				<div class="content-block" style="margin-bottom: 2rem; padding: 1.5rem; background: var(--clr-bg, #FFF9F0); border-left: 4px solid var(--clr-secondary); border-radius: 0.75rem;">
					<h3 style="font-family: var(--font-heading); font-size: 1.3rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
						🕉 <?php echo esc_html( djv_loc( $labels, 'religious_title', $current_lang ) ); ?>
					</h3>
					<p style="font-size: 1rem; line-height: 1.75; color: var(--clr-text); margin: 0;">
						<?php echo esc_html( $content_data['religious_significance'][ $current_lang ] ?? $content_data['religious_significance']['en'] ); ?>
					</p>
				</div>
			<?php endif; ?>

			<!-- 3. Why is Muhurtham Considered? -->
			<?php if ( ! empty( $content_data['why_muhurtham'] ) ) : ?>
				<div class="content-block" style="margin-bottom: 2rem;">
					<h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-primary); margin: 0 0 0.6rem 0;">
						✨ <?php echo esc_html( djv_loc( $labels, 'why_title', $current_lang ) ); ?>
					</h3>
					<p style="font-size: 1.025rem; line-height: 1.8; color: var(--clr-text-secondary); margin: 0;">
						<?php echo esc_html( $content_data['why_muhurtham'][ $current_lang ] ?? $content_data['why_muhurtham']['en'] ); ?>
					</p>
				</div>
			<?php endif; ?>

			<!-- 4. General Selection Principles -->
			<?php if ( ! empty( $content_data['selection_principles'] ) ) : ?>
				<div class="content-block" style="margin-bottom: 2rem;">
					<h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-primary); margin: 0 0 0.6rem 0;">
						📜 <?php echo esc_html( djv_loc( $labels, 'principles_title', $current_lang ) ); ?>
					</h3>
					<p style="font-size: 1.025rem; line-height: 1.8; color: var(--clr-text-secondary); margin: 0;">
						<?php echo esc_html( $content_data['selection_principles'][ $current_lang ] ?? $content_data['selection_principles']['en'] ); ?>
					</p>
				</div>
			<?php endif; ?>

			<!-- 5. What Information is Required -->
			<?php if ( ! empty( $content_data['required_details'] ) ) : ?>
				<div class="content-block" style="padding-top: 1.25rem; border-top: 1px solid var(--clr-border);">
					<h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
						📋 <?php echo esc_html( djv_loc( $labels, 'required_title', $current_lang ) ); ?>
					</h3>
					<p style="font-size: 0.975rem; line-height: 1.7; color: var(--clr-text-muted); margin: 0;">
						<?php echo esc_html( $content_data['required_details'][ $current_lang ] ?? $content_data['required_details']['en'] ); ?>
					</p>
				</div>
			<?php endif; ?>

		</section>

		<!-- ─── INTERACTIVE DYNAMIC FORM (Section 4–11) ───────────────── -->
		<section id="muhurtham-finder-form-section" style="background: #FFF; border: 2px solid var(--clr-secondary); border-radius: 1.25rem; padding: 2.5rem; box-shadow: var(--shadow-lg, 0 8px 30px rgba(0,0,0,0.08)); margin-bottom: 3.5rem;">
			<div style="border-bottom: 1px solid var(--clr-border); padding-bottom: 1.5rem; margin-bottom: 2rem;">
				<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
					🎯 <?php esc_html_e( 'Vedic Astrological Calculator', 'djv-theme' ); ?>
				</span>
				<h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3vw, 2.3rem); color: var(--clr-primary); margin: 0.4rem 0 0.5rem 0;">
					<?php echo esc_html( djv_loc( $labels, 'form_title', $current_lang ) ); ?>
				</h2>
				<p style="font-size: 1rem; color: var(--clr-text-secondary); margin: 0;">
					<?php echo esc_html( djv_loc( $labels, 'form_sub', $current_lang ) ); ?>
				</p>
			</div>

			<form id="djv-muhurtham-form" novalidate>
				<input type="hidden" id="form-event-slug" value="<?php echo esc_attr( $current_slug ); ?>">
				<input type="hidden" id="form-lang" value="<?php echo esc_attr( $current_lang ); ?>">

				<!-- Event-Specific Schema Form Fields -->
				<?php djv_render_event_form_fields( $form_schema, $current_lang, $event_def ); ?>

				<!-- Universal Sections: Location, Date Range, Time of Day -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem;">

					<!-- Location Picker (Integrated with DJV system) -->
					<div>
						<label style="font-weight: 700; color: var(--clr-primary); font-size: 0.95rem; display: block; margin-bottom: 0.5rem;">
							📍 <?php echo esc_html( djv_loc( $labels, 'location_label', $current_lang ) ); ?>
						</label>
						<div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.65rem; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
							<div>
								<div id="form-location-display" style="font-weight: 700; color: var(--clr-dark); font-size: 1rem;">Hyderabad, Telangana</div>
								<div id="form-location-coords" style="font-size: 0.75rem; color: var(--clr-text-muted);">17.3850° N, 78.4867° E (Asia/Kolkata)</div>
							</div>
							<button type="button" id="form-change-loc-btn" class="global-location-pill" style="background: var(--clr-primary-faint, #F9EDE9); border: 1px solid var(--clr-primary); color: var(--clr-primary); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
								<?php esc_html_e( 'Change City', 'djv-theme' ); ?>
							</button>
						</div>
						<!-- Hidden inputs for location coordinates -->
						<input type="hidden" id="form-loc-city" value="Hyderabad">
						<input type="hidden" id="form-loc-state" value="Telangana">
						<input type="hidden" id="form-loc-lat" value="17.3850">
						<input type="hidden" id="form-loc-lon" value="78.4867">
						<input type="hidden" id="form-loc-tz" value="Asia/Kolkata">
					</div>

					<!-- Date Range (From - To) -->
					<div>
						<label style="font-weight: 700; color: var(--clr-primary); font-size: 0.95rem; display: block; margin-bottom: 0.5rem;">
							📅 <?php echo esc_html( djv_loc( $labels, 'date_range_label', $current_lang ) ); ?>
						</label>
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
							<div>
								<span style="font-size: 0.75rem; color: var(--clr-text-muted); display: block; margin-bottom: 0.2rem;"><?php echo esc_html( djv_loc( $labels, 'from_date', $current_lang ) ); ?></span>
								<input type="date" id="form-date-from" value="<?php echo esc_attr( date('Y-m-d') ); ?>" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
							</div>
							<div>
								<span style="font-size: 0.75rem; color: var(--clr-text-muted); display: block; margin-bottom: 0.2rem;"><?php echo esc_html( djv_loc( $labels, 'to_date', $current_lang ) ); ?></span>
								<input type="date" id="form-date-to" value="<?php echo esc_attr( date('Y-m-d', strtotime('+30 days')) ); ?>" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
							</div>
						</div>
						<!-- Quick Range Expansion Buttons -->
						<div style="display: flex; gap: 0.4rem; margin-top: 0.6rem;">
							<button type="button" class="btn-range-preset" data-days="30" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.35rem; cursor: pointer;">+30 Days</button>
							<button type="button" class="btn-range-preset" data-days="60" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.35rem; cursor: pointer;">+60 Days</button>
							<button type="button" class="btn-range-preset" data-days="90" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.35rem; cursor: pointer;">+90 Days</button>
						</div>
					</div>

				</div>

				<!-- Preferred Time of Day (Radio Pills) -->
				<div style="margin-bottom: 2rem;">
					<label style="font-weight: 700; color: var(--clr-primary); font-size: 0.95rem; display: block; margin-bottom: 0.5rem;">
						⏰ <?php echo esc_html( djv_loc( $labels, 'time_pref_label', $current_lang ) ); ?>
					</label>
					<div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
						<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: #FFF; border: 1px solid var(--clr-border); padding: 0.55rem 1rem; border-radius: 9999px; cursor: pointer; font-size: 0.875rem;">
							<input type="radio" name="time_of_day" value="any" checked style="accent-color: var(--clr-primary);">
							<span><?php echo esc_html( djv_loc( $labels, 'time_any', $current_lang ) ); ?></span>
						</label>
						<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: #FFF; border: 1px solid var(--clr-border); padding: 0.55rem 1rem; border-radius: 9999px; cursor: pointer; font-size: 0.875rem;">
							<input type="radio" name="time_of_day" value="morning" style="accent-color: var(--clr-primary);">
							<span><?php echo esc_html( djv_loc( $labels, 'time_morning', $current_lang ) ); ?></span>
						</label>
						<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: #FFF; border: 1px solid var(--clr-border); padding: 0.55rem 1rem; border-radius: 9999px; cursor: pointer; font-size: 0.875rem;">
							<input type="radio" name="time_of_day" value="afternoon" style="accent-color: var(--clr-primary);">
							<span><?php echo esc_html( djv_loc( $labels, 'time_afternoon', $current_lang ) ); ?></span>
						</label>
						<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: #FFF; border: 1px solid var(--clr-border); padding: 0.55rem 1rem; border-radius: 9999px; cursor: pointer; font-size: 0.875rem;">
							<input type="radio" name="time_of_day" value="evening" style="accent-color: var(--clr-primary);">
							<span><?php echo esc_html( djv_loc( $labels, 'time_evening', $current_lang ) ); ?></span>
						</label>
					</div>
				</div>

				<!-- Form Actions -->
				<div style="text-align: center;">
					<button type="submit" id="btn-submit-muhurtham" style="padding: 1rem 2.5rem; border-radius: 9999px; background: linear-gradient(135deg, var(--clr-primary), var(--clr-primary-light, #9B3128)); color: #FFF; border: none; font-size: 1.05rem; font-weight: 700; cursor: pointer; box-shadow: var(--shadow-md); display: inline-flex; align-items: center; gap: 0.6rem; transition: transform 0.2s ease, box-shadow 0.2s ease;">
						<span>🔮</span>
						<span id="btn-submit-text"><?php echo esc_html( djv_loc( $labels, 'submit_btn', $current_lang ) ); ?></span>
					</button>
				</div>
			</form>
		</section>

		<!-- ─── RESULTS CONTAINER (Section 16–23) ─────────────────────── -->
		<section id="muhurtham-results-wrapper" style="display: none; margin-bottom: 4rem;">

			<!-- Search Recap Banner (Section 21) -->
			<div id="results-recap-banner" style="background: linear-gradient(135deg, #241914 0%, #3D2A22 100%); color: #FFF; border-radius: 1rem; padding: 1.5rem 2rem; margin-bottom: 2rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
				<div>
					<span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; letter-spacing: 0.05em;">
						<?php esc_html_e( 'Your Muhurtham Search', 'djv-theme' ); ?>
					</span>
					<h3 style="font-family: var(--font-heading); font-size: 1.5rem; color: #FFF; margin: 0.35rem 0 0.25rem 0;">
						<span id="recap-event-title"><?php echo esc_html( $title_display ); ?></span>
					</h3>
					<div style="font-size: 0.875rem; color: rgba(255,255,255,0.85); display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
						<span>📍 <strong id="recap-location-text">Hyderabad</strong></span>
						<span>📅 <span id="recap-dates-text">Date Range</span></span>
						<span id="recap-mode-badge" style="background: rgba(231,183,90,0.2); border: 1px solid rgba(231,183,90,0.4); padding: 0.15rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; color: var(--clr-accent);">Basic Mode</span>
					</div>
				</div>
				<div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
					<button type="button" id="btn-results-compare" style="display: none; padding: 0.6rem 1.1rem; border-radius: 9999px; background: var(--clr-secondary); color: var(--clr-dark); font-weight: 700; border: none; font-size: 0.85rem; cursor: pointer;">
						⚖️ <?php esc_html_e( 'Compare (0 Selected)', 'djv-theme' ); ?>
					</button>
					<button type="button" id="btn-results-print" style="padding: 0.6rem 1.1rem; border-radius: 9999px; background: rgba(255,255,255,0.15); color: #FFF; border: 1px solid rgba(255,255,255,0.3); font-weight: 600; font-size: 0.85rem; cursor: pointer;">
						🖨️ <?php esc_html_e( 'Print', 'djv-theme' ); ?>
					</button>
					<button type="button" id="btn-results-share" style="padding: 0.6rem 1.1rem; border-radius: 9999px; background: rgba(255,255,255,0.15); color: #FFF; border: 1px solid rgba(255,255,255,0.3); font-weight: 600; font-size: 0.85rem; cursor: pointer;">
						🔗 <?php esc_html_e( 'Share', 'djv-theme' ); ?>
					</button>
				</div>
			</div>

			<!-- Dynamic Results List Cards -->
			<div id="results-cards-container" style="display: flex; flex-direction: column; gap: 1.5rem;">
				<!-- Injected by JavaScript -->
			</div>

			<!-- No Results Found State (Section 23) -->
			<div id="no-results-found-block" style="display: none; background: #FFF; border: 1px solid var(--clr-border); border-radius: 1.25rem; padding: 3rem 2rem; text-align: center;">
				<div style="font-size: 3rem; margin-bottom: 0.75rem;">⏳</div>
				<h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
					<?php esc_html_e( 'No Suitable Muhurtham Found in Selected Range', 'djv-theme' ); ?>
				</h3>
				<p style="color: var(--clr-text-secondary); max-width: 650px; margin: 0 auto 1.75rem auto; line-height: 1.6; font-size: 0.95rem;">
					<?php esc_html_e( 'No suitable Muhurtham was found for the selected event, location, and date range using the classical DJV Muhurtham rules. All dates within this range were constrained by inauspicious tithis, malefic yogas, or severe inauspicious windows.', 'djv-theme' ); ?>
				</p>
				<div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 0.75rem;">
					<button type="button" class="btn-expand-range" data-days="30" style="padding: 0.65rem 1.25rem; border-radius: 9999px; background: var(--clr-primary); color: #FFF; border: none; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
						🔍 <?php esc_html_e( 'Search Next 30 Days', 'djv-theme' ); ?>
					</button>
					<button type="button" class="btn-expand-range" data-days="60" style="padding: 0.65rem 1.25rem; border-radius: 9999px; background: #FFF; color: var(--clr-primary); border: 1px solid var(--clr-primary); font-weight: 600; font-size: 0.875rem; cursor: pointer;">
						🔍 <?php esc_html_e( 'Search Next 60 Days', 'djv-theme' ); ?>
					</button>
					<button type="button" id="btn-no-res-change-loc" class="global-location-pill" style="padding: 0.65rem 1.25rem; border-radius: 9999px; background: var(--clr-bg); color: var(--clr-dark); border: 1px solid var(--clr-border); font-weight: 600; font-size: 0.875rem; cursor: pointer;">
						📍 <?php esc_html_e( 'Change Location', 'djv-theme' ); ?>
					</button>
				</div>
			</div>

			<!-- Accuracy Disclaimer per Section 31 -->
			<div style="margin-top: 2rem; padding: 1.25rem 1.5rem; background: var(--clr-bg); border-radius: 0.75rem; border: 1px solid var(--clr-border); font-size: 0.85rem; color: var(--clr-text-muted); line-height: 1.6; text-align: center;">
				<strong><?php esc_html_e( 'Important Notice:', 'djv-theme' ); ?></strong>
				<span id="results-disclaimer-text">
					<?php esc_html_e( 'Calculated using DJV Panchangam and event-specific Muhurtham rules. Traditions and regional practices can differ. Consult a qualified priest/acharya for final confirmation.', 'djv-theme' ); ?>
				</span>
			</div>
		</section>

		<!-- ─── COMPARE MODAL / DRAWER (Section 22) ──────────────────── -->
		<div id="compare-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 99999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1.5rem;">
			<div style="background: #FFF; border-radius: 1.25rem; max-width: 960px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 2rem; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
				<div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--clr-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
					<div>
						<span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase;">DJV Comparison Engine</span>
						<h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0.25rem 0 0 0;">
							<?php esc_html_e( 'Side-by-Side Muhurtham Comparison', 'djv-theme' ); ?>
						</h3>
					</div>
					<button type="button" id="btn-close-compare" style="background: none; border: none; font-size: 1.75rem; cursor: pointer; color: var(--clr-text-muted); line-height: 1;">&times;</button>
				</div>
				<div id="compare-table-wrapper" style="overflow-x: auto;">
					<!-- Injected by JavaScript -->
				</div>
			</div>
		</div>

		<!-- ─── EVENT FAQ SECTION ────────────────────────────────────── -->
		<?php if ( ! empty( $faqs_data ) ) : ?>
			<section class="event-faq-card" style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1.25rem; padding: 2.5rem; margin-bottom: 3.5rem;">
				<div style="margin-bottom: 1.5rem;">
					<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase; letter-spacing: 0.05em;">❓ <?php esc_html_e( 'Clarifications & Guidelines', 'djv-theme' ); ?></span>
					<h3 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin: 0.35rem 0 0 0;">
						<?php echo esc_html( $title_display ); ?> <?php esc_html_e( 'Frequently Asked Questions', 'djv-theme' ); ?>
					</h3>
				</div>
				<div style="display: flex; flex-direction: column; gap: 0.85rem;">
					<?php foreach ( $faqs_data as $faq ) :
						$q_text = $faq['q'][ $current_lang ] ?? $faq['q']['en'];
						$a_text = $faq['a'][ $current_lang ] ?? $faq['a']['en'];
					?>
						<details style="background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.65rem; padding: 1.15rem; cursor: pointer;">
							<summary style="font-weight: 700; color: var(--clr-primary); font-size: 1rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
								<span><?php echo esc_html( $q_text ); ?></span>
								<span style="font-size: 1.2rem; color: var(--clr-secondary);">+</span>
							</summary>
							<p style="margin-top: 0.75rem; color: var(--clr-text-secondary); font-size: 0.925rem; line-height: 1.7;">
								<?php echo esc_html( $a_text ); ?>
							</p>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<!-- ─── RELATED RESOURCES (Poojas, Mantras, Panchangam) ──────── -->
		<section class="event-related-resources" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
			<!-- Related Poojas -->
			<div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem;">
				<h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
					🌺 <?php esc_html_e( 'Related Poojas & Homams', 'djv-theme' ); ?>
				</h4>
				<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1rem 0;">
					<?php esc_html_e( 'Complete ritual vidhi and sacred procedures recommended for this milestone ceremony.', 'djv-theme' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/pooja/' ) ); ?>" style="color: var(--clr-primary); font-weight: 600; font-size: 0.875rem; text-decoration: none;">
					<?php esc_html_e( 'Explore Pooja Guides', 'djv-theme' ); ?> →
				</a>
			</div>

			<!-- Related Mantras -->
			<div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem;">
				<h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
					🕉 <?php esc_html_e( 'Associated Sacred Mantras', 'djv-theme' ); ?>
				</h4>
				<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1rem 0;">
					<?php esc_html_e( 'Chant sanctifying Vedic stotras, gayatris, and suktams to invoke divine grace.', 'djv-theme' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>" style="color: var(--clr-primary); font-weight: 600; font-size: 0.875rem; text-decoration: none;">
					<?php esc_html_e( 'View Sacred Mantras', 'djv-theme' ); ?> →
				</a>
			</div>

			<!-- Today's Panchangam -->
			<div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem;">
				<h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
					📅 <?php esc_html_e( "Today's Live Panchangam", 'djv-theme' ); ?>
				</h4>
				<p style="font-size: 0.875rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1rem 0;">
					<?php esc_html_e( 'Check current daily Tithi, Nakshatra, and auspicious timings for your city.', 'djv-theme' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" style="color: var(--clr-primary); font-weight: 600; font-size: 0.875rem; text-decoration: none;">
					<?php esc_html_e( 'Open Daily Panchangam', 'djv-theme' ); ?> →
				</a>
			</div>
		</section>

	</div>
</div>

<!-- ─── CLIENT-SIDE DYNAMIC LOGIC & AJAX ENGINE ───────────────────── -->
<script>
(function() {
	'use strict';

	var currentResults = [];
	var selectedForCompare = [];

	// Sync Location from window.DJVLocation
	function syncLocationFromDJV() {
		if (window.DJVLocation && typeof window.DJVLocation.getSelected === 'function') {
			var loc = window.DJVLocation.getSelected();
			if (loc && loc.name) {
				updateLocationFields(loc);
			}
		}
	}

	function updateLocationFields(loc) {
		var name = loc.name || 'Hyderabad';
		var state = loc.state || 'Telangana';
		var lat = Number(loc.latitude || loc.lat || 17.3850);
		var lon = Number(loc.longitude || loc.lon || 78.4867);
		var tz = loc.timezone || 'Asia/Kolkata';

		var disp = document.getElementById('form-location-display');
		var coords = document.getElementById('form-location-coords');
		if (disp) disp.textContent = name + (state ? ', ' + state : '');
		if (coords) coords.textContent = lat.toFixed(4) + '° N, ' + lon.toFixed(4) + '° E (' + tz + ')';

		var cityInput = document.getElementById('form-loc-city');
		var stateInput = document.getElementById('form-loc-state');
		var latInput = document.getElementById('form-loc-lat');
		var lonInput = document.getElementById('form-loc-lon');
		var tzInput = document.getElementById('form-loc-tz');

		if (cityInput) cityInput.value = name;
		if (stateInput) stateInput.value = state;
		if (latInput) latInput.value = lat;
		if (lonInput) lonInput.value = lon;
		if (tzInput) tzInput.value = tz;

		var recapLoc = document.getElementById('recap-location-text');
		if (recapLoc) recapLoc.textContent = name + ', ' + state;
	}

	// Listen for global location changes
	window.addEventListener('djv:locationChanged', function(e) {
		if (e.detail) {
			updateLocationFields(e.detail);
			// Auto recalculate if results are visible
			var resWrapper = document.getElementById('muhurtham-results-wrapper');
			if (resWrapper && resWrapper.style.display !== 'none') {
				executeCalculation();
			}
		}
	});

	// Date Range Presets
	document.querySelectorAll('.btn-range-preset, .btn-expand-range').forEach(function(btn) {
		btn.addEventListener('click', function() {
			var days = parseInt(this.getAttribute('data-days'), 10) || 30;
			var fromInput = document.getElementById('form-date-from');
			var toInput = document.getElementById('form-date-to');
			if (!fromInput || !toInput) return;

			var fromDate = fromInput.value ? new Date(fromInput.value) : new Date();
			var target = new Date(fromDate.getTime() + (days * 86400000));
			var y = target.getFullYear();
			var m = String(target.getMonth() + 1).padStart(2, '0');
			var d = String(target.getDate()).padStart(2, '0');
			toInput.value = y + '-' + m + '-' + d;

			// If clicked from no-results block, execute immediately
			if (this.classList.contains('btn-expand-range')) {
				executeCalculation();
			}
		});
	});

	// Form Submission Handler
	var form = document.getElementById('djv-muhurtham-form');
	if (form) {
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			executeCalculation();
		});
	}

	function executeCalculation() {
		var submitBtn = document.getElementById('btn-submit-muhurtham');
		var submitText = document.getElementById('btn-submit-text');
		var resultsWrapper = document.getElementById('muhurtham-results-wrapper');
		var cardsContainer = document.getElementById('results-cards-container');
		var noResultsBlock = document.getElementById('no-results-found-block');

		if (submitBtn) submitBtn.disabled = true;
		if (submitText) submitText.textContent = 'Calculating Astronomical Ephemeris…';

		var eventSlug = document.getElementById('form-event-slug').value || 'marriage';
		var dateFrom = document.getElementById('form-date-from').value;
		var dateTo = document.getElementById('form-date-to').value;
		var lat = parseFloat(document.getElementById('form-loc-lat').value || '17.3850');
		var lon = parseFloat(document.getElementById('form-loc-lon').value || '78.4867');
		var city = document.getElementById('form-loc-city').value || 'Hyderabad';
		var state = document.getElementById('form-loc-state').value || 'Telangana';
		var tz = document.getElementById('form-loc-tz').value || 'Asia/Kolkata';
		var lang = document.getElementById('form-lang').value || 'en';

		var modeRadio = document.querySelector('input[name="calc_mode"]:checked');
		var mode = modeRadio ? modeRadio.value : 'basic';

		var timeRadio = document.querySelector('input[name="time_of_day"]:checked');
		var timePref = timeRadio ? timeRadio.value : 'any';

		var payload = {
			event: eventSlug,
			date_from: dateFrom,
			date_to: dateTo,
			language: lang,
			mode: mode,
			location: {
				city: city,
				state: state,
				latitude: lat,
				longitude: lon,
				timezone: tz
			},
			preferences: {
				time_of_day: timePref
			},
			participants: {}
		};

		// Collect all event-specific schema form fields
		var formData = {};
		var allFormInputs = form.querySelectorAll('input:not([type="radio"]):not([type="hidden"]), select, textarea');
		allFormInputs.forEach(function(inp) {
			if (inp.id) {
				formData[inp.id] = inp.value;
			}
		});
		payload.form_data = formData;

		// If Marriage personalized mode, collect bride & groom
		if (eventSlug === 'marriage') {
			var bName = document.getElementById('bride-name');
			var bDob = document.getElementById('bride-dob');
			var bTime = document.getElementById('bride-time');
			var bPlace = document.getElementById('bride-place');
			var gName = document.getElementById('groom-name');
			var gDob = document.getElementById('groom-dob');
			var gTime = document.getElementById('groom-time');
			var gPlace = document.getElementById('groom-place');

			if (bDob && bDob.value && gDob && gDob.value) {
				payload.participants = {
					bride: { name: bName ? bName.value : '', dob: bDob.value, time: bTime ? bTime.value : '', place: bPlace ? bPlace.value : '' },
					groom: { name: gName ? gName.value : '', dob: gDob.value, time: gTime ? gTime.value : '', place: gPlace ? gPlace.value : '' },
					bride_dob: bDob.value,
					groom_dob: gDob.value,
					bride_time: bTime ? bTime.value : '',
					groom_time: gTime ? gTime.value : ''
				};
			}
		}

		// Update recap banner
		var recapDates = document.getElementById('recap-dates-text');
		var recapMode = document.getElementById('recap-mode-badge');
		if (recapDates) recapDates.textContent = dateFrom + ' to ' + dateTo;
		if (recapMode) recapMode.textContent = mode === 'personalized' ? 'Personalized Kundali Mode' : 'Basic Panchanga Mode';

		// Call POST /wp-json/djv/v1/muhurtham
		fetch('/wp-json/djv/v1/muhurtham', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(payload)
		})
		.then(function(res) {
			if (!res.ok) {
				return res.json().then(function(j) { throw new Error(j.message || 'Server error'); });
			}
			return res.json();
		})
		.then(function(data) {
			if (submitBtn) submitBtn.disabled = false;
			if (submitText) submitText.textContent = 'Calculate Auspicious Muhurthams';

			if (resultsWrapper) resultsWrapper.style.display = 'block';

			var payloadData = data.data || data;
			var results = payloadData.results || [];
			currentResults = results;
			selectedForCompare = [];
			updateCompareButton();

			if (!results || results.length === 0) {
				if (cardsContainer) cardsContainer.innerHTML = '';
				if (noResultsBlock) noResultsBlock.style.display = 'block';
			} else {
				if (noResultsBlock) noResultsBlock.style.display = 'none';
				renderResults(results, lang);
			}

			// Scroll results into view smoothly
			resultsWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
		})
		.catch(function(err) {
			console.error('Muhurtham calculation error:', err);
			if (submitBtn) submitBtn.disabled = false;
			if (submitText) submitText.textContent = 'Calculate Auspicious Muhurthams';
			alert('Muhurtham calculation failed: ' + (err.message || 'Unknown error. Please check your date range and inputs.'));
		});
	}

	function renderResults(results, lang) {
		var container = document.getElementById('results-cards-container');
		if (!container) return;
		container.innerHTML = '';

		results.forEach(function(item, idx) {
			var rankBadgeHtml = '';
			var rankBorder = 'var(--clr-border)';
			var rankBg = '#FFF';

			if (item.rank === 'best') {
				rankBadgeHtml = '<span style="background: #2E7D5B; color: #FFF; padding: 0.3rem 0.8rem; border-radius: 9999px; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">🥇 ' + (item.rank_label || 'Best') + '</span>';
				rankBorder = '#2E7D5B';
			} else if (item.rank === 'very_good') {
				rankBadgeHtml = '<span style="background: #C89432; color: #FFF; padding: 0.3rem 0.8rem; border-radius: 9999px; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">🥈 ' + (item.rank_label || 'Very Good') + '</span>';
				rankBorder = '#C89432';
			} else if (item.rank === 'good') {
				rankBadgeHtml = '<span style="background: #7A2419; color: #FFF; padding: 0.3rem 0.8rem; border-radius: 9999px; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">🥉 ' + (item.rank_label || 'Good') + '</span>';
				rankBorder = 'rgba(122, 36, 25, 0.4)';
			} else {
				rankBadgeHtml = '<span style="background: #6B4C3B; color: #FFF; padding: 0.3rem 0.8rem; border-radius: 9999px; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">' + (item.rank_label || 'Acceptable') + '</span>';
			}

			var reasonsListHtml = '';
			if (item.why_this_time && item.why_this_time.length) {
				reasonsListHtml = item.why_this_time.map(function(r) {
					return '<li style="margin-bottom: 0.35rem; color: #2E7D5B; font-weight: 500;">' + r + '</li>';
				}).join('');
			}

			var card = document.createElement('div');
			card.className = 'muhurtham-result-card';
			card.style.cssText = 'background: #FFF; border: 1.5px solid ' + rankBorder + '; border-radius: 1.15rem; padding: 1.75rem; box-shadow: var(--shadow-sm); position: relative;';

			card.innerHTML = `
				<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; border-bottom: 1px solid var(--clr-border); padding-bottom: 1.25rem; margin-bottom: 1.25rem;">
					<div>
						<div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.4rem;">
							${rankBadgeHtml}
							<label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; color: var(--clr-text-muted); cursor: pointer;">
								<input type="checkbox" class="compare-checkbox" data-id="${item.id}" style="accent-color: var(--clr-primary);">
								<span>Select to Compare</span>
							</label>
						</div>
						<div style="font-family: var(--font-heading); font-size: 1.45rem; font-weight: 700; color: var(--clr-primary);">
							${item.formatted_date || item.date} (${item.weekday})
						</div>
					</div>

					<div style="text-align: right; background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.75rem; padding: 0.75rem 1.25rem;">
						<div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase;">Auspicious Time Window</div>
						<div style="font-size: 1.35rem; font-weight: 700; color: #2E7D5B; margin: 0.15rem 0;">
							⏰ ${item.time_window}
						</div>
						<div style="font-size: 0.8125rem; color: var(--clr-text-secondary); font-weight: 600;">
							Duration: ${item.duration}
						</div>
					</div>
				</div>

				<!-- Panchangam Core Metrics Grid -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1.25rem; background: #FAFAFA; border: 1px solid var(--clr-border); border-radius: 0.75rem; padding: 1rem;">
					<div>
						<span style="font-size: 0.725rem; color: var(--clr-text-muted); text-transform: uppercase; font-weight: 700;">Tithi:</span>
						<div style="font-weight: 600; font-size: 0.925rem; color: var(--clr-primary);">${item.panchangam.tithi}</div>
					</div>
					<div>
						<span style="font-size: 0.725rem; color: var(--clr-text-muted); text-transform: uppercase; font-weight: 700;">Nakshatra:</span>
						<div style="font-weight: 600; font-size: 0.925rem; color: var(--clr-primary);">${item.panchangam.nakshatra} (Pada ${item.panchangam.pada})</div>
					</div>
					<div>
						<span style="font-size: 0.725rem; color: var(--clr-text-muted); text-transform: uppercase; font-weight: 700;">Yoga:</span>
						<div style="font-weight: 600; font-size: 0.925rem; color: var(--clr-dark);">${item.panchangam.yoga}</div>
					</div>
					<div>
						<span style="font-size: 0.725rem; color: var(--clr-text-muted); text-transform: uppercase; font-weight: 700;">Karana:</span>
						<div style="font-weight: 600; font-size: 0.925rem; color: var(--clr-dark);">${item.panchangam.karana}</div>
					</div>
					<div>
						<span style="font-size: 0.725rem; color: var(--clr-text-muted); text-transform: uppercase; font-weight: 700;">Sunrise / Sunset:</span>
						<div style="font-weight: 600; font-size: 0.925rem; color: var(--clr-dark);">${item.solar.sunrise} – ${item.solar.sunset}</div>
					</div>
				</div>

				<!-- Why This Time Explanation (Section 19) -->
				<div style="margin-bottom: 1rem;">
					<strong style="font-size: 0.875rem; color: var(--clr-primary); display: block; margin-bottom: 0.35rem;">
						🔍 Why this time? (Vedic Astrological Reasoning):
					</strong>
					<ul style="margin: 0; padding-left: 1.25rem; font-size: 0.9rem; line-height: 1.6;">
						${reasonsListHtml}
					</ul>
				</div>

				<!-- Expandable Full Astronomical Ephemeris Details -->
				<details style="margin-top: 1rem; border-top: 1px dashed var(--clr-border); padding-top: 0.75rem;">
					<summary style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary); cursor: pointer;">
						▸ View Full Inauspicious Intervals & Ephemeris Details
					</summary>
					<div style="margin-top: 0.75rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; font-size: 0.8125rem; color: var(--clr-text-secondary); background: var(--clr-bg); padding: 0.85rem; border-radius: 0.5rem;">
						<div><strong>Rahu Kalam:</strong> ${item.inauspicious_periods.rahu_kalam || 'None'}</div>
						<div><strong>Yamagandam:</strong> ${item.inauspicious_periods.yamagandam || 'None'}</div>
						<div><strong>Gulika Kalam:</strong> ${item.inauspicious_periods.gulika_kalam || 'None'}</div>
						<div><strong>Dur Muhurtham:</strong> ${item.inauspicious_periods.dur_muhurtham || 'None'}</div>
						<div><strong>Varjyam:</strong> ${item.inauspicious_periods.varjyam || 'None'}</div>
						<div><strong>Rule Engine:</strong> ${item.rule_set_used}</div>
					</div>
				</details>
			`;

			container.appendChild(card);
		});

		// Attach compare checkbox handlers
		document.querySelectorAll('.compare-checkbox').forEach(function(box) {
			box.addEventListener('change', function() {
				var id = this.getAttribute('data-id');
				if (this.checked) {
					if (selectedForCompare.length >= 3) {
						this.checked = false;
						alert('You can compare a maximum of 3 Muhurthams simultaneously.');
						return;
					}
					selectedForCompare.push(id);
				} else {
					selectedForCompare = selectedForCompare.filter(function(x) { return x !== id; });
				}
				updateCompareButton();
			});
		});
	}

	function updateCompareButton() {
		var compareBtn = document.getElementById('btn-results-compare');
		if (!compareBtn) return;
		if (selectedForCompare.length >= 2) {
			compareBtn.style.display = 'inline-block';
			compareBtn.textContent = '⚖️ Compare (' + selectedForCompare.length + ' Selected)';
		} else {
			compareBtn.style.display = 'none';
		}
	}

	// Compare Modal Trigger (Section 22)
	var compareBtn = document.getElementById('btn-results-compare');
	var compareModal = document.getElementById('compare-modal-backdrop');
	var closeCompareBtn = document.getElementById('btn-close-compare');

	if (compareBtn && compareModal) {
		compareBtn.addEventListener('click', function() {
			renderCompareTable();
			compareModal.style.display = 'flex';
		});
	}
	if (closeCompareBtn && compareModal) {
		closeCompareBtn.addEventListener('click', function() {
			compareModal.style.display = 'none';
		});
	}

	function renderCompareTable() {
		var wrapper = document.getElementById('compare-table-wrapper');
		if (!wrapper) return;

		var selectedItems = currentResults.filter(function(r) {
			return selectedForCompare.indexOf(r.id) !== -1;
		});

		if (selectedItems.length < 2) {
			wrapper.innerHTML = '<p>Please select at least 2 Muhurthams to compare.</p>';
			return;
		}

		var tableHtml = '<table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; text-align: left;">';
		tableHtml += '<thead><tr style="background: var(--clr-primary); color: #FFF;">';
		tableHtml += '<th style="padding: 0.75rem 1rem; border: 1px solid var(--clr-border);">Attribute</th>';
		selectedItems.forEach(function(item) {
			tableHtml += '<th style="padding: 0.75rem 1rem; border: 1px solid var(--clr-border);">' + item.formatted_date + ' (' + item.weekday + ')</th>';
		});
		tableHtml += '</tr></thead><tbody>';

		var rows = [
			{ label: 'Rank', fn: function(x) { return '<strong>' + x.rank_label + '</strong> (Score: ' + x.score + ')'; } },
			{ label: 'Time Window', fn: function(x) { return '<span style="color:#2E7D5B; font-weight:700;">' + x.time_window + '</span>'; } },
			{ label: 'Duration', fn: function(x) { return x.duration; } },
			{ label: 'Tithi', fn: function(x) { return x.panchangam.tithi + ' (' + x.panchangam.paksha + ')'; } },
			{ label: 'Nakshatra', fn: function(x) { return x.panchangam.nakshatra + ' (Pada ' + x.panchangam.pada + ')'; } },
			{ label: 'Yoga', fn: function(x) { return x.panchangam.yoga; } },
			{ label: 'Karana', fn: function(x) { return x.panchangam.karana; } },
			{ label: 'Inauspicious Free', fn: function(x) { return '✓ Rahu Kalam, Yamagandam & Varjyam free'; } },
			{ label: 'Primary Reasons', fn: function(x) { return (x.why_this_time || []).join('<br>'); } }
		];

		rows.forEach(function(row) {
			tableHtml += '<tr style="border-bottom: 1px solid var(--clr-border);">';
			tableHtml += '<td style="padding: 0.75rem 1rem; font-weight: 700; background: #FAFAFA; border: 1px solid var(--clr-border);">' + row.label + '</td>';
			selectedItems.forEach(function(item) {
				tableHtml += '<td style="padding: 0.75rem 1rem; border: 1px solid var(--clr-border);">' + row.fn(item) + '</td>';
			});
			tableHtml += '</tr>';
		});

		tableHtml += '</tbody></table>';
		wrapper.innerHTML = tableHtml;
	}

	// Print Action
	var printBtn = document.getElementById('btn-results-print');
	if (printBtn) {
		printBtn.addEventListener('click', function() {
			window.print();
		});
	}

	// Share Action
	var shareBtn = document.getElementById('btn-results-share');
	if (shareBtn) {
		shareBtn.addEventListener('click', function() {
			var shareUrl = window.location.href;
			if (navigator.share) {
				navigator.share({
					title: document.title,
					text: 'Check these verified Vedic Muhurthams calculated by Dharma Jyothi Vedika:',
					url: shareUrl
				}).catch(function() {});
			} else if (navigator.clipboard) {
				navigator.clipboard.writeText(shareUrl).then(function() {
					alert('Muhurtham page link copied to clipboard!');
				});
			} else {
				prompt('Copy Muhurtham link:', shareUrl);
			}
		});
	}

	// Init sync on load
	syncLocationFromDJV();
})();
</script>

<?php
get_footer();
