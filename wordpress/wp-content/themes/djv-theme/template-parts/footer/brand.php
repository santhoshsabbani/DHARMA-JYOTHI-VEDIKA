<?php
/**
 * Template Part: Footer Brand Column
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$site_name = get_bloginfo( 'name' ) ?: 'DHARMA JYOTHI VEDIKA';
$site_desc = get_bloginfo( 'description' ) ?: 'ధర్మ జ్యోతి వేదిక';
?>
<div class="footer-brand">
  <div class="footer-logo-row" style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
    <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <ellipse cx="20" cy="30" rx="12" ry="4.5" fill="#C89432" opacity="0.9"/>
      <path d="M10 30 Q8 28 9 25 L31 25 Q32 28 30 30 Z" fill="#7A2419"/>
      <path d="M12 25 Q11 22 14 20 Q18 22 20 22 Q22 22 26 20 Q29 22 28 25 Z" fill="#9A7020"/>
      <path d="M20 22 Q17 17 19 12 Q20 8 20 6 Q21 9 22 12 Q23.5 16 21 20 Q20.5 21 20 22 Z" fill="#E7B75A"/>
    </svg>
    <div>
      <h2 class="footer-title" style="margin:0;font-size:1.15rem;font-family:var(--font-heading,serif);color:#FFF;">
        <span class="djv-lang-field" data-lang="en">DHARMA JYOTHI VEDIKA</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu,sans-serif);">ధర్మ జ్యోతి వేదిక</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari',serif;">धर्म ज्योति वेदिका</span>
      </h2>
      <span class="footer-tagline" style="font-size:0.8rem;color:var(--clr-accent,#E7B75A);">
        <span class="djv-lang-field" data-lang="en">Vedic Panchangam &amp; Devotion</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu,sans-serif);">వేద పంచాంగం &amp; భక్తి</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari',serif;">वैदिक पंचांग एवं भक्ति</span>
      </span>
    </div>
  </div>
  <p class="footer-desc" style="font-size:0.875rem;color:#D8C4B4;line-height:1.6;margin-bottom:1rem;">
    <?php esc_html_e( 'Authentic daily Hindu Panchangam, Muhurtham calculations, festival timings, and Vedic devotional guidance. Calculated dynamically according to classical Surya Siddhanta and modern astronomical ephemerides.', 'djv-theme' ); ?>
  </p>
  <div class="footer-social" style="display:flex;gap:0.75rem;font-size:1.25rem;">
    <a href="https://www.youtube.com/@DharmaJyothiVedika" target="_blank" rel="noopener" aria-label="YouTube" style="color:#E7B75A;text-decoration:none;">▶</a>
    <a href="https://www.instagram.com/dharmajyothivedika" target="_blank" rel="noopener" aria-label="Instagram" style="color:#E7B75A;text-decoration:none;">📷</a>
    <a href="https://www.facebook.com/dharmajyothivedika" target="_blank" rel="noopener" aria-label="Facebook" style="color:#E7B75A;text-decoration:none;">📘</a>
  </div>
</div>
