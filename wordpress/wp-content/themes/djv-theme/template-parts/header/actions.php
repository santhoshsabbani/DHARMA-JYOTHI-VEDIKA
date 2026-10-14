<?php
/**
 * Template Part: Header Actions
 *
 * @package DJV_Theme
 */
$current_lang = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';
?>
<div class="header-actions">
  <!-- Global Trilingual Switcher (Reusable Component per Section 4 & 7) -->
  <div class="header-lang-switch" id="header-lang-switch" role="group" aria-label="<?php esc_attr_e( 'Select Language', 'djv-theme' ); ?>">
    <button type="button" class="hdr-lang-btn <?php echo $current_lang === 'en' ? 'active' : ''; ?>" data-lang="en" aria-label="English" title="English">EN</button>
    <button type="button" class="hdr-lang-btn <?php echo $current_lang === 'te' ? 'active' : ''; ?>" data-lang="te" aria-label="తెలుగు (Telugu)" title="తెలుగు">తెలుగు</button>
    <button type="button" class="hdr-lang-btn <?php echo $current_lang === 'hi' ? 'active' : ''; ?>" data-lang="hi" aria-label="हिन्दी (Hindi)" title="हिन्दी">हिन्दी</button>
  </div>

  <!-- Location Pill Button -->
  <button type="button" class="header-btn header-btn--location global-location-pill" id="global-location-btn" aria-label="<?php esc_attr_e( 'Change Location', 'djv-theme' ); ?>">
    <span aria-hidden="true">📍</span>
    <span class="global-location-name" id="global-location-name"><?php esc_html_e( 'Hyderabad', 'djv-theme' ); ?></span>
  </button>

  <!-- Search Trigger Button -->
  <button type="button" class="header-btn header-btn--icon" id="search-open-btn" aria-label="<?php esc_attr_e( 'Search platform', 'djv-theme' ); ?>">
    <span aria-hidden="true">🔍</span>
  </button>

  <!-- Today's Panchangam CTA -->
  <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="header-btn header-btn--panchangam" id="header-today-btn">
    <span aria-hidden="true">🕉</span>
    <?php esc_html_e( "Today's Panchangam", 'djv-theme' ); ?>
  </a>

  <!-- Mobile Hamburger -->
  <button type="button" class="menu-toggle" id="menu-toggle-btn" aria-label="<?php esc_attr_e( 'Open navigation menu', 'djv-theme' ); ?>" aria-expanded="false">
    ☰
  </button>
</div>
