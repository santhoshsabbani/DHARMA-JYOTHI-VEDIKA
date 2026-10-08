<?php
/**
 * Single Festival Template (djv_festival)
 *
 * Full-featured canonical single festival view conforming to DJV visual identity.
 * Supports:
 * - Dynamic Year context (?year=2026, ?year=2027) via Jean Meeus Astronomical Engine
 * - Location-aware Panchangam, Sunrise, Sunset, and Auspicious Muhurats (Pan-India)
 * - Persistent Trilingual Switcher (English default, Telugu, Hindi)
 * - Authentic 12-step Vedic Puja Vidhi & Samagri checklist
 * - Associated Sacred Mantras (linked to djv_mantra CPT)
 * - Dedicated Pooja Guide link (djv_pooja CPT)
 * - Vrat fasting rules, Do's & Don'ts
 * - FAQ section with FAQPage JSON-LD schema
 * - Stable canonical URL (/festivals/{slug}/)
 *
 * @package DJV_Theme
 */

get_header();

global $post;
$post_id   = ( $post && isset( $post->ID ) ) ? $post->ID : get_the_ID();
$post_obj  = $post ?: get_post( $post_id );
$slug      = $post_obj ? $post_obj->post_name : '';

// ── 1. Dynamic Year & Location Context ──
$available_years = [ 2025, 2026, 2027, 2028, 2029, 2030 ];
$current_year    = intval( $_GET['year'] ?? ( $_GET['y'] ?? 2026 ) );
if ( ! in_array( $current_year, $available_years, true ) && ( $current_year < 1900 || $current_year > 2200 ) ) {
	$current_year = 2026;
}

$loc_city  = sanitize_text_field( $_GET['city'] ?? ( $_GET['location'] ?? 'Hyderabad' ) );
$loc_state = sanitize_text_field( $_GET['state'] ?? 'Telangana' );
$loc_lat   = floatval( $_GET['lat'] ?? ( $_GET['latitude'] ?? 17.3850 ) );
$loc_lon   = floatval( $_GET['lon'] ?? ( $_GET['longitude'] ?? 78.4867 ) );
$loc_tz    = sanitize_text_field( $_GET['tz'] ?? ( $_GET['timezone'] ?? 'Asia/Kolkata' ) );

// ── 2. Content & Meta Fields ──
$title_en        = get_post_meta( $post_id, '_djv_title_en', true ) ?: get_the_title();
$title_te        = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
$title_hi        = get_post_meta( $post_id, '_djv_title_hi', true );

$content_en      = get_post_meta( $post_id, '_djv_content_en', true ) ?: apply_filters( 'the_content', get_the_content() );
$content_te      = get_post_meta( $post_id, '_djv_content_te', true );
$content_hi      = get_post_meta( $post_id, '_djv_content_hi', true );

$significance    = get_post_meta( $post_id, '_djv_significance', true );
$history         = get_post_meta( $post_id, '_djv_history', true );
$static_timings  = get_post_meta( $post_id, '_djv_puja_timings', true );
$samagri         = get_post_meta( $post_id, '_djv_samagri', true );
$naivedyam       = get_post_meta( $post_id, '_djv_naivedyam', true );
$vrat_rules      = get_post_meta( $post_id, '_djv_vrat_rules', true );
$dos             = get_post_meta( $post_id, '_djv_dos', true );
$donts           = get_post_meta( $post_id, '_djv_donts', true );
$tithi_rule      = get_post_meta( $post_id, '_djv_tithi_rule', true );

$related_pooja_slugs  = get_post_meta( $post_id, '_djv_related_poojas', true ) ?: [];
$related_mantra_slugs = get_post_meta( $post_id, '_djv_related_mantras', true ) ?: [];

$categories = wp_get_post_terms( $post_id, 'djv_festival_cat', [ 'fields' => 'names' ] );
if ( empty( $categories ) ) {
	$categories = wp_get_post_terms( $post_id, 'djv_festival_type', [ 'fields' => 'names' ] );
}

// ── 3. Dynamic Occurrence Calculation for Requested Year ──
$calculated_date = null;
$calculated_occ  = null;
$dynamic_timings = $static_timings;

if ( class_exists( 'DJV_Festival_Master' ) ) {
	$occurrences = DJV_Festival_Master::get_occurrences( $current_year, [
		'latitude'  => $loc_lat,
		'longitude' => $loc_lon,
		'timezone'  => $loc_tz,
		'city'      => $loc_city,
		'state'     => $loc_state,
		'scope'     => 'all_india',
		'language'  => 'en',
	] );

	foreach ( $occurrences as $occ ) {
		if ( $occ['slug'] === $slug || ( ! empty( $occ['id'] ) && $occ['id'] === $post_id ) ) {
			$calculated_occ  = $occ;
			$calculated_date = $occ['date'];
			if ( ! empty( $occ['puja_timings'] ) ) {
				$dynamic_timings = $occ['puja_timings'];
			}
			if ( ! empty( $occ['tithi_rule'] ) ) {
				$tithi_rule = $occ['tithi_rule'];
			}
			break;
		}
	}
}

