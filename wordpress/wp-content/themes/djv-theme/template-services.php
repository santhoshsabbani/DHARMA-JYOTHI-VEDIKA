<?php
/**
 * Template Name: Vedic Services Directory
 *
 * Fully dynamic page template querying djv_service CPT.
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="services-page-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Vedic Services', 'djv-theme' ); ?></span>
    </nav>

    <header class="page-header" style="margin-bottom: 2.5rem;">
      <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Vedic Services & Consultations', 'djv-theme' ); ?>
        <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
          వైదిక సేవలు, హోమాలు &amp; జ్యోతిష్య సంప్రదింపులు
        </span>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
        <?php esc_html_e( 'Authentic Vedic homams, verified pandit bookings for home vrathams, personalized Jyotish horoscope readings, and traditional Vastu inspections.', 'djv-theme' ); ?>
      </p>
    </header>

    <!-- Services Grid Dynamic -->
    <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
      <?php
      $services_query = new WP_Query([
        'post_type'      => 'djv_service',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
      ]);

      if ( $services_query->have_posts() ) :
        while ( $services_query->have_posts() ) : $services_query->the_post();
          get_template_part( 'template-parts/service/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⭐</div>
          <h3><?php esc_html_e( 'No services found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted);"><?php esc_html_e( 'Please contact us directly for custom Vedic ritual assistance.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php
get_footer();
