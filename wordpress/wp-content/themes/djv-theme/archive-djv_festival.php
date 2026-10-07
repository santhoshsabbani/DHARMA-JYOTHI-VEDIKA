<?php
/**
 * Archive Template for Festivals (djv_festival)
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="archive-festival-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Festivals & Vrats', 'djv-theme' ); ?></span>
    </nav>

    <!-- Header -->
    <header class="archive-header" style="margin-bottom: 2.5rem;">
      <h1 class="archive-title" style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Hindu Festivals & Vrats Calendar', 'djv-theme' ); ?>
        <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
          హిందూ పండుగలు &amp; వ్రతాల క్యాలెండర్
        </span>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
        <?php esc_html_e( 'Accurate festival dates, auspicious fasting timings (vrats), puja vidhi, and rituals calculated according to authentic Vedic tithi, nakshatra, and solar transit rules.', 'djv-theme' ); ?>
      </p>
    </header>

    <!-- Festival Cards Grid -->
    <div class="festival-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'template-parts/festival/card' ); ?>
        <?php endwhile; ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
          <h3><?php esc_html_e( 'No festivals found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted);"><?php esc_html_e( 'Please check back shortly or select another category.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper" style="margin-top: 3rem; text-align: center;">
      <?php
      the_posts_pagination([
        'prev_text' => '← ' . __( 'Previous', 'djv-theme' ),
        'next_text' => __( 'Next', 'djv-theme' ) . ' →',
      ]);
      ?>
    </div>

  </div>
</div>

<?php
get_footer();
