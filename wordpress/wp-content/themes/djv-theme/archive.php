<?php
/**
 * Archive Template for Articles / Posts / Categories / Tags
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="archive-articles-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <!-- Breadcrumbs -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Vedic Articles & Wisdom', 'djv-theme' ); ?></span>
    </nav>

    <!-- Header -->
    <header class="archive-header" style="margin-bottom: 2.5rem;">
      <h1 class="archive-title" style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php
        if ( is_category() ) {
          single_cat_title( __( 'Category: ', 'djv-theme' ) );
        } elseif ( is_tag() ) {
          single_tag_title( __( 'Tag: ', 'djv-theme' ) );
        } elseif ( is_author() ) {
          the_author();
        } else {
          esc_html_e( 'Vedic Articles & Spiritual Wisdom', 'djv-theme' );
        }
        ?>
        <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
          ధార్మిక వ్యాసాలు &amp; వేద విజ్ఞానం
        </span>
      </h1>
      <?php if ( get_the_archive_description() ) : ?>
        <div style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
          <?php echo get_the_archive_description(); ?>
        </div>
      <?php else : ?>
        <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 780px; line-height: 1.6;">
          <?php esc_html_e( 'Essays on Sanatana Dharma, astronomical science behind Hindu festivals, temple architecture, and practical Vedic philosophy.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </header>

    <!-- Posts Grid -->
    <div class="articles-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'template-parts/article/card' ); ?>
        <?php endwhile; ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📰</div>
          <h3><?php esc_html_e( 'No articles found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted);"><?php esc_html_e( 'Check back soon for new publications from our Vedic scholars.', 'djv-theme' ); ?></p>
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
