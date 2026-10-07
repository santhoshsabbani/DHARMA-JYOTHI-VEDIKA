<?php
/**
 * Template Part: Footer Navigation Links
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
<!-- Column 2: Panchangam & Jyotish -->
<div class="footer-links">
  <h3 style="color:#FFF;font-size:1rem;margin-bottom:0.75rem;"><?php esc_html_e( 'Panchangam & Muhurtham', 'djv-theme' ); ?></h3>
  <nav aria-label="<?php esc_attr_e( 'Panchangam links', 'djv-theme' ); ?>">
    <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>"><?php esc_html_e( "Today's Panchangam", 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/muhurtham/' ) ); ?>"><?php esc_html_e( 'Shubh Muhurtham Finder', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>"><?php esc_html_e( 'Festival Calendar', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>"><?php esc_html_e( 'Monthly Vedic Calendar', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/panchangam/?view=choghadiya' ) ); ?>"><?php esc_html_e( 'Day & Night Choghadiya', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/panchangam/?view=horai' ) ); ?>"><?php esc_html_e( 'Hora Timings (గ్రహ హోరలు)', 'djv-theme' ); ?></a>
  </nav>
</div>

<!-- Column 3: Devotional Resources -->
<div class="footer-links">
  <h3 style="color:#FFF;font-size:1rem;margin-bottom:0.75rem;"><?php esc_html_e( 'Devotion & Seva', 'djv-theme' ); ?></h3>
  <nav aria-label="<?php esc_attr_e( 'Devotional links', 'djv-theme' ); ?>">
    <a href="<?php echo esc_url( home_url( '/pooja/' ) ); ?>"><?php esc_html_e( 'Pooja & Vrat Vidhi', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>"><?php esc_html_e( 'Sacred Mantras & Stotrams', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>"><?php esc_html_e( 'Temples of Bharat', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>"><?php esc_html_e( 'Vedic Wisdom & Articles', 'djv-theme' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Vedic Services & Booking', 'djv-theme' ); ?></a>
  </nav>
</div>

<!-- Column 4: Newsletter & Daily Guidance -->
<div class="footer-links">
  <h3 style="color:#FFF;font-size:1rem;margin-bottom:0.75rem;"><?php esc_html_e( 'Daily Vedic Guidance', 'djv-theme' ); ?></h3>
  <p style="font-size:0.8125rem;color:#D8C4B4;margin-bottom:0.75rem;line-height:1.5;">
    <?php esc_html_e( "Receive tomorrow's auspicious timings, Tithi, Nakshatra, and festival alerts directly in your inbox.", 'djv-theme' ); ?>
  </p>
  <form class="footer-newsletter-form" onsubmit="event.preventDefault(); alert('ధన్యవాదాలు! Thank you for subscribing to daily Panchangam updates.');" style="display:flex;gap:0.35rem;margin-bottom:0.75rem;">
    <input type="email" placeholder="<?php esc_attr_e( 'Your email address', 'djv-theme' ); ?>" required style="padding:0.45rem 0.65rem;border-radius:0.35rem;border:1px solid #7A2419;font-size:0.8125rem;background:#FFF;color:#222;flex:1;" />
    <button type="submit" style="padding:0.45rem 0.75rem;background:#C89432;color:#241914;font-weight:700;border:none;border-radius:0.35rem;cursor:pointer;font-size:0.8125rem;">
      <?php esc_html_e( 'Subscribe', 'djv-theme' ); ?>
    </button>
  </form>
  <span style="font-size:0.75rem;color:#A08070;"><?php esc_html_e( 'No spam. Strictly devotional and astronomical updates.', 'djv-theme' ); ?></span>
</div>
