<?php
/**
 * Template Part: Dynamic Temple Card (Location-Aware & Trilingual)
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id  = get_the_ID();
$lang     = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';

$name_en  = get_the_title( $post_id );
$name_te  = get_post_meta( $post_id, '_djv_name_te', true ) ?: get_post_meta( $post_id, '_djv_title_te', true );
$name_hi  = get_post_meta( $post_id, '_djv_name_hi', true ) ?: get_post_meta( $post_id, '_djv_title_hi', true );

$title = $name_en;
if ( $lang === 'te' && ! empty( $name_te ) ) {
	$title = $name_te;
} elseif ( $lang === 'hi' && ! empty( $name_hi ) ) {
	$title = $name_hi;
}

$state     = get_post_meta( $post_id, '_djv_state', true );
$district  = get_post_meta( $post_id, '_djv_district', true );
$city      = get_post_meta( $post_id, '_djv_city', true );
$timings   = get_post_meta( $post_id, '_djv_timings', true );
$lat       = get_post_meta( $post_id, '_djv_lat', true );
$lon       = get_post_meta( $post_id, '_djv_lon', true );
$deity     = get_post_meta( $post_id, '_djv_deity', true ) ?: implode( ', ', wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'names' ] ) );
$category  = get_post_meta( $post_id, '_djv_category', true ) ?: implode( ', ', wp_get_post_terms( $post_id, 'djv_temple_category', [ 'fields' => 'names' ] ) );
$status    = get_post_meta( $post_id, '_djv_verification_status', true ) ?: 'Verified';

$loc_string = $city ?: ( $district ?: $state );
if ( $loc_string && $state && $loc_string !== $state ) {
	$loc_string .= ', ' . $state;
}
?>
<div class="temple-card" id="temple-card-<?php echo esc_attr( $post_id ); ?>"
     data-temple-id="<?php echo esc_attr( $post_id ); ?>"
     data-lat="<?php echo esc_attr( $lat ); ?>"
     data-lon="<?php echo esc_attr( $lon ); ?>"
     data-state="<?php echo esc_attr( strtolower( $state ) ); ?>"
     data-deity="<?php echo esc_attr( strtolower( $deity ) ); ?>"
     data-category="<?php echo esc_attr( strtolower( $category ) ); ?>"
     style="border: 1px solid var(--clr-border, #E2E8F0); background: #FFF; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-sm, 0 2px 4px rgba(0,0,0,0.05)); display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease; position: relative;">

  <!-- Card Top Banner / Media -->
  <div style="position: relative; aspect-ratio: 16/9; overflow: hidden; background: linear-gradient(135deg, #7A2419 0%, #3D100A 100%);">
    <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%; height:100%; object-fit:cover; display:block;' ] ); ?>
    <?php else : ?>
      <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.85); font-size: 3rem;">
        🛕
      </div>
    <?php endif; ?>

    <!-- Category Badge -->
    <?php if ( $category ) : ?>
      <span style="position: absolute; top: 0.75rem; left: 0.75rem; background: rgba(36, 25, 20, 0.88); backdrop-filter: blur(4px); color: var(--clr-accent, #C89432); font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 9999px; border: 1px solid rgba(200, 148, 50, 0.4); text-transform: uppercase; letter-spacing: 0.05em;">
        <?php echo esc_html( $category ); ?>
      </span>
    <?php endif; ?>

    <!-- Verification / Source Badge -->
    <?php if ( $status === 'Verified' ) : ?>
      <span title="<?php esc_attr_e( 'Verified Official Record', 'djv-theme' ); ?>" style="position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(22, 101, 52, 0.9); color: #FFF; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.25rem;">
        ✓ <?php esc_html_e( 'Verified', 'djv-theme' ); ?>
      </span>
    <?php else : ?>
      <span title="<?php esc_attr_e( 'Seed Dataset — Needs Independent Verification', 'djv-theme' ); ?>" style="position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(180, 83, 9, 0.92); color: #FFF; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.25rem;">
        ℹ️ <?php esc_html_e( 'Needs Verification', 'djv-theme' ); ?>
      </span>
    <?php endif; ?>
  </div>

  <!-- Content Section -->
  <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
    <!-- Location & Deity Row -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; font-size: 0.75rem; color: var(--clr-text-muted, #64748B);">
      <?php if ( $loc_string ) : ?>
        <span style="font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase; letter-spacing: 0.04em;">
          📍 <?php echo esc_html( $loc_string ); ?>
        </span>
      <?php endif; ?>
      <?php if ( $deity ) : ?>
        <span style="background: #F1F5F9; color: #475569; padding: 2px 6px; border-radius: 4px; font-weight: 600;">
          🕉 <?php echo esc_html( $deity ); ?>
        </span>
      <?php endif; ?>
    </div>

    <!-- Title -->
    <h3 style="font-family: var(--font-heading, Georgia, serif); font-size: 1.25rem; line-height: 1.35; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">
      <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
        <?php echo esc_html( $title ); ?>
      </a>
    </h3>

    <!-- Dynamic Location Distance & Bearing Placeholder -->
    <div class="temple-distance-badge" style="font-size: 0.8rem; font-weight: 600; color: #0284C7; background: #F0F9FF; border: 1px solid #BAE6FD; padding: 0.3rem 0.65rem; border-radius: 6px; margin-bottom: 0.75rem; display: none;">
      🧭 <span class="dist-text">Calculating distance...</span>
    </div>

    <!-- Timings -->
    <?php if ( $timings ) : ?>
      <div style="font-size: 0.8rem; color: var(--clr-text-muted, #64748B); margin-bottom: 0.75rem; display: flex; align-items: flex-start; gap: 0.35rem; line-height: 1.4;">
        <span>⏰</span>
        <span><?php echo esc_html( wp_trim_words( $timings, 8 ) ); ?></span>
      </div>
    <?php endif; ?>

    <!-- Excerpt / About -->
    <p style="font-size: 0.875rem; color: var(--clr-text-secondary, #475569); line-height: 1.55; margin: 0 0 1.25rem 0; flex: 1;">
      <?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
    </p>

    <!-- Bottom Action Button -->
    <div style="margin-top: auto; padding-top: 0.75rem; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
      <a href="<?php the_permalink(); ?>" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.875rem; font-weight: 700; color: var(--clr-primary, #7A2419); text-decoration: none;">
        <?php esc_html_e( 'Darshan Timings & History', 'djv-theme' ); ?> →
      </a>
      <?php if ( $lat && $lon ) : ?>
        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $lat . ',' . $lon ); ?>" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Get Directions', 'djv-theme' ); ?>" style="font-size: 0.8rem; color: #64748B; text-decoration: none; padding: 4px 8px; background: #F8FAFC; border-radius: 4px; border: 1px solid #E2E8F0;">
          🗺️ <?php esc_html_e( 'Map', 'djv-theme' ); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