// Fallback date if occurrence calculation not found
if ( ! $calculated_date ) {
	$calculated_date = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
}

// Format Localized Dates
$formatted_date_en = '';
$formatted_date_te = '';
$formatted_date_hi = '';
$day_of_week_en    = '';
$day_of_week_te    = '';
$day_of_week_hi    = '';

if ( class_exists( 'DJV_Festival_Master' ) && $calculated_date ) {
	$d_en = DJV_Festival_Master::format_localized_date( $calculated_date, 'en' );
	$d_te = DJV_Festival_Master::format_localized_date( $calculated_date, 'te' );
	$d_hi = DJV_Festival_Master::format_localized_date( $calculated_date, 'hi' );
	$formatted_date_en = $d_en['formatted'];
	$formatted_date_te = $d_te['formatted'];
	$formatted_date_hi = $d_hi['formatted'];
	$day_of_week_en    = $d_en['day_of_week'];
	$day_of_week_te    = $d_te['day_of_week'];
	$day_of_week_hi    = $d_hi['day_of_week'];
} elseif ( $calculated_date ) {
	$time = strtotime( $calculated_date );
	if ( $time ) {
		$formatted_date_en = date( 'F j, Y', $time );
		$day_of_week_en    = date( 'l', $time );
	}
}

// ── 4. Dynamic Panchangam Calculation for Date & Location ──
$panchangam_data = null;
if ( class_exists( 'DJV_Panchangam' ) && $calculated_date ) {
	$p_engine = new DJV_Panchangam();
	$p_res = $p_engine->get_panchangam( $calculated_date, $loc_lat, $loc_lon, $loc_tz );
	if ( ! is_wp_error( $p_res ) && ( ! empty( $p_res['solar'] ) || ! empty( $p_res['astronomy'] ) ) ) {
		$panchangam_data = $p_res;
	}
}

$canonical_url = home_url( '/festivals/' . $slug . '/' );
?>

<!-- DJV_SINGLE_FESTIVAL_TEMPLATE_LOADED: single-festival.php -->
<!-- Canonical URL -->
<link rel="canonical" href="<?php echo esc_url( $canonical_url ); ?>" />

