<?php
/**
 * Template Name: Pooja Guides & Vidhi
 *
 * Fully dynamic page template querying djv_pooja CPT.
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="pooja-page-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Pooja Guides', 'djv-theme' ); ?></span>
    </nav>

    <header class="page-header" style="margin-bottom: 2.5rem;">
      <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Pooja Guides & Vidhi', 'djv-theme' ); ?>
        <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
          పూజా విధానం, సంకల్పం &amp; సామగ్రి
        </span>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
        <?php esc_html_e( 'Authentic step-by-step procedures, required samagri lists, sankalpam, and naivedyam for daily and special worship.', 'djv-theme' ); ?>
      </p>
    </header>

    <!-- Dynamic Pooja Cards Grid -->
    <div class="pooja-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
      <?php
      $pooja_query = new WP_Query([
        'post_type'      => 'djv_pooja',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
      ]);

      if ( $pooja_query->have_posts() ) :
        while ( $pooja_query->have_posts() ) : $pooja_query->the_post();
          get_template_part( 'template-parts/pooja/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
          <h3><?php esc_html_e( 'No pooja guides found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted);"><?php esc_html_e( 'Please check back shortly or explore our festivals.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php
get_footer();
