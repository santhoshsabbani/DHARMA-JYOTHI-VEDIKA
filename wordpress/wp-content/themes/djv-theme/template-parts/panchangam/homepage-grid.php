<?php
/**
 * Template Part: Homepage Full Panchangam Section
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$ssr_data  = djv_get_ssr_panchangam();
$today_str = date( 'l, F j, Y' );
?>
<section class="panchangam-section" aria-labelledby="panchangam-section-heading">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">📅 <?php esc_html_e( 'Live Calculation', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="panchangam-section-heading"><?php esc_html_e( "Today's Complete Panchangam", 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Calculated for', 'djv-theme' ); ?> <strong id="fp-header-location"><?php esc_html_e( 'Hyderabad, Telangana', 'djv-theme' ); ?></strong> ·
        <strong><?php esc_html_e( 'Lahiri Ayanamsa', 'djv-theme' ); ?></strong> · <strong><?php esc_html_e( 'Amanta', 'djv-theme' ); ?></strong> <?php esc_html_e( 'month system', 'djv-theme' ); ?>
      </p>
    </div>

    <div class="panchangam-layout">
      <!-- Main Panchangam Card -->
      <div class="panchanga-card" id="full-panchangam-card">

        <div class="panchanga-card-header">
          <div>
            <div class="panchanga-card-title" id="full-pc-title"><?php esc_html_e( 'Panchangam', 'djv-theme' ); ?> — <?php echo esc_html( date( 'Y-m-d' ) ); ?></div>
            <div class="panchanga-card-date" id="full-pc-vara"><?php echo esc_html( $today_str ); ?></div>
          </div>
          <div class="panchanga-actions">
            <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="panchanga-action-btn" id="panchangam-date-btn">📅 <?php esc_html_e( 'Pan-India Dashboard', 'djv-theme' ); ?></a>
          </div>
        </div>

        <!-- Full section loading skeleton -->
        <div id="full-pc-loading" style="<?php echo $ssr_data ? 'display:none;' : ''; ?>padding:2rem;text-align:center;color:var(--clr-text-muted);" aria-live="polite">
          <div class="djv-loading-pulse" style="font-size:1.1rem;"><?php esc_html_e( 'Loading live Panchangam data…', 'djv-theme' ); ?></div>
        </div>

        <!-- Full section live data -->
        <div id="full-pc-data" class="panchanga-grid" style="<?php echo $ssr_data ? '' : 'display:none;'; ?>">

          <!-- Pancha Angas -->
          <div class="panchanga-section-block">
            <div class="psb-title"><span class="psb-icon" aria-hidden="true">🌙</span> <?php esc_html_e( 'Pancha Angas', 'djv-theme' ); ?></div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Vara (Weekday)', 'djv-theme' ); ?></span>
              <div class="pancha-item-value" id="fp-vara"><?php echo esc_html( $ssr_data['vara']['name'] ?? '—' ); ?></div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Tithi', 'djv-theme' ); ?></span>
              <div class="pancha-item-value">
                <span id="fp-tithi"><?php echo esc_html( $ssr_data['tithi']['name'] ?? '—' ); ?></span>
              </div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Paksha', 'djv-theme' ); ?></span>
              <div class="pancha-item-value" id="fp-paksha"><?php echo esc_html( $ssr_data['tithi']['paksha'] ?? '—' ); ?></div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Nakshatra', 'djv-theme' ); ?></span>
              <div class="pancha-item-value">
                <span id="fp-nakshatra"><?php echo esc_html( $ssr_data['nakshatra']['nakshatra']['name'] ?? '—' ); ?></span>
                <span class="pancha-item-time" id="fp-nakshatra-pada"><?php echo isset( $ssr_data['nakshatra']['pada'] ) ? 'Pada ' . esc_html( $ssr_data['nakshatra']['pada'] ) : ''; ?></span>
              </div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Yoga', 'djv-theme' ); ?></span>
              <div class="pancha-item-value">
                <span id="fp-yoga"><?php echo esc_html( $ssr_data['yoga']['name'] ?? '—' ); ?></span>
              </div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Karana', 'djv-theme' ); ?></span>
              <div class="pancha-item-value">
                <span id="fp-karana"><?php echo esc_html( $ssr_data['karana']['name'] ?? '—' ); ?></span>
              </div>
            </div>
          </div>

          <!-- Solar / Lunar -->
          <div class="panchanga-section-block">
            <div class="psb-title"><span class="psb-icon" aria-hidden="true">☀️</span> <?php esc_html_e( 'Solar & Lunar', 'djv-theme' ); ?></div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Sunrise', 'djv-theme' ); ?></span>
              <div class="pancha-item-value" id="fp-sunrise"><?php echo esc_html( $ssr_data['solar']['sunriseStr'] ?? '—' ); ?></div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Sunset', 'djv-theme' ); ?></span>
              <div class="pancha-item-value" id="fp-sunset"><?php echo esc_html( $ssr_data['solar']['sunsetStr'] ?? '—' ); ?></div>
            </div>
            <div class="pancha-item" id="fp-moonrise-row">
              <span class="pancha-item-label"><?php esc_html_e( 'Moonrise', 'djv-theme' ); ?></span>
              <div class="pancha-item-value" id="fp-moonrise"><?php echo esc_html( $ssr_data['moonrise']['time'] ?? '—' ); ?></div>
            </div>
          </div>

          <!-- Auspicious Timings -->
          <div class="panchanga-section-block">
            <div class="psb-title"><span class="psb-icon" aria-hidden="true">✨</span> <?php esc_html_e( 'Auspicious Timings', 'djv-theme' ); ?></div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Abhijit Muhurtham', 'djv-theme' ); ?></span>
              <div class="pancha-item-value timing-auspicious" id="fp-abhijit"><?php echo esc_html( $ssr_data['timings']['abhijitMuhurtham']['text'] ?? '—' ); ?></div>
            </div>
          </div>

          <!-- Inauspicious Timings -->
          <div class="panchanga-section-block">
            <div class="psb-title"><span class="psb-icon" aria-hidden="true">⚠️</span> <?php esc_html_e( 'Inauspicious Timings', 'djv-theme' ); ?></div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Rahu Kalam', 'djv-theme' ); ?></span>
              <div class="pancha-item-value timing-warning" id="fp-rahu"><?php echo esc_html( $ssr_data['timings']['rahuKalam']['text'] ?? '—' ); ?></div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Yamagandam', 'djv-theme' ); ?></span>
              <div class="pancha-item-value timing-warning" id="fp-yamagandam"><?php echo esc_html( $ssr_data['timings']['yamagandam']['text'] ?? '—' ); ?></div>
            </div>
            <div class="pancha-item">
              <span class="pancha-item-label"><?php esc_html_e( 'Gulika Kalam', 'djv-theme' ); ?></span>
              <div class="pancha-item-value timing-warning" id="fp-gulika"><?php echo esc_html( $ssr_data['timings']['gulikaKalam']['text'] ?? '—' ); ?></div>
            </div>
          </div>
        </div>

        <!-- Calculation Metadata Footer -->
        <div id="full-pc-meta-footer" style="padding: 0.75rem 1.75rem; background: #FFF9F0; border-top: 1px solid var(--clr-border); font-size: 0.72rem; color: var(--clr-text-muted); display: flex; flex-wrap: wrap; gap: 1rem;">
          <span id="fp-meta-location">📍 <span class="global-location-name"><?php esc_html_e( 'Hyderabad', 'djv-theme' ); ?></span> (17.3850°N, 78.4867°E)</span>
          <span>🕐 Asia/Kolkata (IST, UTC+5:30)</span>
          <span>📖 <?php esc_html_e( 'Lahiri Ayanamsa · Amanta', 'djv-theme' ); ?></span>
          <span id="fp-meta-engine">⚙️ <?php esc_html_e( 'Engine v1.0.0 · Rules: telugu-1.0', 'djv-theme' ); ?></span>
          <span style="color: #B5241A; font-style: italic;">⚠️ <?php esc_html_e( 'For informational purposes — consult a Jyotishi for ceremonial muhurtham', 'djv-theme' ); ?></span>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="panchanga-sidebar" aria-label="<?php esc_attr_e( 'Related information', 'djv-theme' ); ?>">
        <div class="sidebar-card">
          <div class="sidebar-card-header">
            <span aria-hidden="true">🎊</span> <?php esc_html_e( "Today's Occasion", 'djv-theme' ); ?>
          </div>
          <div class="sidebar-card-body">
            <div class="festival-today">
              <div class="festival-icon-wrap" aria-hidden="true">🪔</div>
              <div>
                <div class="festival-name"><?php esc_html_e( 'Navaratri Festive Period', 'djv-theme' ); ?></div>
                <div class="festival-desc"><?php esc_html_e( 'Nine sacred nights worshipping Goddess Durga in her nine divine forms.', 'djv-theme' ); ?></div>
                <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>" class="festival-link">
                  <?php esc_html_e( 'Learn More', 'djv-theme' ); ?> <span aria-hidden="true">→</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="sidebar-card location-card">
          <div class="sidebar-card-header">
            <span aria-hidden="true">📍</span> <?php esc_html_e( 'Location', 'djv-theme' ); ?>
          </div>
          <div class="sidebar-card-body">
            <div class="location-display" aria-label="<?php esc_attr_e( 'Current location', 'djv-theme' ); ?>">
              <span class="location-pin" aria-hidden="true">📍</span>
              <div>
                <div class="location-city global-location-name"><?php esc_html_e( 'Hyderabad', 'djv-theme' ); ?></div>
                <div class="location-region"><?php esc_html_e( 'India · IST', 'djv-theme' ); ?></div>
              </div>
            </div>
            <button type="button" class="location-change-btn global-location-pill" id="change-location-btn">
              <?php esc_html_e( 'Change Location', 'djv-theme' ); ?>
            </button>
          </div>
        </div>

        <div class="sidebar-card">
          <div class="sidebar-card-header">
            <span aria-hidden="true">ℹ️</span> <?php esc_html_e( 'Astronomical Accuracy', 'djv-theme' ); ?>
          </div>
          <div class="sidebar-card-body">
            <p style="font-size: 0.78rem; color: var(--clr-text-muted); line-height: 1.6;">
              <?php esc_html_e( 'Calculated dynamically via DJV Panchangam Core utilizing Jean Meeus astronomical algorithms and Chitrapaksha Lahiri Ayanamsa. Zero hardcoding.', 'djv-theme' ); ?>
            </p>
          </div>
        </div>
      </aside>
    </div>

    <div style="text-align:center; margin-top: 2rem;">
      <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.875rem 2rem;background:var(--clr-primary);color:white;border-radius:9999px;font-weight:700;text-decoration:none;transition:all 0.25s;box-shadow:var(--shadow-md);">
        <?php esc_html_e( 'Open Pan-India Panchangam Dashboard', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>
