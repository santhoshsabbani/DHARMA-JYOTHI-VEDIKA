<?php
/**
 * Main Fallback / Blog Index Template
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="archive-wrapper" style="padding: 3rem 0 5rem 0;">
  <div class="container">

    <header class="archive-header" style="margin-bottom: 2.5rem; text-align: center;">
      <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php
        if ( is_search() ) {
          printf( __( 'Search Results for: %s', 'djv-theme' ), '<span>' . get_search_query() . '</span>' );
        } elseif ( is_archive() ) {
          the_archive_title();
        } else {
          _e( 'Vedic Articles & Knowledge', 'djv-theme' );
        }
        ?>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem;">
        Explore teachings, Jyotish insights, traditions, and devotional guides.
      </p>
    </header>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:2rem;">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:#FFF;border-radius:1rem;border:1px solid var(--clr-border);overflow:hidden;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;">
          <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%;height:200px;object-fit:cover;display:block;' ] ); ?>
            </a>
          <?php endif; ?>

          <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
            <div style="font-size:0.75rem;color:var(--clr-accent);font-weight:700;margin-bottom:0.5rem;text-transform:uppercase;">
              <?php the_category( ', ' ); ?>
            </div>
            <h2 style="font-family:var(--font-heading);font-size:1.35rem;color:var(--clr-primary);margin:0 0 0.75rem 0;">
              <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a>
            </h2>
            <p style="font-size:0.9rem;color:var(--clr-text-secondary);line-height:1.6;margin-bottom:1rem;flex:1;">
              <?php echo wp_trim_words( get_the_excerpt(), 24 ); ?>
            </p>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.8125rem;color:var(--clr-text-muted);">
              <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
              <a href="<?php the_permalink(); ?>" style="font-weight:600;color:var(--clr-primary);text-decoration:none;">Read More →</a>
            </div>
          </div>
        </article>
      <?php endwhile; else : ?>
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--clr-text-muted);">
          No articles found matching your criteria.
        </div>
      <?php endif; ?>
    </div>

    <div class="pagination" style="margin-top: 3rem; text-align: center;">
      <?php the_posts_pagination(); ?>
    </div>

  </div>
</div>

<?php
get_footer();
