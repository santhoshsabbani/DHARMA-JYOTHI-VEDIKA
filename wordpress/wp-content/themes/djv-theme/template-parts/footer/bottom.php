<?php
/**
 * Template Part: Footer Bottom Bar
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="footer-bottom">
  <div style="max-width:1200px;margin:0 auto;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:0.75rem;font-size:0.8rem;color:#A08070;">
    <div>
      <p style="margin:0;">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> (DJV). <?php esc_html_e( 'All rights reserved.', 'djv-theme' ); ?></p>
      <div style="display:flex;gap:1rem;margin-top:0.35rem;">
        <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:#A08070;text-decoration:none;"><?php esc_html_e( 'Privacy Policy', 'djv-theme' ); ?></a>
        <span>•</span>
        <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" style="color:#A08070;text-decoration:none;"><?php esc_html_e( 'Terms of Service', 'djv-theme' ); ?></a>
        <span>•</span>
        <a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>" style="color:#A08070;text-decoration:none;"><?php esc_html_e( 'Astronomical Disclaimer', 'djv-theme' ); ?></a>
      </div>
    </div>
    <p style="margin:0;text-align:right;">
      <?php esc_html_e( 'Astronomical calculations based on Lahiri Ayanamsa (Chitrapaksha) & Jean Meeus Ephemeris.', 'djv-theme' ); ?>
    </p>
  </div>
</div>
