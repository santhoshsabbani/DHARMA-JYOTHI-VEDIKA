<?php
/**
 * Template Part: Dynamic Mantra Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id          = get_the_ID();
$sanskrit_text    = get_post_meta( $post_id, '_djv_original_text', true );
$telugu_text      = get_post_meta( $post_id, '_djv_telugu_text', true );
$meaning          = get_post_meta( $post_id, '_djv_meaning', true );
$chant_count      = get_post_meta( $post_id, '_djv_chant_count', true );
?>
<div class="mantra-card" id="mantra-card-<?php echo esc_attr( $post_id ); ?>" style="border:1px solid var(--clr-border);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
    <h3 style="font-family:var(--font-heading,serif);font-size:1.25rem;color:var(--clr-primary);margin:0;line-height:1.3;">
      <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
        <?php the_title(); ?>
      </a>
    </h3>
    <span style="font-size:1.5rem;" aria-hidden="true">📿</span>
  </div>

  <?php if ( $sanskrit_text ) : ?>
    <div style="font-family:'Noto Sans Devanagari',serif;font-size:1rem;color:var(--clr-primary);background:#FFF9F0;padding:0.75rem 1rem;border-radius:0.5rem;margin-bottom:0.75rem;line-height:1.6;border-left:3px solid var(--clr-primary);">
      <?php echo esc_html( wp_trim_words( $sanskrit_text, 16 ) ); ?>
    </div>
  <?php endif; ?>

  <?php if ( $meaning ) : ?>
    <p style="font-size:0.85rem;color:var(--clr-text-secondary);line-height:1.6;margin:0 0 1rem 0;flex:1;">
      <strong><?php esc_html_e( 'Meaning:', 'djv-theme' ); ?></strong> <?php echo esc_html( wp_trim_words( $meaning, 18 ) ); ?>
    </p>
  <?php endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:auto;font-size:0.8125rem;">
    <?php if ( $chant_count ) : ?>
      <span style="color:var(--clr-accent,#C89432);font-weight:600;">
        🔁 <?php echo esc_html( $chant_count ); ?>
      </span>
    <?php endif; ?>
    <a href="<?php the_permalink(); ?>" style="font-weight:600;color:var(--clr-primary);text-decoration:none;">
      <?php esc_html_e( 'Full Stotram & Audio', 'djv-theme' ); ?> →
    </a>
  </div>
</div>
