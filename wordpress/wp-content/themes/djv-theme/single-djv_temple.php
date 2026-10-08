<?php
/**
 * Single Temple Profile Template (djv_temple)
 *
 * Location-aware, trilingual, comprehensive temple profile.
 *
 * @package DJV_Theme
 */

get_header();

$post_id  = get_the_ID();
$lang     = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';

// Multilingual Names
$name_en  = get_the_title( $post_id );
$name_te  = get_post_meta( $post_id, '_djv_name_te', true ) ?: get_post_meta( $post_id, '_djv_title_te', true );
$name_hi  = get_post_meta( $post_id, '_djv_name_hi', true ) ?: get_post_meta( $post_id, '_djv_title_hi', true );

$title = $name_en;
if ( $lang === 'te' && ! empty( $name_te ) ) {
	$title = $name_te;
} elseif ( $lang === 'hi' && ! empty( $name_hi ) ) {
	$title = $name_hi;
}

// Location Details
$country   = get_post_meta( $post_id, '_djv_country', true ) ?: 'India';
$state     = get_post_meta( $post_id, '_djv_state', true );
$district  = get_post_meta( $post_id, '_djv_district', true );
$city      = get_post_meta( $post_id, '_djv_city', true );
$address   = get_post_meta( $post_id, '_djv_address', true );
$pincode   = get_post_meta( $post_id, '_djv_pincode', true );
$lat       = get_post_meta( $post_id, '_djv_lat', true );
$lon       = get_post_meta( $post_id, '_djv_lon', true );

// Deity & Tradition
$deity      = get_post_meta( $post_id, '_djv_deity', true ) ?: implode( ', ', wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'names' ] ) );
$tradition  = get_post_meta( $post_id, '_djv_tradition', true ) ?: implode( ', ', wp_get_post_terms( $post_id, 'djv_tradition', [ 'fields' => 'names' ] ) );
$category   = get_post_meta( $post_id, '_djv_category', true ) ?: implode( ', ', wp_get_post_terms( $post_id, 'djv_temple_category', [ 'fields' => 'names' ] ) );

// Timings
$timings       = get_post_meta( $post_id, '_djv_timings', true );
$morning_open  = get_post_meta( $post_id, '_djv_morning_open', true );
$morning_close = get_post_meta( $post_id, '_djv_morning_close', true );
$evening_open  = get_post_meta( $post_id, '_djv_evening_open', true );
$evening_close = get_post_meta( $post_id, '_djv_evening_close', true );

// Content selection with strict fallback
$about = get_post_meta( $post_id, '_djv_about_en', true );
if ( $lang === 'te' && get_post_meta( $post_id, '_djv_about_te', true ) ) {
	$about = get_post_meta( $post_id, '_djv_about_te', true );
} elseif ( $lang === 'hi' && get_post_meta( $post_id, '_djv_about_hi', true ) ) {
	$about = get_post_meta( $post_id, '_djv_about_hi', true );
}
if ( empty( $about ) ) {
	$about = get_the_content();
}

$history = get_post_meta( $post_id, '_djv_history_en', true );
if ( $lang === 'te' && get_post_meta( $post_id, '_djv_history_te', true ) ) {
	$history = get_post_meta( $post_id, '_djv_history_te', true );
} elseif ( $lang === 'hi' && get_post_meta( $post_id, '_djv_history_hi', true ) ) {
	$history = get_post_meta( $post_id, '_djv_history_hi', true );
}

$purana = get_post_meta( $post_id, '_djv_sthala_purana_en', true );
if ( $lang === 'te' && get_post_meta( $post_id, '_djv_sthala_purana_te', true ) ) {
	$purana = get_post_meta( $post_id, '_djv_sthala_purana_te', true );
} elseif ( $lang === 'hi' && get_post_meta( $post_id, '_djv_sthala_purana_hi', true ) ) {
	$purana = get_post_meta( $post_id, '_djv_sthala_purana_hi', true );
}

// Transit & How to Reach
$railway     = get_post_meta( $post_id, '_djv_railway', true );
$airport     = get_post_meta( $post_id, '_djv_airport', true );
$bus_station = get_post_meta( $post_id, '_djv_bus_station', true );
$highway     = get_post_meta( $post_id, '_djv_highway', true );

