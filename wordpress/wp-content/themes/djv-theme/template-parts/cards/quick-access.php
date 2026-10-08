<?php
/**
 * Template Part: Quick Access Navigation Cards
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
<section class="quick-access" aria-labelledby="quick-access-heading">
  <div class="container">
    <h2 class="sr-only" id="quick-access-heading"><?php esc_html_e( 'Quick Access', 'djv-theme' ); ?></h2>
    <div class="qa-grid" role="list">

      <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="qa-card" id="qa-panchangam" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( "Today's Panchangam", 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Daily Vedic Calendar', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>" class="qa-card" id="qa-festivals" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Annual Vrats & Days', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/muhurtham/' ) ); ?>" class="qa-card" id="qa-muhurtham" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Muhurtham', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Auspicious Timings', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/pooja/' ) ); ?>" class="qa-card" id="qa-pooja" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Pooja Guides', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Vedic Vidhi & Rituals', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>" class="qa-card" id="qa-mantras" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"/><path d="M12 6v6l4 2"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Mantras', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Sacred Chants & Stotras', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>" class="qa-card" id="qa-temples" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Temples', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Sacred Shrines of Bharat', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>" class="qa-card" id="qa-calendar" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Calendar', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Monthly Hindu Calendar', 'djv-theme' ); ?></span>
      </a>

      <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" class="qa-card" id="qa-services" role="listitem">
        <div class="qa-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
        <span class="qa-label"><?php esc_html_e( 'Services', 'djv-theme' ); ?></span>
        <span class="qa-sublabel"><?php esc_html_e( 'Purohit & Vedic Seva', 'djv-theme' ); ?></span>
      </a>

    </div>
  </div>
</section>
