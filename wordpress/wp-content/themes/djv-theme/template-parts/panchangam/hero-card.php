<?php
/**
 * Template Part: Hero Quick Panchangam Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

// Passed or fetch SSR data
$ssr_data  = djv_get_ssr_panchangam();
$today_str = date( 'l, F j, Y' );
?>
<div class="hero-quick-card" id="hero-panchangam-card" aria-label="<?php esc_attr_e( "Today's Panchangam summary", 'djv-theme' ); ?>">
  <div class="quick-card-header">
    <div class="quick-card-date" id="hero-date-display" aria-live="polite">
      <?php echo esc_html( $today_str ); ?>
    </div>
    <button type="button" class="quick-card-location global-location-pill" id="hero-loc-btn" aria-label="<?php esc_attr_e( 'Change location', 'djv-theme' ); ?>">
      <span aria-hidden="true">📍</span>
      <span id="hero-loc-name"><?php esc_html_e( 'Hyderabad', 'djv-theme' ); ?></span>
      <span aria-hidden="true">›</span>
    </button>
  </div>

  <!-- Loading skeleton -->
  <div id="hero-pc-loading" aria-live="polite" aria-label="<?php esc_attr_e( 'Loading Panchangam data', 'djv-theme' ); ?>" style="<?php echo $ssr_data ? 'display:none;' : ''; ?>">
    <div class="panchanga-quick-grid" role="list">
      <div class="pq-item" role="listitem"><div class="pq-label"><?php esc_html_e( 'Tithi', 'djv-theme' ); ?></div><div class="pq-value djv-loading-pulse">…</div><div class="pq-time">&nbsp;</div></div>
      <div class="pq-item" role="listitem"><div class="pq-label"><?php esc_html_e( 'Nakshatra', 'djv-theme' ); ?></div><div class="pq-value djv-loading-pulse">…</div><div class="pq-time">&nbsp;</div></div>
      <div class="pq-item" role="listitem"><div class="pq-label"><?php esc_html_e( 'Yoga', 'djv-theme' ); ?></div><div class="pq-value djv-loading-pulse">…</div><div class="pq-time">&nbsp;</div></div>
      <div class="pq-item" role="listitem"><div class="pq-label"><?php esc_html_e( 'Karana', 'djv-theme' ); ?></div><div class="pq-value djv-loading-pulse">…</div><div class="pq-time">&nbsp;</div></div>
    </div>
  </div>

  <!-- Live data container -->
  <div id="hero-pc-data" style="<?php echo $ssr_data ? '' : 'display:none;'; ?>">
    <div class="panchanga-quick-grid" role="list" aria-label="<?php esc_attr_e( 'Panchangam details', 'djv-theme' ); ?>">
      <div class="pq-item" role="listitem">
        <div class="pq-label"><?php esc_html_e( 'Tithi', 'djv-theme' ); ?></div>
        <div class="pq-value" id="hero-tithi"><?php echo esc_html( $ssr_data['tithi']['name'] ?? '—' ); ?></div>
        <div class="pq-time" id="hero-tithi-end"><?php echo esc_html( $ssr_data['tithi']['spanStr'] ?? '' ); ?></div>
      </div>
      <div class="pq-item" role="listitem">
        <div class="pq-label"><?php esc_html_e( 'Nakshatra', 'djv-theme' ); ?></div>
        <div class="pq-value" id="hero-nakshatra"><?php echo esc_html( $ssr_data['nakshatra']['nakshatra']['name'] ?? '—' ); ?></div>
        <div class="pq-time" id="hero-nakshatra-end"><?php echo esc_html( $ssr_data['nakshatra']['spanStr'] ?? '' ); ?></div>
      </div>
      <div class="pq-item" role="listitem">
        <div class="pq-label"><?php esc_html_e( 'Yoga', 'djv-theme' ); ?></div>
        <div class="pq-value" id="hero-yoga"><?php echo esc_html( $ssr_data['yoga']['name'] ?? '—' ); ?></div>
        <div class="pq-time" id="hero-yoga-end"><?php echo esc_html( $ssr_data['yoga']['spanStr'] ?? '' ); ?></div>
      </div>
      <div class="pq-item" role="listitem">
        <div class="pq-label"><?php esc_html_e( 'Karana', 'djv-theme' ); ?></div>
        <div class="pq-value" id="hero-karana"><?php echo esc_html( $ssr_data['karana']['name'] ?? '—' ); ?></div>
        <div class="pq-time" id="hero-karana-end"><?php echo esc_html( $ssr_data['karana']['spanStr'] ?? '' ); ?></div>
      </div>
    </div>

    <div class="sun-row" role="list" aria-label="<?php esc_attr_e( 'Solar and lunar timings', 'djv-theme' ); ?>">
      <div class="sun-item" role="listitem">
        <span class="sun-icon" aria-hidden="true">🌅</span>
        <div class="sun-data">
          <span class="sun-data-label"><?php esc_html_e( 'Sunrise', 'djv-theme' ); ?></span>
          <span class="sun-data-value" id="hero-sunrise"><?php
            $hero_sunrise = $ssr_data['solar']['sunriseStr'] ?? djv_format_ssr_time( $ssr_data['solar']['sunrise'] ?? null );
            echo esc_html( $hero_sunrise ?: '—' );
          ?></span>
        </div>
      </div>
      <div class="sun-item" role="listitem">
        <span class="sun-icon" aria-hidden="true">🌇</span>
        <div class="sun-data">
          <span class="sun-data-label"><?php esc_html_e( 'Sunset', 'djv-theme' ); ?></span>
          <span class="sun-data-value" id="hero-sunset"><?php
            $hero_sunset = $ssr_data['solar']['sunsetStr'] ?? djv_format_ssr_time( $ssr_data['solar']['sunset'] ?? null );
            echo esc_html( $hero_sunset ?: '—' );
          ?></span>
        </div>
      </div>
      <div class="sun-item" role="listitem">
        <span class="sun-icon" aria-hidden="true">🌕</span>
        <div class="sun-data">
          <span class="sun-data-label"><?php esc_html_e( 'Moonrise', 'djv-theme' ); ?></span>
          <span class="sun-data-value" id="hero-moonrise"><?php
            $hero_moonrise = $ssr_data['moonrise']['time'] ?? ( $ssr_data['solar']['moonrise'] ?? djv_format_ssr_time( $ssr_data['moonrise']['datetime'] ?? null ) );
            echo esc_html( $hero_moonrise ?: '—' );
          ?></span>
        </div>
      </div>
    </div>

    <div class="rahu-bar" role="note" aria-label="<?php esc_attr_e( 'Rahu Kalam timing', 'djv-theme' ); ?>">
      <span class="rahu-label">⚠️ <?php esc_html_e( 'Rahu Kalam', 'djv-theme' ); ?></span>
      <span class="rahu-time" id="hero-rahu"><?php
        $hero_rahu = $ssr_data['timings']['rahuKalam']['text'] ?? djv_format_ssr_period( $ssr_data['timings']['rahuKalam'] ?? null );
        echo esc_html( $hero_rahu ?: '—' );
      ?></span>
    </div>
  </div>

  <!-- Error state -->
  <div id="hero-pc-error" style="display:none;" role="alert" aria-live="assertive">
    <div style="padding:1.25rem;text-align:center;color:var(--clr-text-muted);">
      <div style="font-size:2rem;margin-bottom:0.5rem;">🙏</div>
      <div style="font-weight:600;margin-bottom:0.25rem;"><?php esc_html_e( 'Panchangam Unavailable', 'djv-theme' ); ?></div>
      <div style="font-size:0.8rem;" id="hero-pc-error-msg"><?php esc_html_e( 'Unable to load live Panchangam data. Please try again shortly.', 'djv-theme' ); ?></div>
      <button type="button" id="hero-pc-retry-btn" style="margin-top:0.75rem;padding:0.4rem 1rem;background:var(--clr-primary);color:white;border:none;border-radius:9999px;cursor:pointer;font-size:0.8rem;"><?php esc_html_e( 'Retry', 'djv-theme' ); ?></button>
    </div>
  </div>

  <div class="quick-card-cta">
    <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" id="hero-full-panchangam-link">
      <?php esc_html_e( 'View complete Panchangam', 'djv-theme' ); ?> <span aria-hidden="true">→</span>
    </a>
  </div>
</div>
