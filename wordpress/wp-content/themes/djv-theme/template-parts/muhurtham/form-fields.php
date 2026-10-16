<?php
/**
 * Template Part: Unified Common Muhurtham Form Component
 *
 * Renders exactly the same reusable form component for ALL 58 Muhurtham ceremonies:
 * - Marriage Muhurtham
 * - Griha Pravesh
 * - Bhoomi Pujan
 * - Vehicle Purchase
 * - Business Opening
 * - Namakaran
 * - Annaprashana
 * - Travel Muhurtham
 * - All other configured Muhurtham events
 *
 * @package DJV_Theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render the unified common Muhurtham form component.
 *
 * @param array $event_def Master definition for current event.
 * @param string $lang Current active language (en, te, hi).
 */
function djv_render_common_muhurtham_form( array $event_def, string $lang = 'en' ): void {
	$slug       = $event_def['slug'] ?? 'marriage';
	$title      = $event_def['title'][ $lang ] ?? $event_def['title']['en'];
	$icon       = $event_def['icon'] ?? '✨';
	$category   = $event_def['category'] ?? 'general';
	$is_marriage = ( $slug === 'marriage' || ( $event_def['form_schema'] ?? '' ) === 'marriage' );

	$i18n = [
		'calc_form'     => [ 'en' => 'Vedic Astrological Calculator', 'te' => 'వైదిక ముహూర్త గణన యంత్రం', 'hi' => 'वैदिक मुहूर्त गणक' ],
		'find_title'    => [ 'en' => "Find {$title}", 'te' => "{$title} కనుగొనండి", 'hi' => "{$title} खोजें" ],
		'form_desc'     => [
			'en' => 'Configure your ceremony preferences to calculate verified auspicious time windows.',
			'te' => 'శాస్త్రోక్త శుభ సమయాలను లెక్కించడానికి మీ వివరాలను పూరించండి.',
			'hi' => 'सटीक शास्त्रोक्त मुहूर्त जानने हेतु अपनी प्राथमिकताएं दर्ज करें।',
		],
		'event_details' => [ 'en' => 'Selected Ceremony Event', 'te' => 'ఎంపిక చేసిన శుభకార్యం', 'hi' => 'चयनित मांगलिक कार्य' ],
		'calc_mode'     => [ 'en' => 'Calculation Mode', 'te' => 'గణన విధానం', 'hi' => 'गणना मोड' ],
		'mode_basic'    => [ 'en' => 'Basic Panchanga Muhurtham', 'te' => 'ప్రాథమిక పంచాంగ ముహూర్తం', 'hi' => 'सामान्य पंचांग मुहूर्त' ],
		'mode_basic_d'  => [ 'en' => 'Universal auspicious dates & lagnas (Birth details optional)', 'te' => 'సర్వజన శుభ ముహూర్తాలు (జనన వివరాలు ఐచ్ఛికం)', 'hi' => 'सार्वजनिक शुभ तिथियां एवं लग्न (जन्म विवरण वैकल्पिक)' ],
		'mode_pers'     => [ 'en' => 'Personalized Calculation Mode', 'te' => 'వ్యక్తిగత ముహూర్తం (జాతక పరిశీలన)', 'hi' => 'व्यक्तिगत मुहूर्त (कुंडली विचार)' ],
		'mode_pers_d'   => [ 'en' => 'Includes birth star synergy, Tarabala & Chandrabala analysis', 'te' => 'తారాబలం, చంద్రబలం మరియు జన్మ నక్షత్ర విశ్లేషణతో', 'hi' => 'ताराबल, चंद्रबल एवं जन्म नक्षत्र विश्लेषण सहित' ],
		'personal_hdr'  => [ 'en' => 'Applicant & Personal Details', 'te' => 'దరఖాస్తుదారు & వ్యక్తిగత వివరాలు', 'hi' => 'आवेदक एवं व्यक्तिगत विवरण' ],
		'applicant_name'=> [ 'en' => 'Applicant Name', 'te' => 'దరఖాస్తుదారు పేరు', 'hi' => 'आवेदक का नाम' ],
		'contact_email' => [ 'en' => 'Contact Email (Optional)', 'te' => 'ఈమెయిల్ (ఐచ్ఛికం)', 'hi' => 'ईमेल (वैकल्पिक)' ],
		'phone_number'  => [ 'en' => 'Phone Number (Optional)', 'te' => 'ఫోన్ నంబర్ (ఐచ్ఛికం)', 'hi' => 'फ़ोन नंबर (वैकल्पिक)' ],
		'bride_details' => [ 'en' => 'Bride Details (Optional for Basic)', 'te' => 'వధువు వివరాలు (ఐచ్ఛికం)', 'hi' => 'वधू विवरण (वैकल्पिक)' ],
		'groom_details' => [ 'en' => 'Groom Details (Optional for Basic)', 'te' => 'వరుని వివరాలు (ఐచ్ఛికం)', 'hi' => 'वर विवरण (वैकल्पिक)' ],
		'birth_hdr'     => [ 'en' => 'Birth Details (Optional unless personalized calculation)', 'te' => 'జన్మ వివరాలు (వ్యక్తిగత గణనకు ఉపయోగకరం)', 'hi' => 'जन्म विवरण (व्यक्तिगत गणना हेतु)' ],
		'dob'           => [ 'en' => 'Date of Birth', 'te' => 'పుట్టిన తేదీ', 'hi' => 'जन्म तिथि' ],
		'birth_time'    => [ 'en' => 'Birth Time', 'te' => 'పుట్టిన సమయం', 'hi' => 'जन्म समय' ],
		'birth_city'    => [ 'en' => 'Birth City', 'te' => 'పుట్టిన ఊరు / నగరం', 'hi' => 'जन्म स्थान / नगर' ],
		'location_hdr'  => [ 'en' => 'Ceremony & Event Location', 'te' => 'కార్యక్రమ ప్రదేశం & నగరం', 'hi' => 'कार्यक्रम स्थल एवं नगर' ],
		'venue_label'   => [ 'en' => 'Ceremony Venue / Location Name', 'te' => 'కార్యక్రమ వేదిక / చిరునామా', 'hi' => 'कार्यक्रम स्थल / पता' ],
		'change_city'   => [ 'en' => 'Change City', 'te' => 'నగరాన్ని మార్చండి', 'hi' => 'नगर बदलें' ],
		'dates_hdr'     => [ 'en' => 'Preferred Date Range & Time', 'te' => 'తేదీల పరిధి & ప్రాధాన్య సమయం', 'hi' => 'दिनांक सीमा एवं समय प्राथमिकता' ],
		'from_date'     => [ 'en' => 'From Date', 'te' => 'ప్రారంభ తేదీ', 'hi' => 'प्रारंभिक तिथि' ],
		'to_date'       => [ 'en' => 'To Date', 'te' => 'ముగింపు తేదీ', 'hi' => 'अंतिम तिथि' ],
		'time_any'      => [ 'en' => 'Any Time (Full Day)', 'te' => 'రోజంతా (ఏ సమయమైనా)', 'hi' => 'संपूर्ण दिन' ],
		'time_morning'  => [ 'en' => 'Morning (Sunrise to 12 PM)', 'te' => 'ఉదయం (సూర్యోదయం - 12 PM)', 'hi' => 'प्रातःकाल (सूर्योदय से 12 बजे)' ],
		'time_afternoon'=> [ 'en' => 'Afternoon (12 PM to 5 PM)', 'te' => 'మధ్యాహ్నం (12 PM - 5 PM)', 'hi' => 'मध्याह्न (12 से 5 बजे)' ],
		'time_evening'  => [ 'en' => 'Evening (5 PM to 8:30 PM)', 'te' => 'సాయంత్రం (5 PM - 8:30 PM)', 'hi' => 'सायंकाल (5 से 8:30 बजे)' ],
		'submit_btn'    => [ 'en' => 'Calculate Auspicious Muhurthams', 'te' => 'శుభ ముహూర్తాలను లెక్కించండి', 'hi' => 'शुभ मुहूर्त की गणना करें' ],
	];

	$t = function( string $k ) use ( $i18n, $lang ): string {
		return $i18n[ $k ][ $lang ] ?? $i18n[ $k ]['en'] ?? '';
	};
	?>
	<section id="muhurtham-finder-form-section" class="djv-common-form-container" style="background: #FFF; border: 2px solid var(--clr-secondary, #C89432); border-radius: 1.25rem; padding: clamp(1.5rem, 3vw, 2.5rem); box-shadow: var(--shadow-lg, 0 8px 30px rgba(0,0,0,0.08)); margin-bottom: 3.5rem;">

		<!-- Form Header Banner -->
		<div style="border-bottom: 1px solid var(--clr-border, #E8D5C4); padding-bottom: 1.5rem; margin-bottom: 2rem;">
			<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
				<span style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
					🎯 <?php echo esc_html( $t( 'calc_form' ) ); ?>
				</span>
				<span style="font-size: 0.75rem; background: var(--clr-primary-faint, #F9EDE9); color: var(--clr-primary, #7A2419); padding: 0.2rem 0.65rem; border-radius: 9999px; font-weight: 600;">
					<?php echo esc_html( ucfirst( str_replace( '_', ' ', $category ) ) ); ?>
				</span>
			</div>
			<h2 style="font-family: var(--font-heading, serif); font-size: clamp(1.8rem, 3vw, 2.3rem); color: var(--clr-primary); margin: 0 0 0.35rem 0;">
				<?php echo esc_html( $t( 'find_title' ) ); ?>
			</h2>
			<p style="font-size: 0.975rem; color: var(--clr-text-secondary, #6B574F); margin: 0;">
				<?php echo esc_html( $t( 'form_desc' ) ); ?>
			</p>
		</div>

		<form id="djv-muhurtham-form" class="djv-muhurtham-common-form" novalidate>
			<!-- Hidden identification tokens -->
			<input type="hidden" id="form-event-slug" name="event" value="<?php echo esc_attr( $slug ); ?>">
			<input type="hidden" id="form-lang" name="language" value="<?php echo esc_attr( $lang ); ?>">
			<!-- Honeypot anti-spam field -->
			<input type="text" name="djv_honey" id="djv_honey" style="display:none !important;" tabindex="-1" autocomplete="off">

			<!-- ─── 1. EVENT DETAILS & CALCULATION MODE ────────────────── -->
			<div class="form-section-block" style="background: var(--clr-bg, #FFF9F0); border: 1px solid var(--clr-border, #E8D5C4); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
				<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem;">
					<div>
						<span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-secondary); text-transform: uppercase; letter-spacing: 0.05em; display: block;">
							<?php echo esc_html( $t( 'event_details' ) ); ?>
						</span>
						<div style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--clr-primary); display: flex; align-items: center; gap: 0.5rem; margin-top: 0.15rem;">
							<span><?php echo esc_html( $icon ); ?></span>
							<span><?php echo esc_html( $title ); ?></span>
						</div>
					</div>
					<div style="font-size: 0.8125rem; color: var(--clr-text-muted, #8C7B74);">
						Rule Set: <strong style="color: var(--clr-dark, #241914);">v1.2.0 (Classical Jyotish)</strong>
					</div>
				</div>

				<!-- Calculation Mode Toggle (Basic vs. Personalized) -->
				<div>
					<label style="font-weight: 700; color: var(--clr-primary); font-size: 0.925rem; display: block; margin-bottom: 0.65rem;">
						<?php echo esc_html( $t( 'calc_mode' ) ); ?>:
					</label>
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
						<label style="display: flex; align-items: flex-start; gap: 0.65rem; background: #FFF; padding: 0.85rem 1.15rem; border-radius: 0.65rem; border: 1.5px solid var(--clr-border); cursor: pointer; transition: border-color 0.2s ease;">
							<input type="radio" name="calc_mode" value="basic" checked style="margin-top: 0.25rem; accent-color: var(--clr-primary);">
							<div>
								<strong style="color: var(--clr-primary); font-size: 0.95rem; display: block;"><?php echo esc_html( $t( 'mode_basic' ) ); ?></strong>
								<span style="font-size: 0.8125rem; color: var(--clr-text-muted); line-height: 1.4; display: block; margin-top: 0.15rem;"><?php echo esc_html( $t( 'mode_basic_d' ) ); ?></span>
							</div>
						</label>
						<label style="display: flex; align-items: flex-start; gap: 0.65rem; background: #FFF; padding: 0.85rem 1.15rem; border-radius: 0.65rem; border: 1.5px solid var(--clr-border); cursor: pointer; transition: border-color 0.2s ease;">
							<input type="radio" name="calc_mode" value="personalized" style="margin-top: 0.25rem; accent-color: var(--clr-primary);">
							<div>
								<strong style="color: var(--clr-primary); font-size: 0.95rem; display: block;"><?php echo esc_html( $t( 'mode_pers' ) ); ?></strong>
								<span style="font-size: 0.8125rem; color: var(--clr-text-muted); line-height: 1.4; display: block; margin-top: 0.15rem;"><?php echo esc_html( $t( 'mode_pers_d' ) ); ?></span>
							</div>
						</label>
					</div>
				</div>
			</div>

			<!-- ─── 2. PERSONAL DETAILS ─────────────────────────────────── -->
			<div class="form-section-block" style="border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
				<h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--clr-primary); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
					<span>👤</span> <?php echo esc_html( $t( 'personal_hdr' ) ); ?>
				</h3>

				<!-- Common Personal Fields: Applicant Name (Required), Email & Phone (Optional) -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
					<div>
						<label for="applicant-name" style="font-size: 0.85rem; font-weight: 700; color: var(--clr-text); display: block; margin-bottom: 0.35rem;">
							<?php echo esc_html( $t( 'applicant_name' ) ); ?> <span style="color: #B52020;">*</span>
						</label>
						<input type="text" id="applicant-name" name="applicant_name" required placeholder="e.g. Rajesh Sharma" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.95rem; box-sizing: border-box;">
					</div>
					<div>
						<label for="contact-email" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.35rem;">
							<?php echo esc_html( $t( 'contact_email' ) ); ?>
						</label>
						<input type="email" id="contact-email" name="contact_email" placeholder="e.g. rajesh@example.com" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.95rem; box-sizing: border-box;">
					</div>
					<div>
						<label for="phone-number" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.35rem;">
							<?php echo esc_html( $t( 'phone_number' ) ); ?>
						</label>
						<input type="tel" id="phone-number" name="phone_number" placeholder="e.g. +91 98765 43210" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.95rem; box-sizing: border-box;">
					</div>
				</div>

				<?php if ( $is_marriage ) : ?>
					<!-- Marriage-Specific Optional Fields: Bride & Groom Details -->
					<div id="marriage-participants-block" style="background: var(--clr-bg); border: 1px dashed var(--clr-border); border-radius: 0.75rem; padding: 1.25rem; margin-top: 1rem;">
						<div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-primary); margin-bottom: 1rem;">
							💍 <?php esc_html_e( 'Bride & Groom Details (Optional for Basic; required for personalized Kundali & Tarabala matching)', 'djv-theme' ); ?>
						</div>

						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
							<!-- Bride -->
							<div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.5rem; padding: 1rem;">
								<strong style="color: var(--clr-primary); font-size: 0.95rem; display: block; margin-bottom: 0.75rem;">👰 <?php echo esc_html( $t( 'bride_details' ) ); ?></strong>
								<div style="display: flex; flex-direction: column; gap: 0.75rem;">
									<div>
										<label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php esc_html_e( 'Bride Name', 'djv-theme' ); ?></label>
										<input type="text" id="bride-name" placeholder="e.g. Lakshmi" style="width: 100%; padding: 0.5rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; box-sizing: border-box;">
									</div>
									<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
										<div>
											<label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php echo esc_html( $t( 'dob' ) ); ?></label>
											<input type="date" id="bride-dob" style="width: 100%; padding: 0.45rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.85rem; box-sizing: border-box;">
										</div>
										<div>
											<label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php echo esc_html( $t( 'birth_time' ) ); ?></label>
											<input type="time" id="bride-time" value="10:30" style="width: 100%; padding: 0.45rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.85rem; box-sizing: border-box;">
										</div>
									</div>
									<div>
										<label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php echo esc_html( $t( 'birth_city' ) ); ?></label>
										<input type="text" id="bride-place" placeholder="e.g. Hyderabad" style="width: 100%; padding: 0.5rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; box-sizing: border-box;">
									</div>
								</div>
							</div>

							<!-- Groom -->
							<div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.5rem; padding: 1rem;">
								<strong style="color: var(--clr-primary); font-size: 0.95rem; display: block; margin-bottom: 0.75rem;">🤵 <?php echo esc_html( $t( 'groom_details' ) ); ?></strong>
								<div style="display: flex; flex-direction: column; gap: 0.75rem;">
									<div>
										<label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php esc_html_e( 'Groom Name', 'djv-theme' ); ?></label>
										<input type="text" id="groom-name" placeholder="e.g. Venkatesh" style="width: 100%; padding: 0.5rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; box-sizing: border-box;">
									</div>
									<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
										<div>
											<label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php echo esc_html( $t( 'dob' ) ); ?></label>
											<input type="date" id="groom-dob" style="width: 100%; padding: 0.45rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.85rem; box-sizing: border-box;">
										</div>
										<div>
											<label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php echo esc_html( $t( 'birth_time' ) ); ?></label>
											<input type="time" id="groom-time" value="09:15" style="width: 100%; padding: 0.45rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.85rem; box-sizing: border-box;">
										</div>
									</div>
									<div>
										<label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.2rem;"><?php echo esc_html( $t( 'birth_city' ) ); ?></label>
										<input type="text" id="groom-place" placeholder="e.g. Vijayawada" style="width: 100%; padding: 0.5rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; box-sizing: border-box;">
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php else : ?>
					<!-- Other Ceremonies Optional Birth Details Block (Optional unless personalized mode active) -->
					<div id="general-birth-block" style="background: var(--clr-bg); border: 1px dashed var(--clr-border); border-radius: 0.75rem; padding: 1.25rem; margin-top: 1rem;">
						<div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-primary); margin-bottom: 0.75rem;">
							✨ <?php echo esc_html( $t( 'birth_hdr' ) ); ?>
						</div>
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
							<div>
								<label for="applicant-dob" style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">
									<?php echo esc_html( $t( 'dob' ) ); ?>
								</label>
								<input type="date" id="applicant-dob" name="applicant_dob" style="width: 100%; padding: 0.55rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.9rem; box-sizing: border-box;">
							</div>
							<div>
								<label for="applicant-time" style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">
									<?php echo esc_html( $t( 'birth_time' ) ); ?>
								</label>
								<input type="time" id="applicant-time" name="applicant_time" value="12:00" style="width: 100%; padding: 0.55rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.9rem; box-sizing: border-box;">
							</div>
							<div>
								<label for="applicant-city" style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">
									<?php echo esc_html( $t( 'birth_city' ) ); ?>
								</label>
								<input type="text" id="applicant-city" name="applicant_city" placeholder="e.g. Hyderabad" style="width: 100%; padding: 0.55rem; border: 1px solid var(--clr-border); border-radius: 0.4rem; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- ─── 3. CEREMONY LOCATION & TIMEZONE ─────────────────────── -->
			<div class="form-section-block" style="border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
				<h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--clr-primary); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
					<span>📍</span> <?php echo esc_html( $t( 'location_hdr' ) ); ?>
				</h3>

				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; align-items: flex-start;">
					<!-- Venue / Specific Location Name (Required) -->
					<div>
						<label for="ceremony-location" style="font-size: 0.85rem; font-weight: 700; color: var(--clr-text); display: block; margin-bottom: 0.35rem;">
							<?php echo esc_html( $t( 'venue_label' ) ); ?> <span style="color: #B52020;">*</span>
						</label>
						<input type="text" id="ceremony-location" name="ceremony_location" required placeholder="e.g. Home, Grand Banquet Hall, Temple, or City Venue" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.95rem; box-sizing: border-box;">
					</div>

					<!-- City & Coordinates Selector -->
					<div>
						<label style="font-size: 0.85rem; font-weight: 700; color: var(--clr-text); display: block; margin-bottom: 0.35rem;">
							<?php esc_html_e( 'Calculation City & Timezone', 'djv-theme' ); ?>
						</label>
						<div style="background: var(--clr-bg); border: 1px solid var(--clr-border); border-radius: 0.65rem; padding: 0.65rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
							<div>
								<div id="form-location-display" style="font-weight: 700; color: var(--clr-dark); font-size: 0.95rem;">Hyderabad, Telangana</div>
								<div id="form-location-coords" style="font-size: 0.75rem; color: var(--clr-text-muted);">17.3850° N, 78.4867° E (Asia/Kolkata)</div>
							</div>
							<button type="button" id="form-change-loc-btn" class="global-location-pill" style="background: #FFF; border: 1px solid var(--clr-primary); color: var(--clr-primary); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; cursor: pointer; white-space: nowrap;">
								<?php echo esc_html( $t( 'change_city' ) ); ?>
							</button>
						</div>
					</div>
				</div>

				<!-- Hidden coordinates inputs -->
				<input type="hidden" id="form-loc-city" name="city" value="Hyderabad">
				<input type="hidden" id="form-loc-state" name="state" value="Telangana">
				<input type="hidden" id="form-loc-lat" name="latitude" value="17.3850">
				<input type="hidden" id="form-loc-lon" name="longitude" value="78.4867">
				<input type="hidden" id="form-loc-tz" name="timezone" value="Asia/Kolkata">
			</div>

			<!-- ─── 4. PREFERRED DATE RANGE & TIME OF DAY ──────────────── -->
			<div class="form-section-block" style="border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
				<h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--clr-primary); margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
					<span>📅</span> <?php echo esc_html( $t( 'dates_hdr' ) ); ?>
				</h3>

				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
					<!-- Dates -->
					<div>
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
							<div>
								<label for="form-date-from" style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text); display: block; margin-bottom: 0.25rem;">
									<?php echo esc_html( $t( 'from_date' ) ); ?> <span style="color: #B52020;">*</span>
								</label>
								<input type="date" id="form-date-from" name="date_from" value="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1.5px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem; box-sizing: border-box;">
							</div>
							<div>
								<label for="form-date-to" style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text); display: block; margin-bottom: 0.25rem;">
									<?php echo esc_html( $t( 'to_date' ) ); ?> <span style="color: #B52020;">*</span>
								</label>
								<input type="date" id="form-date-to" name="date_to" value="<?php echo esc_attr( date( 'Y-m-d', strtotime( '+30 days' ) ) ); ?>" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1.5px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>
						<!-- Quick Extension Buttons -->
						<div style="display: flex; gap: 0.5rem; margin-top: 0.65rem;">
							<button type="button" class="btn-range-preset" data-days="30" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 600; background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.35rem; cursor: pointer;">+30 Days</button>
							<button type="button" class="btn-range-preset" data-days="60" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 600; background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.35rem; cursor: pointer;">+60 Days</button>
							<button type="button" class="btn-range-preset" data-days="90" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 600; background: #FFF; border: 1px solid var(--clr-border); border-radius: 0.35rem; cursor: pointer;">+90 Days</button>
						</div>
					</div>

					<!-- Time of Day Radios -->
					<div>
						<label style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text); display: block; margin-bottom: 0.5rem;">
							⏰ <?php esc_html_e( 'Preferred Time of Day', 'djv-theme' ); ?>
						</label>
						<div style="display: flex; flex-wrap: wrap; gap: 0.65rem;">
							<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--clr-bg); border: 1px solid var(--clr-border); padding: 0.5rem 0.85rem; border-radius: 9999px; cursor: pointer; font-size: 0.85rem;">
								<input type="radio" name="time_of_day" value="any" checked style="accent-color: var(--clr-primary);">
								<span><?php echo esc_html( $t( 'time_any' ) ); ?></span>
							</label>
							<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--clr-bg); border: 1px solid var(--clr-border); padding: 0.5rem 0.85rem; border-radius: 9999px; cursor: pointer; font-size: 0.85rem;">
								<input type="radio" name="time_of_day" value="morning" style="accent-color: var(--clr-primary);">
								<span><?php echo esc_html( $t( 'time_morning' ) ); ?></span>
							</label>
							<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--clr-bg); border: 1px solid var(--clr-border); padding: 0.5rem 0.85rem; border-radius: 9999px; cursor: pointer; font-size: 0.85rem;">
								<input type="radio" name="time_of_day" value="afternoon" style="accent-color: var(--clr-primary);">
								<span><?php echo esc_html( $t( 'time_afternoon' ) ); ?></span>
							</label>
							<label style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--clr-bg); border: 1px solid var(--clr-border); padding: 0.5rem 0.85rem; border-radius: 9999px; cursor: pointer; font-size: 0.85rem;">
								<input type="radio" name="time_of_day" value="evening" style="accent-color: var(--clr-primary);">
								<span><?php echo esc_html( $t( 'time_evening' ) ); ?></span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<!-- Inline Validation Error Alert Banner -->
			<div id="form-validation-alert" style="display: none; background: #FDF2F2; border: 1.5px solid #F5C6CB; color: #721C24; padding: 0.85rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
				<strong>⚠️ Validation Error:</strong> <span id="form-validation-msg"></span>
			</div>

			<!-- Submit Button Row -->
			<div style="text-align: center;">
				<button type="submit" id="btn-submit-muhurtham" style="padding: 1.1rem 2.75rem; border-radius: 9999px; background: linear-gradient(135deg, var(--clr-primary, #7A2419), var(--clr-primary-light, #9B3128)); color: #FFF; border: none; font-size: 1.05rem; font-weight: 700; cursor: pointer; box-shadow: var(--shadow-md, 0 4px 15px rgba(122,36,25,0.25)); display: inline-flex; align-items: center; gap: 0.6rem; transition: transform 0.2s ease, box-shadow 0.2s ease;">
					<span>🔮</span>
					<span id="btn-submit-text"><?php echo esc_html( $t( 'submit_btn' ) ); ?></span>
				</button>
			</div>
		</form>
	</section>
	<?php
}

/**
 * Backward compatibility wrapper.
 */
function djv_render_event_form_fields( string $schema, string $lang, array $event_def ): void {
	djv_render_common_muhurtham_form( $event_def, $lang );
}
