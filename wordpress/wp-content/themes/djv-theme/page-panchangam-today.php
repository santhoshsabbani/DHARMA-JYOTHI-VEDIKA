<?php
/**
 * Template Name: Today's Panchangam
 * Description: Dedicated WordPress template for Today's Hindu Panchangam (/panchangam/today/).
 * Fully dynamic: WordPress Custom DJV Theme -> DJV Core REST API -> Node.js Panchangam Engine.
 *
 * @package DJV_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$today_iso = date( 'Y-m-d' );
$ssr_data  = function_exists( 'djv_get_ssr_panchangam' ) ? djv_get_ssr_panchangam() : null;
?>

<style>
  .pc-controls-bar {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 1rem !important;
    background: #fff !important;
    padding: 1rem 1.5rem !important;
    border-radius: 1rem !important;
    border: 1px solid #e8d5c4 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    margin-bottom: 1.25rem !important;
  }
  .pc-location-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    background: rgba(200, 148, 50, 0.12) !important;
    border: 1px solid rgba(200, 148, 50, 0.35) !important;
    padding: 0.5rem 1rem !important;
    border-radius: 9999px !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    color: #2b1810 !important;
    cursor: pointer !important;
    font-family: var(--font-primary, sans-serif) !important;
    transition: all 0.2s ease !important;
    line-height: 1.4 !important;
    outline: none !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    box-shadow: none !important;
  }
  .pc-location-pill:hover {
    background: rgba(200, 148, 50, 0.22) !important;
    border-color: #7a2419 !important;
    color: #7a2419 !important;
  }
  .pc-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.4rem !important;
    padding: 0.55rem 0.95rem !important;
    border-radius: 0.5rem !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    font-family: var(--font-primary, sans-serif) !important;
    transition: all 0.2s ease !important;
    border: 1px solid #e8d5c4 !important;
    background: #fdfaf6 !important;
    color: #333 !important;
    text-decoration: none !important;
    line-height: 1.4 !important;
    outline: none !important;
    box-shadow: none !important;
    -webkit-appearance: none !important;
    appearance: none !important;
  }
  .pc-btn:hover {
    border-color: #7a2419 !important;
    color: #7a2419 !important;
    background: rgba(122, 36, 25, 0.06) !important;
  }
  .pc-btn--primary {
    background: #7a2419 !important;
    color: #fff !important;
    border-color: #7a2419 !important;
  }
  .pc-btn--primary:hover {
    background: #5c1a11 !important;
    border-color: #5c1a11 !important;
    color: #fff !important;
  }
  .pc-date-picker-input {
    padding: 0.45rem 0.75rem !important;
    border: 1px solid #e8d5c4 !important;
    border-radius: 0.5rem !important;
    font-family: var(--font-primary, sans-serif) !important;
    font-size: 0.875rem !important;
    color: #2b1810 !important;
    background: #fff !important;
    cursor: pointer !important;
    outline: none !important;
  }
  .pc-date-picker-input:focus {
    border-color: #7a2419 !important;
  }
  .pc-preset-btn {
    padding: 0.35rem 0.85rem !important;
    font-size: 0.78rem !important;
    border-radius: 9999px !important;
    background: #fff !important;
    border: 1px solid #e8d5c4 !important;
    color: #333 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    line-height: 1.4 !important;
    outline: none !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    box-shadow: none !important;
  }
  .pc-preset-btn:hover {
    background: rgba(122, 36, 25, 0.06) !important;
    border-color: #7a2419 !important;
    color: #7a2419 !important;
  }
  .timing-period-slot {
    display: block !important;
    font-size: 0.95rem !important;
    line-height: 1.4 !important;
    margin-top: 0.25rem !important;
    white-space: nowrap !important;
  }
  .timing-period-slot:first-child {
    margin-top: 0 !important;
  }
</style>

<!-- ════════════════════════════════════════════════════════════
     PAGE HERO: TODAY'S PANCHANGAM
════════════════════════════════════════════════════════════ -->
<section class="page-hero" aria-labelledby="page-title" style="background: linear-gradient(135deg, #2b1810 0%, #1a0f0a 100%); color: #fff; padding: 2.5rem 0 2rem 0; border-bottom: 2px solid var(--clr-border, #e8d5c4);">
  <div class="container page-hero-inner">
    <nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: rgba(255,255,255,0.7); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
      <span class="breadcrumb-sep" style="margin: 0 0.4rem;">›</span>
      <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Panchangam', 'djv-theme' ); ?></a>
      <span class="breadcrumb-sep" style="margin: 0 0.4rem;">›</span>
      <span aria-current="page" style="color: #f5c542; font-weight: 600;"><?php esc_html_e( 'Today', 'djv-theme' ); ?></span>
    </nav>

    <div class="page-hero-badge" style="display: inline-block; background: rgba(200,148,50,0.2); border: 1px solid rgba(200,148,50,0.4); color: #f5c542; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.75rem; letter-spacing: 0.04em;">
      🕉 <?php esc_html_e( 'Real-Time Astronomical Engine · Drik Ganita (Lahiri Ayanamsa)', 'djv-theme' ); ?>
    </div>

    <h1 class="page-hero-title" id="page-title" style="font-family: var(--font-heading); font-size: 2.25rem; color: #fff; margin: 0 0 0.5rem 0; line-height: 1.2;">
      <?php esc_html_e( "Today's Hindu Panchangam", 'djv-theme' ); ?>
      <span style="display: block; font-family: var(--font-telugu, sans-serif); font-size: 1.25rem; font-weight: 500; color: #f5c542; margin-top: 0.25rem;">
        <?php esc_html_e( 'నేటి హిందూ పంచాంగం', 'djv-theme' ); ?>
      </span>
    </h1>

    <p class="page-hero-sub" id="pc-date-heading" style="color: rgba(255,255,255,0.85); font-size: 1.05rem; margin: 0 0 1rem 0;">
      <?php esc_html_e( "Loading today's panchangam…", 'djv-theme' ); ?>
    </p>

    <!-- Context Meta Badges -->
    <div class="page-hero-meta" style="display: flex; flex-wrap: wrap; gap: 0.75rem; font-size: 0.8125rem; color: rgba(255,255,255,0.75);">
      <span id="pc-location-heading" style="background: rgba(255,255,255,0.08); padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.15);">
        📍 Hyderabad, Telangana
      </span>
      <span id="pc-coord-badge" style="background: rgba(255,255,255,0.08); padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.15);">
        17.3850° N, 78.4867° E
      </span>
      <span id="pc-tz-badge" style="background: rgba(255,255,255,0.08); padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.15);">
        Timezone: Asia/Kolkata (IST UTC+05:30)
      </span>
      <span id="pc-ayanamsa-badge" style="background: rgba(255,255,255,0.08); padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.15);">
        Lahiri Ayanamsa (Chitrapaksha)
      </span>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     MAIN INTERACTIVE PANCHANGAM SECTION
════════════════════════════════════════════════════════════ -->
<div class="panchangam-page-wrapper" style="padding: 2rem 0 4rem 0; background: var(--clr-bg, #fdfaf6);">
  <div class="container">

    <!-- Error Banner (Hidden by default) -->
    <div id="pc-error-banner" style="display:none; background:#ffebee; color:#c62828; padding:1.25rem 1.5rem; border-radius:var(--radius-lg, 0.75rem); margin-bottom:1.5rem; font-weight:500; border:1px solid #ffcdd2; box-shadow:0 2px 8px rgba(198,40,40,0.08);" role="alert">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
          <strong style="font-size:1.05rem; display:block; margin-bottom:0.25rem;">⚠️ <?php esc_html_e( 'Unable to load Panchangam', 'djv-theme' ); ?></strong>
          <span id="pc-error-banner-msg"><?php esc_html_e( 'Could not fetch astronomical calculations from the server.', 'djv-theme' ); ?></span>
        </div>
        <button type="button" class="pc-btn pc-btn--primary" id="pc-retry-btn" style="background:#c62828; border-color:#c62828; color:#fff;">
          🔄 <?php esc_html_e( 'Retry', 'djv-theme' ); ?>
        </button>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════════════
         TOP INTERACTIVE CONTROLS BAR: DATE & LOCATION & ACTIONS
    ════════════════════════════════════════════════════════ -->
    <div class="pc-controls-bar" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; background:var(--clr-white, #fff); padding:1rem 1.5rem; border-radius:var(--radius-xl, 1rem); border:1px solid var(--clr-border, #e8d5c4); box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04)); margin-bottom:1.25rem;">
      
      <!-- Location Controls -->
      <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        <button type="button" class="pc-location-pill global-location-pill" id="pc-location-pill-btn" title="<?php esc_attr_e( 'Change Location Across India', 'djv-theme' ); ?>">
          <span aria-hidden="true">📍</span>
          <span id="pc-current-location-text">Hyderabad, Telangana</span>
          <span style="font-size:0.75rem; opacity:0.7;">▼ <?php esc_html_e( 'Change', 'djv-theme' ); ?></span>
        </button>
        <button type="button" class="pc-btn" id="pc-gps-quick-btn" title="<?php esc_attr_e( 'Detect Current GPS Location', 'djv-theme' ); ?>">
          🎯 <?php esc_html_e( 'Use My Location', 'djv-theme' ); ?>
        </button>
      </div>

      <!-- Date Navigation Controls -->
      <div class="pc-nav-group" style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
        <button type="button" class="pc-btn" id="pc-prev-day-btn" title="<?php esc_attr_e( 'Previous Day', 'djv-theme' ); ?>">
          ‹ <?php esc_html_e( 'Previous Day', 'djv-theme' ); ?>
        </button>
        <button type="button" class="pc-btn pc-btn--primary" id="pc-today-btn">
          <?php esc_html_e( 'Today', 'djv-theme' ); ?>
        </button>
        <button type="button" class="pc-btn" id="pc-next-day-btn" title="<?php esc_attr_e( 'Next Day', 'djv-theme' ); ?>">
          <?php esc_html_e( 'Next Day', 'djv-theme' ); ?> ›
        </button>
      </div>

      <!-- Jump Date & Action Controls (Print/Share) -->
      <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        <div class="pc-date-picker-wrap" style="display:flex; align-items:center; gap:0.5rem;">
          <label for="pc-date-picker-input" style="font-size:0.8125rem; font-weight:600; color:var(--clr-text-muted, #777);">
            📅 <?php esc_html_e( 'Jump Date:', 'djv-theme' ); ?>
          </label>
          <input type="date" id="pc-date-picker-input" class="pc-date-picker-input" value="<?php echo esc_attr( $today_iso ); ?>" aria-label="<?php esc_attr_e( 'Select Date', 'djv-theme' ); ?>" style="padding:0.45rem 0.75rem; border:1px solid var(--clr-border, #e8d5c4); border-radius:var(--radius-md, 0.5rem); font-family:var(--font-primary, sans-serif); font-size:0.875rem; background:#fff; cursor:pointer;" />
        </div>

        <button type="button" class="pc-btn" id="pc-print-btn" title="<?php esc_attr_e( 'Print Panchangam', 'djv-theme' ); ?>">
          🖨️ <?php esc_html_e( 'Print', 'djv-theme' ); ?>
        </button>
        <button type="button" class="pc-btn" id="pc-share-btn" title="<?php esc_attr_e( 'Share Panchangam', 'djv-theme' ); ?>">
          🔗 <?php esc_html_e( 'Share', 'djv-theme' ); ?>
        </button>
      </div>

    </div>

    <!-- Quick Date Presets Bar -->
    <div style="display:flex; gap:0.5rem; overflow-x:auto; padding-bottom:0.5rem; margin-bottom:1.5rem;" aria-label="<?php esc_attr_e( 'Date Presets', 'djv-theme' ); ?>">
      <button type="button" class="pc-btn pc-preset-btn" data-preset="today"><?php esc_html_e( 'Today', 'djv-theme' ); ?></button>
      <button type="button" class="pc-btn pc-preset-btn" data-preset="tomorrow"><?php esc_html_e( 'Tomorrow', 'djv-theme' ); ?></button>
      <button type="button" class="pc-btn pc-preset-btn" data-preset="yesterday"><?php esc_html_e( 'Yesterday', 'djv-theme' ); ?></button>
    </div>

    <!-- ════════════════════════════════════════════════════════
         CALCULATING / LOADING OVERLAY STATE
    ════════════════════════════════════════════════════════ -->
    <div id="pc-loading-state" style="display:none; text-align:center; padding:3rem 1.5rem; background:#fff; border-radius:var(--radius-xl, 1rem); border:1px solid var(--clr-border, #e8d5c4); margin-bottom:2rem; box-shadow:var(--shadow-sm);">
      <div style="display:inline-block; font-size:2.5rem; animation:pcSpin 1.5s linear infinite;">🕉</div>
      <h3 style="font-family:var(--font-heading); font-size:1.35rem; color:var(--clr-primary, #7a2419); margin:0.75rem 0 0.25rem 0;">
        <?php esc_html_e( 'Calculating Panchangam…', 'djv-theme' ); ?>
      </h3>
      <p style="color:var(--clr-text-secondary, #666); font-size:0.9rem; margin:0;">
        <?php esc_html_e( 'Running Drik Ganita planetary equations via DJV Core engine.', 'djv-theme' ); ?>
      </p>
    </div>

    <!-- ════════════════════════════════════════════════════════
         MAIN TWO-COLUMN PANCHANGAM DISPLAY GRID
    ════════════════════════════════════════════════════════ -->
    <div class="panchangam-page-grid" id="pc-content-container">

      <!-- ──────────────────────────────────────────────────────────
           LEFT COLUMN: Primary Astronomical & Anga Timings
      ────────────────────────────────────────────────────────── -->
      <div>

        <!-- 1. SUN & MOON TIMINGS (Astronomical Solar/Lunar Row) -->
        <div class="solar-row fade-in" role="list" aria-label="<?php esc_attr_e( 'Solar and lunar astronomical timings', 'djv-theme' ); ?>">
          <div class="solar-item" role="listitem">
            <span class="solar-icon" aria-hidden="true">🌅</span>
            <div class="solar-label"><?php esc_html_e( 'Sunrise (సూర్యోదయం)', 'djv-theme' ); ?></div>
            <div class="solar-time" id="val-sunrise"><?php echo esc_html( $ssr_data['solar']['sunriseStr'] ?? djv_format_ssr_time( $ssr_data['solar']['sunrise'] ?? null ) ); ?></div>
          </div>
          <div class="solar-item" role="listitem">
            <span class="solar-icon" aria-hidden="true">🌇</span>
            <div class="solar-label"><?php esc_html_e( 'Sunset (సూర్యాస్తమయం)', 'djv-theme' ); ?></div>
            <div class="solar-time" id="val-sunset"><?php echo esc_html( $ssr_data['solar']['sunsetStr'] ?? djv_format_ssr_time( $ssr_data['solar']['sunset'] ?? null ) ); ?></div>
          </div>
          <div class="solar-item" role="listitem">
            <span class="solar-icon" aria-hidden="true">🌕</span>
            <div class="solar-label"><?php esc_html_e( 'Moonrise (చంద్రోదయం)', 'djv-theme' ); ?></div>
            <div class="solar-time" id="val-moonrise"><?php echo esc_html( $ssr_data['moonrise']['time'] ?? ( $ssr_data['solar']['moonrise'] ?? djv_format_ssr_time( $ssr_data['moonrise']['datetime'] ?? null ) ) ); ?></div>
          </div>
          <div class="solar-item" role="listitem">
            <span class="solar-icon" aria-hidden="true">🌑</span>
            <div class="solar-label"><?php esc_html_e( 'Moonset (చంద్రాస్తమయం)', 'djv-theme' ); ?></div>
            <div class="solar-time" id="val-moonset"><?php echo esc_html( $ssr_data['moonset']['time'] ?? ( $ssr_data['solar']['moonset'] ?? djv_format_ssr_time( $ssr_data['moonset']['datetime'] ?? null ) ) ); ?></div>
          </div>
        </div>

        <!-- 2. PANCHANGAM SUMMARY CARD (Prominent 5 Angas Overview) -->
        <div class="pc-card fade-in" style="margin-bottom:1.5rem; background:#fff; border-radius:var(--radius-xl, 1rem); border:1px solid var(--clr-border, #e8d5c4); box-shadow:var(--shadow-sm); overflow:hidden;">
          <div class="pc-card-head" style="background:var(--clr-secondary-faint, rgba(200,148,50,0.08)); border-bottom:1px solid var(--clr-border, #e8d5c4); padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <span class="pc-card-icon" aria-hidden="true" style="font-size:1.25rem;">🕉</span>
              <h2 style="font-family:var(--font-heading); font-size:1.25rem; color:var(--clr-primary, #7a2419); margin:0;">
                <?php esc_html_e( 'Pancha Angas Summary (పంచాంగ సంక్షేపం)', 'djv-theme' ); ?>
              </h2>
            </div>
            <span class="paksha-pill" id="val-summary-paksha-pill">
              Krishna Paksha
            </span>
          </div>

          <div class="pc-card-body" style="padding:1.25rem;">
            <table class="pancha-table" aria-label="<?php esc_attr_e( 'Pancha Angas Summary Table', 'djv-theme' ); ?>" style="width:100%; border-collapse:collapse;">
              <tbody>
                <!-- 1. Vara (Weekday) -->
                <tr>
                  <td class="pt-label">
                    <strong><?php esc_html_e( 'Vara (వారం)', 'djv-theme' ); ?></strong>
                    <div style="font-size:0.75rem; color:var(--clr-text-muted);"><?php esc_html_e( 'Weekday & Planetary Ruler', 'djv-theme' ); ?></div>
                  </td>
                  <td class="pt-value">
                    <span id="val-vara-en">—</span>
                    <span class="pt-value-te" id="val-vara-te"></span>
                    <span class="pt-time" id="val-vara-ruler"></span>
                  </td>
                </tr>

                <!-- 2. Tithi -->
                <tr>
                  <td class="pt-label">
                    <strong><?php esc_html_e( 'Tithi (తిథి)', 'djv-theme' ); ?></strong>
                    <div style="font-size:0.75rem; color:var(--clr-text-muted);"><?php esc_html_e( 'Lunar Day & Ending Time', 'djv-theme' ); ?></div>
                  </td>
                  <td class="pt-value">
                    <span id="val-tithi-name">—</span>
                    <span class="pt-value-te" id="val-tithi-te"></span>
                    <span class="pt-time" id="val-tithi-end" style="color:var(--clr-primary); font-weight:600;"></span>
                  </td>
                </tr>

                <!-- 3. Nakshatra -->
                <tr>
                  <td class="pt-label">
                    <strong><?php esc_html_e( 'Nakshatra (నక్షత్రం)', 'djv-theme' ); ?></strong>
                    <div style="font-size:0.75rem; color:var(--clr-text-muted);"><?php esc_html_e( 'Lunar Mansion, Pada & End Time', 'djv-theme' ); ?></div>
                  </td>
                  <td class="pt-value">
                    <span id="val-nakshatra-name">—</span>
                    <span class="pt-value-te" id="val-nakshatra-te"></span>
                    <span class="pt-time" id="val-nakshatra-end" style="color:var(--clr-primary); font-weight:600;"></span>
                    <span class="pt-time" id="val-nakshatra-meta" style="color:var(--clr-text-secondary); font-style:normal;"></span>
                  </td>
                </tr>

                <!-- 4. Yoga -->
                <tr>
                  <td class="pt-label">
                    <strong><?php esc_html_e( 'Yoga (యోగం)', 'djv-theme' ); ?></strong>
                    <div style="font-size:0.75rem; color:var(--clr-text-muted);"><?php esc_html_e( 'Solar-Lunar Combination', 'djv-theme' ); ?></div>
                  </td>
                  <td class="pt-value">
                    <span id="val-yoga-name">—</span>
                    <span class="pt-value-te" id="val-yoga-te"></span>
                    <span class="pt-time" id="val-yoga-end" style="color:var(--clr-primary); font-weight:600;"></span>
                  </td>
                </tr>

                <!-- 5. Karana -->
                <tr>
                  <td class="pt-label">
                    <strong><?php esc_html_e( 'Karana (కరణం)', 'djv-theme' ); ?></strong>
                    <div style="font-size:0.75rem; color:var(--clr-text-muted);"><?php esc_html_e( 'Half Lunar Day', 'djv-theme' ); ?></div>
                  </td>
                  <td class="pt-value">
                    <span id="val-karana-name">—</span>
                    <span class="pt-value-te" id="val-karana-te"></span>
                    <span class="pt-time" id="val-karana-end" style="color:var(--clr-primary); font-weight:600;"></span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 3. AUSPICIOUS TIMINGS (Shubha Muhurtham — శుభ సమయాలు) -->
        <div class="pc-card fade-in" style="margin-bottom:1.5rem; background:#fff; border-radius:var(--radius-xl, 1rem); border:1px solid #c8e6c9; box-shadow:var(--shadow-sm); overflow:hidden;">
          <div class="pc-card-head" style="background:#f1fbf6; border-bottom:1px solid #c8e6c9; padding:0.875rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
            <span class="pc-card-icon" aria-hidden="true" style="font-size:1.25rem;">✨</span>
            <h2 style="font-family:var(--font-heading); font-size:1.2rem; color:#1b5e20; margin:0;">
              <?php esc_html_e( 'Auspicious Timings (శుభ సమయాలు)', 'djv-theme' ); ?>
            </h2>
          </div>
          <div class="pc-card-body" style="padding:1.25rem;">
            <div class="timing-list">
              <!-- Abhijit Muhurtham -->
              <div class="timing-bar timing-bar--good">
                <span class="timing-icon" aria-hidden="true">🌟</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Abhijit Muhurtham', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'అభిజిత్ ముహూర్తం · 8వ ముహూర్తం (విజయప్రదం)', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--good" id="val-abhijit"><?php
                  $ssr_abh = $ssr_data['timings']['abhijitMuhurtham'] ?? null;
                  echo $ssr_abh ? djv_format_ssr_period_list( $ssr_abh ) : '—';
                ?></div>
              </div>

              <!-- Amrit Kalam -->
              <div class="timing-bar timing-bar--good">
                <span class="timing-icon" aria-hidden="true">🪷</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Amrit Kalam', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'అమృత కాలం · శుభ ప్రదం', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--good" id="val-amritkalam"><?php
                  $ssr_amr = $ssr_data['timings']['amritKalam'] ?? null;
                  echo $ssr_amr ? djv_format_ssr_period_list( $ssr_amr ) : '—';
                ?></div>
              </div>

              <!-- Brahma Muhurtham -->
              <div class="timing-bar timing-bar--good">
                <span class="timing-icon" aria-hidden="true">🌅</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Brahma Muhurtham', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'బ్రహ్మ ముహూర్తం · సూర్యోదయానికి పూర్వం', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--good" id="val-brahmamuhurtham"><?php
                  $ssr_bm = $ssr_data['timings']['brahmaMuhurtham'] ?? null;
                  echo $ssr_bm ? djv_format_ssr_period_list( $ssr_bm ) : '—';
                ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. INAUSPICIOUS TIMINGS (Ashubha Muhurtham — అశుభ సమయాలు) -->
        <div class="pc-card fade-in" style="margin-bottom:1.5rem; background:#fff; border-radius:var(--radius-xl, 1rem); border:1px solid #ffcdd2; box-shadow:var(--shadow-sm); overflow:hidden;">
          <div class="pc-card-head" style="background:#fff5f5; border-bottom:1px solid #ffcdd2; padding:0.875rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
            <span class="pc-card-icon" aria-hidden="true" style="font-size:1.25rem;">⚠️</span>
            <h2 style="font-family:var(--font-heading); font-size:1.2rem; color:#b71c1c; margin:0;">
              <?php esc_html_e( 'Inauspicious Timings (అశుభ సమయాలు)', 'djv-theme' ); ?>
            </h2>
          </div>
          <div class="pc-card-body" style="padding:1.25rem;">
            <div class="timing-list">
              <!-- Rahu Kalam -->
              <div class="timing-bar timing-bar--warn">
                <span class="timing-icon" aria-hidden="true">🛑</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Rahu Kalam', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'రాహు కాలం · రాహువు అధిపతి', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--warn" id="val-rahukalam"><?php
                  $ssr_rahu = $ssr_data['timings']['rahuKalam'] ?? null;
                  echo $ssr_rahu ? djv_format_ssr_period_list( $ssr_rahu ) : '—';
                ?></div>
              </div>

              <!-- Yamagandam -->
              <div class="timing-bar timing-bar--warn">
                <span class="timing-icon" aria-hidden="true">⚠️</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Yamagandam', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'యమగండం · యముని అధిపత్యం', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--warn" id="val-yamagandam"><?php
                  $ssr_yama = $ssr_data['timings']['yamagandam'] ?? null;
                  echo $ssr_yama ? djv_format_ssr_period_list( $ssr_yama ) : '—';
                ?></div>
              </div>

              <!-- Gulika Kalam -->
              <div class="timing-bar timing-bar--neutral">
                <span class="timing-icon" aria-hidden="true">⏳</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Gulika Kalam', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'గుళిక కాలం · శని పుత్ర గుళిక', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--neutral" id="val-gulikakalam"><?php
                  $ssr_gul = $ssr_data['timings']['gulikaKalam'] ?? null;
                  echo $ssr_gul ? djv_format_ssr_period_list( $ssr_gul ) : '—';
                ?></div>
              </div>

              <!-- Dur Muhurtam -->
              <div class="timing-bar timing-bar--warn">
                <span class="timing-icon" aria-hidden="true">🚫</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Dur Muhurtam', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'దుర్ముహూర్తం · నిషిద్ధ సమయం', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--warn" id="val-durmuhurtham"><?php
                  $ssr_dur = $ssr_data['timings']['durMuhurtham'] ?? ( $ssr_data['durMuhurtham'] ?? null );
                  echo $ssr_dur ? djv_format_ssr_period_list( $ssr_dur ) : '—';
                ?></div>
              </div>

              <!-- Varjyam (Tyajyam) -->
              <div class="timing-bar timing-bar--warn">
                <span class="timing-icon" aria-hidden="true">⛔</span>
                <div class="timing-name">
                  <?php esc_html_e( 'Varjyam (Tyajyam)', 'djv-theme' ); ?>
                  <small><?php esc_html_e( 'వర్జ్యం / త్యాజ్యం · త్యాజ్య కాలం', 'djv-theme' ); ?></small>
                </div>
                <div class="timing-period timing-period--warn" id="val-varjyam"><?php
                  $ssr_varj = $ssr_data['timings']['varjyam'] ?? ( $ssr_data['varjyam'] ?? null );
                  echo $ssr_varj ? djv_format_ssr_period_list( $ssr_varj ) : '—';
                ?></div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ──────────────────────────────────────────────────────────
           RIGHT COLUMN: Sidebar Widgets (Solar, Lunar, Methodology)
      ────────────────────────────────────────────────────────── -->
      <aside class="pc-sidebar">

        <!-- Solar Day Details -->
        <div class="sidebar-widget">
          <div class="sw-head"><span>☀️</span> <?php esc_html_e( 'Solar Day Details', 'djv-theme' ); ?></div>
          <div class="sw-body calendar-info">
            <div class="ci-row">
              <span class="ci-label"><?php esc_html_e( 'Solar Noon (మధ్యాహ్నం)', 'djv-theme' ); ?></span>
              <span class="ci-value" id="val-solarnoon">—</span>
            </div>
            <div class="ci-row">
              <span class="ci-label"><?php esc_html_e( 'Day Length (పగటి కాలం)', 'djv-theme' ); ?></span>
              <span class="ci-value" id="val-daylength">—</span>
            </div>
          </div>
        </div>

        <!-- Lunar Phase & Illumination -->
        <div class="sidebar-widget">
          <div class="sw-head"><span>🌙</span> <?php esc_html_e( 'Lunar Phase & Illumination', 'djv-theme' ); ?></div>
          <div class="sw-body calendar-info">
            <div class="ci-row">
              <span class="ci-label"><?php esc_html_e( 'Moon Phase', 'djv-theme' ); ?></span>
              <span class="ci-value" id="val-moonphase">—</span>
            </div>
            <div class="ci-row">
              <span class="ci-label"><?php esc_html_e( 'Telugu Description', 'djv-theme' ); ?></span>
              <span class="ci-value" id="val-moonphase-te" style="font-family:var(--font-telugu, sans-serif);">—</span>
            </div>
            <div class="ci-row">
              <span class="ci-label"><?php esc_html_e( 'Illumination %', 'djv-theme' ); ?></span>
              <span class="ci-value" id="val-illumination">—</span>
            </div>
          </div>
        </div>

        <!-- Calculation Methodology & Ayanamsa Notice -->
        <div class="sidebar-widget">
          <div class="sw-head"><span>📖</span> <?php esc_html_e( 'Calculation Convention', 'djv-theme' ); ?></div>
          <div class="sw-body" style="font-size:0.8125rem; line-height:1.6; color:var(--clr-text-secondary);">
            <p style="margin:0 0 0.5rem 0;">
              <strong>Ayanamsa:</strong> Lahiri (Chitrapaksha) — Government of India Standard Calendar Reform Committee recommendation.
            </p>
            <p style="margin:0 0 0.5rem 0;">
              <strong>Coordinate System:</strong> Topocentric coordinates accounting for lunar horizontal parallax, semi-diameter, and atmospheric refraction.
            </p>
            <p style="margin:0 0 0.5rem 0;">
              <strong>Convention:</strong> Astronomical Udaya Tithi (Tithi at local sunrise) and civil date attribution.
            </p>
            <p style="margin:0;">
              <strong>Timezone:</strong> Indian Standard Time (IST, UTC+05:30) calculated from exact observer latitude & longitude.
            </p>
          </div>
        </div>

        <!-- Data Integrity Inspector (Metadata) -->
        <div class="sidebar-widget">
          <div class="sw-head"><span>⚙️</span> <?php esc_html_e( 'Engine Verification Metadata', 'djv-theme' ); ?></div>
          <div class="sw-body">
            <p style="font-size:0.75rem; color:var(--clr-text-muted); margin:0 0 0.5rem 0;">
              <?php esc_html_e( 'Calculated in real-time from verified astronomical coordinates:', 'djv-theme' ); ?>
            </p>
            <pre class="pc-meta-box" id="pc-meta-json" style="background:#1e1e24; color:#e6e6e6; border-radius:var(--radius-lg, 0.75rem); padding:0.875rem; font-family:monospace; font-size:0.72rem; overflow-x:auto; max-height:220px;"><?php esc_html_e( 'Loading metadata…', 'djv-theme' ); ?></pre>
          </div>
        </div>

        <!-- Devotional Disclaimer -->
        <div class="disclaimer" role="note" style="margin-top:1rem;">
          <strong>⚠ <?php esc_html_e( 'Important Note', 'djv-theme' ); ?></strong>
          <?php esc_html_e( 'Panchangam calculations are provided for devotional and informational purposes based on Drik Ganita ephemeris. For ceremonial Muhurtham decisions, please consult an authoritative Jyotishi.', 'djv-theme' ); ?>
        </div>

      </aside>

    </div>

  </div>
</div>

<style>
@keyframes pcSpin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>

<?php
get_footer();
