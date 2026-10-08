<?php
/**
 * Template Part: Dynamic Mantra Card (DJV Standard)
 *
 * Conforms to DJV visual identity and design specs:
 * Deity image/icon, English title, Telugu title, Category, Short description, "Read Mantra" button.
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id          = get_the_ID();
$telugu_title     = get_post_meta( $post_id, '_djv_telugu_title', true );
$deity            = get_post_meta( $post_id, '_djv_deity', true );
if ( empty( $deity ) ) {
	$terms = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'names' ] );
	$deity = ! empty( $terms ) ? $terms[0] : '';
}
$categories = wp_get_post_terms( $post_id, 'djv_mantra_cat', [ 'fields' => 'names' ] );
if ( empty( $categories ) && ! empty( $deity ) ) {
	$categories = [ $deity ];
}
$cat_label = ! empty( $categories ) ? implode( ' • ', array_slice( $categories, 0, 2 ) ) : ( $deity ?: 'Sacred Chant' );

// Map deities to sacred iconography
$deity_lower = strtolower( $deity . ' ' . get_the_title() );
$icon = '🕉️';
if ( strpos( $deity_lower, 'hanuman' ) !== false || strpos( $deity_lower, 'anjaneya' ) !== false ) {
	$icon = '🚩';
} elseif ( strpos( $deity_lower, 'shiva' ) !== false || strpos( $deity_lower, 'mrityunjaya' ) !== false ) {
	$icon = '🔱';
} elseif ( strpos( $deity_lower, 'ganesh' ) !== false || strpos( $deity_lower, 'ganapati' ) !== false || strpos( $deity_lower, 'vakratunda' ) !== false ) {
	$icon = '🐘';
} elseif ( strpos( $deity_lower, 'lakshmi' ) !== false || strpos( $deity_lower, 'kuber' ) !== false || strpos( $deity_lower, 'shreem' ) !== false ) {
	$icon = '🪷';
} elseif ( strpos( $deity_lower, 'saraswati' ) !== false || strpos( $deity_lower, 'vidya' ) !== false ) {
	$icon = '🪕';
} elseif ( strpos( $deity_lower, 'durga' ) !== false || strpos( $deity_lower, 'devi' ) !== false || strpos( $deity_lower, 'chandi' ) !== false ) {
	$icon = '🦁';
} elseif ( strpos( $deity_lower, 'krishna' ) !== false ) {
	$icon = '🦚';
} elseif ( strpos( $deity_lower, 'vishnu' ) !== false || strpos( $deity_lower, 'narayana' ) !== false ) {
	$icon = '🐚';
} elseif ( strpos( $deity_lower, 'surya' ) !== false ) {
	$icon = '☀️';
} elseif ( strpos( $deity_lower, 'shani' ) !== false || strpos( $deity_lower, 'navagraha' ) !== false || strpos( $deity_lower, 'chandra' ) !== false || strpos( $deity_lower, 'rahu' ) !== false ) {
	$icon = '🪐';
}

$thumb_url = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium' ) : '';
?>
<article class="mantra-card" id="mantra-card-<?php echo esc_attr( $post_id ); ?>" data-deity="<?php echo esc_attr( strtolower( $deity ) ); ?>" data-categories="<?php echo esc_attr( strtolower( implode( ' ', $categories ) ) ); ?>">
  <!-- Card Visual Header -->
  <div class="mantra-card-media">
    <?php if ( $thumb_url ) : ?>
      <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" class="mantra-card-img" />
    <?php else : ?>
      <div class="mantra-icon-placeholder" aria-hidden="true">
        <span class="mantra-deity-symbol"><?php echo esc_html( $icon ); ?></span>
      </div>
    <?php endif; ?>
  </div>

  <div class="mantra-card-body">
    <!-- Meta Category Badge -->
    <div class="mantra-category-tag">
      <span class="badge-dot" aria-hidden="true"></span>
      <span class="badge-text"><?php echo esc_html( $cat_label ); ?></span>
    </div>

    <!-- Title (English) -->
    <h3 class="mantra-title">
      <a href="<?php the_permalink(); ?>" class="mantra-title-link">
        <?php the_title(); ?>
      </a>
    </h3>

    <!-- Localized Subtitle -->
    <?php 
    $hindi_title = get_post_meta( $post_id, '_djv_hindi_title', true );
    if ( $telugu_title ) : ?>
      <div class="mantra-telugu-title djv-lang-field" data-lang="te" lang="te" style="display:none; font-family:var(--font-telugu, sans-serif);">
        <?php echo esc_html( $telugu_title ); ?>
      </div>
    <?php endif; ?>
    <?php if ( $hindi_title ) : ?>
      <div class="mantra-hindi-title djv-lang-field" data-lang="hi" lang="hi" style="display:none; font-family:'Noto Sans Devanagari', serif;">
        <?php echo esc_html( $hindi_title ); ?>
      </div>
    <?php endif; ?>

    <!-- Short Description -->
    <p class="mantra-excerpt">
      <?php
      $excerpt = get_the_excerpt();
      if ( empty( $excerpt ) ) {
        $content = get_post_meta( $post_id, '_djv_meaning', true ) ?: get_the_content();
        $excerpt = wp_trim_words( wp_strip_all_tags( $content ), 16, '...' );
      } else {
        $excerpt = wp_trim_words( $excerpt, 16, '...' );
      }
      echo esc_html( $excerpt );
      ?>
    </p>

    <!-- Card Footer / CTA -->
    <div class="mantra-card-footer">
      <a href="<?php the_permalink(); ?>" class="btn-read-mantra" aria-label="<?php echo esc_attr( sprintf( __( 'Read Mantra: %s', 'djv-theme' ), get_the_title() ) ); ?>">
        <span><?php esc_html_e( 'Read Mantra', 'djv-theme' ); ?></span>
        <span class="arrow" aria-hidden="true">→</span>
      </a>
    </div>
  </div>
</article>
