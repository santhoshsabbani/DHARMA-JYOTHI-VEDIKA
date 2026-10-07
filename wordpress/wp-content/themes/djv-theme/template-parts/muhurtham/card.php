<?php
/**
 * Template Part: Dynamic Muhurtham Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id    = get_the_ID();
$category   = get_post_meta( $post_id, '_djv_category', true );
$auspicious = get_post_meta( $post_id, '_djv_auspicious', true );
?>
<div class="muhurtham-card" id="muhurtham-card-<?php echo esc_attr( $post_id ); ?>" style="border:1px solid var(--clr-border);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;position:relative;">
  <?php if ( $category ) : ?>
    <span style="font-size:0.75rem;font-weight:700;color:var(--clr-accent,#C89432);text-transform:uppercase;margin-bottom:0.4rem;letter-spacing:0.05em;">
      ✨ <?php echo esc_html( $category ); ?> Muhurtham
    </span>
  <?php endif; ?>

  <h3 style="font-family:var(--font-heading,serif);font-size:1.3rem;color:var(--clr-primary);margin:0 0 0.5rem 0;line-height:1.3;">
    <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
      <?php the_title(); ?>
    </a>
  </h3>

  <?php if ( $auspicious ) : ?>
    <div style="background:#FFF9F0;border-left:3px solid var(--clr-accent,#C89432);padding:0.6rem 0.8rem;border-radius:0.35rem;font-size:0.8125rem;color:var(--clr-dark,#241914);margin-bottom:0.75rem;">
      <strong><?php esc_html_e( 'Favorable Nakshatras:', 'djv-theme' ); ?></strong> <?php echo esc_html( $auspicious ); ?>
    </div>
  <?php endif; ?>

  <p style="font-size:0.875rem;color:var(--clr-text-secondary,#55433C);line-height:1.6;margin:0 0 1rem 0;flex:1;">
    <?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
  </p>

  <div style="margin-top:auto;">
    <a href="<?php the_permalink(); ?>" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary);text-decoration:none;">
      <?php esc_html_e( 'Check Auspicious Dates', 'djv-theme' ); ?> →
    </a>
  </div>
</div>
