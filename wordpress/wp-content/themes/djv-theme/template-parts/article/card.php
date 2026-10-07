<?php
/**
 * Template Part: Dynamic Article Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id    = get_the_ID();
$categories = get_the_category( $post_id );
$cat_name   = ! empty( $categories ) ? $categories[0]->name : __( 'Vedic Wisdom', 'djv-theme' );
?>
<article class="article-card" id="post-card-<?php echo esc_attr( $post_id ); ?>" style="background:#FFF;border-radius:1rem;border:1px solid var(--clr-border);overflow:hidden;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
  <?php if ( has_post_thumbnail() ) : ?>
    <div style="aspect-ratio:16/9;overflow:hidden;">
      <?php the_post_thumbnail( 'medium', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
    </div>
  <?php endif; ?>

  <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;font-size:0.75rem;">
      <span style="color:var(--clr-accent,#C89432);font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">
        <?php echo esc_html( $cat_name ); ?>
      </span>
      <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" style="color:var(--clr-text-muted);">
        <?php echo esc_html( get_the_date() ); ?>
      </time>
    </div>

    <h3 style="font-family:var(--font-heading,serif);font-size:1.25rem;color:var(--clr-primary);margin:0 0 0.5rem 0;line-height:1.35;">
      <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
        <?php the_title(); ?>
      </a>
    </h3>

    <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.6;margin:0 0 1rem 0;flex:1;">
      <?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
    </p>

    <div style="margin-top:auto;">
      <a href="<?php the_permalink(); ?>" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary);text-decoration:none;">
        <?php esc_html_e( 'Read Article', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</article>
