<?php
/**
 * Dharma Jyothi Vedika — Theme Header
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="theme-color" content="#7A2419" />
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ════════════════════════════════════════════════════════════
     SITE HEADER
════════════════════════════════════════════════════════════ -->
<header class="site-header" id="site-header" role="banner">
  <div class="header-inner">
    <!-- Reusable Branding -->
    <?php get_template_part( 'template-parts/header/branding' ); ?>

    <!-- Reusable Desktop Navigation -->
    <?php get_template_part( 'template-parts/header/navigation' ); ?>

    <!-- Reusable Header Actions -->
    <?php get_template_part( 'template-parts/header/actions' ); ?>
  </div>
</header>

<!-- ════════════════════════════════════════════════════════════
     MOBILE NAVIGATION DRAWER
════════════════════════════════════════════════════════════ -->
<div class="mobile-nav" id="mobile-nav" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Navigation menu', 'djv-theme' ); ?>">
  <div class="mobile-nav-drawer" id="mobile-nav-drawer">
    <div class="mobile-nav-header">
      <div class="mobile-nav-brand">
        <div class="brand-title"><?php echo esc_html( function_exists( 'djv__' ) ? djv__( 'PujaPanchang' ) : ( get_bloginfo( 'name' ) ?: 'PujaPanchang' ) ); ?></div>
        <div class="brand-sub">Vedic Panchangam &amp; Devotion</div>
      </div>
      <button id="mobile-nav-close-btn" class="mobile-nav-close" aria-label="<?php esc_attr_e( 'Close navigation menu', 'djv-theme' ); ?>">✕</button>
    </div>

    <!-- Mobile Drawer Location & Language Bar -->
    <div class="mobile-drawer-toolbar">
      <!-- Location Button in Drawer -->
      <button type="button" class="mobile-drawer-loc-btn global-location-pill" aria-label="<?php esc_attr_e( 'Change Location', 'djv-theme' ); ?>">
        <span>📍</span>
        <span class="global-location-name"><?php esc_html_e( 'Hyderabad', 'djv-theme' ); ?></span>
        <span class="mobile-loc-edit"><?php esc_html_e( 'Change', 'djv-theme' ); ?> ▾</span>
      </button>

      <!-- Language Switcher in Drawer -->
      <div class="mobile-drawer-lang-switch" role="group" aria-label="<?php esc_attr_e( 'Select Language', 'djv-theme' ); ?>">
        <?php $cur_lang = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en'; ?>
        <button type="button" class="hdr-lang-btn <?php echo $cur_lang === 'en' ? 'active' : ''; ?>" data-lang="en">EN</button>
        <button type="button" class="hdr-lang-btn <?php echo $cur_lang === 'te' ? 'active' : ''; ?>" data-lang="te">తెలుగు</button>
        <button type="button" class="hdr-lang-btn <?php echo $cur_lang === 'hi' ? 'active' : ''; ?>" data-lang="hi">हिन्दी</button>
      </div>
    </div>

    <nav aria-label="<?php esc_attr_e( 'Mobile Navigation', 'djv-theme' ); ?>" class="mobile-drawer-links">
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/' ) ) ); ?>" class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>">🏠 <?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/panchangam/' ) ) ); ?>" class="nav-link <?php echo is_page( 'panchangam' ) ? 'active' : ''; ?>">📅 <?php esc_html_e( 'Panchangam', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/festivals/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_festival' ) ? 'active' : ''; ?>">🎊 <?php esc_html_e( 'Festivals', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/muhurtham/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_muhurtham' ) ? 'active' : ''; ?>">⏰ <?php esc_html_e( 'Muhurtham', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/pooja/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_pooja' ) ? 'active' : ''; ?>">🪔 <?php esc_html_e( 'Pooja', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/mantras/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_mantra' ) ? 'active' : ''; ?>">📿 <?php esc_html_e( 'Mantras', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/temples/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_temple' ) ? 'active' : ''; ?>">🛕 <?php esc_html_e( 'Temples', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/articles/' ) ) ); ?>" class="nav-link <?php echo is_home() || is_archive() || is_singular( 'post' ) ? 'active' : ''; ?>">📰 <?php esc_html_e( 'Articles', 'djv-theme' ); ?></a>
      <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/services/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_service' ) ? 'active' : ''; ?>">⭐ <?php esc_html_e( 'Services', 'djv-theme' ); ?></a>
    </nav>

    <!-- Drawer Today's Panchangam CTA -->
    <div class="mobile-drawer-footer">
      <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="mobile-drawer-cta">
        <span>🕉</span> <?php esc_html_e( "Today's Panchangam", 'djv-theme' ); ?>
      </a>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     SEARCH OVERLAY
════════════════════════════════════════════════════════════ -->
<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search overlay', 'djv-theme' ); ?>">
  <div class="search-box">
    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <div class="search-input-wrap">
        <input type="search" name="s" class="search-input" id="search-input" placeholder="<?php esc_attr_e( 'Search panchangam, festivals, mantras, temples...', 'djv-theme' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'djv-theme' ); ?>" value="<?php echo get_search_query(); ?>" />
        <button type="submit" class="header-btn header-btn--panchangam" id="search-submit-btn" aria-label="<?php esc_attr_e( 'Submit Search', 'djv-theme' ); ?>">🔍</button>
        <button type="button" class="header-btn header-btn--icon" id="search-close-btn" aria-label="<?php esc_attr_e( 'Close search', 'djv-theme' ); ?>">✕</button>
      </div>
    </form>
    <div class="search-chips" aria-label="<?php esc_attr_e( 'Quick search suggestions', 'djv-theme' ); ?>">
      <span class="search-chip" data-search="Panchangam"><?php esc_html_e( "Today's Panchangam", 'djv-theme' ); ?></span>
      <span class="search-chip" data-search="Diwali"><?php esc_html_e( 'Diwali 2026', 'djv-theme' ); ?></span>
      <span class="search-chip" data-search="Vivaha Muhurtham"><?php esc_html_e( 'Marriage Muhurtham', 'djv-theme' ); ?></span>
      <span class="search-chip" data-search="Gayatri"><?php esc_html_e( 'Gayatri Mantra', 'djv-theme' ); ?></span>
      <span class="search-chip" data-search="Tirupati"><?php esc_html_e( 'Tirupati Temple', 'djv-theme' ); ?></span>
      <span class="search-chip" data-search="Satyanarayana"><?php esc_html_e( 'Satyanarayana Pooja', 'djv-theme' ); ?></span>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     REUSABLE PAN-INDIA LOCATION MODAL
════════════════════════════════════════════════════════════ -->
<?php get_template_part( 'template-parts/location/modal' ); ?>

<main id="main-content">
