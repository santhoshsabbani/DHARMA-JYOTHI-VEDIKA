<?php
/**
 * Template Part: Dynamic Temple Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id  = get_the_ID();
$state    = get_post_meta( $post_id, '_djv_state', true );
$district = get_post_meta( $post_id, '_djv_district', true );
$timings  = get_post_meta( $post_id, '_djv_timings', true );
$address  = get_post_meta( $post_id, '_djv_address', true );

$loc_string = $district ? $district . ', ' . $state : $state;
?>
<div class="temple-card" id="temple-card-<?php echo esc_attr( $post_id ); ?>" style="border:1px solid var(--clr-border);background:#FFF;border-radius:1rem;overflow:hidden;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
  <?php if ( has_post_thumbnail() ) : ?>
    <div style="aspect-ratio:16/9;overflow:hidden;">
      <?php the_post_thumbnail( 'medium', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
    </div>
  <?php else : ?>
    <div style="aspect-ratio:16/9;background:linear-gradient(135deg, #7A2419 0%, #4A1209 100%);display:flex;align-items:center;justify-content:center;color:#FFF;font-size:2.75rem;">
      🛕
    </div>
  <?php endif; ?>

  <div style="padding:1.25rem;flex:1;display:flex;flex-direction:column;">
    <?php if ( $loc_string ) : ?>
      <div style="font-size:0.75rem;font-weight:700;color:var(--clr-accent,#C89432);text-transform:uppercase;margin-bottom:0.35rem;letter-spacing:0.05em;">
        📍 <?php echo esc_html( $loc_string ); ?>
      </div>
    <?php endif; ?>

    <h3 style="font-family:var(--font-heading,serif);font-size:1.2rem;color:var(--clr-primary);margin:0 0 0.4rem 0;line-height:1.3;">
      <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
        <?php the_title(); ?>
      </a>
    </h3>

    <?php if ( $timings ) : ?>
      <div style="font-size:0.8rem;color:var(--clr-text-muted);margin-bottom:0.5rem;">
        ⏰ <?php echo esc_html( wp_trim_words( $timings, 8 ) ); ?>
      </div>
    <?php endif; ?>

    <p style="font-size:0.85rem;color:var(--clr-text-secondary);line-height:1.55;margin:0 0 1rem 0;flex:1;">
      <?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
    </p>

    <div style="margin-top:auto;">
      <a href="<?php the_permalink(); ?>" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.85rem;font-weight:600;color:var(--clr-primary);text-decoration:none;">
        <?php esc_html_e( 'Darshan Timings & History', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</div>
