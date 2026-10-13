<?php
/**
 * Template Part: Event-Specific Form Fields Renderer
 *
 * Renders dedicated, event-specific form inputs for each of the 58 Muhurtham events.
 * Only the relevant fields are rendered for the active schema.
 *
 * @package DJV_Theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Output event specific form fields based on schema.
 *
 * @param string $schema
 * @param string $lang
 * @param array $event_def
 */
function djv_render_event_form_fields( string $schema, string $lang, array $event_def ): void {
	$title = $event_def['title'][ $lang ] ?? $event_def['title']['en'];
	?>
	<div class="djv-schema-fields-wrapper" data-schema="<?php echo esc_attr( $schema ); ?>" style="margin-bottom: 2rem;">

		<?php if ( $schema === 'marriage' ) : ?>
			<!-- Marriage: Basic vs Personalized Mode Toggle -->
			<div style="background: var(--clr-bg, #FFF9F0); border: 1px solid var(--clr-border, #E8D5C4); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
				<label style="font-weight: 700; color: var(--clr-primary, #7A2419); font-size: 1rem; display: block; margin-bottom: 0.75rem;">
					<?php esc_html_e( 'Calculation Mode:', 'djv-theme' ); ?>
				</label>
				<div style="display: flex; flex-wrap: wrap; gap: 1rem;">
					<label style="display: flex; align-items: center; gap: 0.5rem; background: #FFF; padding: 0.75rem 1.25rem; border-radius: 0.5rem; border: 1px solid var(--clr-border); cursor: pointer; flex: 1; min-width: 240px;">
						<input type="radio" name="calc_mode" value="basic" checked style="accent-color: var(--clr-primary);">
						<div>
							<strong style="color: var(--clr-primary); font-size: 0.95rem; display: block;"><?php esc_html_e( 'Basic Panchanga Muhurtham', 'djv-theme' ); ?></strong>
							<span style="font-size: 0.8125rem; color: var(--clr-text-muted);"><?php esc_html_e( 'Universal auspicious wedding days & lagnas (Birth details optional)', 'djv-theme' ); ?></span>
						</div>
					</label>
					<label style="display: flex; align-items: center; gap: 0.5rem; background: #FFF; padding: 0.75rem 1.25rem; border-radius: 0.5rem; border: 1px solid var(--clr-border); cursor: pointer; flex: 1; min-width: 240px;">
						<input type="radio" name="calc_mode" value="personalized" style="accent-color: var(--clr-primary);">
						<div>
							<strong style="color: var(--clr-primary); font-size: 0.95rem; display: block;"><?php esc_html_e( 'Personalized Muhurtham', 'djv-theme' ); ?></strong>
							<span style="font-size: 0.8125rem; color: var(--clr-text-muted);"><?php esc_html_e( 'Includes Bride & Groom Tarabala & Chandrabala star synergy analysis', 'djv-theme' ); ?></span>
						</div>
					</label>
				</div>
			</div>

			<!-- Bride & Groom Details Fields -->
			<div id="marriage-participants-block" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
				<!-- Bride -->
				<div style="background: #FAFAFA; border: 1px solid var(--clr-border); border-radius: 0.85rem; padding: 1.5rem;">
					<h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--clr-primary); margin: 0 0 1rem 0;">👰 <?php esc_html_e( 'Bride Details', 'djv-theme' ); ?></h4>
					<div style="display: flex; flex-direction: column; gap: 0.85rem;">
						<div>
							<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Bride Name', 'djv-theme' ); ?></label>
							<input type="text" id="bride-name" placeholder="e.g. Lakshmi" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
						</div>
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
							<div>
								<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Date of Birth (Optional for Basic)', 'djv-theme' ); ?></label>
								<input type="date" id="bride-dob" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
							</div>
							<div>
								<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Birth Time', 'djv-theme' ); ?></label>
								<input type="time" id="bride-time" value="10:30" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
							</div>
						</div>
						<div>
							<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Birth City', 'djv-theme' ); ?></label>
							<input type="text" id="bride-place" placeholder="e.g. Hyderabad" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
						</div>
					</div>
				</div>
				<!-- Groom -->
				<div style="background: #FAFAFA; border: 1px solid var(--clr-border); border-radius: 0.85rem; padding: 1.5rem;">
					<h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--clr-primary); margin: 0 0 1rem 0;">🤵 <?php esc_html_e( 'Groom Details', 'djv-theme' ); ?></h4>
					<div style="display: flex; flex-direction: column; gap: 0.85rem;">
						<div>
							<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Groom Name', 'djv-theme' ); ?></label>
							<input type="text" id="groom-name" placeholder="e.g. Venkatesh" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
						</div>
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
							<div>
								<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Date of Birth (Optional for Basic)', 'djv-theme' ); ?></label>
								<input type="date" id="groom-dob" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
							</div>
							<div>
								<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Birth Time', 'djv-theme' ); ?></label>
								<input type="time" id="groom-time" value="09:15" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
							</div>
						</div>
						<div>
							<label style="font-size: 0.8125rem; font-weight: 600; color: var(--clr-text); display: block; margin-bottom: 0.25rem;"><?php esc_html_e( 'Birth City', 'djv-theme' ); ?></label>
							<input type="text" id="groom-place" placeholder="e.g. Vijayawada" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--clr-border); border-radius: 0.5rem; font-size: 0.9rem;">
						</div>
					</div>
				</div>
			</div>

		<?php elseif ( $schema === 'engagement' || $schema === 'roka_ceremony' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Bride / Bride Family Name', 'djv-theme' ); ?></label>
					<input type="text" id="eng-bride-name" placeholder="e.g. Sneha" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Groom / Groom Family Name', 'djv-theme' ); ?></label>
					<input type="text" id="eng-groom-name" placeholder="e.g. Rajesh" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Ceremony Tradition', 'djv-theme' ); ?></label>
					<select id="eng-tradition" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="south_indian"><?php esc_html_e( 'South Indian / Telugu Nischayathartham', 'djv-theme' ); ?></option>
						<option value="north_indian"><?php esc_html_e( 'North Indian Sagai / Roka', 'djv-theme' ); ?></option>
						<option value="universal"><?php esc_html_e( 'Universal Vedic Commitment', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'wedding_ceremonies' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Couple Names', 'djv-theme' ); ?></label>
					<input type="text" id="wed-couple-names" placeholder="e.g. Sneha & Rajesh" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Ceremony Type', 'djv-theme' ); ?></label>
					<select id="wed-ceremony-type" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="haldi"><?php esc_html_e( 'Haldi / Mangalasnanam / Pellikuthuru', 'djv-theme' ); ?></option>
						<option value="mehendi"><?php esc_html_e( 'Mehendi Ceremony', 'djv-theme' ); ?></option>
						<option value="mandap"><?php esc_html_e( 'Mandapam / Gauri Puja', 'djv-theme' ); ?></option>
						<option value="sangeet"><?php esc_html_e( 'Sangeet Celebration', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'griha_pravesh' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'House Owner Name', 'djv-theme' ); ?></label>
					<input type="text" id="gp-owner-name" placeholder="e.g. Srinivas Rao" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'House Type', 'djv-theme' ); ?></label>
					<select id="gp-house-type" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="apartment"><?php esc_html_e( 'Apartment / Flat', 'djv-theme' ); ?></option>
						<option value="independent"><?php esc_html_e( 'Independent House', 'djv-theme' ); ?></option>
						<option value="villa"><?php esc_html_e( 'Villa', 'djv-theme' ); ?></option>
					</select>
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Entry Category', 'djv-theme' ); ?></label>
					<select id="gp-entry-type" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="apoorva"><?php esc_html_e( 'Apoorva (First Entry into Newly Built Home)', 'djv-theme' ); ?></option>
						<option value="sapoorva"><?php esc_html_e( 'Sapoorva (Entry into Existing / Resale / Rental Home)', 'djv-theme' ); ?></option>
						<option value="dwandwa"><?php esc_html_e( 'Dwandwa (Entry after Major Renovation)', 'djv-theme' ); ?></option>
					</select>
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Vastu Preference', 'djv-theme' ); ?></label>
					<select id="gp-vastu-pref" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="yes"><?php esc_html_e( 'Strict Sthira Lagna & Vastu Alignment', 'djv-theme' ); ?></option>
						<option value="no"><?php esc_html_e( 'Standard Auspicious Timing', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'bhoomi_pujan' || $schema === 'shilanyas' || $schema === 'start_construction' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Property Owner / Builder Name', 'djv-theme' ); ?></label>
					<input type="text" id="bp-owner-name" placeholder="e.g. Prasad" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Construction Category', 'djv-theme' ); ?></label>
					<select id="bp-prop-type" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="residential"><?php esc_html_e( 'Residential Home / Villa', 'djv-theme' ); ?></option>
						<option value="commercial"><?php esc_html_e( 'Commercial Building / Office', 'djv-theme' ); ?></option>
						<option value="temple"><?php esc_html_e( 'Temple / Sacred Mandir', 'djv-theme' ); ?></option>
						<option value="industrial"><?php esc_html_e( 'Industrial Factory / Warehouse', 'djv-theme' ); ?></option>
					</select>
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Plot Orientation / Facing', 'djv-theme' ); ?></label>
					<select id="bp-facing" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="east"><?php esc_html_e( 'East Facing (Purva)', 'djv-theme' ); ?></option>
						<option value="north"><?php esc_html_e( 'North Facing (Uttara)', 'djv-theme' ); ?></option>
						<option value="north_east"><?php esc_html_e( 'North-East (Ishanya)', 'djv-theme' ); ?></option>
						<option value="west"><?php esc_html_e( 'West Facing (Pashchima)', 'djv-theme' ); ?></option>
						<option value="south"><?php esc_html_e( 'South Facing (Dakshina)', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'start_renovation' || $schema === 'move_new_home' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Resident / Owner Name', 'djv-theme' ); ?></label>
					<input type="text" id="ren-owner-name" placeholder="e.g. K. Narayana" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Scope of Alteration', 'djv-theme' ); ?></label>
					<select id="ren-scope" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="interior"><?php esc_html_e( 'Interior Remodeling & Vastu Correction', 'djv-theme' ); ?></option>
						<option value="structural"><?php esc_html_e( 'Structural Additions & Roof Repair', 'djv-theme' ); ?></option>
						<option value="painting"><?php esc_html_e( 'House Shifting & Painting Work', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'property_purchase' || $schema === 'property_registration' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Buyer / Conveyance Signee Name', 'djv-theme' ); ?></label>
					<input type="text" id="prop-buyer-name" placeholder="e.g. Ramesh Chandra" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Property Category', 'djv-theme' ); ?></label>
					<select id="prop-category" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="plot"><?php esc_html_e( 'Residential Open Plot / Land', 'djv-theme' ); ?></option>
						<option value="flat"><?php esc_html_e( 'Apartment / Villa Deed', 'djv-theme' ); ?></option>
						<option value="agricultural"><?php esc_html_e( 'Agricultural Farmland', 'djv-theme' ); ?></option>
						<option value="commercial"><?php esc_html_e( 'Commercial Space / Office', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'namakaran' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Baby Name (if decided)', 'djv-theme' ); ?></label>
					<input type="text" id="nk-baby-name" placeholder="e.g. Ananya" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Baby Date of Birth', 'djv-theme' ); ?></label>
					<input type="date" id="nk-baby-dob" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( "Father's Name", 'djv-theme' ); ?></label>
					<input type="text" id="nk-father-name" placeholder="e.g. Suresh" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( "Mother's Name", 'djv-theme' ); ?></label>
					<input type="text" id="nk-mother-name" placeholder="e.g. Shailaja" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'annaprashana' || $schema === 'mundan' || $schema === 'karnavedha' || $schema === 'vidyarambham' || $schema === 'upanayanam' || $schema === 'seemantham' || $schema === 'pumsavana' || $schema === 'jatakarma' || $schema === 'ayushya_homam' || $schema === 'family_milestones' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Child / Native Name', 'djv-theme' ); ?></label>
					<input type="text" id="samskara-child-name" placeholder="e.g. Aditya" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Date of Birth (Optional)', 'djv-theme' ); ?></label>
					<input type="date" id="samskara-dob" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Parent / Guardian Name', 'djv-theme' ); ?></label>
					<input type="text" id="samskara-parent-name" placeholder="e.g. Sharma" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Ceremony Venue / Location', 'djv-theme' ); ?></label>
					<input type="text" id="samskara-venue" placeholder="e.g. Tirupati / Home Mandir" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'vehicle_purchase' || $schema === 'vehicle_delivery' || $schema === 'vehicle_puja' || $schema === 'vehicle_registration' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Vehicle Purchaser / Owner Name', 'djv-theme' ); ?></label>
					<input type="text" id="veh-owner-name" placeholder="e.g. Ramesh" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Vehicle Type', 'djv-theme' ); ?></label>
					<select id="veh-type" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="car"><?php esc_html_e( 'Four-Wheeler (Car / SUV)', 'djv-theme' ); ?></option>
						<option value="bike"><?php esc_html_e( 'Two-Wheeler (Motorcycle / Scooter)', 'djv-theme' ); ?></option>
						<option value="commercial"><?php esc_html_e( 'Commercial Truck / Van', 'djv-theme' ); ?></option>
						<option value="ev"><?php esc_html_e( 'Electric Vehicle (EV)', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'gold_purchase' || $schema === 'electronics_purchase' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Purchaser Name', 'djv-theme' ); ?></label>
					<input type="text" id="pur-owner-name" placeholder="e.g. Smt. Radhika" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Purchase Intent / Category', 'djv-theme' ); ?></label>
					<select id="pur-category" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
						<option value="jewellery"><?php esc_html_e( 'Gold & Diamond Jewellery / Bullion', 'djv-theme' ); ?></option>
						<option value="electronics"><?php esc_html_e( 'Computers, Servers & High-Value Machinery', 'djv-theme' ); ?></option>
						<option value="appliances"><?php esc_html_e( 'Home Appliances & Equipment', 'djv-theme' ); ?></option>
					</select>
				</div>
			</div>

		<?php elseif ( $schema === 'business_opening' || $schema === 'shop_opening' || $schema === 'office_inauguration' || $schema === 'business_commencement' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Business / Firm Name', 'djv-theme' ); ?></label>
					<input type="text" id="biz-firm-name" placeholder="e.g. Sri Venkateswara Enterprises" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Primary Founder / Owner Name', 'djv-theme' ); ?></label>
					<input type="text" id="biz-owner-name" placeholder="e.g. K. V. Rao" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Business Trade / Industry', 'djv-theme' ); ?></label>
					<input type="text" id="biz-industry" placeholder="e.g. Retail / Tech Services" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'job_joining' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Candidate / Employee Name', 'djv-theme' ); ?></label>
					<input type="text" id="job-candidate-name" placeholder="e.g. Sandeep Reddy" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Role / Designation', 'djv-theme' ); ?></label>
					<input type="text" id="job-role" placeholder="e.g. Senior Manager" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Company / Workplace Location', 'djv-theme' ); ?></label>
					<input type="text" id="job-company" placeholder="e.g. HITEC City, Hyderabad" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'project_commencement' || $schema === 'contract_signing' || $schema === 'agreement_signing' || $schema === 'loan_application' || $schema === 'account_opening' || $schema === 'financial_beginning' || $schema === 'trading_commencement' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Principal Individual / Signatory Name', 'djv-theme' ); ?></label>
					<input type="text" id="fin-person-name" placeholder="e.g. Ananth Kumar" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Institution / Counterparty / Venture', 'djv-theme' ); ?></label>
					<input type="text" id="fin-institution" placeholder="e.g. Bank / Commercial Venture" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'general_puja' || $schema === 'griha_shanti' || $schema === 'ganapati_homam' || $schema === 'navagraha_shanti' || $schema === 'sudarshana_homam' || $schema === 'rudrabhishekam' || $schema === 'satyanarayana_puja' || $schema === 'temple_kumbhabhishekam' || $schema === 'deity_pratishtha' || $schema === 'yajna_homa' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Yajamana / Devotee Name', 'djv-theme' ); ?></label>
					<input type="text" id="puja-devotee-name" placeholder="e.g. B. Satyanarayana" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Ritual Intention / Sankalpa', 'djv-theme' ); ?></label>
					<input type="text" id="puja-sankalpa" placeholder="e.g. Family Well-being / Shanti" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Venue (Temple / Home Mandir)', 'djv-theme' ); ?></label>
					<input type="text" id="puja-venue" placeholder="e.g. Residence / Local Mandir" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'travel' || $schema === 'foreign_travel' || $schema === 'pilgrimage_departure' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Traveler Name', 'djv-theme' ); ?></label>
					<input type="text" id="trv-traveler-name" placeholder="e.g. Vijay Kumar" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Origin Departure City', 'djv-theme' ); ?></label>
					<input type="text" id="trv-origin" placeholder="e.g. Hyderabad" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Destination (City / Country / Kshetra)', 'djv-theme' ); ?></label>
					<input type="text" id="trv-destination" placeholder="e.g. Varanasi / London" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php elseif ( $schema === 'start_education' || $schema === 'start_course' || $schema === 'farming_sowing' ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Learner / Farmer Name', 'djv-theme' ); ?></label>
					<input type="text" id="edu-person-name" placeholder="e.g. Karthik" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Discipline / Course / Crop Type', 'djv-theme' ); ?></label>
					<input type="text" id="edu-subject" placeholder="e.g. Engineering / Paddy Sowing" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>

		<?php else : ?>
			<!-- General Karya Fallback -->
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Primary Individual / Devotee Name', 'djv-theme' ); ?></label>
					<input type="text" id="gen-person-name" placeholder="e.g. K. Narayana" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
				<div>
					<label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.35rem;"><?php esc_html_e( 'Purpose / Specific Intention', 'djv-theme' ); ?></label>
					<input type="text" id="gen-specifics" placeholder="e.g. Milestone / Special Initiative" style="width: 100%; padding: 0.65rem; border: 1px solid var(--clr-border); border-radius: 0.5rem;">
				</div>
			</div>
		<?php endif; ?>

	</div>
	<?php
}
