<?php
/**
 * Template Part: Dynamic Pooja Guide Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id  = get_the_ID();
$duration = get_post_meta( $post_id, '_djv_duration', true );
?>
<a href="<?php the_permalink(); ?>" class="pooja-card" id="pooja-card-<?php echo esc_attr( $post_id ); ?>" style="text-decoration:none;display:flex;align-items:flex-start;gap:1.25rem;background:#FFF;border:1px solid var(--clr-border);border-radius:1rem;padding:1.25rem;transition:transform 0.2s,box-shadow 0.2s;">
  <div class="pooja-card-icon" style="font-size:2.25rem;background:rgba(200,148,50,0.12);width:56px;height:56px;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;" aria-hidden="true">
    <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( [56, 56], [ 'style' => 'width:100%;height:100%;object-fit:cover;border-radius:0.75rem;' ] ); ?>
    <?php else : ?>
      🪔
    <?php endif; ?>
  </div>

  <div class="pooja-card-content" style="flex:1;">
    <h3 class="pooja-card-title" style="font-family:var(--font-heading,serif);font-size:1.15rem;color:var(--clr-primary);margin:0 0 0.25rem 0;line-height:1.3;">
      <?php the_title(); ?>
    </h3>
    
    <p style="font-size:0.85rem;color:var(--clr-text-secondary);margin:0 0 0.65rem 0;line-height:1.5;">
      <?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?>
    </p>

    <div class="pooja-card-tags" style="display:flex;gap:0.4rem;flex-wrap:wrap;align-items:center;">
      <span class="pooja-tag" style="background:#FFF9F0;color:var(--clr-primary);border:1px solid var(--clr-border);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
        <?php esc_html_e( 'Procedure', 'djv-theme' ); ?>
      </span>
      <span class="pooja-tag" style="background:#FFF9F0;color:var(--clr-primary);border:1px solid var(--clr-border);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
        <?php esc_html_e( 'Samagri', 'djv-theme' ); ?>
      </span>
      <?php if ( $duration ) : ?>
        <span class="pooja-tag" style="background:rgba(122,36,25,0.08);color:var(--clr-primary);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
          ⏱ <?php echo esc_html( $duration ); ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
</a>
