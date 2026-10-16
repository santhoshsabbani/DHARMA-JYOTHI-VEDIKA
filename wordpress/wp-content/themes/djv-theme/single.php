<?php
/**
 * Single Post Template for Articles
 *
 * @package DJV_Theme
 */

get_header();

$post_id   = get_the_ID();
$post_lang = get_post_meta( $post_id, 'djv_language', true ) ?: get_post_meta( $post_id, '_lang', true ) ?: 'en';

$font_family_heading = 'var(--font-heading, serif)';
$font_family_body    = 'inherit';
$lang_label          = 'English';
$badge_bg            = '#eff6ff';
$badge_clr           = '#1e40af';

if ( $post_lang === 'te' ) {
    $font_family_heading = 'var(--font-telugu, sans-serif)';
    $font_family_body    = 'var(--font-telugu, sans-serif)';
    $lang_label          = 'తెలుగు';
    $badge_bg            = '#fef3c7';
    $badge_clr           = '#92400e';
} elseif ( $post_lang === 'hi' ) {
    $font_family_heading = 'var(--font-hindi, sans-serif)';
    $font_family_body    = 'var(--font-hindi, sans-serif)';
    $lang_label          = 'हिन्दी';
    $badge_bg            = '#fce7f3';
    $badge_clr           = '#9d174d';
}
?>

<div class="single-post-wrapper" style="padding: calc(var(--header-h, 72px) + 2rem) 0 5rem 0; background: var(--clr-bg, #fdfaf6);">
  <div class="container" style="max-width: 860px; margin: 0 auto; padding: 0 1.25rem;">

    <!-- Breadcrumbs -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #78716c); margin-bottom: 1.25rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Articles', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7a2419); font-weight: 600; font-family: <?php echo esc_attr( $font_family_heading ); ?>;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header" style="margin-bottom: 2rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--clr-accent, #c89432); text-transform: uppercase; letter-spacing: 0.05em;">
              <?php the_category( ', ' ); ?>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 9999px; background: <?php echo esc_attr( $badge_bg ); ?>; color: <?php echo esc_attr( $badge_clr ); ?>; font-family: <?php echo esc_attr( $font_family_heading ); ?>;">
              <?php echo esc_html( $lang_label ); ?>
            </span>
          </div>

          <h1 class="entry-title" style="font-family: <?php echo esc_attr( $font_family_heading ); ?>; font-size: 2.35rem; color: var(--clr-primary, #7a2419); line-height: 1.3; margin: 0 0 1rem 0;">
            <?php the_title(); ?>
          </h1>
          <div style="display:flex;align-items:center;gap:1rem;color:var(--clr-text-muted, #78716c);font-size:0.875rem;border-bottom:1px solid var(--clr-border, #e7e5e4);padding-bottom:1rem;">
            <span>By <?php the_author(); ?></span>
            <span>•</span>
            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
          </div>
        </header>

        <?php if ( has_post_thumbnail() ) : ?>
          <div class="post-featured-image" style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md, 0 4px 16px rgba(0,0,0,0.08));">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;display:block;' ] ); ?>
          </div>
        <?php endif; ?>

        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text, #1c1917); margin-bottom: 3rem; font-family: <?php echo esc_attr( $font_family_body ); ?>;">
          <?php the_content(); ?>
        </div>

        <footer class="entry-footer" style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--clr-border, #e7e5e4);">
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
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #e7e5e4);">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.75rem; color: var(--clr-primary, #7a2419); margin-bottom: 1.5rem;">
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
