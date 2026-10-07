<?php
/**
 * Template Part: Dynamic Festival Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id     = get_the_ID();
$telugu_name = get_post_meta( $post_id, '_djv_telugu_name', true );
$date_meta   = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
$month_meta  = get_post_meta( $post_id, '_djv_month', true );
$type_meta   = get_post_meta( $post_id, '_djv_type', true );

$formatted_date = '';
if ( $date_meta ) {
	$time = strtotime( $date_meta );
	if ( $time ) {
		$formatted_date = date( 'F j, Y', $time );
	}
}

// Month badge
$badge = '';
if ( $formatted_date ) {
	$badge = strtoupper( date( 'F', strtotime( $date_meta ) ) );
} elseif ( $month_meta ) {
	$badge = strtoupper( $month_meta );
}
?>
<div class="festival-card" id="festival-card-<?php echo esc_attr( $post_id ); ?>" style="border:1px solid var(--clr-border);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm);position:relative;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
  <?php if ( $badge ) : ?>
    <span class="festival-card-badge" style="position:absolute;top:1rem;right:1rem;background:var(--clr-primary,#7A2419);color:#FFF;padding:0.25rem 0.6rem;border-radius:0.35rem;font-size:0.7rem;font-weight:700;letter-spacing:0.05em;">
      <?php echo esc_html( $badge ); ?>
    </span>
  <?php endif; ?>

  <?php if ( has_post_thumbnail() ) : ?>
    <div style="border-radius:0.75rem;overflow:hidden;margin-bottom:1rem;aspect-ratio:16/9;">
      <?php the_post_thumbnail( 'medium', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
    </div>
  <?php else : ?>
    <span style="font-size:2.25rem;display:block;margin-bottom:0.75rem;" aria-hidden="true">🪔</span>
  <?php endif; ?>

  <h3 style="font-family:var(--font-heading,serif);font-size:1.3rem;color:var(--clr-primary);margin:0 0 0.35rem 0;line-height:1.3;">
    <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
      <?php the_title(); ?>
    </a>
  </h3>

  <?php if ( $telugu_name ) : ?>
    <div style="font-family:var(--font-telugu,sans-serif);font-size:0.95rem;color:var(--clr-primary-light,#9A3022);margin-bottom:0.4rem;">
      <?php echo esc_html( $telugu_name ); ?>
    </div>
  <?php endif; ?>

  <?php if ( $formatted_date ) : ?>
    <div style="font-size:0.85rem;color:var(--clr-accent,#C89432);font-weight:700;margin-bottom:0.6rem;">
      📅 <?php echo esc_html( $formatted_date ); ?>
    </div>
  <?php endif; ?>

  <p style="font-size:0.875rem;color:var(--clr-text-secondary,#55433C);line-height:1.6;margin:0 0 1rem 0;flex:1;">
    <?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
  </p>

  <div style="margin-top:auto;">
    <a href="<?php the_permalink(); ?>" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary);text-decoration:none;">
      <?php esc_html_e( 'View Rituals & Vidhi', 'djv-theme' ); ?> →
    </a>
  </div>
</div>