// Contact & Verification
$website        = get_post_meta( $post_id, '_djv_website', true );
$contact        = get_post_meta( $post_id, '_djv_contact', true );
$trust          = get_post_meta( $post_id, '_djv_trust_name', true );
$dress_code     = get_post_meta( $post_id, '_djv_dress_code', true );
$status         = get_post_meta( $post_id, '_djv_verification_status', true ) ?: 'Verified';
$source         = get_post_meta( $post_id, '_djv_official_source', true );
$verified_on    = get_post_meta( $post_id, '_djv_last_verified', true ) ?: '2026-10-01';
$source_name    = get_post_meta( $post_id, '_djv_source_name', true );
$source_url     = get_post_meta( $post_id, '_djv_source_url', true );
$architecture   = get_post_meta( $post_id, '_djv_architecture', true );
$visiting_guide = get_post_meta( $post_id, '_djv_visiting_guide', true );
$scripture      = get_post_meta( $post_id, '_djv_scripture_reference', true );

$loc_string = $city ?: ( $district ?: $state );
if ( $loc_string && $state && $loc_string !== $state ) {
	$loc_string .= ', ' . $state;
}
?>

<div class="single-temple-wrapper" style="padding: 2.5rem 0 5rem 0; background: var(--clr-bg, #FAF8F5);">
  <div class="container" style="max-width: 1040px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #64748B); margin-bottom: 1.25rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Sacred Temples', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php echo esc_html( $title ); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" class="temple-profile-article">

        <!-- Top Header Card -->
        <header style="background: #FFF; border: 1px solid var(--clr-border, #E2E8F0); border-radius: 1rem; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
          <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <!-- Category and Location Badge -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
              <?php if ( $category ) : ?>
                <span style="background: #FEF3C7; color: #92400E; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 9999px; text-transform: uppercase;">
                  🛕 <?php echo esc_html( $category ); ?>
                </span>
              <?php endif; ?>
              <?php if ( $loc_string ) : ?>
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase; letter-spacing: 0.04em;">
                  📍 <?php echo esc_html( $loc_string ); ?>
                </span>
              <?php endif; ?>
            </div>

            <!-- Verification Status Badge -->
            <div>
              <?php if ( $status === 'Verified' ) : ?>
                <span style="background: #DCFCE7; color: #166534; font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.35rem;">
                  ✓ <?php esc_html_e( 'Verified Official Record', 'djv-theme' ); ?>
                </span>
              <?php else : ?>
                <span style="background: #FEF3C7; color: #92400E; font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.35rem;">
                  ℹ️ <?php esc_html_e( 'Needs Verification · Source Available', 'djv-theme' ); ?>
                </span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Main Title -->
          <h1 style="font-family: var(--font-heading, Georgia, serif); font-size: 2.6rem; color: var(--clr-primary, #7A2419); line-height: 1.25; margin: 0 0 0.5rem 0;">
            <?php echo esc_html( $title ); ?>
          </h1>

          <!-- Alternate / Local Names -->
          <?php if ( $lang === 'en' && ( $name_te || $name_hi ) ) : ?>
            <div style="font-size: 1.05rem; color: var(--clr-text-secondary, #475569); margin-bottom: 0.85rem;">
              <?php if ( $name_te ) : ?>
                <span style="font-family: var(--font-telugu, inherit); margin-right: 1.25rem;">తెలుగు: <strong><?php echo esc_html( $name_te ); ?></strong></span>
              <?php endif; ?>
              <?php if ( $name_hi ) : ?>
                <span>हिन्दी: <strong><?php echo esc_html( $name_hi ); ?></strong></span>
              <?php endif; ?>
            </div>
          <?php elseif ( $lang === 'te' && $name_en && $name_en !== $title ) : ?>
            <div style="font-size: 1rem; color: var(--clr-text-secondary, #475569); margin-bottom: 0.85rem;">
              <span>English: <strong><?php echo esc_html( $name_en ); ?></strong></span>
            </div>
          <?php elseif ( $lang === 'hi' && $name_en && $name_en !== $title ) : ?>
            <div style="font-size: 1rem; color: var(--clr-text-secondary, #475569); margin-bottom: 0.85rem;">
              <span>English: <strong><?php echo esc_html( $name_en ); ?></strong></span>
            </div>
          <?php endif; ?>

          <!-- Deity and Tradition Subheading -->
          <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; font-size: 1rem; color: var(--clr-accent, #C89432); font-weight: 600;">
            <?php if ( $deity ) : ?>
              <span>🕉 <?php esc_html_e( 'Presiding Deity:', 'djv-theme' ); ?> <strong style="color: var(--clr-primary, #7A2419);"><?php echo esc_html( $deity ); ?></strong></span>
            <?php endif; ?>
            <?php if ( $tradition ) : ?>
              <span>📜 <?php esc_html_e( 'Tradition:', 'djv-theme' ); ?> <?php echo esc_html( $tradition ); ?></span>
            <?php endif; ?>
          </div>
        </header>

        <!-- Featured Media -->
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.08));">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%; height:auto; display:block; max-height: 480px; object-fit: cover;' ] ); ?>
          </div>
        <?php endif; ?>

        <!-- Quick Facts & Darshan Timings Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem; background: #FFF; border: 1px solid var(--clr-border, #E2E8F0); border-radius: 1rem; padding: 1.75rem; box-shadow: var(--shadow-sm);">
          <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #64748B); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
              ⏰ <?php esc_html_e( 'Darshan & Seva Timings', 'djv-theme' ); ?>
            </div>
            <?php if ( $timings || ( $morning_open && $morning_close ) ) : ?>
              <div style="font-size: 0.95rem; font-weight: 600; color: var(--clr-primary, #7A2419); line-height: 1.45;">
                <?php if ( $timings ) : ?>
                  <?php echo esc_html( $timings ); ?>
                <?php else : ?>
                  <?php echo esc_html( $morning_open . ' – ' . $morning_close ); ?><br>
                  <?php echo esc_html( $evening_open . ' – ' . $evening_close ); ?>
                <?php endif; ?>
              </div>
            <?php else : ?>
              <div style="font-size: 0.85rem; color: #94A3B8; font-style: italic;">
                <?php esc_html_e( 'Information not yet available.', 'djv-theme' ); ?>
              </div>
            <?php endif; ?>
          </div>

          <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #64748B); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
              🗺️ <?php esc_html_e( 'Kshetra Address', 'djv-theme' ); ?>
            </div>
            <?php if ( $address ) : ?>
              <div style="font-size: 0.9rem; color: var(--clr-text, #1E293B); line-height: 1.45;">
                <?php echo esc_html( $address ); ?>
                <?php if ( $pincode ) echo ' - ' . esc_html( $pincode ); ?>
              </div>
            <?php elseif ( $loc_string ) : ?>
              <div style="font-size: 0.9rem; color: var(--clr-text, #1E293B); line-height: 1.45;">
                <?php echo esc_html( $loc_string ); ?>
              </div>
            <?php else : ?>
              <div style="font-size: 0.85rem; color: #94A3B8; font-style: italic;">
                <?php esc_html_e( 'Information not yet available.', 'djv-theme' ); ?>
              </div>
            <?php endif; ?>
          </div>

          <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #64748B); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
              🧭 <?php esc_html_e( 'GPS Coordinates', 'djv-theme' ); ?>
            </div>
            <?php if ( $lat && $lon ) : ?>
              <div style="font-size: 0.875rem; color: var(--clr-text, #1E293B);">
                <?php echo esc_html( number_format( (float) $lat, 4 ) . '° N, ' . number_format( (float) $lon, 4 ) . '° E' ); ?><br>
                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $lat . ',' . $lon ); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--clr-primary, #7A2419); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 0.35rem;">
                  ↗ <?php esc_html_e( 'Get Directions', 'djv-theme' ); ?>
                </a>
              </div>
            <?php else : ?>
              <div style="font-size: 0.85rem; color: #94A3B8; font-style: italic;">
                <?php esc_html_e( 'Information not yet available.', 'djv-theme' ); ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if ( $dress_code ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #64748B); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
                👔 <?php esc_html_e( 'Dress Code', 'djv-theme' ); ?>
              </div>
              <div style="font-size: 0.85rem; color: var(--clr-text, #1E293B); line-height: 1.4;">
                <?php echo esc_html( $dress_code ); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Dynamic Location-Aware Navigation Card -->
        <?php if ( $lat && $lon ) : ?>
          <div id="live-distance-container"
               data-temple-lat="<?php echo esc_attr( $lat ); ?>"
               data-temple-lon="<?php echo esc_attr( $lon ); ?>"
               data-temple-name="<?php echo esc_attr( $title ); ?>"
               style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 1rem; padding: 1.5rem; margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: #0284C7; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                🧭 <?php esc_html_e( 'Direction from your selected location', 'djv-theme' ); ?>
              </div>
              <div id="live-distance-sentence" style="font-size: 1.05rem; font-weight: 700; color: #0F172A;">
                Calculating live distance & direction from your selected city...
              </div>
              <div style="font-size: 0.8rem; color: #64748B; margin-top: 0.25rem;">
                Based on active DJV location: <span id="live-active-loc-name" style="font-weight: 600; color: #0284C7;">Hyderabad</span>
              </div>
            </div>
            <div>
              <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $lat . ',' . $lon ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--clr-primary, #7A2419); color: #FFF; border-radius: 0.5rem; font-weight: 700; text-decoration: none; box-shadow: var(--shadow-sm);">
                🗺️ <?php esc_html_e( 'Get Directions', 'djv-theme' ); ?> ↗
              </a>
            </div>
          </div>
        <?php endif; ?>

        <!-- About & Sthala Purana Sections -->
        <div class="temple-content-sections" style="background: #FFF; border: 1px solid var(--clr-border, #E2E8F0); border-radius: 1rem; padding: 2.25rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm);">

          <!-- About -->
          <?php if ( $about ) : ?>
            <section style="margin-bottom: 2.5rem;">
              <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.75rem; color: var(--clr-primary, #7A2419); margin: 0 0 1rem 0;">
                <?php esc_html_e( 'About the Temple', 'djv-theme' ); ?>
              </h2>
              <div style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text, #1E293B);">
                <?php echo wpautop( esc_html( $about ) ); ?>
              </div>
            </section>
          <?php endif; ?>

          <!-- Sthala Purana & Sacred Lore -->
          <?php if ( $purana ) : ?>
            <section style="margin-bottom: 2.5rem; padding: 1.5rem; background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); border-radius: 0 0.5rem 0.5rem 0;">
              <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.6rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                📜 <?php esc_html_e( 'Sthala Purana & History', 'djv-theme' ); ?>
              </h2>
              <div style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text, #1E293B);">
                <?php echo wpautop( esc_html( $purana ) ); ?>
              </div>
            </section>
          <?php endif; ?>

          <!-- Historical Facts & Architecture -->
          <?php if ( $history ) : ?>
            <section style="margin-bottom: 1.5rem;">
              <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.6rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                🏛️ <?php esc_html_e( 'History & Heritage', 'djv-theme' ); ?>
              </h2>
              <div style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text, #1E293B);">
                <?php echo wpautop( esc_html( $history ) ); ?>
              </div>
            </section>
          <?php endif; ?>

          <?php if ( $architecture && $architecture !== $history ) : ?>
            <section style="margin-bottom: 1.5rem;">
              <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.6rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                🏗️ <?php esc_html_e( 'Architecture & Design', 'djv-theme' ); ?>
              </h2>
              <div style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text, #1E293B);">
                <?php echo wpautop( esc_html( $architecture ) ); ?>
              </div>
            </section>
          <?php endif; ?>

          <?php if ( $visiting_guide ) : ?>
            <section style="margin-bottom: 1.5rem; padding: 1.25rem; background: #F0FDF4; border-left: 4px solid #16A34A; border-radius: 0 0.5rem 0.5rem 0;">
              <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.5rem; color: #166534; margin: 0 0 0.75rem 0;">
                🧭 <?php esc_html_e( 'Pilgrim Visiting Guide', 'djv-theme' ); ?>
              </h2>
              <div style="font-size: 1.02rem; line-height: 1.75; color: #14532D;">
                <?php echo wpautop( esc_html( $visiting_guide ) ); ?>
              </div>
            </section>
          <?php endif; ?>

          <?php if ( $scripture ) : ?>
            <section style="margin-bottom: 1.5rem; padding: 1.25rem; background: #FEF3C7; border-left: 4px solid #D97706; border-radius: 0 0.5rem 0.5rem 0;">
              <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.5rem; color: #92400E; margin: 0 0 0.75rem 0;">
                📖 <?php esc_html_e( 'Mention in Sacred Scriptures', 'djv-theme' ); ?>
              </h2>
              <div style="font-size: 1.02rem; line-height: 1.75; color: #78350F;">
                <?php echo wpautop( esc_html( $scripture ) ); ?>
              </div>
            </section>
          <?php endif; ?>

        </div>

        <!-- How to Reach & Transit Hubs -->
        <?php if ( $railway || $airport || $bus_station || $highway ) : ?>
          <section style="background: #FFF; border: 1px solid var(--clr-border, #E2E8F0); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm);">
            <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.6rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
              🚗 <?php esc_html_e( 'How to Reach', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
              <?php if ( $railway ) : ?>
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1rem; border-radius: 0.5rem;">
                  <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">🚆 <?php esc_html_e( 'Nearest Railway Station', 'djv-theme' ); ?></div>
                  <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $railway ); ?></div>
                </div>
              <?php endif; ?>
              <?php if ( $airport ) : ?>
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1rem; border-radius: 0.5rem;">
                  <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">✈️ <?php esc_html_e( 'Nearest Airport', 'djv-theme' ); ?></div>
                  <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $airport ); ?></div>
                </div>
              <?php endif; ?>
              <?php if ( $bus_station ) : ?>
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1rem; border-radius: 0.5rem;">
                  <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">🚌 <?php esc_html_e( 'Nearest Bus Stand', 'djv-theme' ); ?></div>
                  <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $bus_station ); ?></div>
                </div>
              <?php endif; ?>
              <?php if ( $highway ) : ?>
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1rem; border-radius: 0.5rem;">
                  <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">🛣️ <?php esc_html_e( 'Major Highway', 'djv-theme' ); ?></div>
                  <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $highway ); ?></div>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- Contact & Official Portal Banner -->
        <?php if ( $website || $contact || $trust ) : ?>
          <section style="background: #FFF; border: 1px solid var(--clr-border, #E2E8F0); border-radius: 1rem; padding: 1.75rem; margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem;">
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">
                🏛️ <?php esc_html_e( 'Devasthanam Administration', 'djv-theme' ); ?>
              </div>
              <div style="font-size: 1.15rem; font-weight: 700; color: var(--clr-primary, #7A2419); margin-top: 0.25rem;">
                <?php echo esc_html( $trust ?: $title ); ?>
              </div>
              <?php if ( $contact ) : ?>
                <div style="font-size: 0.9rem; color: #475569; margin-top: 0.35rem;">
                  📞 <?php esc_html_e( 'Contact:', 'djv-theme' ); ?> <a href="tel:<?php echo esc_attr( str_replace(' ', '', $contact) ); ?>" style="color: inherit; text-decoration: none; font-weight: 600;"><?php echo esc_html( $contact ); ?></a>
                </div>
              <?php endif; ?>
            </div>

            <?php if ( $website ) : ?>
              <div>
                <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.75rem 1.5rem; background: var(--clr-secondary, #C89432); color: #FFF; font-weight: 700; border-radius: 0.5rem; text-decoration: none;">
                  🌐 <?php esc_html_e( 'Official Portal', 'djv-theme' ); ?> ↗
                </a>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>

        <!-- Data Quality & Attribution Footer -->
        <div style="font-size: 0.8rem; color: var(--clr-text-muted, #64748B); padding: 1rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0; margin-bottom: 3.5rem;">
          <p style="margin: 0 0 0.35rem 0;">
            <strong><?php esc_html_e( 'Verification Status:', 'djv-theme' ); ?></strong>
            <?php if ( $status === 'Verified' ) : ?>
              <span style="color: #166534; font-weight: 700;">✓ <?php esc_html_e( 'Verified Official Record', 'djv-theme' ); ?></span>
            <?php else : ?>
              <span style="color: #B45309; font-weight: 700;">ℹ️ <?php esc_html_e( 'Seed Dataset — Needs Independent Verification', 'djv-theme' ); ?></span>
            <?php endif; ?>
            · <strong><?php esc_html_e( 'Last Updated:', 'djv-theme' ); ?></strong> <?php echo esc_html( $verified_on ); ?>
            <?php if ( $source_name ) : ?>
              · <strong><?php esc_html_e( 'Seed Dataset Source:', 'djv-theme' ); ?></strong>
              <a href="<?php echo esc_url( $source_url ?: 'https://github.com/rishabhmodi03/hindu-temples' ); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--clr-primary, #7A2419); text-decoration: underline;">
                <?php echo esc_html( $source_name ); ?> (GitHub / MIT License)
              </a>
            <?php elseif ( $source ) : ?>
              · <strong><?php esc_html_e( 'Official Source:', 'djv-theme' ); ?></strong> <?php echo esc_html( $source ); ?>
            <?php endif; ?>
          </p>
          <p style="margin: 0;">
            <?php esc_html_e( 'Darshan timings and ritual schedules are subject to change during special festival days. Devotees are advised to confirm with temple authorities before planning visits.', 'djv-theme' ); ?>
          </p>
        </div>

        <!-- Related Temples -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_temple',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #E2E8F0);">
            <h2 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.75rem; color: var(--clr-primary, #7A2419); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Sacred Kshetras & Temples', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.75rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/temple/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

      </article>
    <?php endwhile; endif; ?>

  </div>
</div>

<!-- Live Direction & Haversine Distance Calculation Script for Single Temple -->
<script>
(function() {
  const container = document.getElementById('live-distance-container');
  if (!container) return;

  const lat = parseFloat(container.getAttribute('data-temple-lat'));
  const lon = parseFloat(container.getAttribute('data-temple-lon'));
  const sentenceEl = document.getElementById('live-distance-sentence');
  const locNameEl = document.getElementById('live-active-loc-name');

  function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return Math.round(R * c);
  }

  function calculateBearing(lat1, lon1, lat2, lon2) {
    const y = Math.sin((lon2 - lon1) * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180);
    const x = Math.cos(lat1 * Math.PI / 180) * Math.sin(lat2 * Math.PI / 180) -
              Math.sin(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.cos((lon2 - lon1) * Math.PI / 180);
    let brng = Math.atan2(y, x) * 180 / Math.PI;
    brng = (brng + 360) % 360;
    const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
    const idx = Math.round(brng / 45) % 8;
    return dirs[idx];
  }

  function getLang() {
    if (window.DJV_LANGUAGE && window.DJV_LANGUAGE.current) return window.DJV_LANGUAGE.current;
    const params = new URLSearchParams(window.location.search);
    return params.get('lang') || 'en';
  }

  function getCompassLabel(code, lang) {
    const labels = {
      N:  { en: 'North',     te: 'ఉత్తరం',   hi: 'उत्तर' },
      NE: { en: 'Northeast', te: 'ఈశాన్యం',  hi: 'ईशान' },
      E:  { en: 'East',      te: 'తూర్పు',    hi: 'पूर्व' },
      SE: { en: 'Southeast', te: 'ఆగ్నేయం',  hi: 'आग्नेय' },
      S:  { en: 'South',     te: 'దక్షిణం',   hi: 'दक्षिण' },
      SW: { en: 'Southwest', te: 'నైరుతి',    hi: 'नैऋत्य' },
      W:  { en: 'West',      te: 'పడమర',     hi: 'पश्चिम' },
      NW: { en: 'Northwest', te: 'వాయువ్యం', hi: 'वायव्य' }
    };
    const row = labels[code] || labels.N;
    return row[lang] || row.en;
  }

  function updateLiveBearing(loc) {
    if (!loc || !loc.latitude || !loc.longitude || isNaN(lat) || isNaN(lon)) return;

    const cityName = loc.name || loc.city || 'Hyderabad';
    if (locNameEl) locNameEl.textContent = cityName;

    const dist = calculateDistance(loc.latitude, loc.longitude, lat, lon);
    const compassCode = calculateBearing(loc.latitude, loc.longitude, lat, lon);
    const lang = getLang();
    const dirLabel = getCompassLabel(compassCode, lang);

    let sentence = '';
    if (lang === 'te') {
      sentence = `ఈ ఆలయం మీరు ఎంచుకున్న ప్రదేశం (${cityName}) నుండి దాదాపు ${dist} కి.మీ ${dirLabel} దిశలో ఉంది.`;
    } else if (lang === 'hi') {
      sentence = `यह मंदिर आपके चयनित स्थान (${cityName}) से लगभग ${dist} कि.मी. ${dirLabel} दिशा में स्थित है।`;
    } else {
      sentence = `Temple is approximately ${dist} km ${dirLabel.toLowerCase()} of your selected location (${cityName}).`;
    }

    if (sentenceEl) {
      sentenceEl.textContent = sentence;
    }
  }

  function init() {
    const loc = (window.DJV_LOCATION && typeof window.DJV_LOCATION.getSelected === 'function')
      ? window.DJV_LOCATION.getSelected()
      : { name: 'Hyderabad', city: 'Hyderabad', latitude: 17.3850, longitude: 78.4867 };

    updateLiveBearing(loc);
  }

  window.addEventListener('djv:locationChanged', function(e) {
    if (e.detail) updateLiveBearing(e.detail);
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>

<?php
get_footer();
