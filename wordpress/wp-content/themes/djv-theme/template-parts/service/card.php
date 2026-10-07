<?php
/**
 * Template Part: Dynamic Service Card
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id  = get_the_ID();
$stype    = get_post_meta( $post_id, '_djv_service_type', true );
$price    = get_post_meta( $post_id, '_djv_price', true );
$duration = get_post_meta( $post_id, '_djv_duration', true );
$contact  = get_post_meta( $post_id, '_djv_contact', true );
?>
<div class="service-card" id="service-card-<?php echo esc_attr( $post_id ); ?>" style="border:1px solid var(--clr-border);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;position:relative;">
  <?php if ( $stype ) : ?>
    <span style="font-size:0.75rem;font-weight:700;color:var(--clr-accent,#C89432);text-transform:uppercase;margin-bottom:0.4rem;letter-spacing:0.05em;">
      ⭐ <?php echo esc_html( $stype ); ?>
    </span>
  <?php endif; ?>

  <h3 style="font-family:var(--font-heading,serif);font-size:1.25rem;color:var(--clr-primary);margin:0 0 0.5rem 0;line-height:1.3;">
    <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
      <?php the_title(); ?>
    </a>
  </h3>

  <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.6;margin:0 0 1rem 0;flex:1;">
    <?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
  </p>

  <div style="background:#FFF9F0;padding:0.6rem 0.8rem;border-radius:0.5rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;font-size:0.8125rem;">
    <?php if ( $duration ) : ?>
      <span>⏱ <strong><?php echo esc_html( $duration ); ?></strong></span>
    <?php endif; ?>
    <?php if ( $price ) : ?>
      <span style="color:var(--clr-primary);font-weight:700;"><?php echo esc_html( $price ); ?></span>
    <?php endif; ?>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:auto;">
    <a href="<?php the_permalink(); ?>" style="font-size:0.85rem;font-weight:600;color:var(--clr-primary);text-decoration:none;">
      <?php esc_html_e( 'Service Details', 'djv-theme' ); ?> →
    </a>
    <?php if ( $contact ) : ?>
      <a href="tel:<?php echo esc_attr( str_replace(' ', '', $contact) ); ?>" style="padding:0.35rem 0.75rem;background:var(--clr-primary);color:#FFF;border-radius:9999px;font-size:0.75rem;font-weight:600;text-decoration:none;">
        📞 <?php esc_html_e( 'Enquire', 'djv-theme' ); ?>
      </a>
    <?php endif; ?>
  </div>
</div>
