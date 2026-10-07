<?php
/**
 * Single Post Template for Articles
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="single-post-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 860px;">

    <!-- Breadcrumbs -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Articles', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header" style="margin-bottom: 2rem;">
          <div style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
            <?php the_category( ', ' ); ?>
          </div>
          <h1 class="entry-title" style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); line-height: 1.25; margin: 0 0 1rem 0;">
            <?php the_title(); ?>
          </h1>
          <div style="display:flex;align-items:center;gap:1rem;color:var(--clr-text-muted);font-size:0.875rem;border-bottom:1px solid var(--clr-border);padding-bottom:1rem;">
            <span>By <?php the_author(); ?></span>
            <span>•</span>
            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
          </div>
        </header>

        <?php if ( has_post_thumbnail() ) : ?>
          <div class="post-featured-image" style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md);">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;display:block;' ] ); ?>
          </div>
        <?php endif; ?>

        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text); margin-bottom: 3rem;">
          <?php the_content(); ?>
        </div>

        <footer class="entry-footer" style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--clr-border);">
          <?php the_tags( '<div style="font-size:0.85rem;color:var(--clr-text-muted);"><span style="font-weight:600;">Tags:</span> ', ', ', '</div>' ); ?>
        </footer>

        <!-- Related Articles -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'post',
          'posts_per_page' => 3,
          'post__not_in'   => [ get_the_ID() ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Related Articles', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/article/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

      </article>
    <?php endwhile; endif; ?>

  </div>
</div>

<?php
get_footer();
