<?php
/**
 * Template Part: Header Branding
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$site_name = get_bloginfo( 'name' ) ?: 'DHARMA JYOTHI VEDIKA';
$site_desc = get_bloginfo( 'description' ) ?: 'ధర్మ జ్యోతి వేదిక';
?>
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" aria-label="<?php echo esc_attr( $site_name ); ?> — <?php esc_attr_e( 'Home', 'djv-theme' ); ?>">
  <?php if ( has_custom_logo() ) : ?>
    <?php the_custom_logo(); ?>
  <?php else : ?>
    <div class="logo-symbol">
      <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-flame" aria-hidden="true">
        <!-- Diya base -->
        <ellipse cx="20" cy="30" rx="12" ry="4.5" fill="#C89432" opacity="0.9"/>
        <path d="M10 30 Q8 28 9 25 L31 25 Q32 28 30 30 Z" fill="#7A2419"/>
        <path d="M12 25 Q11 22 14 20 Q18 22 20 22 Q22 22 26 20 Q29 22 28 25 Z" fill="#9A7020"/>
        <!-- Lotus petals -->
        <ellipse cx="20" cy="21" rx="4" ry="6" fill="none" stroke="#C89432" stroke-width="1" opacity="0.6" transform="rotate(-20 20 21)"/>
        <ellipse cx="20" cy="21" rx="4" ry="6" fill="none" stroke="#C89432" stroke-width="1" opacity="0.6" transform="rotate(20 20 21)"/>
        <!-- Flame -->
        <path d="M20 22 Q17 17 19 12 Q20 8 20 6 Q21 9 22 12 Q23.5 16 21 20 Q20.5 21 20 22 Z" fill="url(#hdrFlameGrad)"/>
        <path d="M20 22 Q18.5 19 19.5 15 Q20 13 20 12 Q20.5 14 21 16 Q21.5 19 20 22 Z" fill="url(#hdrInnerFlame)" opacity="0.8"/>
        <defs>
          <linearGradient id="hdrFlameGrad" x1="20" y1="22" x2="20" y2="6" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#C89432"/>
            <stop offset="60%" stop-color="#E7B75A"/>
            <stop offset="100%" stop-color="#FFF0CC"/>
          </linearGradient>
          <linearGradient id="hdrInnerFlame" x1="20" y1="22" x2="20" y2="12" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.6"/>
            <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
          </linearGradient>
        </defs>
      </svg>
    </div>
    <div class="logo-text">
      <span class="brand-name djv-lang-field" data-lang="en"><?php echo esc_html( $site_name ); ?></span>
      <span class="brand-name djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu, sans-serif);">ధర్మ జ్యోతి వేదిక</span>
      <span class="brand-name djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari', serif;">धर्म ज्योति वेदिका</span>

      <span class="brand-sub djv-lang-field" data-lang="en" style="font-size:0.75rem;color:var(--clr-accent,#C89432);font-weight:600;">Vedic Panchangam &amp; Devotion</span>
      <span class="brand-sub djv-lang-field" data-lang="te" style="display:none;font-size:0.75rem;color:var(--clr-accent,#C89432);font-family:var(--font-telugu, sans-serif);font-weight:600;">వేద పంచాంగం &amp; భక్తి వేదిక</span>
      <span class="brand-sub djv-lang-field" data-lang="hi" style="display:none;font-size:0.75rem;color:var(--clr-accent,#C89432);font-family:'Noto Sans Devanagari', serif;font-weight:600;">वैदिक पंचांग एवं भक्ति मंच</span>
    </div>
  <?php endif; ?>
</a>
