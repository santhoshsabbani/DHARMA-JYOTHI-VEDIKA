<?php
/**
 * Archive Template for Mantras & Slokas (djv_mantra)
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="archive-mantra-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Mantras & Slokas', 'djv-theme' ); ?></span>
    </nav>

    <!-- Header -->
    <header class="archive-header" style="margin-bottom: 2.5rem;">
      <h1 class="archive-title" style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Mantras &amp; Slokas Library', 'djv-theme' ); ?>
        <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
          మంత్రాలు, స్తోత్రాలు &amp; నిత్య ప్రార్థనా శ్లోకాలు
        </span>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
        <?php esc_html_e( 'Sacred Vedic hymns, deity stotras, daily devotional verses with authentic Devanagari Sanskrit, Telugu script, transliteration, and spiritual meanings.', 'djv-theme' ); ?>
      </p>
    </header>

    <!-- Mantra Cards Grid -->
    <div class="mantra-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'template-parts/mantra/card' ); ?>
        <?php endwhile; ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📿</div>
          <h3><?php esc_html_e( 'No mantras found', 'djv-theme' ); ?></h3>
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
