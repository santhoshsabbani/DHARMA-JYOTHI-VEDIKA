<?php
/**
 * DJV Core — Temple Master Engine & Mathematical Location Calculator
 *
 * Provides:
 * - High-precision Haversine spherical distance calculation
 * - 8-point compass bearing & navigation string generation
 * - Multilingual translation resolution (English, Telugu, Hindi)
 * - Automatic catalog synchronization into djv_temple CPT
 * - Comprehensive WordPress Admin editor with structured sections
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DJV_Temple_Master {

	const VERSION = '1.0.0';

	/**
	 * Compute Haversine great-circle distance between two GPS coordinates in kilometers.
	 */
	public static function haversine_distance( float $lat1, float $lon1, float $lat2, float $lon2 ): float {
		$earth_radius = 6371.0; // Mean Earth radius in kilometers

		$d_lat = deg2rad( $lat2 - $lat1 );
		$d_lon = deg2rad( $lon2 - $lon1 );

		$a = sin( $d_lat / 2 ) * sin( $d_lat / 2 ) +
		     cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) *
		     sin( $d_lon / 2 ) * sin( $d_lon / 2 );

		$c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );

		return round( $earth_radius * $c, 1 );
	}

	/**
	 * Compute initial compass bearing from (lat1, lon1) to (lat2, lon2) in degrees [0, 360).
	 */
	public static function calculate_bearing( float $lat1, float $lon1, float $lat2, float $lon2 ): float {
		$lat1_rad = deg2rad( $lat1 );
		$lat2_rad = deg2rad( $lat2 );
		$d_lon    = deg2rad( $lon2 - $lon1 );

		$y = sin( $d_lon ) * cos( $lat2_rad );
		$x = cos( $lat1_rad ) * sin( $lat2_rad ) - sin( $lat1_rad ) * cos( $lat2_rad ) * cos( $d_lon );

		$deg = rad2deg( atan2( $y, $x ) );
		return round( ( $deg + 360 ) % 360, 1 );
	}

	/**
	 * Convert compass bearing in degrees to 8-point compass directions with trilingual labels.
	 */
	public static function bearing_to_compass( float $deg, string $lang = 'en' ): array {
		$points = [
			[ 'code' => 'N',  'label_en' => 'North',     'label_te' => 'ఉత్తరం',   'label_hi' => 'उत्तर' ],
			[ 'code' => 'NE', 'label_en' => 'Northeast', 'label_te' => 'ఈశాన్యం',  'label_hi' => 'ईशान' ],
			[ 'code' => 'E',  'label_en' => 'East',      'label_te' => 'తూర్పు',    'label_hi' => 'पूर्व' ],
			[ 'code' => 'SE', 'label_en' => 'Southeast', 'label_te' => 'ఆగ్నేయం',  'label_hi' => 'आग्नेय' ],
			[ 'code' => 'S',  'label_en' => 'South',     'label_te' => 'దక్షిణం',   'label_hi' => 'दक्षिण' ],
			[ 'code' => 'SW', 'label_en' => 'Southwest', 'label_te' => 'నైరుతి',    'label_hi' => 'नैऋत्य' ],
			[ 'code' => 'W',  'label_en' => 'West',      'label_te' => 'పడమర',     'label_hi' => 'पश्चिम' ],
			[ 'code' => 'NW', 'label_en' => 'Northwest', 'label_te' => 'వాయువ్యం', 'label_hi' => 'वायव्य' ],
		];

		$idx = (int) round( $deg / 45 ) % 8;
		$item = $points[ $idx ];

		$label = $item['label_en'];
		if ( $lang === 'te' ) {
			$label = $item['label_te'];
		} elseif ( $lang === 'hi' ) {
			$label = $item['label_hi'];
		}

		return [
			'code'  => $item['code'],
			'label' => $label,
			'deg'   => $deg,
		];
	}

	/**
	 * Formats a localized distance & direction narrative string.
	 */
	public static function format_direction_sentence( float $distance_km, string $compass_code, string $lang = 'en' ): string {
		$compass = self::bearing_to_compass( self::compass_to_deg( $compass_code ), $lang );
		$dir = $compass['label'];

		if ( $lang === 'te' ) {
			return sprintf( 'ఈ ఆలయం మీరు ఎంచుకున్న ప్రదేశానికి దాదాపు %s కి.మీ %s దిశలో ఉంది.', $distance_km, $dir );
		} elseif ( $lang === 'hi' ) {
			return sprintf( 'यह मंदिर आपके चयनित स्थान से लगभग %s कि.मी %s दिशा में स्थित है।', $distance_km, $dir );
		}

		return sprintf( 'Temple is approximately %s km %s of your selected location.', $distance_km, strtolower( $dir ) );
	}

	private static function compass_to_deg( string $code ): float {
		$map = [ 'N' => 0, 'NE' => 45, 'E' => 90, 'SE' => 135, 'S' => 180, 'SW' => 225, 'W' => 270, 'NW' => 315 ];
		return (float) ( $map[ strtoupper( $code ) ] ?? 0 );
	}

	/**
	 * Automatically seed or synchronize catalog if database has fewer records than catalog.
	 */
	public static function maybe_auto_sync(): void {
		$count = (int) ( wp_count_posts( 'djv_temple' )->publish ?? 0 );
		if ( $count < 20 || isset( $_GET['djv_force_temple_sync'] ) ) {
			self::sync_temples_catalog();
		}
	}

	/**
	 * Synchronize master catalog into WordPress database.
	 */
	public static function sync_temples_catalog(): int {
		if ( ! function_exists( 'djv_get_master_temples_catalog' ) ) {
			require_once DJV_PLUGIN_DIR . 'includes/data-temple-master.php';
		}

		$catalog = djv_get_master_temples_catalog();
		$count   = 0;

		foreach ( $catalog as $slug => $t ) {
			$existing = get_page_by_path( $slug, OBJECT, 'djv_temple' );

			$post_data = [
				'post_title'   => $t['title'],
				'post_name'    => $slug,
				'post_content' => $t['about_en'] . "\n\n" . $t['history_en'] . "\n\n" . $t['purana_en'],
				'post_excerpt' => $t['about_en'],
				'post_status'  => 'publish',
				'post_type'    => 'djv_temple',
			];

			if ( $existing ) {
				$pid = $existing->ID;
				$post_data['ID'] = $pid;
				wp_update_post( $post_data );
			} else {
				$pid = wp_insert_post( $post_data );
			}

			if ( ! $pid || is_wp_error( $pid ) ) {
				continue;
			}

			// Update all Post Meta Fields
			update_post_meta( $pid, '_djv_name_en', $t['title'] );
			update_post_meta( $pid, '_djv_name_te', $t['title_te'] );
			update_post_meta( $pid, '_djv_name_hi', $t['title_hi'] );
			update_post_meta( $pid, '_djv_title_te', $t['title_te'] );
			update_post_meta( $pid, '_djv_title_hi', $t['title_hi'] );
			update_post_meta( $pid, '_djv_deity', $t['deity'] );
			update_post_meta( $pid, '_djv_tradition', $t['tradition'] );
			update_post_meta( $pid, '_djv_category', $t['category'] );
			update_post_meta( $pid, '_djv_state', $t['state'] );
			update_post_meta( $pid, '_djv_district', $t['district'] );
			update_post_meta( $pid, '_djv_city', $t['city'] );
			update_post_meta( $pid, '_djv_address', $t['address'] );
			update_post_meta( $pid, '_djv_pincode', $t['pincode'] );
			update_post_meta( $pid, '_djv_lat', (float) $t['lat'] );
			update_post_meta( $pid, '_djv_lon', (float) $t['lon'] );
			update_post_meta( $pid, '_djv_timings', $t['timings'] );
			update_post_meta( $pid, '_djv_morning_open', $t['morning_open'] ?? '' );
			update_post_meta( $pid, '_djv_morning_close', $t['morning_close'] ?? '' );
			update_post_meta( $pid, '_djv_evening_open', $t['evening_open'] ?? '' );
			update_post_meta( $pid, '_djv_evening_close', $t['evening_close'] ?? '' );
			update_post_meta( $pid, '_djv_about_en', $t['about_en'] );
			update_post_meta( $pid, '_djv_about_te', $t['about_te'] );
			update_post_meta( $pid, '_djv_about_hi', $t['about_hi'] );
			update_post_meta( $pid, '_djv_history_en', $t['history_en'] );
			update_post_meta( $pid, '_djv_history_te', $t['history_te'] );
			update_post_meta( $pid, '_djv_history_hi', $t['history_hi'] );
			update_post_meta( $pid, '_djv_sthala_purana_en', $t['purana_en'] );
			update_post_meta( $pid, '_djv_sthala_purana_te', $t['purana_te'] );
			update_post_meta( $pid, '_djv_sthala_purana_hi', $t['purana_hi'] );
			update_post_meta( $pid, '_djv_railway', $t['railway'] );
			update_post_meta( $pid, '_djv_airport', $t['airport'] );
			update_post_meta( $pid, '_djv_bus_station', $t['bus_station'] );
			update_post_meta( $pid, '_djv_highway', $t['highway'] );
			update_post_meta( $pid, '_djv_website', $t['website'] );
			update_post_meta( $pid, '_djv_contact', $t['contact'] );
			update_post_meta( $pid, '_djv_trust_name', $t['trust'] );
			update_post_meta( $pid, '_djv_dress_code', $t['dress_code'] );
			update_post_meta( $pid, '_djv_verification_status', $t['status'] );
			update_post_meta( $pid, '_djv_official_source', $t['source'] );
			update_post_meta( $pid, '_djv_last_verified', '2026-10-01' );

			// Set taxonomies
			wp_set_object_terms( $pid, $t['deity'], 'djv_deity' );
			wp_set_object_terms( $pid, $t['state'], 'djv_state' );
			wp_set_object_terms( $pid, $t['state'], 'djv_region' );
			wp_set_object_terms( $pid, $t['category'], 'djv_temple_category' );
			wp_set_object_terms( $pid, $t['tradition'], 'djv_tradition' );

			$count++;
		}

		return $count;
	}

	/**
	 * Register admin meta box for djv_temple
	 */
	public static function register_admin_metabox(): void {
		add_meta_box(
			'djv_temple_master_metabox',
			__( '🛕 DJV Temple Master Directory & Location Details', 'djv-core' ),
			[ __CLASS__, 'render_admin_metabox' ],
			'djv_temple',
			'normal',
			'high'
		);
	}

	/**
	 * Render admin meta box
	 */
	public static function render_admin_metabox( WP_Post $post ): void {
		wp_nonce_field( 'djv_temple_meta_save', 'djv_temple_meta_nonce' );

		$id = $post->ID;

		$fields = [
			'name_en'      => get_post_meta( $id, '_djv_name_en', true ) ?: $post->post_title,
			'name_te'      => get_post_meta( $id, '_djv_name_te', true ),
			'name_hi'      => get_post_meta( $id, '_djv_name_hi', true ),
			'state'        => get_post_meta( $id, '_djv_state', true ),
			'district'     => get_post_meta( $id, '_djv_district', true ),
			'city'         => get_post_meta( $id, '_djv_city', true ),
			'address'      => get_post_meta( $id, '_djv_address', true ),
			'pincode'      => get_post_meta( $id, '_djv_pincode', true ),
			'lat'          => get_post_meta( $id, '_djv_lat', true ),
			'lon'          => get_post_meta( $id, '_djv_lon', true ),
			'timings'      => get_post_meta( $id, '_djv_timings', true ),
			'morning_open' => get_post_meta( $id, '_djv_morning_open', true ),
			'morning_close'=> get_post_meta( $id, '_djv_morning_close', true ),
			'evening_open' => get_post_meta( $id, '_djv_evening_open', true ),
			'evening_close'=> get_post_meta( $id, '_djv_evening_close', true ),
			'about_en'     => get_post_meta( $id, '_djv_about_en', true ),
			'about_te'     => get_post_meta( $id, '_djv_about_te', true ),
			'about_hi'     => get_post_meta( $id, '_djv_about_hi', true ),
			'purana_en'    => get_post_meta( $id, '_djv_sthala_purana_en', true ),
			'purana_te'    => get_post_meta( $id, '_djv_sthala_purana_te', true ),
			'purana_hi'    => get_post_meta( $id, '_djv_sthala_purana_hi', true ),
			'railway'      => get_post_meta( $id, '_djv_railway', true ),
			'airport'      => get_post_meta( $id, '_djv_airport', true ),
			'bus_station'  => get_post_meta( $id, '_djv_bus_station', true ),
			'highway'      => get_post_meta( $id, '_djv_highway', true ),
			'website'      => get_post_meta( $id, '_djv_website', true ),
			'contact'      => get_post_meta( $id, '_djv_contact', true ),
			'trust_name'   => get_post_meta( $id, '_djv_trust_name', true ),
			'dress_code'   => get_post_meta( $id, '_djv_dress_code', true ),
			'status'       => get_post_meta( $id, '_djv_verification_status', true ) ?: 'Verified',
			'source'       => get_post_meta( $id, '_djv_official_source', true ),
			'last_verified'=> get_post_meta( $id, '_djv_last_verified', true ) ?: '2026-10-01',
		];
		?>
		<style>
			.djv-meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-bottom: 16px; }
			.djv-meta-card { background: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 8px; }
			.djv-meta-card label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748B; margin-bottom: 4px; }
			.djv-meta-card input, .djv-meta-card textarea, .djv-meta-card select { width: 100%; border: 1px solid #CBD5E1; border-radius: 4px; padding: 6px 8px; font-size: 13px; }
			.djv-sec-title { font-size: 13px; font-weight: 700; color: #7A2419; margin: 18px 0 8px 0; border-bottom: 2px solid #E2E8F0; padding-bottom: 4px; }
		</style>

		<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 6px;">
			<!-- Verification Status Ribbon -->
			<div style="background: #ECFDF5; border: 1px solid #A7F3D0; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
				<div>
					<span style="font-weight: 700; color: #065F46; font-size: 13px;">✓ Verification Status:</span>
					<select name="_djv_verification_status" style="margin-left: 8px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
						<option value="Verified" <?php selected( $fields['status'], 'Verified' ); ?>>Verified Official Record</option>
						<option value="Partially Verified" <?php selected( $fields['status'], 'Partially Verified' ); ?>>Partially Verified</option>
						<option value="Needs Verification" <?php selected( $fields['status'], 'Needs Verification' ); ?>>Needs Verification</option>
					</select>
				</div>
				<div style="font-size: 12px; color: #047857;">
					<strong>Last Verified:</strong>
					<input type="date" name="_djv_last_verified" value="<?php echo esc_attr( $fields['last_verified'] ); ?>" style="padding: 2px 6px;">
				</div>
			</div>

			<!-- Multilingual Names -->
			<div class="djv-sec-title">🪔 Multilingual Names (No Language Mixing)</div>
			<div class="djv-meta-grid">
				<div class="djv-meta-card">
					<label>English Name</label>
					<input type="text" name="_djv_name_en" value="<?php echo esc_attr( $fields['name_en'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Telugu Name (తెలుగు)</label>
					<input type="text" name="_djv_name_te" value="<?php echo esc_attr( $fields['name_te'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Hindi Name (हिन्दी)</label>
					<input type="text" name="_djv_name_hi" value="<?php echo esc_attr( $fields['name_hi'] ); ?>">
				</div>
			</div>

			<!-- Location & Coordinates -->
			<div class="djv-sec-title">📍 Location & Precise GPS Coordinates</div>
			<div class="djv-meta-grid">
				<div class="djv-meta-card">
					<label>State</label>
					<input type="text" name="_djv_state" value="<?php echo esc_attr( $fields['state'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>District</label>
					<input type="text" name="_djv_district" value="<?php echo esc_attr( $fields['district'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>City / Town</label>
					<input type="text" name="_djv_city" value="<?php echo esc_attr( $fields['city'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>PIN Code</label>
					<input type="text" name="_djv_pincode" value="<?php echo esc_attr( $fields['pincode'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Latitude (Float)</label>
					<input type="number" step="0.0001" name="_djv_lat" value="<?php echo esc_attr( $fields['lat'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Longitude (Float)</label>
					<input type="number" step="0.0001" name="_djv_lon" value="<?php echo esc_attr( $fields['lon'] ); ?>">
				</div>
			</div>
			<div class="djv-meta-card" style="margin-bottom: 16px;">
				<label>Full Address</label>
				<input type="text" name="_djv_address" value="<?php echo esc_attr( $fields['address'] ); ?>">
			</div>

			<!-- Darshan & Timings -->
			<div class="djv-sec-title">⏰ Darshan & Daily Timings</div>
			<div class="djv-meta-card" style="margin-bottom: 12px;">
				<label>Timings Summary String</label>
				<input type="text" name="_djv_timings" value="<?php echo esc_attr( $fields['timings'] ); ?>">
			</div>
			<div class="djv-meta-grid">
				<div class="djv-meta-card">
					<label>Morning Opening</label>
					<input type="text" name="_djv_morning_open" value="<?php echo esc_attr( $fields['morning_open'] ); ?>" placeholder="05:00 AM">
				</div>
				<div class="djv-meta-card">
					<label>Morning Closing</label>
					<input type="text" name="_djv_morning_close" value="<?php echo esc_attr( $fields['morning_close'] ); ?>" placeholder="12:30 PM">
				</div>
				<div class="djv-meta-card">
					<label>Evening Opening</label>
					<input type="text" name="_djv_evening_open" value="<?php echo esc_attr( $fields['evening_open'] ); ?>" placeholder="04:00 PM">
				</div>
				<div class="djv-meta-card">
					<label>Evening Closing</label>
					<input type="text" name="_djv_evening_close" value="<?php echo esc_attr( $fields['evening_close'] ); ?>" placeholder="09:30 PM">
				</div>
			</div>

			<!-- Travel & How to Reach -->
			<div class="djv-sec-title">🚗 How to Reach & Transit Hubs</div>
			<div class="djv-meta-grid">
				<div class="djv-meta-card">
					<label>Nearest Railway Station</label>
					<input type="text" name="_djv_railway" value="<?php echo esc_attr( $fields['railway'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Nearest Airport</label>
					<input type="text" name="_djv_airport" value="<?php echo esc_attr( $fields['airport'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Nearest Bus Station</label>
					<input type="text" name="_djv_bus_station" value="<?php echo esc_attr( $fields['bus_station'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Major Highway</label>
					<input type="text" name="_djv_highway" value="<?php echo esc_attr( $fields['highway'] ); ?>">
				</div>
			</div>

			<!-- Official Contact & Portal -->
			<div class="djv-sec-title">📞 Official Contact & Portal</div>
			<div class="djv-meta-grid">
				<div class="djv-meta-card">
					<label>Official Portal URL</label>
					<input type="url" name="_djv_website" value="<?php echo esc_attr( $fields['website'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Contact Phone</label>
					<input type="text" name="_djv_contact" value="<?php echo esc_attr( $fields['contact'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Temple Trust / Devasthanam</label>
					<input type="text" name="_djv_trust_name" value="<?php echo esc_attr( $fields['trust_name'] ); ?>">
				</div>
				<div class="djv-meta-card">
					<label>Official Source Attribution</label>
					<input type="text" name="_djv_official_source" value="<?php echo esc_attr( $fields['source'] ); ?>">
				</div>
			</div>

			<!-- Sthala Purana & History -->
			<div class="djv-sec-title">📜 Sthala Purana & Sacred Lore</div>
			<div style="margin-bottom: 12px;">
				<label style="font-size: 11px; font-weight: 700; color: #64748B;">English Sthala Purana</label>
				<textarea name="_djv_sthala_purana_en" rows="3" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 4px; padding: 6px;"><?php echo esc_textarea( $fields['purana_en'] ); ?></textarea>
			</div>
			<div style="margin-bottom: 12px;">
				<label style="font-size: 11px; font-weight: 700; color: #64748B;">Telugu Sthala Purana (తెలుగు)</label>
				<textarea name="_djv_sthala_purana_te" rows="3" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 4px; padding: 6px;"><?php echo esc_textarea( $fields['purana_te'] ); ?></textarea>
			</div>
			<div style="margin-bottom: 12px;">
				<label style="font-size: 11px; font-weight: 700; color: #64748B;">Hindi Sthala Purana (हिन्दी)</label>
				<textarea name="_djv_sthala_purana_hi" rows="3" style="width: 100%; border: 1px solid #CBD5E1; border-radius: 4px; padding: 6px;"><?php echo esc_textarea( $fields['purana_hi'] ); ?></textarea>
			</div>
		</div>
		<?php
	}

	/**
	 * Save admin meta box
	 */
	public static function save_admin_metabox( int $post_id ): void {
		if ( ! isset( $_POST['djv_temple_meta_nonce'] ) || ! wp_verify_nonce( $_POST['djv_temple_meta_nonce'], 'djv_temple_meta_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$meta_keys = [
			'_djv_name_en'            => 'sanitize_text_field',
			'_djv_name_te'            => 'sanitize_text_field',
			'_djv_name_hi'            => 'sanitize_text_field',
			'_djv_state'              => 'sanitize_text_field',
			'_djv_district'           => 'sanitize_text_field',
			'_djv_city'               => 'sanitize_text_field',
			'_djv_address'            => 'sanitize_text_field',
			'_djv_pincode'            => 'sanitize_text_field',
			'_djv_lat'                => 'floatval',
			'_djv_lon'                => 'floatval',
			'_djv_timings'            => 'sanitize_text_field',
			'_djv_morning_open'       => 'sanitize_text_field',
			'_djv_morning_close'      => 'sanitize_text_field',
			'_djv_evening_open'       => 'sanitize_text_field',
			'_djv_evening_close'      => 'sanitize_text_field',
			'_djv_railway'            => 'sanitize_text_field',
			'_djv_airport'            => 'sanitize_text_field',
			'_djv_bus_station'        => 'sanitize_text_field',
			'_djv_highway'            => 'sanitize_text_field',
			'_djv_website'            => 'esc_url_raw',
			'_djv_contact'            => 'sanitize_text_field',
			'_djv_trust_name'         => 'sanitize_text_field',
			'_djv_dress_code'         => 'sanitize_text_field',
			'_djv_verification_status'=> 'sanitize_text_field',
			'_djv_official_source'    => 'sanitize_text_field',
			'_djv_last_verified'      => 'sanitize_text_field',
			'_djv_sthala_purana_en'   => 'sanitize_textarea_field',
			'_djv_sthala_purana_te'   => 'sanitize_textarea_field',
			'_djv_sthala_purana_hi'   => 'sanitize_textarea_field',
		];

		foreach ( $meta_keys as $key => $sanitizer ) {
			if ( isset( $_POST[ $key ] ) ) {
				$val = call_user_func( $sanitizer, $_POST[ $key ] );
				update_post_meta( $post_id, $key, $val );
			}
		}
	}
}
