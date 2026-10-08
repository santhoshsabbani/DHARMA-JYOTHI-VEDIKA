<?php
/**
 * Template Part: Header Actions
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="header-actions">
  <!-- Global Trilingual Switcher -->
  <div class="header-lang-switch" id="header-lang-switch" style="display:inline-flex;align-items:center;background:#FFF;border:1.5px solid var(--clr-border, #E8DFD3);border-radius:9999px;padding:0.2rem 0.35rem;gap:0.2rem;box-shadow:var(--shadow-sm, 0 1px 3px rgba(0,0,0,0.05));">
    <button type="button" class="hdr-lang-btn active" data-lang="en" style="border:none;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.25rem 0.55rem;border-radius:9999px;cursor:pointer;font-size:0.75rem;font-weight:700;transition:all 0.2s;">EN</button>
    <button type="button" class="hdr-lang-btn" data-lang="te" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.25rem 0.55rem;border-radius:9999px;cursor:pointer;font-size:0.75rem;font-weight:700;font-family:var(--font-telugu, sans-serif);transition:all 0.2s;">తె</button>
    <button type="button" class="hdr-lang-btn" data-lang="hi" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.25rem 0.55rem;border-radius:9999px;cursor:pointer;font-size:0.75rem;font-weight:700;font-family:'Noto Sans Devanagari', serif;transition:all 0.2s;">हि</button>
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
