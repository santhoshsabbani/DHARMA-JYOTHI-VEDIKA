<?php
/**
 * Single Temple Profile Template (djv_temple)
 *
 * Location-aware, trilingual, comprehensive temple profile.
 * Features semantic Markdown content formatting, structured section cards,
 * factual verification highlight UI, and responsive 1100-1200px desktop layout.
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

// Content selection with trilingual fallback
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

$architecture   = get_post_meta( $post_id, '_djv_architecture', true );
$visiting_guide = get_post_meta( $post_id, '_djv_visiting_guide', true );
$scripture      = get_post_meta( $post_id, '_djv_scripture_reference', true );

// Transit & How to Reach
$railway     = get_post_meta( $post_id, '_djv_railway', true );
$airport     = get_post_meta( $post_id, '_djv_airport', true );
$bus_station = get_post_meta( $post_id, '_djv_bus_station', true );
$highway     = get_post_meta( $post_id, '_djv_highway', true );

// Contact & Verification Status
$website     = get_post_meta( $post_id, '_djv_website', true );
$contact     = get_post_meta( $post_id, '_djv_contact', true );
$trust       = get_post_meta( $post_id, '_djv_trust_name', true );
$dress_code  = get_post_meta( $post_id, '_djv_dress_code', true );

$raw_status  = get_post_meta( $post_id, '_djv_verification_status', true );
$is_verified = ( strtolower( trim( (string) $raw_status ) ) === 'verified' );

$source      = get_post_meta( $post_id, '_djv_official_source', true );
$verified_on = get_post_meta( $post_id, '_djv_last_verified', true ) ?: '2026-10-08';
$source_name = get_post_meta( $post_id, '_djv_source_name', true );
$source_url  = get_post_meta( $post_id, '_djv_source_url', true );

$loc_string = $city ?: ( $district ?: $state );
if ( $loc_string && $state && $loc_string !== $state ) {
	$loc_string .= ', ' . $state;
}
?>

<style>
/* DJV Temple Single Page Responsive & Typography System */
.single-temple-wrapper {
  padding: 2rem 0 5rem 0;
  background: var(--clr-bg, #FAF8F5);
  color: var(--clr-text, #1E293B);
  font-family: var(--font-body, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
}

.djv-temple-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.25rem;
}

.djv-temple-breadcrumb {
  font-size: 0.8125rem;
  color: var(--clr-text-muted, #64748B);
  margin-bottom: 1.25rem;
}
.djv-temple-breadcrumb a {
  color: inherit;
  text-decoration: none;
}
.djv-temple-breadcrumb a:hover {
  color: var(--clr-primary, #7A2419);
  text-decoration: underline;
}

/* Two-column 70/30 responsive layout */
.djv-temple-layout-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 340px;
  gap: 2.25rem;
  align-items: start;
}

@media (max-width: 1024px) {
  .djv-temple-layout-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}

/* Temple Header Card */
.djv-temple-header-card {
  background: #FFF;
  border: 1px solid var(--clr-border, #E2E8F0);
  border-radius: 1rem;
  padding: 2.25rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  margin-bottom: 2rem;
}

.djv-temple-meta-badges {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.djv-category-pill {
  background: #FEF3C7;
  color: #92400E;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.3rem 0.75rem;
  border-radius: 9999px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}

.djv-location-pill {
  font-size: 0.875rem;
  font-weight: 700;
  color: var(--clr-accent, #C89432);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.djv-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.3rem 0.75rem;
  border-radius: 9999px;
  letter-spacing: 0.02em;
}

.djv-status-verified {
  background: #DCFCE7;
  color: #166534;
  border: 1px solid #BBF7D0;
}

.djv-status-unverified {
  background: #FEF3C7;
  color: #92400E;
  border: 1px solid #FDE68A;
}

.djv-temple-h1 {
  font-family: var(--font-heading, Georgia, serif);
  font-size: 2.6rem;
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
  line-height: 1.25;
  margin: 0.25rem 0 0.75rem 0;
}

@media (max-width: 768px) {
  .djv-temple-h1 {
    font-size: 1.95rem;
    line-height: 1.3;
  }
}

.djv-temple-alt-names {
  font-size: 1rem;
  color: #475569;
  margin-bottom: 1rem;
}

.djv-temple-deity-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: center;
  font-size: 1rem;
  color: var(--clr-accent, #C89432);
  font-weight: 600;
  padding-top: 0.75rem;
  border-top: 1px solid #F1F5F9;
}
.djv-temple-deity-bar strong {
  color: var(--clr-primary, #7A2419);
}

/* Featured Media */
.djv-temple-featured-media {
  margin-bottom: 2rem;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* Dynamic Live Distance Card */
.djv-live-distance-card {
  background: #F0F9FF;
  border: 1px solid #BAE6FD;
  border-radius: 1rem;
  padding: 1.35rem 1.65rem;
  margin-bottom: 2rem;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

/* Structured Section Cards */
.djv-temple-section {
  background: #FFF;
  border: 1px solid var(--clr-border, #E2E8F0);
  border-radius: 1rem;
  padding: 2.25rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}

.djv-temple-section-purana {
  background: #FFFBF5;
  border-left: 5px solid var(--clr-accent, #C89432);
}

.djv-temple-section-guide {
  background: #F8FCF9;
  border-left: 5px solid #16A34A;
}

.djv-temple-section-scripture {
  background: #FEFCF6;
  border-left: 5px solid #D97706;
}

.djv-temple-section-header {
  margin-bottom: 1.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
}

.djv-temple-section-eyebrow {
  display: block;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--clr-accent, #C89432);
  margin-bottom: 0.25rem;
}

.djv-temple-section-title {
  font-family: var(--font-heading, Georgia, serif);
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
  margin: 0;
  line-height: 1.3;
}

@media (max-width: 768px) {
  .djv-temple-section {
    padding: 1.5rem;
  }
  .djv-temple-section-title {
    font-size: 1.45rem;
  }
}

/* Temple Content Body & Typography */
.djv-temple-body {
  font-size: 1.0625rem; /* ~17px desktop */
  line-height: 1.78;
  color: #1E293B;
}

@media (max-width: 768px) {
  .djv-temple-body {
    font-size: 1rem; /* 16px mobile */
    line-height: 1.7;
  }
}

/* Headings generated by Markdown parser */
.djv-temple-heading {
  font-family: var(--font-heading, Georgia, serif);
  color: var(--clr-primary, #7A2419);
  line-height: 1.35;
  margin: 1.65rem 0 0.75rem 0;
}
.djv-heading-h2 {
  font-size: 1.625rem;
  font-weight: 800;
  border-bottom: 2px solid #F8ECE1;
  padding-bottom: 0.35rem;
}
.djv-heading-h3 {
  font-size: 1.25rem;
  font-weight: 700;
}

.djv-temple-para {
  margin: 0 0 1.25rem 0;
  line-height: 1.78;
}
.djv-temple-para:last-child {
  margin-bottom: 0;
}

/* Clean Bullet List UI */
.djv-temple-list {
  list-style: none;
  padding-left: 0;
  margin: 1rem 0 1.5rem 0;
}

.djv-temple-list > li {
  position: relative;
  padding-left: 1.65rem;
  margin-bottom: 0.85rem;
  line-height: 1.75;
}

.djv-temple-list > li::before {
  content: "•";
  position: absolute;
  left: 0.35rem;
  color: var(--clr-primary, #7A2419);
  font-weight: 800;
  font-size: 1.35rem;
  line-height: 1.35;
}

/* Clean Ordered List UI */
.djv-temple-olist {
  padding-left: 1.35rem;
  margin: 1rem 0 1.5rem 0;
}

.djv-temple-olist > li {
  margin-bottom: 0.85rem;
  line-height: 1.75;
}

.djv-temple-body strong {
  font-weight: 700;
  color: #0F172A;
}

/* Sidebar Factual Checklist Card */
.djv-checklist-card {
  background: #FFF;
  border: 1px solid var(--clr-border, #E2E8F0);
  border-radius: 1rem;
  padding: 1.75rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  margin-bottom: 1.75rem;
}

.djv-checklist-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.15rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #F1F5F9;
}

.djv-checklist-title {
  font-family: var(--font-heading, Georgia, serif);
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
  margin: 0;
}

.djv-checklist-items {
  list-style: none;
  padding: 0;
  margin: 0 0 1.25rem 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.djv-check-item {
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
  font-size: 0.9375rem;
  line-height: 1.5;
  color: #334155;
}

.djv-check-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #DCFCE7;
  color: #166534;
  font-size: 0.75rem;
  font-weight: 800;
  flex-shrink: 0;
  margin-top: 2px;
}

.djv-checklist-badge-wrap {
  padding-top: 1rem;
  border-top: 1px dashed #E2E8F0;
}

/* Sidebar Darshan & Kshetra Essentials */
.djv-sidebar-card {
  background: #FFF;
  border: 1px solid var(--clr-border, #E2E8F0);
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  margin-bottom: 1.75rem;
}

.djv-sidebar-label {
  font-size: 0.75rem;
  font-weight: 800;
  color: var(--clr-text-muted, #64748B);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 0.4rem;
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.djv-sidebar-val {
  font-size: 0.95rem;
  color: #1E293B;
  line-height: 1.5;
}
</style>

<div class="single-temple-wrapper">
  <div class="djv-temple-container">

    <!-- Breadcrumb -->
    <nav class="djv-temple-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>"><?php esc_html_e( 'Sacred Temples', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php echo esc_html( $title ); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" class="temple-profile-article">

        <!-- Top Temple Header Card -->
        <header class="djv-temple-header-card">
          <div class="djv-temple-meta-badges">
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
              <?php if ( $category ) : ?>
                <span class="djv-category-pill">
                  🛕 <?php echo esc_html( $category ); ?>
                </span>
              <?php endif; ?>
              <?php if ( $loc_string ) : ?>
                <span class="djv-location-pill">
                  📍 <?php echo esc_html( $loc_string ); ?>
                </span>
              <?php endif; ?>
            </div>

            <!-- Verification Status Badge -->
            <div>
              <?php if ( $is_verified ) : ?>
                <span class="djv-status-badge djv-status-verified">
                  ✓ <?php esc_html_e( 'Verified Official Record', 'djv-theme' ); ?>
                </span>
              <?php else : ?>
                <span class="djv-status-badge djv-status-unverified">
                  ℹ️ <?php esc_html_e( 'Needs Verification · Source Available', 'djv-theme' ); ?>
                </span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Temple H1 -->
          <h1 class="djv-temple-h1">
            <?php echo esc_html( $title ); ?>
          </h1>

          <!-- Alternate / Trilingual Names -->
          <?php if ( $lang === 'en' && ( $name_te || $name_hi ) ) : ?>
            <div class="djv-temple-alt-names">
              <?php if ( $name_te ) : ?>
                <span style="font-family: var(--font-telugu, inherit); margin-right: 1.25rem;">తెలుగు: <strong><?php echo esc_html( $name_te ); ?></strong></span>
              <?php endif; ?>
              <?php if ( $name_hi ) : ?>
                <span>हिन्दी: <strong><?php echo esc_html( $name_hi ); ?></strong></span>
              <?php endif; ?>
            </div>
          <?php elseif ( $lang === 'te' && $name_en && $name_en !== $title ) : ?>
            <div class="djv-temple-alt-names">
              <span>English: <strong><?php echo esc_html( $name_en ); ?></strong></span>
            </div>
          <?php elseif ( $lang === 'hi' && $name_en && $name_en !== $title ) : ?>
            <div class="djv-temple-alt-names">
              <span>English: <strong><?php echo esc_html( $name_en ); ?></strong></span>
            </div>
          <?php endif; ?>

          <!-- Deity and Tradition Subheading -->
          <div class="djv-temple-deity-bar">
            <?php if ( $deity ) : ?>
              <span>🕉 <?php esc_html_e( 'Presiding Deity:', 'djv-theme' ); ?> <strong><?php echo esc_html( $deity ); ?></strong></span>
            <?php endif; ?>
            <?php if ( $tradition ) : ?>
              <span>📜 <?php esc_html_e( 'Tradition:', 'djv-theme' ); ?> <span style="color: #475569;"><?php echo esc_html( $tradition ); ?></span></span>
            <?php endif; ?>
          </div>
        </header>

        <!-- Main Layout Grid (70% Content / 30% Sidebar) -->
        <div class="djv-temple-layout-grid">

          <!-- Left / Main Content Column (70-75%) -->
          <div class="djv-temple-main-column">

            <!-- Featured Media -->
            <?php if ( has_post_thumbnail() ) : ?>
              <div class="djv-temple-featured-media">
                <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%; height:auto; display:block; max-height: 480px; object-fit: cover;' ] ); ?>
              </div>
            <?php endif; ?>

            <!-- Dynamic Location-Aware Navigation Card -->
            <?php if ( $lat && $lon ) : ?>
              <div id="live-distance-container"
                   class="djv-live-distance-card"
                   data-temple-lat="<?php echo esc_attr( $lat ); ?>"
                   data-temple-lon="<?php echo esc_attr( $lon ); ?>"
                   data-temple-name="<?php echo esc_attr( $title ); ?>">
                <div>
                  <div style="font-size: 0.75rem; font-weight: 800; color: #0284C7; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.25rem;">
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
                  <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $lat . ',' . $lon ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.75rem 1.35rem; background: var(--clr-primary, #7A2419); color: #FFF; border-radius: 0.5rem; font-weight: 700; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    🗺️ <?php esc_html_e( 'Get Directions', 'djv-theme' ); ?> ↗
                  </a>
                </div>
              </div>
            <?php endif; ?>

            <!-- SECTION 1: ABOUT THE TEMPLE -->
            <?php if ( ! empty( $about ) ) : ?>
              <section class="djv-temple-section">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow"><?php esc_html_e( 'About The Temple', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title"><?php esc_html_e( 'Introduction & Sanctity', 'djv-theme' ); ?></h2>
                </div>
                <div class="djv-temple-body">
                  <?php echo djv_format_temple_markdown( $about ); ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- SECTION 2: STHALA PURANA / TEMPLE STORY -->
            <?php if ( ! empty( $purana ) ) : ?>
              <section class="djv-temple-section djv-temple-section-purana">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow"><?php esc_html_e( 'Temple Story', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title">📜 <?php esc_html_e( 'Sthala Purana & Sacred Lore', 'djv-theme' ); ?></h2>
                </div>
                <div class="djv-temple-body">
                  <?php echo djv_format_temple_markdown( $purana ); ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- SECTION 3: HISTORICAL SIGNIFICANCE -->
            <?php if ( ! empty( $history ) && $history !== $purana ) : ?>
              <section class="djv-temple-section">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow"><?php esc_html_e( 'Historical Significance', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title">🏛️ <?php esc_html_e( 'History & Heritage', 'djv-theme' ); ?></h2>
                </div>
                <div class="djv-temple-body">
                  <?php echo djv_format_temple_markdown( $history ); ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- SECTION 4: ARCHITECTURE & DESIGN -->
            <?php if ( ! empty( $architecture ) && $architecture !== $history ) : ?>
              <section class="djv-temple-section">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow"><?php esc_html_e( 'Architecture', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title">🏗️ <?php esc_html_e( 'Architectural Marvel & Design', 'djv-theme' ); ?></h2>
                </div>
                <div class="djv-temple-body">
                  <?php echo djv_format_temple_markdown( $architecture ); ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- SECTION 5: PILGRIM VISITING GUIDE -->
            <?php if ( ! empty( $visiting_guide ) ) : ?>
              <section class="djv-temple-section djv-temple-section-guide">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow" style="color: #166534;"><?php esc_html_e( 'Visiting Guide', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title" style="color: #14532D;">🧭 <?php esc_html_e( 'Pilgrim Visiting Guide & Darshan Info', 'djv-theme' ); ?></h2>
                </div>
                <div class="djv-temple-body">
                  <?php echo djv_format_temple_markdown( $visiting_guide ); ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- SECTION 6: SCRIPTURAL REFERENCES -->
            <?php if ( ! empty( $scripture ) ) : ?>
              <section class="djv-temple-section djv-temple-section-scripture">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow" style="color: #92400E;"><?php esc_html_e( 'Scriptural References', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title" style="color: #78350F;">📖 <?php esc_html_e( 'Mention in Sacred Hindu Scriptures', 'djv-theme' ); ?></h2>
                </div>
                <div class="djv-temple-body">
                  <?php echo djv_format_temple_markdown( $scripture ); ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- SECTION 7: TRANSIT & HOW TO REACH -->
            <?php if ( $railway || $airport || $bus_station || $highway ) : ?>
              <section class="djv-temple-section">
                <div class="djv-temple-section-header">
                  <span class="djv-temple-section-eyebrow"><?php esc_html_e( 'Transit & Connectivity', 'djv-theme' ); ?></span>
                  <h2 class="djv-temple-section-title">🚗 <?php esc_html_e( 'How to Reach the Kshetra', 'djv-theme' ); ?></h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                  <?php if ( $railway ) : ?>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1.15rem; border-radius: 0.5rem;">
                      <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">🚆 <?php esc_html_e( 'Nearest Railway Station', 'djv-theme' ); ?></div>
                      <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $railway ); ?></div>
                    </div>
                  <?php endif; ?>
                  <?php if ( $airport ) : ?>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1.15rem; border-radius: 0.5rem;">
                      <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">✈️ <?php esc_html_e( 'Nearest Airport', 'djv-theme' ); ?></div>
                      <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $airport ); ?></div>
                    </div>
                  <?php endif; ?>
                  <?php if ( $bus_station ) : ?>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1.15rem; border-radius: 0.5rem;">
                      <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">🚌 <?php esc_html_e( 'Nearest Bus Stand', 'djv-theme' ); ?></div>
                      <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $bus_station ); ?></div>
                    </div>
                  <?php endif; ?>
                  <?php if ( $highway ) : ?>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1.15rem; border-radius: 0.5rem;">
                      <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">🛣️ <?php esc_html_e( 'Major Highway Route', 'djv-theme' ); ?></div>
                      <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.35rem;"><?php echo esc_html( $highway ); ?></div>
                    </div>
                  <?php endif; ?>
                </div>
              </section>
            <?php endif; ?>

            <!-- Devasthanam Administration Banner -->
            <?php if ( $website || $contact || $trust ) : ?>
              <section style="background: #FFF; border: 1px solid var(--clr-border, #E2E8F0); border-radius: 1rem; padding: 1.75rem; margin-bottom: 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                <div>
                  <div style="font-size: 0.75rem; font-weight: 800; color: var(--clr-accent, #C89432); text-transform: uppercase; letter-spacing: 0.05em;">
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
                    <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.75rem 1.35rem; background: var(--clr-secondary, #C89432); color: #FFF; font-weight: 700; border-radius: 0.5rem; text-decoration: none;">
                      🌐 <?php esc_html_e( 'Official Portal', 'djv-theme' ); ?> ↗
                    </a>
                  </div>
                <?php endif; ?>
              </section>
            <?php endif; ?>

            <!-- Data Quality & Attribution Transparency Footer -->
            <div style="font-size: 0.8125rem; color: var(--clr-text-muted, #64748B); padding: 1.25rem; border-radius: 0.75rem; background: #F8FAFC; border: 1px solid #E2E8F0; margin-bottom: 2.5rem; line-height: 1.6;">
              <p style="margin: 0 0 0.4rem 0;">
                <strong><?php esc_html_e( 'Verification Status:', 'djv-theme' ); ?></strong>
                <?php if ( $is_verified ) : ?>
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

          </div>

          <!-- Right / Sidebar Column (25-30%) -->
          <aside class="djv-temple-sidebar-column">

            <!-- Check / Highlight Information Card -->
            <div class="djv-checklist-card">
              <div class="djv-checklist-header">
                <span style="font-size: 1.25rem;">✓</span>
                <h3 class="djv-checklist-title"><?php esc_html_e( 'Temple Highlights & Status', 'djv-theme' ); ?></h3>
              </div>
              <ul class="djv-checklist-items">
                <?php if ( $category ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php echo esc_html( $category ); ?></span>
                  </li>
                <?php endif; ?>
                <?php if ( $deity ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php esc_html_e( 'Main deity:', 'djv-theme' ); ?> <strong><?php echo esc_html( $deity ); ?></strong></span>
                  </li>
                <?php endif; ?>
                <?php if ( $loc_string ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php esc_html_e( 'Temple location available', 'djv-theme' ); ?></span>
                  </li>
                <?php endif; ?>
                <?php if ( ! empty( $visiting_guide ) ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php esc_html_e( 'Visiting information available', 'djv-theme' ); ?></span>
                  </li>
                <?php endif; ?>
                <?php if ( ! empty( $architecture ) ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php esc_html_e( 'Architecture information available', 'djv-theme' ); ?></span>
                  </li>
                <?php endif; ?>
                <?php if ( ! empty( $purana ) ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php esc_html_e( 'Sthala Purana documented', 'djv-theme' ); ?></span>
                  </li>
                <?php endif; ?>
                <?php if ( ! empty( $scripture ) ) : ?>
                  <li class="djv-check-item">
                    <span class="djv-check-icon">✓</span>
                    <span><?php esc_html_e( 'Scripture references available', 'djv-theme' ); ?></span>
                  </li>
                <?php endif; ?>
              </ul>

              <!-- Verification Status Badge -->
              <div class="djv-checklist-badge-wrap">
                <div style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 0.35rem;">
                  <?php esc_html_e( 'Data Record Status', 'djv-theme' ); ?>
                </div>
                <?php if ( $is_verified ) : ?>
                  <span class="djv-status-badge djv-status-verified" style="display: inline-flex;">
                    ✓ <?php esc_html_e( 'Verified Official Record', 'djv-theme' ); ?>
                  </span>
                <?php else : ?>
                  <span class="djv-status-badge djv-status-unverified" style="display: inline-flex;">
                    ℹ️ <?php esc_html_e( 'Needs Verification', 'djv-theme' ); ?>
                  </span>
                  <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.35rem;">
                    <?php esc_html_e( 'Source dataset available · Pending independent audit', 'djv-theme' ); ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Darshan & Seva Timings Card -->
            <div class="djv-sidebar-card">
              <div class="djv-sidebar-label">⏰ <?php esc_html_e( 'Darshan & Seva Timings', 'djv-theme' ); ?></div>
              <?php if ( $timings || ( $morning_open && $morning_close ) ) : ?>
                <div class="djv-sidebar-val" style="font-weight: 700; color: var(--clr-primary, #7A2419);">
                  <?php if ( $timings ) : ?>
                    <?php echo esc_html( $timings ); ?>
                  <?php else : ?>
                    <?php echo esc_html( $morning_open . ' – ' . $morning_close ); ?><br>
                    <?php echo esc_html( $evening_open . ' – ' . $evening_close ); ?>
                  <?php endif; ?>
                </div>
              <?php else : ?>
                <div class="djv-sidebar-val" style="color: #94A3B8; font-style: italic;">
                  <?php esc_html_e( 'General Darshan: Check Visiting Guide', 'djv-theme' ); ?>
                </div>
              <?php endif; ?>
            </div>

            <!-- Kshetra Address Card -->
            <div class="djv-sidebar-card">
              <div class="djv-sidebar-label">🗺️ <?php esc_html_e( 'Kshetra Address', 'djv-theme' ); ?></div>
              <div class="djv-sidebar-val">
                <?php if ( $address ) : ?>
                  <?php echo esc_html( $address ); ?>
                  <?php if ( $pincode ) echo ' - ' . esc_html( $pincode ); ?>
                <?php elseif ( $loc_string ) : ?>
                  <?php echo esc_html( $loc_string ); ?>
                <?php else : ?>
                  <span style="color: #94A3B8; font-style: italic;"><?php esc_html_e( 'Address details pending verification', 'djv-theme' ); ?></span>
                <?php endif; ?>
              </div>
            </div>

            <!-- GPS Coordinates Card -->
            <?php if ( $lat && $lon ) : ?>
              <div class="djv-sidebar-card">
                <div class="djv-sidebar-label">🧭 <?php esc_html_e( 'GPS Coordinates', 'djv-theme' ); ?></div>
                <div class="djv-sidebar-val">
                  <?php echo esc_html( number_format( (float) $lat, 4 ) . '° N, ' . number_format( (float) $lon, 4 ) . '° E' ); ?>
                  <div style="margin-top: 0.5rem;">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $lat . ',' . $lon ); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--clr-primary, #7A2419); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                      ↗ <?php esc_html_e( 'Open in Google Maps', 'djv-theme' ); ?>
                    </a>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- Dress Code Card -->
            <?php if ( $dress_code ) : ?>
              <div class="djv-sidebar-card">
                <div class="djv-sidebar-label">👔 <?php esc_html_e( 'Dress Code', 'djv-theme' ); ?></div>
                <div class="djv-sidebar-val">
                  <?php echo esc_html( $dress_code ); ?>
                </div>
              </div>
            <?php endif; ?>

          </aside>

        </div>

        <!-- Related Sacred Temples -->
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
