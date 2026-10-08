<?php
/**
 * Archive Template for Festivals (djv_festival)
 *
 * Dedicated directory for Hindu & Vedic festivals with live search,
 * trilingual language engine (English default, Telugu, Hindi),
 * Pan-India location-aware filtering, and year calculator (2025–2030).
 *
 * @package DJV_Theme
 */

get_header();

$current_page_year = (int) date( 'Y' );
$initial_lang      = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';

// Fetch initial published festivals for default view
$all_festivals = get_posts([
	'post_type'      => 'djv_festival',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'meta_value',
	'meta_key'       => '_djv_festival_date',
	'order'          => 'ASC',
]);

// Determine upcoming / spotlight festival based on current date
$today_str          = current_time( 'Y-m-d' );
$spotlight_festival = null;
foreach ( $all_festivals as $f ) {
	$f_date = get_post_meta( $f->ID, '_djv_festival_date', true );
	if ( $f_date && $f_date >= $today_str ) {
		$spotlight_festival = $f;
		break;
	}
}
if ( ! $spotlight_festival && ! empty( $all_festivals ) ) {
	$spotlight_festival = $all_festivals[0];
}
?>

<style>
.festival-page-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 42px;
  height: 42px;
  padding: 0 0.85rem;
  border-radius: 9999px;
  border: 1.5px solid var(--clr-border, #E8DFD3);
  background: #FFF;
  color: var(--clr-text, #2A1F1D);
  font-weight: 700;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: var(--shadow-sm, 0 2px 6px rgba(0,0,0,0.04));
  font-family: inherit;
  text-decoration: none;
}
.festival-page-btn:hover {
  border-color: var(--clr-primary, #7A2419);
  color: var(--clr-primary, #7A2419);
  background: var(--clr-bg, #FDFBF7);
  transform: translateY(-1px);
}
.festival-page-btn.active {
  background: var(--clr-primary, #7A2419) !important;
  color: #FFF !important;
  border-color: var(--clr-primary, #7A2419) !important;
  cursor: default;
  box-shadow: 0 4px 12px rgba(122, 36, 25, 0.25);
  transform: none;
}
.festival-page-btn.dots {
  border: none;
  background: transparent;
  cursor: default;
  min-width: 28px;
  box-shadow: none;
  color: var(--clr-text-muted, #7A6F68);
  font-size: 1.1rem;
}
</style>

<div class="archive-festival-wrapper" style="padding: 2rem 0 5rem 0; background: var(--clr-bg, #FDFBF7);">
  <div class="container">

    <!-- ── Breadcrumb ── -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #7A6F68); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;">
        <span class="djv-lang-field" data-lang="en">Home</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">హోమ్</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">होम</span>
      </a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;">
        <span class="djv-lang-field" data-lang="en">Festivals &amp; Vrats</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">పండుగలు &amp; వ్రతాలు</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">त्योहार एवं व्रत</span>
      </span>
    </nav>

    <!-- ── Hero & Trilingual Switcher Header ── -->
    <header class="festival-hero" style="margin-bottom: 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1.5rem; border-bottom: 1px solid var(--clr-border, #E8DFD3); padding-bottom: 1.75rem;">
      <div style="flex: 1; min-width: 300px;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(200,148,50,0.12); color: var(--clr-accent, #C89432); font-weight: 700; font-size: 0.8rem; padding: 0.35rem 0.85rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
          🪔 <span class="djv-lang-field" data-lang="en">Vedic Calendar &amp; Celebrations</span>
          <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);">వేద క్యాలెండర్ &amp; పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;">वैदिक पंचांग एवं प्रमुख पर्व</span>
        </div>

        <!-- Dynamic Trilingual Heading (Requirement 11) -->
        <h1 style="font-family: var(--font-heading, serif); font-size: 2.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0; line-height: 1.2;">
          <span class="djv-lang-field" data-lang="en"><span class="djv-heading-year-val"><?php echo esc_html( $current_page_year ); ?></span> Hindu Festivals</span>
          <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><span class="djv-heading-year-val"><?php echo esc_html( $current_page_year ); ?></span> సంవత్సర హిందూ పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><span class="djv-heading-year-val"><?php echo esc_html( $current_page_year ); ?></span> के हिंदू त्योहार</span>
        </h1>

        <p style="color: var(--clr-text-secondary, #55433C); font-size: 1.05rem; line-height: 1.6; max-width: 760px; margin: 0;">
          <span class="djv-lang-field" data-lang="en">Calculated dynamically according to authentic Vedic tithi, nakshatra, and solar transit rules. Complete puja vidhi, muhurats, and mantras.</span>
          <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);">శాస్త్రీయ పంచాంగ తిథి, నక్షత్రాలు, సౌర పరివర్తనల ఆధారంగా లెక్కించబడిన పండుగలు, పూజా ముహూర్తాలు, విశిష్టత మరియు విధానాలు.</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;">वैदिक पंचांग, तिथि, नक्षत्र एवं सौर संक्रांति के अनुसार गणना किए गए प्रामाणिक त्योहार, शुभ पूजा मुहूर्त एवं संपूर्ण विधि।</span>
        </p>
      </div>

      <!-- Trilingual Selector Bar: English (Default), Telugu, Hindi -->
      <div class="djv-lang-bar" id="djv-festival-lang-bar" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.35rem 0.5rem; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.5rem; text-transform: uppercase;">🌐 Lang:</span>
        <button type="button" class="djv-lang-btn active" data-lang="en" style="border:none;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.4rem 0.85rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;">English</button>
        <button type="button" class="djv-lang-btn" data-lang="te" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.4rem 0.85rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:var(--font-telugu, sans-serif);">తెలుగు</button>
        <button type="button" class="djv-lang-btn" data-lang="hi" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.4rem 0.85rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:'Noto Sans Devanagari', serif;">हिन्दी</button>
      </div>
    </header>

    <!-- ── Spotlight / Upcoming Festival Card ── -->
    <?php if ( $spotlight_festival ) :
      $sp_id       = $spotlight_festival->ID;
      $sp_title_en = get_post_meta( $sp_id, '_djv_title_en', true ) ?: $spotlight_festival->post_title;
      $sp_title_te = get_post_meta( $sp_id, '_djv_title_te', true ) ?: get_post_meta( $sp_id, '_djv_telugu_name', true );
      $sp_title_hi = get_post_meta( $sp_id, '_djv_title_hi', true );
      $sp_desc_en  = get_post_meta( $sp_id, '_djv_description_en', true ) ?: ( $spotlight_festival->post_excerpt ?: $spotlight_festival->post_content );
      $sp_desc_te  = get_post_meta( $sp_id, '_djv_description_te', true ) ?: $sp_desc_en;
      $sp_desc_hi  = get_post_meta( $sp_id, '_djv_description_hi', true ) ?: $sp_desc_en;
      $sp_date     = get_post_meta( $sp_id, '_djv_festival_date', true );
      $sp_timings  = get_post_meta( $sp_id, '_djv_puja_timings', true );
      $sp_tithi    = get_post_meta( $sp_id, '_djv_tithi_rule', true );
      $sp_link     = add_query_arg( 'year', $current_page_year, get_permalink( $sp_id ) );

      $sp_date_en  = $sp_date ? ( class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::format_localized_date( $sp_date, 'en' ) : [ 'formatted' => date( 'F j, Y', strtotime( $sp_date ) ), 'day_of_week' => date( 'l', strtotime( $sp_date ) ) ] ) : [];
      $sp_date_te  = $sp_date ? ( class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::format_localized_date( $sp_date, 'te' ) : [] ) : [];
      $sp_date_hi  = $sp_date ? ( class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::format_localized_date( $sp_date, 'hi' ) : [] ) : [];
    ?>
      <section class="festival-spotlight" id="djv-festival-spotlight" style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF4E5 100%); border: 1.5px solid var(--clr-accent, #C89432); border-radius: 1.25rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-md, 0 4px 16px rgba(0,0,0,0.08));">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem;">
          <div style="flex: 1; min-width: 280px;">
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--clr-primary, #7A2419); font-weight: 700; font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase;">
              ⭐ <span class="djv-lang-field" data-lang="en">Featured / Upcoming Observance</span>
              <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);">రాబోయే ప్రధాన పండుగ విశేషాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;">आगामी प्रमुख व्रत एवं पर्व</span>
            </div>

            <!-- Spotlight Title (Zero un-gated Telugu leak) -->
            <h2 style="font-family: var(--font-heading, serif); font-size: 2rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">
              <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $sp_title_en ); ?></span>
              <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $sp_title_te ?: $sp_title_en ); ?></span>
              <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $sp_title_hi ?: $sp_title_en ); ?></span>
            </h2>

            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; font-size: 0.95rem; color: var(--clr-text, #2A1F1D); margin-bottom: 1rem;">
              <?php if ( ! empty( $sp_date_en['formatted'] ) ) : ?>
                <span style="font-weight: 700; color: var(--clr-primary, #7A2419);">
                  📅
                  <span class="djv-lang-field" data-lang="en"><?php echo esc_html( ( $sp_date_en['day_of_week'] ? $sp_date_en['day_of_week'] . ', ' : '' ) . $sp_date_en['formatted'] ); ?></span>
                  <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( ( ! empty( $sp_date_te['day_of_week'] ) ? $sp_date_te['day_of_week'] . ', ' : '' ) . ( $sp_date_te['formatted'] ?? '' ) ); ?></span>
                  <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( ( ! empty( $sp_date_hi['day_of_week'] ) ? $sp_date_hi['day_of_week'] . ', ' : '' ) . ( $sp_date_hi['formatted'] ?? '' ) ); ?></span>
                </span>
              <?php endif; ?>
              <?php if ( $sp_tithi ) : ?>
                <span style="color: var(--clr-text-secondary, #55433C);">🌙 <?php echo esc_html( $sp_tithi ); ?></span>
              <?php endif; ?>
              <?php if ( $sp_timings ) : ?>
                <span style="color: var(--clr-accent, #C89432); font-weight: 600;">⏰ <?php echo esc_html( $sp_timings ); ?></span>
              <?php endif; ?>
            </div>

            <!-- Spotlight Description -->
            <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.6; margin: 0 0 1.25rem 0; font-size: 0.95rem;">
              <span class="djv-lang-field" data-lang="en"><?php echo esc_html( wp_trim_words( $sp_desc_en, 35 ) ); ?></span>
              <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( wp_trim_words( $sp_desc_te, 35 ) ); ?></span>
              <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( wp_trim_words( $sp_desc_hi, 35 ) ); ?></span>
            </p>

            <a href="<?php echo esc_url( $sp_link ); ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--clr-primary, #7A2419); color: #FFF; padding: 0.7rem 1.4rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">
              <span class="djv-lang-field" data-lang="en">Explore Puja Vidhi &amp; Muhurat →</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">సంపూర్ణ పూజా విధానం &amp; ముహూర్తం చూడండి →</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">संपूर्ण पूजा विधि एवं मुहूर्त देखें →</span>
            </a>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ── Filter & Search Controls ── -->
    <div class="festival-controls" style="margin-bottom: 2rem;">

      <!-- ── Location Context Bar ── -->
      <div class="djv-location-scope-bar" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem; background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 0.85rem 1.25rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
        
        <!-- Current Location Indicator & Modal Trigger -->
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <span style="font-size: 1.1rem;">📍</span>
          <div>
            <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--clr-text-muted, #7A6F68);">
              <span class="djv-lang-field" data-lang="en">Calendar Location:</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">క్యాలెండర్ ప్రాంతం:</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">पंचांग स्थान:</span>
            </div>
            <div style="font-size: 0.95rem; font-weight: 700; color: var(--clr-primary, #7A2419);">
              <span id="djv-fest-location-display">Hyderabad, Telangana</span>
            </div>
          </div>
          <button type="button" id="djv-change-loc-btn" style="border: 1px solid var(--clr-border, #E8DFD3); background: var(--clr-bg, #FDFBF7); color: var(--clr-primary, #7A2419); padding: 0.3rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; margin-left: 0.5rem; transition: all 0.2s;">
            <span class="djv-lang-field" data-lang="en">Change Location ▼</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">ప్రాంతం మార్చండి ▼</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">स्थान बदलें ▼</span>
          </button>
        </div>
      </div>

      <!-- ── Year Selector Bar: ‹ 2025  2026  2027  2028  2029  2030 › ── -->
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
        <div class="djv-year-bar" id="djv-year-selector" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.35rem 0.6rem; display: inline-flex; align-items: center; gap: 0.3rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.4rem; text-transform: uppercase;">📅 Year:</span>
          <button type="button" class="djv-year-nav" id="djv-year-prev-btn" style="border:none;background:transparent;color:var(--clr-primary, #7A2419);font-size:1.2rem;font-weight:700;cursor:pointer;padding:0.2rem 0.5rem;border-radius:9999px;line-height:1;" title="Previous Year">‹</button>
          <?php
          foreach ( [ 2025, 2026, 2027, 2028, 2029, 2030 ] as $y_btn ) :
            $is_active = ( $y_btn === $current_page_year );
          ?>
            <button type="button" class="djv-year-btn <?php echo $is_active ? 'active' : ''; ?>" data-year="<?php echo esc_attr( $y_btn ); ?>"
                    style="border:none;background:<?php echo $is_active ? 'var(--clr-primary, #7A2419)' : 'transparent'; ?>;color:<?php echo $is_active ? '#FFF' : 'var(--clr-text, #2A1F1D)'; ?>;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.85rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
              <?php echo esc_html( $y_btn ); ?>
            </button>
          <?php endforeach; ?>
          <button type="button" class="djv-year-nav" id="djv-year-next-btn" style="border:none;background:transparent;color:var(--clr-primary, #7A2419);font-size:1.2rem;font-weight:700;cursor:pointer;padding:0.2rem 0.5rem;border-radius:9999px;line-height:1;" title="Next Year">›</button>
        </div>

        <div id="djv-year-indicator" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-text-muted, #7A6F68);">
          <span class="djv-lang-field" data-lang="en">Calculated for Year <strong id="djv-current-year-label" style="color:var(--clr-primary, #7A2419);"><?php echo esc_html( $current_page_year ); ?></strong></span>
          <span class="djv-lang-field" data-lang="te" style="display:none;"><strong id="djv-current-year-label-te" style="color:var(--clr-primary, #7A2419);"><?php echo esc_html( $current_page_year ); ?></strong> సంవత్సర పంచాంగ పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">वर्ष <strong id="djv-current-year-label-hi" style="color:var(--clr-primary, #7A2419);"><?php echo esc_html( $current_page_year ); ?></strong> के प्रामाणिक व्रत एवं त्योहार</span>
        </div>
      </div>

      <!-- Search Box with Localized Placeholder -->
      <div style="position: relative; margin-bottom: 1.25rem; max-width: 600px;">
        <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.1rem; color: var(--clr-text-muted, #7A6F68);">🔍</span>
        <input type="text" id="djv-festival-search" placeholder="Search festivals (e.g., Ugadi, Diwali, Vijayadashami, Shivaratri)..."
               style="width: 100%; padding: 0.85rem 1rem 0.85rem 2.85rem; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; font-size: 0.95rem; background: #FFF; outline: none; box-sizing: border-box; transition: border-color 0.2s;">
      </div>

      <!-- Trilingual Category Filter Pills (Requirement 10) -->
      <div class="festival-filter-pills" id="djv-festival-pills" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
        <button type="button" class="djv-pill active" data-filter="all" style="border: 1px solid var(--clr-primary, #7A2419); background: var(--clr-primary, #7A2419); color: #FFF; padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">All Festivals</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">అన్ని పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">सभी त्योहार</span>
        </button>
        <button type="button" class="djv-pill" data-filter="major-festivals" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Major Festivals</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">ప్రధాన పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">प्रमुख त्योहार</span>
        </button>
        <button type="button" class="djv-pill" data-filter="regional" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Telugu / Regional</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">తెలుగు / ప్రాంతీయ</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">तेलुगु / क्षेत्रीय</span>
        </button>
        <button type="button" class="djv-pill" data-filter="shiva" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Shiva</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">శివ</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">शिव</span>
        </button>
        <button type="button" class="djv-pill" data-filter="vishnu" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Vishnu</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">విష్ణు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">विष्णु</span>
        </button>
        <button type="button" class="djv-pill" data-filter="krishna" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Krishna</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">కృష్ణ</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">कृष्ण</span>
        </button>
        <button type="button" class="djv-pill" data-filter="ganesha" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Ganesha</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">గణేశ</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">गणेश</span>
        </button>
        <button type="button" class="djv-pill" data-filter="hanuman" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Hanuman</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">హనుమాన్</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">हनुमान</span>
        </button>
        <button type="button" class="djv-pill" data-filter="devi" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Devi / Durga</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">దేవి / దుర్గ</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">देवी / दुर्गा</span>
        </button>
        <button type="button" class="djv-pill" data-filter="lakshmi" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Lakshmi</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">లక్ష్మీ</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">लक्ष्मी</span>
        </button>
        <button type="button" class="djv-pill" data-filter="saraswati" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Saraswati</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">సరస్వతి</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">सरस्वती</span>
        </button>
        <button type="button" class="djv-pill" data-filter="sankranti" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Sankranti</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">సంక్రాంతి</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">संक्रांति</span>
        </button>
        <button type="button" class="djv-pill" data-filter="fasting" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Fasting &amp; Ekadashi</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">ఏకాదశి / ఉపవాసం</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">व्रत एवं एकादशी</span>
        </button>
        <button type="button" class="djv-pill" data-filter="purnima" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <span class="djv-lang-field" data-lang="en">Purnima</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">పౌర్ణమి</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">पूर्णिमा</span>
        </button>
      </div>
    </div>

    <!-- ── Festival Cards Grid (3 Columns Desktop, 2 Columns Tablet, 1 Column Mobile) ── -->
    <div class="festival-cards-grid" id="djv-festival-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
      <?php if ( ! empty( $all_festivals ) ) : ?>
        <?php foreach ( $all_festivals as $post ) : setup_postdata( $post ); ?>
          <?php get_template_part( 'template-parts/festival/card' ); ?>
        <?php endforeach; wp_reset_postdata(); ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
          <h3><?php esc_html_e( 'No festivals found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted, #7A6F68);"><?php esc_html_e( 'Database sync is in progress. Please refresh shortly.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <div id="djv-no-festivals-match" style="display: none; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3); margin-top: 1.5rem;">
      <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
      <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">
        <span class="djv-lang-field" data-lang="en">No matching festivals found</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">ఎలాంటి పండుగలు కనుగొనబడలేదు</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">कोई त्योहार नहीं मिला</span>
      </h3>
      <p style="color: var(--clr-text-muted, #7A6F68); margin: 0;">
        <span class="djv-lang-field" data-lang="en">Try adjusting your keyword or scope/category filter.</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">మీ శోధన పదం లేదా కేటగిరీని మార్చి ప్రయత్నించండి.</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">कृपया अपना खोज शब्द या श्रेणी फ़िल्टर बदलकर पुनः प्रयास करें।</span>
      </p>
    </div>

    <!-- ── Pagination Area (18 per view) ── -->
    <div class="festival-pagination-area" id="djv-festival-pagination-area" style="margin-top: 3rem; display: none; flex-direction: column; align-items: center; gap: 0.85rem;">
      <div id="djv-festival-page-info" style="font-size: 0.85rem; color: var(--clr-text-muted, #7A6F68); font-weight: 600;">
      </div>
      <div class="festival-pagination-controls" id="djv-festival-pagination-controls" style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap; justify-content: center;">
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // ── Language State & Persistence (Requirement 1 & 2) ──
  const langKey = 'djv_lang';
  const urlParams = new URLSearchParams(window.location.search);
  const urlLang = urlParams.get('lang');
  
  // Single source of truth for language (default strictly 'en')
  let currentLanguage = (urlLang && ['en', 'te', 'hi'].includes(urlLang))
    ? urlLang
    : (localStorage.getItem(langKey) || 'en');

  if (!['en', 'te', 'hi'].includes(currentLanguage)) {
    currentLanguage = 'en';
  }

  // ── Location & Scope State (Pan-India Architecture) ──
  let currentLocation = {
    name: 'Hyderabad',
    state: 'Telangana',
    latitude: 17.3850,
    longitude: 78.4867,
    timezone: 'Asia/Kolkata'
  };

  if (window.DJV_LOCATION && typeof window.DJV_LOCATION.getSelected === 'function') {
    const loc = window.DJV_LOCATION.getSelected();
    if (loc && loc.name) {
      currentLocation = loc;
    }
  }

  let currentYear = <?php echo (int) $current_page_year; ?>;
  let currentScope = 'relevant'; // 'relevant', 'pan_india', 'my_state', 'all_india'
  let currentCategory = 'all';

  // ── Pagination State (18 per view) ──
  const PER_PAGE = 18;
  let currentPage = 1;

  const grid = document.getElementById('djv-festival-grid');
  const searchInput = document.getElementById('djv-festival-search');
  const pills = document.querySelectorAll('#djv-festival-pills .djv-pill');
  const noMatch = document.getElementById('djv-no-festivals-match');

  // ── Sync UI with Location ──
  function updateLocationDisplayUI() {
    const locDisplay = document.getElementById('djv-fest-location-display');
    if (locDisplay) {
      locDisplay.textContent = `${currentLocation.name}${currentLocation.state ? ', ' + currentLocation.state : ''}`;
    }
  }

  // Bind Location Change trigger button
  const changeLocBtn = document.getElementById('djv-change-loc-btn');
  if (changeLocBtn) {
    changeLocBtn.addEventListener('click', function() {
      const globalBtn = document.getElementById('global-location-btn');
      if (globalBtn) {
        globalBtn.click();
      } else {
        const modal = document.getElementById('location-modal-backdrop');
        if (modal) {
          modal.classList.add('open');
          modal.style.display = 'flex';
        }
      }
    });
  }

  // Listen for global location changes
  window.addEventListener('djv:locationChanged', function(e) {
    if (e.detail && e.detail.name) {
      currentLocation = e.detail;
      updateLocationDisplayUI();
      refetchFestivals();
    }
  });

  // ── Language Application ──
  function applyLanguage(lang, triggerFetch = false) {
    currentLanguage = lang;
    localStorage.setItem(langKey, lang);

    // Update URL param without refreshing
    try {
      const url = new URL(window.location.href);
      url.searchParams.set('lang', lang);
      window.history.replaceState({}, '', url);
    } catch(e) {}

    // Update HTML lang attribute
    document.documentElement.setAttribute('lang', lang);

    // Update switcher buttons
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

    // Toggle all static trilingual text spans
    document.querySelectorAll('.djv-lang-field').forEach(el => {
      if (el.getAttribute('data-lang') === lang) {
        el.style.display = '';
      } else {
        el.style.display = 'none';
      }
    });

    // Update search placeholder (Requirement 9)
    if (searchInput) {
      if (lang === 'te') {
        searchInput.placeholder = 'పండుగలను వెతకండి (ఉదా: విజయదశమి, ఉగాది, దీపావళి)...';
      } else if (lang === 'hi') {
        searchInput.placeholder = 'त्योहार खोजें (जैसे: विजयादशमी, उगादि, दिवाली)...';
      } else {
        searchInput.placeholder = 'Search festivals (e.g., Vijayadashami, Ugadi, Diwali, Shivaratri)...';
      }
    }

    if (triggerFetch) {
      refetchFestivals();
    }
  }

  // Bind Language buttons
  document.querySelectorAll('.djv-lang-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const selected = this.getAttribute('data-lang');
      if (selected && selected !== currentLanguage) {
        applyLanguage(selected, true);
      }
    });
  });

  // Listen for global language changes from header or other components
  window.addEventListener('djv:languageChanged', function(e) {
    if (e.detail && e.detail.lang && e.detail.lang !== currentLanguage) {
      applyLanguage(e.detail.lang, true);
    }
  });

  // ── Year Nav ──
  const availableYears = [ 2025, 2026, 2027, 2028, 2029, 2030 ];

  function updateYearButtonStyles(year) {
    document.querySelectorAll('.djv-year-btn').forEach(btn => {
      const btnYear = parseInt(btn.getAttribute('data-year'), 10);
      if (btnYear === year) {
        btn.classList.add('active');
        btn.style.background = 'var(--clr-primary, #7A2419)';
        btn.style.color = '#FFF';
      } else {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--clr-text, #2A1F1D)';
      }
    });

    document.querySelectorAll('.djv-heading-year-val').forEach(el => el.textContent = year);
    const lEn = document.getElementById('djv-current-year-label');
    const lTe = document.getElementById('djv-current-year-label-te');
    const lHi = document.getElementById('djv-current-year-label-hi');
    if (lEn) lEn.textContent = year;
    if (lTe) lTe.textContent = year;
    if (lHi) lHi.textContent = year;
  }

  document.querySelectorAll('.djv-year-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const yr = parseInt(this.getAttribute('data-year'), 10);
      if (yr && yr !== currentYear) {
        currentYear = yr;
        updateYearButtonStyles(yr);
        refetchFestivals();
      }
    });
  });

  const prevYearBtn = document.getElementById('djv-year-prev-btn');
  if (prevYearBtn) {
    prevYearBtn.addEventListener('click', function() {
      const prevIdx = availableYears.indexOf(currentYear) - 1;
      if (prevIdx >= 0) {
        currentYear = availableYears[prevIdx];
        updateYearButtonStyles(currentYear);
        refetchFestivals();
      }
    });
  }

  const nextYearBtn = document.getElementById('djv-year-next-btn');
  if (nextYearBtn) {
    nextYearBtn.addEventListener('click', function() {
      const nextIdx = availableYears.indexOf(currentYear) + 1;
      if (nextIdx < availableYears.length) {
        currentYear = availableYears[nextIdx];
        updateYearButtonStyles(currentYear);
        refetchFestivals();
      }
    });
  }


  // ── Category Pills ──
  pills.forEach(pill => {
    pill.addEventListener('click', function() {
      pills.forEach(p => {
        p.classList.remove('active');
        p.style.background = '#FFF';
        p.style.color = 'var(--clr-text, #2A1F1D)';
        p.style.borderColor = 'var(--clr-border, #E8DFD3)';
      });
      this.classList.add('active');
      this.style.background = 'var(--clr-primary, #7A2419)';
      this.style.color = '#FFF';
      this.style.borderColor = 'var(--clr-primary, #7A2419)';

      currentCategory = this.getAttribute('data-filter') || 'all';
      filterClientSideCards(true);
    });
  });

  // ── Search Input ──
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      filterClientSideCards(true);
    });
  }

  function filterClientSideCards(resetPage = false) {
    if (resetPage) {
      currentPage = 1;
    }

    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
    const cards = Array.from(document.querySelectorAll('#djv-festival-grid .festival-card'));
    const matchingCards = [];

    cards.forEach(card => {
      const matchCat = (currentCategory === 'all') || card.classList.contains(currentCategory);
      
      const titleEn = card.getAttribute('data-title-en') || '';
      const titleTe = card.getAttribute('data-title-te') || '';
      const titleHi = card.getAttribute('data-title-hi') || '';
      const textContent = card.textContent.toLowerCase();

      const matchSearch = !query ||
        titleEn.includes(query) ||
        titleTe.includes(query) ||
        titleHi.includes(query) ||
        textContent.includes(query);

      if (matchCat && matchSearch) {
        matchingCards.push(card);
      } else {
        card.style.display = 'none';
      }
    });

    const totalMatching = matchingCards.length;
    const totalPages = Math.ceil(totalMatching / PER_PAGE) || 1;

    if (currentPage > totalPages) {
      currentPage = 1;
    }

    const startIdx = (currentPage - 1) * PER_PAGE;
    const endIdx = startIdx + PER_PAGE;

    matchingCards.forEach((card, idx) => {
      if (idx >= startIdx && idx < endIdx) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });

    if (noMatch) {
      noMatch.style.display = totalMatching === 0 ? 'block' : 'none';
    }

    renderPagination(totalMatching, totalPages, currentPage);
  }

  function renderPagination(total, totalPages, page) {
    const pagArea = document.getElementById('djv-festival-pagination-area');
    const pagControls = document.getElementById('djv-festival-pagination-controls');
    const pagInfo = document.getElementById('djv-festival-page-info');
    if (!pagArea || !pagControls) return;

    if (total <= PER_PAGE) {
      pagArea.style.display = 'none';
      return;
    }

    pagArea.style.display = 'flex';

    // Trilingual page info text
    const startNum = (page - 1) * PER_PAGE + 1;
    const endNum = Math.min(page * PER_PAGE, total);
    let infoText = '';
    if (currentLanguage === 'te') {
      infoText = `మొత్తం ${total} పండుగలలో ${startNum}–${endNum} చూపిస్తోంది (పేజీ ${page} / ${totalPages})`;
    } else if (currentLanguage === 'hi') {
      infoText = `कुल ${total} में से ${startNum}–${endNum} त्योहार प्रदर्शित (पृष्ठ ${page} / ${totalPages})`;
    } else {
      infoText = `Showing ${startNum}–${endNum} of ${total} festivals (Page ${page} of ${totalPages})`;
    }
    if (pagInfo) pagInfo.textContent = infoText;

    // Trilingual Prev / Next
    const prevLabel = currentLanguage === 'te' ? '← మునుపటి' : (currentLanguage === 'hi' ? '← पिछला' : '← Previous');
    const nextLabel = currentLanguage === 'te' ? 'తరువాతి →' : (currentLanguage === 'hi' ? 'अगला →' : 'Next →');

    let html = '';

    // Prev button
    if (page > 1) {
      html += `<button type="button" class="festival-page-btn prev" data-page="${page - 1}">${prevLabel}</button>`;
    }

    // Numbered buttons with ellipsis
    for (let p = 1; p <= totalPages; p++) {
      if (p === page) {
        html += `<button type="button" class="festival-page-btn active" data-page="${p}">${p}</button>`;
      } else if (p === 1 || p === totalPages || Math.abs(p - page) <= 2) {
        html += `<button type="button" class="festival-page-btn" data-page="${p}">${p}</button>`;
      } else if (p === page - 3 || p === page + 3) {
        html += `<span class="festival-page-btn dots">…</span>`;
      }
    }

    // Next button
    if (page < totalPages) {
      html += `<button type="button" class="festival-page-btn next" data-page="${page + 1}">${nextLabel}</button>`;
    }

    pagControls.innerHTML = html;

    pagControls.querySelectorAll('.festival-page-btn[data-page]').forEach(btn => {
      btn.addEventListener('click', function() {
        const targetPage = parseInt(this.getAttribute('data-page'), 10);
        if (targetPage && targetPage !== currentPage) {
          currentPage = targetPage;
          filterClientSideCards(false);
          const gridEl = document.getElementById('djv-festival-grid');
          if (gridEl) {
            gridEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });
    });
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  // ── Dynamic Card Renderer (Multilingual) ──
  function renderFestivalCard(f) {
    const title = f.title || f.title_en || '';
    const titleEn = f.title_en || '';
    const titleTe = f.title_te || '';
    const titleHi = f.title_hi || '';
    const dateFormatted = f.formatted_date || f.date || '';
    const dayName = f.day_of_week || '';
    const dateBadge = f.date_badge || '';
    const baseLink = f.link || `/festivals/${f.slug}/`;
    const link = currentYear ? (baseLink.includes('?') ? `${baseLink}&year=${currentYear}` : `${baseLink}?year=${currentYear}`) : baseLink;
    const excerpt = f.excerpt || '';
    const category = f.category || (f.categories && f.categories.length ? f.categories[0] : '');
    const scopeVal = f.scope || 'pan_india';
    const stateVal = f.state || '';
    const isRegional = scopeVal !== 'pan_india' && stateVal && stateVal !== 'Pan-India';

    // Filter classes
    const classes = ['festival-card', 'djv-filter-item'];
    if (f.deity_slug) classes.push(f.deity_slug);
    if (f.is_major) classes.push('major-festivals');
    if (f.is_telugu) classes.push('regional');
    if (f.categories) {
      f.categories.forEach(c => {
        classes.push(c.toLowerCase().replace(/[^a-z0-9]+/g, '-'));
      });
    }
    const filterClassStr = Array.from(new Set(classes)).join(' ');

    const ctaText = currentLanguage === 'te'
      ? 'పూజా విధానం & ముహూర్తం →'
      : (currentLanguage === 'hi' ? 'पूजा विधि एवं मुहूर्त →' : 'Puja Vidhi &amp; Muhurat →');

    return `
      <div class="${filterClassStr}" id="festival-card-${f.id || f.slug}"
           data-scope="${escapeHtml(scopeVal)}"
           data-state="${escapeHtml(stateVal)}"
           data-title-en="${escapeHtml(titleEn.toLowerCase())}"
           data-title-te="${escapeHtml(titleTe.toLowerCase())}"
           data-title-hi="${escapeHtml(titleHi.toLowerCase())}"
           style="border:1px solid var(--clr-border, #E8DFD3);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));position:relative;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
        
        ${dateBadge ? `
          <span class="festival-card-badge" style="position:absolute;top:1rem;right:1rem;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.25rem 0.65rem;border-radius:0.4rem;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;">
            ${escapeHtml(dateBadge)}
          </span>
        ` : ''}

        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.85rem;position:relative;z-index:2;">
          <a href="${escapeHtml(link)}" aria-label="${escapeHtml(title)}" style="display:block;text-decoration:none;flex-shrink:0;position:relative;z-index:2;pointer-events:auto;">
            ${f.thumbnail ? `
              <div style="width:48px;height:48px;border-radius:0.75rem;overflow:hidden;flex-shrink:0;">
                <img src="${escapeHtml(f.thumbnail)}" alt="${escapeHtml(title)}" style="width:100%;height:100%;object-fit:cover;" />
              </div>
            ` : `
              <div style="width:48px;height:48px;background:rgba(200,148,50,0.12);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;" aria-hidden="true">
                🪔
              </div>
            `}
          </a>

          ${isRegional ? `
            <span class="festival-scope-pill" style="font-size:0.72rem;font-weight:600;padding:0.2rem 0.6rem;border-radius:9999px;background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA;">
              📍 ${escapeHtml(stateVal)}
            </span>
          ` : ''}
        </div>

        <h3 class="festival-card-title" style="font-family:var(--font-heading, serif);font-size:1.25rem;color:var(--clr-primary, #7A2419);margin:0 0 0.45rem 0;line-height:1.35;position:relative;z-index:2;">
          <a href="${escapeHtml(link)}" class="festival-card-title-link" style="color:inherit;text-decoration:none;display:inline-block;position:relative;z-index:2;pointer-events:auto;">
            ${escapeHtml(title)}
          </a>
        </h3>

        ${dateFormatted ? `
          <div style="font-size:0.85rem;color:var(--clr-primary, #7A2419);font-weight:600;margin-bottom:0.4rem;">
            📅 ${escapeHtml((dayName ? dayName + ', ' : '') + dateFormatted)}
          </div>
        ` : ''}

        ${tithiRule ? `
          <div style="font-size:0.78rem;color:var(--clr-text-muted, #7A6F68);margin-bottom:0.6rem;line-height:1.4;">
            🌙 ${escapeHtml(tithiRule)}
          </div>
        ` : ''}

        <p style="font-size:0.875rem;color:var(--clr-text-secondary, #55433C);line-height:1.6;margin:0 0 1rem 0;flex:1;">
          ${escapeHtml(excerpt)}
        </p>

        <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:0.75rem;border-top:1px solid #F5EFEB;position:relative;z-index:2;">
          <a href="${escapeHtml(link)}" class="festival-card-cta-link" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary, #7A2419);text-decoration:none;position:relative;z-index:2;pointer-events:auto;">
            ${ctaText}
          </a>
          ${category ? `
            <span style="font-size:0.72rem;background:#FFF9F0;color:var(--clr-text-muted, #7A6F68);padding:0.2rem 0.5rem;border-radius:0.25rem;border:1px solid var(--clr-border, #E8DFD3);">
              ${escapeHtml(category)}
            </span>
          ` : ''}
        </div>
      </div>
    `;
  }

  // ── Core Fetch Function (Requirement 7 & 8) ──
  function refetchFestivals() {
    if (!grid) return;
    grid.style.opacity = '0.5';

    const apiUrl = `/wp-json/djv/v1/festivals?year=${currentYear}&language=${currentLanguage}&scope=${currentScope}&state=${encodeURIComponent(currentLocation.state || 'Telangana')}&city=${encodeURIComponent(currentLocation.name || 'Hyderabad')}&latitude=${currentLocation.latitude || 17.3850}&longitude=${currentLocation.longitude || 78.4867}&timezone=${encodeURIComponent(currentLocation.timezone || 'Asia/Kolkata')}`;

    fetch(apiUrl)
      .then(res => res.json())
      .then(payload => {
        if (!payload.success || !Array.isArray(payload.data)) {
          console.warn('DJV: Could not fetch festival occurrences', payload);
          grid.style.opacity = '1';
          return;
        }

        const occurrences = payload.data;
        if (occurrences.length === 0) {
          grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3);">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
              <h3>No festivals found</h3>
              <p style="color: var(--clr-text-muted, #7A6F68);">Try selecting a different year or category.</p>
            </div>
          `;
        } else {
          grid.innerHTML = occurrences.map(renderFestivalCard).join('');
        }
        grid.style.opacity = '1';

        // Re-apply static text toggle on newly rendered DOM elements
        applyLanguage(currentLanguage, false);
        filterClientSideCards(true);
      })
      .catch(err => {
        console.error('DJV: Festivals fetch error:', err);
        grid.style.opacity = '1';
      });
  }

  // Initial UI setup
  updateLocationDisplayUI();
  updateYearButtonStyles(currentYear);
  applyLanguage(currentLanguage, false);
  filterClientSideCards(true);
});
</script>

<?php
get_footer();
