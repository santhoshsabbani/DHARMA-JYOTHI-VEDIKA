<?php
/**
 * Template Name: Hindu Festivals & Vrats Calendar
 * Description: Dedicated page template for the festivals directory.
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="festivals-page-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></span>
    </nav>

    <!-- Header -->
    <header style="margin-bottom: 2.5rem;">
      <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Hindu Festivals & Vrats (హిందూ పండుగలు & వ్రతాలు)', 'djv-theme' ); ?>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem;">
        <?php esc_html_e( 'Calculated dynamically according to Vedic tithi, nakshatra, and solar transit rules.', 'djv-theme' ); ?>
      </p>
    </header>

    <!-- Dynamic Festival Cards Grid -->
    <div class="festival-cards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
      <?php
      $festivals_query = new WP_Query([
        'post_type'      => 'djv_festival',
        'posts_per_page' => 20,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value',
        'meta_key'       => '_djv_festival_date',
        'order'          => 'ASC',
      ]);

      if ( $festivals_query->have_posts() ) :
        while ( $festivals_query->have_posts() ) : $festivals_query->the_post();
          get_template_part( 'template-parts/festival/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column: 1/-1; text-align: center; color: var(--clr-text-muted);">
          <?php esc_html_e( 'No festivals found in the database.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php
get_footer();