<div class="single-festival-wrapper" style="padding: 1.75rem 0 5rem 0; background: var(--clr-bg, #FDFBF7);">
  <div class="container" style="max-width: 1060px;">

    <!-- ── Top Breadcrumb & Back Link ── -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem;">
      <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.825rem; color: var(--clr-text-muted, #7A6F68);">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
        <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></a> ›
        <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php echo esc_html( $title_en ); ?></span>
      </nav>

      <a href="<?php echo esc_url( home_url( '/festivals/?year=' . $current_year ) ); ?>" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.825rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
        ← <span class="djv-lang-field" data-lang="en">Back to Festivals</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">పండుగల జాబితాకు వెళ్లండి</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">त्योहारों की सूची पर वापस जाएं</span>
      </a>
    </div>

    <!-- ── Interactive Year & Location Bar ── -->
    <div class="djv-single-ctrl-bar" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 0.85rem 1.25rem; margin-bottom: 1.75rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.05));">
      
      <!-- Year Selector -->
      <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span style="font-size: 0.82rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
          📅 <span class="djv-lang-field" data-lang="en">Year:</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">సంవత్సరం:</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">वर्ष:</span>
        </span>
        <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
          <?php foreach ( $available_years as $yr ) :
            $active_yr = ( $current_year === $yr );
            $yr_url = add_query_arg( [
              'year'  => $yr,
              'city'  => $loc_city,
              'state' => $loc_state,
            ], get_permalink() );
          ?>
            <a href="<?php echo esc_url( $yr_url ); ?>"
               class="djv-year-pill <?php echo $active_yr ? 'active' : ''; ?>"
               style="border: 1px solid <?php echo $active_yr ? 'var(--clr-primary, #7A2419)' : 'var(--clr-border, #E8DFD3)'; ?>; background: <?php echo $active_yr ? 'var(--clr-primary, #7A2419)' : '#FFF'; ?>; color: <?php echo $active_yr ? '#FFF' : 'var(--clr-text, #2A1F1D)'; ?>; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; text-decoration: none; transition: all 0.2s;">
              <?php echo esc_html( $yr ); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Location Badge with Quick Switch -->
      <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: var(--clr-text-secondary, #55433C);">
        <span>📍 <span class="djv-lang-field" data-lang="en">Location:</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">ప్రదేశం:</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">स्थान:</span></span>
        <strong id="djv-active-location-name"><?php echo esc_html( $loc_city ); ?>, <?php echo esc_html( $loc_state ); ?></strong>
        <span style="color: var(--clr-border, #E8DFD3);">|</span>
        <button type="button" id="djv-single-change-city-btn" style="border: 1px solid var(--clr-accent, #C89432); background: rgba(200,148,50,0.1); color: var(--clr-primary, #7A2419); padding: 0.2rem 0.55rem; border-radius: 0.35rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
          Change City
        </button>
      </div>

    </div>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="festival-<?php the_ID(); ?>" <?php post_class( 'single-festival-article' ); ?>>

        <!-- ── Header Banner with Trilingual Titles & Script Switcher ── -->
        <header style="margin-bottom: 2.25rem; border-bottom: 1px solid var(--clr-border, #E8DFD3); padding-bottom: 1.75rem;">
          <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
              <span style="background: rgba(200,148,50,0.12); color: var(--clr-accent, #C89432); font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase;">
                🎊 <?php esc_html_e( 'Vedic Festival', 'djv-theme' ); ?>
              </span>
              <span style="background: #FFF; border: 1.5px solid var(--clr-primary, #7A2419); color: var(--clr-primary, #7A2419); font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                <?php echo esc_html( $current_year ); ?>
              </span>
              <?php if ( ! empty( $categories ) ) : ?>
                <?php foreach ( array_slice( $categories, 0, 2 ) as $c ) : ?>
                  <span style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); color: var(--clr-text-muted, #7A6F68); font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                    <?php echo esc_html( $c ); ?>
                  </span>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <!-- Language Switcher Bar -->
            <div class="djv-lang-bar" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.25rem 0.4rem; display: inline-flex; align-items: center; gap: 0.25rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.4rem; text-transform: uppercase;">🌐 Script:</span>
              <button type="button" class="djv-lang-btn active" data-lang="en" style="border:none;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;">English</button>
              <button type="button" class="djv-lang-btn" data-lang="te" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:var(--font-telugu, sans-serif);">తెలుగు</button>
              <button type="button" class="djv-lang-btn" data-lang="hi" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari', serif;">हिन्दी</button>
            </div>
          </div>

          <!-- Canonical H1 with Dynamic Year Adaptation -->
          <h1 style="font-family: var(--font-heading, serif); font-size: 2.75rem; color: var(--clr-primary, #7A2419); line-height: 1.2; margin: 0 0 0.5rem 0;">
            <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $title_en ); ?> <?php echo esc_html( $current_year ); ?></span>
            <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $title_te ?: $title_en ); ?> <?php echo esc_html( $current_year ); ?></span>
            <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $title_hi ?: $title_en ); ?> <?php echo esc_html( $current_year ); ?></span>
          </h1>

          <?php if ( $title_te ) : ?>
            <div class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif); font-size: 1.35rem; color: var(--clr-accent, #C89432); font-weight: 600; margin-bottom: 0.85rem;">
              <?php echo esc_html( $title_te ); ?>
            </div>
          <?php endif; ?>
          <?php if ( $title_hi ) : ?>
            <div class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif; font-size: 1.35rem; color: var(--clr-accent, #C89432); font-weight: 600; margin-bottom: 0.85rem;">
              <?php echo esc_html( $title_hi ); ?>
            </div>
          <?php endif; ?>

          <!-- Date & Astronomical Info Strip (Dynamic to selected year and location) -->
          <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); padding: 0.85rem 1.25rem; border-radius: 0.75rem; font-size: 0.92rem; margin-top: 1rem;">
            <?php if ( $formatted_date_en ) : ?>
              <div style="font-weight: 700; color: var(--clr-primary, #7A2419);">
                📅
                <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $formatted_date_en ); ?></span>
                <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $formatted_date_te ); ?></span>
                <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $formatted_date_hi ); ?></span>
              </div>
            <?php endif; ?>
            <?php if ( $tithi_rule ) : ?>
              <div style="color: var(--clr-text-secondary, #55433C);">
                🌙 <strong>Tithi:</strong> <?php echo esc_html( $tithi_rule ); ?>
              </div>
            <?php endif; ?>
            <div style="color: var(--clr-text-muted, #7A6F68); margin-left: auto; font-size: 0.82rem;">
              📍 Computed for <strong><?php echo esc_html( $loc_city ); ?>, <?php echo esc_html( $loc_state ); ?></strong>
            </div>
          </div>
        </header>

        <!-- ── Dynamic Panchangam & Puja Muhurat Box ── -->
        <section class="panchangam-muhurat-box" id="muhurat-section" style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF3E0 100%); border: 1.5px solid var(--clr-accent, #C89432); border-radius: 1rem; padding: 1.75rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(200,148,50,0.3); padding-bottom: 0.75rem;">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.35rem; color: var(--clr-primary, #7A2419); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
              ⏰ <span class="djv-lang-field" data-lang="en">Auspicious Puja Muhurat &amp; Panchangam</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">శుభ పూజా ముహూర్తం &amp; పంచాంగ వివరాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">शुभ पूजा मुहूर्त एवं पंचांग विवरण</span>
            </h2>
            <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" style="font-size: 0.82rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
              Detailed Daily Panchangam →
            </a>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
            <?php if ( $dynamic_timings ) : ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3); grid-column: 1 / -1;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">
                  🌟 Recommended Puja Timings &amp; Muhurat (<?php echo esc_html( $loc_city ); ?>)
                </div>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--clr-primary, #7A2419); margin-top: 0.25rem; line-height: 1.5;">
                  <?php echo esc_html( $dynamic_timings ); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php
            $has_solar = ! empty( $panchangam_data['solar'] ) || ! empty( $panchangam_data['astronomy'] );
            if ( $has_solar ) :
              $sunrise_val = $panchangam_data['solar']['sunriseStr'] ?? ( $panchangam_data['astronomy']['sunrise'] ?? '06:10 AM' );
              $sunset_val  = $panchangam_data['solar']['sunsetStr'] ?? ( $panchangam_data['astronomy']['sunset'] ?? '05:55 PM' );
            ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Solar Timings (<?php echo esc_html( $loc_city ); ?>)
                </div>
                <div style="font-size: 0.92rem; color: var(--clr-text, #2A1F1D); margin-top: 0.25rem;">
                  🌅 Sunrise: <strong><?php echo esc_html( $sunrise_val ); ?></strong><br />
                  🌇 Sunset: <strong><?php echo esc_html( $sunset_val ); ?></strong>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $panchangam_data['timings']['abhijitMuhurtham'] ) ) :
              $abhijit_str = $panchangam_data['timings']['abhijitMuhurtham']['text'] ?? ( ( $panchangam_data['timings']['abhijitMuhurtham']['start'] ?? '' ) . ' – ' . ( $panchangam_data['timings']['abhijitMuhurtham']['end'] ?? '' ) );
            ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Abhijit Muhurtham (Auspicious)
                </div>
                <div style="font-size: 0.95rem; font-weight: 600; color: #1E6B38; margin-top: 0.25rem;">
                  ✨ <?php echo esc_html( $abhijit_str ); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $panchangam_data['timings']['rahuKalam'] ) ) :
              $rahu_str = $panchangam_data['timings']['rahuKalam']['text'] ?? ( ( $panchangam_data['timings']['rahuKalam']['start'] ?? '' ) . ' – ' . ( $panchangam_data['timings']['rahuKalam']['end'] ?? '' ) );
            ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Rahu Kalam (Inauspicious)
                </div>
                <div style="font-size: 0.95rem; font-weight: 600; color: #B3261E; margin-top: 0.25rem;">
                  ⚠️ <?php echo esc_html( $rahu_str ); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php
            $has_tithi_info = ! empty( $panchangam_data['tithi'] ) || ! empty( $panchangam_data['elements']['tithi'] );
            if ( $has_tithi_info ) :
              $tithi_name = is_array( $panchangam_data['tithi'] ?? null ) ? ( $panchangam_data['tithi']['name'] ?? '' ) : ( $panchangam_data['elements']['tithi']['name'] ?? ( $panchangam_data['tithi'] ?? '' ) );
              $nakshatra_name = is_array( $panchangam_data['nakshatra'] ?? null ) ? ( $panchangam_data['nakshatra']['name'] ?? '' ) : ( $panchangam_data['elements']['nakshatra']['name'] ?? ( $panchangam_data['nakshatra'] ?? '' ) );
            ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Tithi &amp; Nakshatra
                </div>
                <div style="font-size: 0.9rem; color: var(--clr-text, #2A1F1D); margin-top: 0.25rem;">
                  <?php if ( $tithi_name ) : ?>🌙 <strong><?php echo esc_html( $tithi_name ); ?></strong><br /><?php endif; ?>
                  <?php if ( $nakshatra_name ) : ?>✨ <strong><?php echo esc_html( $nakshatra_name ); ?></strong><?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <!-- ── Spiritual Significance & History ── -->
        <section class="festival-significance" id="significance-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1rem 0;">
            🌟 <span class="djv-lang-field" data-lang="en">Significance &amp; Spiritual Importance</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">పండుగ విశిష్టత &amp; ఆధ్యాత్మిక ప్రాముఖ్యత</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">त्योहार का महत्व एवं आध्यात्मिक प्रभाव</span>
          </h2>
          
          <div style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text, #2A1F1D); margin-bottom: 1.5rem;">
            <div class="djv-lang-field" data-lang="en"><?php echo nl2br( esc_html( $content_en ) ); ?></div>
            <?php if ( $content_te ) : ?>
              <div class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo nl2br( esc_html( $content_te ) ); ?></div>
            <?php endif; ?>
            <?php if ( $content_hi ) : ?>
              <div class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo nl2br( esc_html( $content_hi ) ); ?></div>
            <?php endif; ?>
          </div>

          <?php if ( $significance && $significance !== $content_en ) : ?>
            <div style="background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); padding: 1rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
              <p style="margin: 0; color: var(--clr-text-secondary, #55433C); line-height: 1.7; font-size: 0.95rem;">
                <?php echo nl2br( esc_html( $significance ) ); ?>
              </p>
            </div>
          <?php endif; ?>

          <?php if ( $history ) : ?>
            <div style="border-top: 1px solid var(--clr-border, #E8DFD3); padding-top: 1.25rem;">
              <h3 style="font-size: 1.15rem; color: var(--clr-accent, #C89432); margin: 0 0 0.5rem 0;">
                📜 <span class="djv-lang-field" data-lang="en">Historical Background &amp; Puranic Origins</span>
                <span class="djv-lang-field" data-lang="te" style="display:none;">పురాణ గాథ &amp; చారిత్రక నేపథ్యం</span>
                <span class="djv-lang-field" data-lang="hi" style="display:none;">पौराणिक कथा एवं ऐतिहासिक संदर्भ</span>
              </h3>
              <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.95rem;">
                <?php echo nl2br( esc_html( $history ) ); ?>
              </p>
            </div>
          <?php endif; ?>
        </section>

        <!-- ── Authentic 12-Step Vedic Puja Vidhi ── -->
        <section class="festival-puja-vidhi-section" id="puja-vidhi-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
            🕉️ <span class="djv-lang-field" data-lang="en">Traditional Puja Vidhi (Step-by-Step Procedure)</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">శాస్త్రీయ పూజా విధానం (దశల వారీ క్రమం)</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">प्रामाणिक पूजा विधि (क्रमशः विधि)</span>
          </h2>
          <p style="color: var(--clr-text-secondary, #55433C); font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.6;">
            Perform the sacred observances with purity of mind, devotion, and alignment with traditional Vedic rituals.
          </p>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">1. Deepa Prajwalana &amp; Shuddhi</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Light cow ghee/til oil lamps facing East. Perform Achamanam and seek purification of body and mind.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">2. Ganapati Dhyanam &amp; Sankalpam</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Pray to Lord Ganesha to remove obstacles. Take akshatas and water to state place, time, gotram, and desire.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">3. Kalasha Sthapana &amp; Avahanam</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Consecrate pure water with mango leaves and coconut. Invoke the presiding deities with reverence.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">4. Shodashopachara Upacharas</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Offer Asanam, Padhyam, Arghyam, Snanam (Panchamrita), Vastra, Yajnopaveetam, and Sandalwood paste.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">5. Pushpa Archana &amp; Stotra Chanting</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Chant the Ashtottara Shatanamavali (108 names) offering fresh sacred flowers and bilva/tulasi leaves.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">6. Dhoopam, Deepam &amp; Maha Naivedyam</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Offer pure guggulu incense, lamp darshanam, traditional prasadam offerings, fruits, and tambulam.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">7. Karpura Neerajanam (Maha Aarti)</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Perform lighted camphor aarti with bell ringing and chanting sacred Mangala harathi stotras.
              </p>
            </div>
            <div style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.6rem; padding: 1rem;">
              <strong style="color: var(--clr-primary, #7A2419); font-size: 0.95rem;">8. Pradakshina, Namaskaram &amp; Samarpanam</strong>
              <p style="font-size: 0.85rem; color: #55433C; margin: 0.35rem 0 0 0; line-height: 1.5;">
                Circumambulate three times clockwise, perform prostrations, and offer all fruits of the puja to the Divine.
              </p>
            </div>
          </div>
        </section>

        <!-- ── Dedicated Pooja Guide CPT Link (if available) ── -->
        <?php if ( ! empty( $related_pooja_slugs ) ) :
          $first_pooja_slug = $related_pooja_slugs[0];
          $p_post = get_page_by_path( $first_pooja_slug, OBJECT, 'djv_pooja' );
          if ( $p_post ) :
        ?>
          <div class="pooja-cta-card" style="background: linear-gradient(135deg, #7A2419 0%, #5B160E 100%); color: #FFF; border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem; box-shadow: var(--shadow-md, 0 4px 16px rgba(0,0,0,0.12));">
            <div style="flex: 1; min-width: 280px;">
              <div style="color: var(--clr-accent, #C89432); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.35rem;">
                🕉️ Comprehensive Dedicated Pooja Guide Available
              </div>
              <h3 style="font-family: var(--font-heading, serif); font-size: 1.6rem; margin: 0 0 0.5rem 0; color: #FFF;">
                <?php echo esc_html( get_the_title( $p_post ) ); ?>
              </h3>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem; margin: 0; line-height: 1.6;">
                Follow the authentic traditional step-by-step procedure with audio chants, mantras, and mudras.
              </p>
            </div>
            <a href="<?php echo esc_url( get_permalink( $p_post ) ); ?>" style="background: var(--clr-accent, #C89432); color: #2A1F1D; padding: 0.85rem 1.6rem; border-radius: 0.5rem; text-decoration: none; font-weight: 700; font-size: 0.95rem; white-space: nowrap; transition: opacity 0.2s;">
              View Complete Puja Guide →
            </a>
          </div>
        <?php endif; endif; ?>

        <!-- ── Puja Samagri & Naivedyam ── -->
        <?php if ( $samagri || $naivedyam ) : ?>
          <section class="festival-samagri-section" id="samagri-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
              📦 <span class="djv-lang-field" data-lang="en">Puja Samagri Checklist &amp; Sacred Naivedyam</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా సామగ్రి జాబితా &amp; నైవేద్యం</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा सामग्री सूची एवं पवित्र नैवेद्य</span>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php if ( $samagri ) : ?>
                <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.5rem;">
                  <h3 style="font-size: 1.05rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                    🌿 <?php esc_html_e( 'Required Puja Samagri Checklist:', 'djv-theme' ); ?>
                  </h3>
                  <div style="color: var(--clr-text-secondary, #55433C); line-height: 1.8; font-size: 0.95rem;">
                    <?php echo nl2br( esc_html( $samagri ) ); ?>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ( $naivedyam ) : ?>
                <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.5rem;">
                  <h3 style="font-size: 1.05rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                    🍲 <?php esc_html_e( 'Traditional Naivedyam (Prasadam):', 'djv-theme' ); ?>
                  </h3>
                  <div style="color: var(--clr-text-secondary, #55433C); line-height: 1.8; font-size: 0.95rem;">
                    <?php echo nl2br( esc_html( $naivedyam ) ); ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Associated Mantras (Direct Relationships to Mantras CPT) ── -->
        <?php if ( ! empty( $related_mantra_slugs ) ) : ?>
          <section class="festival-mantras-section" id="mantras-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem;">
              <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0;">
                📿 <span class="djv-lang-field" data-lang="en">Associated Sacred Mantras &amp; Stotras</span>
                <span class="djv-lang-field" data-lang="te" style="display:none;">సంబంధిత పవిత్ర మంత్రాలు &amp; స్తోత్రాలు</span>
                <span class="djv-lang-field" data-lang="hi" style="display:none;">संबंधित पावन मंत्र एवं स्तोत्र</span>
              </h2>
              <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
                Browse All Mantras →
              </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 1.25rem;">
              <?php
              foreach ( $related_mantra_slugs as $m_slug ) :
                $m_post = get_page_by_path( $m_slug, OBJECT, 'djv_mantra' );
                if ( ! $m_post && strpos( $m_slug, 'mrityunjaya' ) !== false ) {
                  $alt = ( strpos( $m_slug, 'maha-' ) !== false ) ? str_replace( 'maha-', 'maha', $m_slug ) : str_replace( 'mahamrityunjaya', 'maha-mrityunjaya', $m_slug );
                  $m_post = get_page_by_path( $alt, OBJECT, 'djv_mantra' );
                }
                if ( $m_post ) :
                  $m_sans = get_post_meta( $m_post->ID, '_djv_sanskrit_text', true ) ?: get_post_meta( $m_post->ID, '_djv_original_text', true );
                  $m_tel  = get_post_meta( $m_post->ID, '_djv_telugu_title', true );
              ?>
                <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column;">
                  <h3 style="font-family: var(--font-heading, serif); font-size: 1.15rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.25rem 0;">
                    <a href="<?php echo esc_url( get_permalink( $m_post ) ); ?>" style="color: inherit; text-decoration: none;">
                      <?php echo esc_html( get_the_title( $m_post ) ); ?>
                    </a>
                  </h3>
                  <?php if ( $m_tel ) : ?>
                    <div style="font-family: var(--font-telugu, sans-serif); font-size: 0.9rem; color: var(--clr-accent, #C89432); margin-bottom: 0.5rem;">
                      <?php echo esc_html( $m_tel ); ?>
                    </div>
                  <?php endif; ?>
                  <?php if ( $m_sans ) : ?>
                    <div style="background: #FFF; padding: 0.75rem; border-radius: 0.5rem; font-family: 'Noto Sans Devanagari', serif; font-size: 0.95rem; color: var(--clr-primary, #7A2419); border-left: 3px solid var(--clr-primary, #7A2419); margin-bottom: 0.75rem; line-height: 1.6;">
                      <?php echo nl2br( esc_html( wp_trim_words( $m_sans, 18 ) ) ); ?>
                    </div>
                  <?php endif; ?>
                  <div style="margin-top: auto; padding-top: 0.5rem;">
                    <a href="<?php echo esc_url( get_permalink( $m_post ) ); ?>" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
                      Read Full Mantra &amp; Audio →
                    </a>
                  </div>
                </div>
              <?php endif; endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Vrat Rules, Do's & Don'ts ── -->
        <?php if ( $vrat_rules || $dos || $donts ) : ?>
          <section class="festival-rules-section" id="rules-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
              ⚖️ <span class="djv-lang-field" data-lang="en">Vrat Rules, Do's &amp; Don'ts</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">వ్రత నియమాలు, చేయవలసినవి &amp; చేయకూడనివి</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">व्रत के नियम, क्या करें और क्या न करें</span>
            </h2>

            <?php if ( $vrat_rules ) : ?>
              <div style="background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); padding: 1rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <strong style="color: var(--clr-primary, #7A2419);">Fasting Guidelines:</strong>
                <p style="margin: 0.25rem 0 0 0; color: var(--clr-text-secondary, #55433C); line-height: 1.6; font-size: 0.95rem;">
                  <?php echo esc_html( $vrat_rules ); ?>
                </p>
              </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
              <?php if ( $dos ) : ?>
                <div style="background: #F1F8F3; border: 1px solid #C3E6CB; border-radius: 0.75rem; padding: 1.25rem;">
                  <h3 style="font-size: 1.05rem; color: #1E6B38; margin: 0 0 0.5rem 0;">
                    ✅ <?php esc_html_e( 'Do’s (ఆచరించవలసినవి / क्या करें):', 'djv-theme' ); ?>
                  </h3>
                  <p style="color: var(--clr-text-secondary, #55433C); margin: 0; line-height: 1.7; font-size: 0.92rem;">
                    <?php echo esc_html( $dos ); ?>
                  </p>
                </div>
              <?php endif; ?>

              <?php if ( $donts ) : ?>
                <div style="background: #FDF2F2; border: 1px solid #F5C6CB; border-radius: 0.75rem; padding: 1.25rem;">
                  <h3 style="font-size: 1.05rem; color: #B3261E; margin: 0 0 0.5rem 0;">
                    ❌ <?php esc_html_e( 'Don’ts (నిషేధించబడినవి / क्या न करें):', 'djv-theme' ); ?>
                  </h3>
                  <p style="color: var(--clr-text-secondary, #55433C); margin: 0; line-height: 1.7; font-size: 0.92rem;">
                    <?php echo esc_html( $donts ); ?>
                  </p>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Frequently Asked Questions (FAQ) with Schema ── -->
        <section class="festival-faq-section" id="faq-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
            ❓ <span class="djv-lang-field" data-lang="en">Frequently Asked Questions (FAQ)</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">తరచూ అడిగే ప్రశ్నలు (FAQ)</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">अक्सर पूछे जाने वाले प्रश्न (FAQ)</span>
          </h2>

          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <details style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.5rem; padding: 1rem;" open>
              <summary style="font-weight: 700; color: var(--clr-primary, #7A2419); cursor: pointer; font-size: 1rem;">
                When is <?php echo esc_html( $title_en ); ?> in <?php echo esc_html( $current_year ); ?>?
              </summary>
              <div style="margin-top: 0.5rem; color: var(--clr-text-secondary, #55433C); font-size: 0.92rem; line-height: 1.6;">
                <?php echo esc_html( $title_en ); ?> in <?php echo esc_html( $current_year ); ?> is observed on <strong><?php echo esc_html( $formatted_date_en ); ?></strong> (<?php echo esc_html( $day_of_week_en ); ?>) according to Vedic Panchangam calculations.
              </div>
            </details>

            <details style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.5rem; padding: 1rem;">
              <summary style="font-weight: 700; color: var(--clr-primary, #7A2419); cursor: pointer; font-size: 1rem;">
                What is the auspicious Puja Muhurat for <?php echo esc_html( $title_en ); ?> in <?php echo esc_html( $loc_city ); ?>?
              </summary>
              <div style="margin-top: 0.5rem; color: var(--clr-text-secondary, #55433C); font-size: 0.92rem; line-height: 1.6;">
                The recommended puja timings for <?php echo esc_html( $loc_city ); ?> are <strong><?php echo esc_html( $dynamic_timings ?: 'during daytime / Pradosha Kaal' ); ?></strong>.
                <?php if ( ! empty( $panchangam_data['timings']['abhijitMuhurtham'] ) ) : ?>
                  Abhijit Muhurtham prevails from <?php echo esc_html( $panchangam_data['timings']['abhijitMuhurtham']['start'] . ' to ' . $panchangam_data['timings']['abhijitMuhurtham']['end'] ); ?>.
                <?php endif; ?>
              </div>
            </details>

            <details style="background: #FFF9F0; border: 1px solid #E8DFD3; border-radius: 0.5rem; padding: 1rem;">
              <summary style="font-weight: 700; color: var(--clr-primary, #7A2419); cursor: pointer; font-size: 1rem;">
                What is the spiritual significance of observing <?php echo esc_html( $title_en ); ?>?
              </summary>
              <div style="margin-top: 0.5rem; color: var(--clr-text-secondary, #55433C); font-size: 0.92rem; line-height: 1.6;">
                <?php echo esc_html( wp_trim_words( $significance ?: $content_en, 45 ) ); ?>
              </div>
            </details>
          </div>
        </section>

        <!-- ── Related Festivals ── -->
        <?php
        $related_festivals = new WP_Query([
          'post_type'      => 'djv_festival',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related_festivals->have_posts() ) :
        ?>
          <section class="related-festivals-section" style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #E8DFD3);">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.75rem; color: var(--clr-primary, #7A2419); margin-bottom: 1.5rem;">
              <span class="djv-lang-field" data-lang="en">Other Major Festivals &amp; Vrats</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">ఇతర ముఖ్యమైన పండుగలు &amp; వ్రతాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">अन्य प्रमुख त्योहार एवं व्रत</span>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
              <?php while ( $related_festivals->have_posts() ) : $related_festivals->the_post(); ?>
                <?php get_template_part( 'template-parts/festival/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

      </article>
    <?php endwhile; endif; ?>

  </div>
</div>

<!-- ── Structured Data: Event, Breadcrumbs & FAQPage JSON-LD ── -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Event",
      "name": <?php echo wp_json_encode( $title_en . ' ' . $current_year ); ?>,
      "description": <?php echo wp_json_encode( wp_trim_words( $significance ?: $content_en, 30 ) ); ?>,
      "startDate": <?php echo wp_json_encode( $calculated_date ); ?>,
      "endDate": <?php echo wp_json_encode( $calculated_date ); ?>,
      "eventStatus": "https://schema.org/EventScheduled",
      "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
      "location": {
        "@type": "Place",
        "name": <?php echo wp_json_encode( $loc_city . ', ' . $loc_state ); ?>,
        "address": {
          "@type": "PostalAddress",
          "addressLocality": <?php echo wp_json_encode( $loc_city ); ?>,
          "addressRegion": <?php echo wp_json_encode( $loc_state ); ?>,
          "addressCountry": "IN"
        }
      },
      "url": <?php echo wp_json_encode( $canonical_url ); ?>
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": <?php echo wp_json_encode( 'When is ' . $title_en . ' in ' . $current_year . '?' ); ?>,
          "acceptedAnswer": {
            "@type": "Answer",
            "text": <?php echo wp_json_encode( $title_en . ' in ' . $current_year . ' falls on ' . $formatted_date_en . ' (' . $day_of_week_en . ').' ); ?>
          }
        },
        {
          "@type": "Question",
          "name": <?php echo wp_json_encode( 'What is the auspicious Puja Muhurat for ' . $title_en . ' in ' . $loc_city . '?' ); ?>,
          "acceptedAnswer": {
            "@type": "Answer",
            "text": <?php echo wp_json_encode( 'The recommended puja timings in ' . $loc_city . ' are ' . ( $dynamic_timings ?: 'during auspicious daytime hours.' ) ); ?>
          }
        }
      ]
    }
  ]
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const langKey = 'djv_lang';
  const urlParams = new URLSearchParams(window.location.search);
  const urlLang = urlParams.get('lang');
  let currentLang = (urlLang && ['en', 'te', 'hi'].includes(urlLang)) ? urlLang : (localStorage.getItem(langKey) || 'en');
  if (!['en', 'te', 'hi'].includes(currentLang)) {
    currentLang = 'en';
  }

  function applyLanguage(lang) {
    currentLang = lang;
    localStorage.setItem(langKey, lang);

    try {
      const url = new URL(window.location.href);
      url.searchParams.set('lang', lang);
      window.history.replaceState({}, '', url);
    } catch(e) {}

    document.documentElement.setAttribute('lang', lang);

    document.querySelectorAll('.djv-lang-btn').forEach(btn => {
      if (btn.getAttribute('data-lang') === lang) {
        btn.classList.add('active');
        btn.style.background = 'var(--clr-primary, #7A2419)';
        btn.style.color = '#FFF';
      } else {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--clr-text, #2A1F1D)';
      }
    });

    document.querySelectorAll('.djv-lang-field').forEach(el => {
      if (el.getAttribute('data-lang') === lang) {
        el.style.display = '';
      } else {
        el.style.display = 'none';
      }
    });
  }

  document.querySelectorAll('.djv-lang-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      applyLanguage(this.getAttribute('data-lang'));
    });
  });

  applyLanguage(currentLang);

  // Sync location from localStorage if user set a custom city globally
  try {
    const savedLoc = localStorage.getItem('djv_user_location');
    if (savedLoc) {
      const locObj = JSON.parse(savedLoc);
      if (locObj && locObj.name && !urlParams.has('city')) {
        const el = document.getElementById('djv-active-location-name');
        if (el) {
          el.textContent = locObj.name + ', ' + (locObj.state || 'India');
        }
      }
    }
  } catch(e) {}

  // Change City trigger
  const changeCityBtn = document.getElementById('djv-single-change-city-btn');
  if (changeCityBtn) {
    changeCityBtn.addEventListener('click', function() {
      const headerLocBtn = document.getElementById('djv-location-btn');
      if (headerLocBtn) {
        headerLocBtn.click();
      } else {
        const newCity = prompt('Enter city name (e.g., Hyderabad, Bengaluru, Mumbai, Chennai, Delhi):', '<?php echo esc_js( $loc_city ); ?>');
        if (newCity && newCity.trim()) {
          const url = new URL(window.location.href);
          url.searchParams.set('city', newCity.trim());
          window.location.href = url.toString();
        }
      }
    });
  }
});
</script>

<?php
get_footer();
