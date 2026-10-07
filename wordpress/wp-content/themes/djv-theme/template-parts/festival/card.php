<?php
/**
 * Template Part: Dynamic Festival Card
 *
 * Supports trilingual titles (EN, TE, HI), tithi rules, dates, categories,
 * and seamless client-side language switching.
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id     = get_the_ID();
$title_en    = get_post_meta( $post_id, '_djv_title_en', true ) ?: get_the_title();
$title_te    = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
$title_hi    = get_post_meta( $post_id, '_djv_title_hi', true );
$date_meta   = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
$month_meta  = get_post_meta( $post_id, '_djv_month', true );
$tithi_rule  = get_post_meta( $post_id, '_djv_tithi_rule', true );
$is_major    = get_post_meta( $post_id, '_djv_is_major', true );
$is_telugu   = get_post_meta( $post_id, '_djv_is_telugu', true );

$categories  = wp_get_post_terms( $post_id, 'djv_festival_cat', [ 'fields' => 'names' ] );
if ( empty( $categories ) ) {
	$categories = wp_get_post_terms( $post_id, 'djv_festival_type', [ 'fields' => 'names' ] );
}

$formatted_date = '';
if ( $date_meta ) {
	$time = strtotime( $date_meta );
	if ( $time ) {
		$formatted_date = date( 'F j, Y', $time );
	}
}

$badge = '';
if ( $formatted_date ) {
	$badge = strtoupper( date( 'M j', strtotime( $date_meta ) ) );
} elseif ( $month_meta ) {
	$badge = strtoupper( $month_meta );
}

$deity_terms = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'slugs' ] );
$cat_slugs   = wp_get_post_terms( $post_id, 'djv_festival_cat', [ 'fields' => 'slugs' ] );
$all_filter_classes = array_merge( $deity_terms ?: [], $cat_slugs ?: [] );
if ( $is_major ) $all_filter_classes[] = 'major-festivals';
if ( $is_telugu ) $all_filter_classes[] = 'regional';
$filter_class_str = esc_attr( implode( ' ', array_unique( $all_filter_classes ) ) );
?>
<div class="festival-card djv-filter-item <?php echo $filter_class_str; ?>" id="festival-card-<?php echo esc_attr( $post_id ); ?>"
     data-title-en="<?php echo esc_attr( strtolower( $title_en ) ); ?>"
     data-title-te="<?php echo esc_attr( strtolower( $title_te ?: '' ) ); ?>"
     data-title-hi="<?php echo esc_attr( strtolower( $title_hi ?: '' ) ); ?>"
     style="border:1px solid var(--clr-border, #E8DFD3);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));position:relative;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">

  <?php if ( $badge ) : ?>
    <span class="festival-card-badge" style="position:absolute;top:1rem;right:1rem;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.25rem 0.65rem;border-radius:0.4rem;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;">
      <?php echo esc_html( $badge ); ?>
    </span>
  <?php endif; ?>

  <?php if ( has_post_thumbnail() ) : ?>
    <div style="border-radius:0.75rem;overflow:hidden;margin-bottom:1rem;aspect-ratio:16/9;">
      <?php the_post_thumbnail( 'medium', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
    </div>
  <?php else : ?>
    <div style="width:48px;height:48px;background:rgba(200,148,50,0.12);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.75rem;margin-bottom:0.85rem;" aria-hidden="true">
      🪔
    </div>
  <?php endif; ?>

  <!-- Multilingual Title -->
  <h3 class="festival-card-title" style="font-family:var(--font-heading, serif);font-size:1.25rem;color:var(--clr-primary, #7A2419);margin:0 0 0.35rem 0;line-height:1.35;">
    <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
      <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $title_en ); ?></span>
      <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu, sans-serif);"><?php echo esc_html( $title_te ?: $title_en ); ?></span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari', serif;"><?php echo esc_html( $title_hi ?: $title_en ); ?></span>
    </a>
  </h3>

  <!-- Secondary Script Subtitle -->
  <?php if ( $title_te ) : ?>
    <div class="festival-card-te-sub djv-lang-field" data-lang="en" style="font-family:var(--font-telugu, sans-serif);font-size:0.92rem;color:var(--clr-accent, #C89432);font-weight:600;margin-bottom:0.4rem;">
      <?php echo esc_html( $title_te ); ?>
    </div>
  <?php endif; ?>

  <?php if ( $formatted_date ) : ?>
    <div style="font-size:0.85rem;color:var(--clr-primary, #7A2419);font-weight:600;margin-bottom:0.4rem;">
      📅 <?php echo esc_html( $formatted_date ); ?>
    </div>
  <?php endif; ?>

  <?php if ( $tithi_rule ) : ?>
    <div style="font-size:0.78rem;color:var(--clr-text-muted, #7A6F68);margin-bottom:0.6rem;line-height:1.4;">
      🌙 <?php echo esc_html( $tithi_rule ); ?>
    </div>
  <?php endif; ?>

  <p style="font-size:0.875rem;color:var(--clr-text-secondary, #55433C);line-height:1.6;margin:0 0 1rem 0;flex:1;">
    <?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
  </p>

  <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:0.75rem;border-top:1px solid #F5EFEB;">
    <a href="<?php the_permalink(); ?>" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary, #7A2419);text-decoration:none;">
      <span class="djv-lang-field" data-lang="en">View Vidhi & Muhurat →</span>
      <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా విధానం & ముహూర్తం →</span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा विधि एवं मुहूर्त →</span>
    </a>
    <?php if ( ! empty( $categories[0] ) ) : ?>
      <span style="font-size:0.72rem;background:#FFF9F0;color:var(--clr-text-muted, #7A6F68);padding:0.2rem 0.5rem;border-radius:0.25rem;border:1px solid var(--clr-border, #E8DFD3);">
        <?php echo esc_html( $categories[0] ); ?>
      </span>
    <?php endif; ?>
  </div>
</div>
