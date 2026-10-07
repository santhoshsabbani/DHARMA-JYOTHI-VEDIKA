<?php
/**
 * Template Name: Shubh Muhurtham Finder
 *
 * Fully Dynamic Page Template querying djv_muhurtham CPT.
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="muhurtham-page-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Shubh Muhurtham', 'djv-theme' ); ?></span>
    </nav>

    <header class="page-header" style="margin-bottom: 2.5rem;">
      <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Shubh Muhurtham Finder', 'djv-theme' ); ?>
        <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
          శుభ ముహూర్తములు &amp; కాల నిర్ణయం
        </span>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
        <?php esc_html_e( 'Find auspicious timings for Vivaha (Marriage), Griha Pravesha (House Warming), Namakarana, and new beginnings. Grounded in Vedic Jyotish principles.', 'djv-theme' ); ?>
      </p>
    </header>

    <!-- Muhurtham Grid Dynamic -->
    <div class="muhurtham-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
      <?php
      $muhurtham_query = new WP_Query([
        'post_type'      => 'djv_muhurtham',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
      ]);

      if ( $muhurtham_query->have_posts() ) :
        while ( $muhurtham_query->have_posts() ) : $muhurtham_query->the_post();
          get_template_part( 'template-parts/muhurtham/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏰</div>
          <h3><?php esc_html_e( 'No muhurtham entries found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted);"><?php esc_html_e( 'Please check back shortly or consult our Vedic pandits.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Live Panchangam Integration Banner -->
    <div style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF 100%); border: 1px solid var(--clr-secondary); border-radius: 1rem; padding: 2.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
      <div style="max-width: 600px;">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; letter-spacing: 0.05em;">
          🕉 <?php esc_html_e( 'Auspicious Timing Accuracy', 'djv-theme' ); ?>
        </span>
        <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin: 0.5rem 0 0.75rem 0;">
          <?php esc_html_e( 'Match Muhurtham with Exact Daily Ephemeris', 'djv-theme' ); ?>
        </h2>
        <p style="color: var(--clr-text-secondary); margin: 0; font-size: 0.95rem; line-height: 1.6;">
          <?php esc_html_e( 'Avoid Rahu Kalam, Yamagandam, and Dur Muhurtham periods by cross-referencing your auspicious date with our live local astronomical calendar.', 'djv-theme' ); ?>
        </p>
      </div>
      <div>
        <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="btn-hero-primary" style="text-decoration: none; padding: 0.85rem 1.75rem; border-radius: 9999px; background: var(--clr-primary); color: #FFF; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
          <span>📅</span> <?php esc_html_e( "Open Today's Panchangam", 'djv-theme' ); ?>
        </a>
      </div>
    </div>

  </div>
</div>

<?php
get_footer();
