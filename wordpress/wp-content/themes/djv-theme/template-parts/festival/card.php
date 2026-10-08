<?php
/**
 * Template Part: Dynamic Festival Card
 *
 * Fully multilingual (English default, Telugu, Hindi) and Pan-India location-aware.
 * Strictly avoids showing Telugu content in English mode.
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id    = get_the_ID();
$title_en   = get_post_meta( $post_id, '_djv_title_en', true ) ?: get_the_title();
$title_te   = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
$title_hi   = get_post_meta( $post_id, '_djv_title_hi', true );

$desc_en    = get_post_meta( $post_id, '_djv_description_en', true ) ?: get_the_excerpt();
$desc_te    = get_post_meta( $post_id, '_djv_description_te', true ) ?: ( get_post_meta( $post_id, '_djv_content_te', true ) ?: $desc_en );
$desc_hi    = get_post_meta( $post_id, '_djv_description_hi', true ) ?: ( get_post_meta( $post_id, '_djv_content_hi', true ) ?: $desc_en );

$date_meta  = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
$month_meta = get_post_meta( $post_id, '_djv_month', true );
$tithi_rule = get_post_meta( $post_id, '_djv_tithi_rule', true );
$is_major   = get_post_meta( $post_id, '_djv_is_major', true );
$is_telugu  = get_post_meta( $post_id, '_djv_is_telugu', true );

$scope      = get_post_meta( $post_id, '_djv_scope', true ) ?: 'pan_india';
$state      = get_post_meta( $post_id, '_djv_state', true ) ?: 'Pan-India';

$categories = wp_get_post_terms( $post_id, 'djv_festival_cat', [ 'fields' => 'names' ] );
if ( empty( $categories ) ) {
	$categories = wp_get_post_terms( $post_id, 'djv_festival_type', [ 'fields' => 'names' ] );
}
$cat_primary = ! empty( $categories[0] ) ? $categories[0] : '';

// Localized Date & Badge
$date_en = [ 'formatted' => '', 'day_of_week' => '', 'badge' => '' ];
$date_te = [ 'formatted' => '', 'day_of_week' => '', 'badge' => '' ];
$date_hi = [ 'formatted' => '', 'day_of_week' => '', 'badge' => '' ];

if ( class_exists( 'DJV_Festival_Master' ) && $date_meta ) {
	$date_en = DJV_Festival_Master::format_localized_date( $date_meta, 'en' );
	$date_te = DJV_Festival_Master::format_localized_date( $date_meta, 'te' );
	$date_hi = DJV_Festival_Master::format_localized_date( $date_meta, 'hi' );
} elseif ( $date_meta ) {
	$t = strtotime( $date_meta );
	if ( $t ) {
		$date_en = [
			'formatted'   => date( 'F j, Y', $t ),
			'day_of_week' => date( 'l', $t ),
			'badge'       => strtoupper( date( 'M j', $t ) ),
		];
	}
}

// Scope Labels
$scope_label_en = class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::get_localized_scope_label( $scope, $state, 'en' ) : ( $scope === 'pan_india' ? '🇮🇳 Pan-India' : "📍 {$state}" );
$scope_label_te = class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::get_localized_scope_label( $scope, $state, 'te' ) : ( $scope === 'pan_india' ? 'భారతదేశం అంతటా' : "📍 {$state}" );
$scope_label_hi = class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::get_localized_scope_label( $scope, $state, 'hi' ) : ( $scope === 'pan_india' ? 'अखिल भारतीय' : "📍 {$state}" );

// Category Labels
$cat_en = class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::get_localized_category( $cat_primary, 'en' ) : $cat_primary;
$cat_te = class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::get_localized_category( $cat_primary, 'te' ) : $cat_primary;
$cat_hi = class_exists( 'DJV_Festival_Master' ) ? DJV_Festival_Master::get_localized_category( $cat_primary, 'hi' ) : $cat_primary;

$deity_terms = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'slugs' ] );
$cat_slugs   = wp_get_post_terms( $post_id, 'djv_festival_cat', [ 'fields' => 'slugs' ] );
$all_filter_classes = array_merge( $deity_terms ?: [], $cat_slugs ?: [] );
if ( $is_major ) $all_filter_classes[] = 'major-festivals';
if ( $is_telugu ) $all_filter_classes[] = 'regional';
$filter_class_str = esc_attr( implode( ' ', array_unique( $all_filter_classes ) ) );
?>
<div class="festival-card djv-filter-item <?php echo $filter_class_str; ?>" id="festival-card-<?php echo esc_attr( $post_id ); ?>"
     data-scope="<?php echo esc_attr( $scope ); ?>"
     data-state="<?php echo esc_attr( $state ); ?>"
     data-title-en="<?php echo esc_attr( strtolower( $title_en ) ); ?>"
     data-title-te="<?php echo esc_attr( strtolower( $title_te ?: '' ) ); ?>"
     data-title-hi="<?php echo esc_attr( strtolower( $title_hi ?: '' ) ); ?>"
     style="border:1px solid var(--clr-border, #E8DFD3);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));position:relative;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">

  <!-- Date Badge -->
  <?php if ( ! empty( $date_en['badge'] ) ) : ?>
    <span class="festival-card-badge" style="position:absolute;top:1rem;right:1rem;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.25rem 0.65rem;border-radius:0.4rem;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;">
      <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $date_en['badge'] ); ?></span>
      <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( $date_te['badge'] ?: $date_en['badge'] ); ?></span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( $date_hi['badge'] ?: $date_en['badge'] ); ?></span>
    </span>
  <?php endif; ?>

  <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.85rem;">
    <?php if ( has_post_thumbnail() ) : ?>
      <div style="width:48px;height:48px;border-radius:0.75rem;overflow:hidden;flex-shrink:0;">
        <?php the_post_thumbnail( 'thumbnail', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
      </div>
    <?php else : ?>
      <div style="width:48px;height:48px;background:rgba(200,148,50,0.12);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;" aria-hidden="true">
        🪔
      </div>
    <?php endif; ?>

    <!-- Scope / Region Badge -->
    <span class="festival-scope-pill" style="font-size:0.72rem;font-weight:600;padding:0.2rem 0.6rem;border-radius:9999px;background:<?php echo $scope === 'pan_india' ? '#EFF6FF' : '#FFF7ED'; ?>;color:<?php echo $scope === 'pan_india' ? '#1D4ED8' : '#C2410C'; ?>;border:1px solid <?php echo $scope === 'pan_india' ? '#BFDBFE' : '#FED7AA'; ?>;">
      <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $scope_label_en ); ?></span>
      <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( $scope_label_te ); ?></span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( $scope_label_hi ); ?></span>
    </span>
  </div>

  <!-- Multilingual Title (English default, never leak Telugu into English) -->
  <h3 class="festival-card-title" style="font-family:var(--font-heading, serif);font-size:1.25rem;color:var(--clr-primary, #7A2419);margin:0 0 0.45rem 0;line-height:1.35;">
    <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;">
      <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $title_en ); ?></span>
      <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu, sans-serif);"><?php echo esc_html( $title_te ?: $title_en ); ?></span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari', serif;"><?php echo esc_html( $title_hi ?: $title_en ); ?></span>
    </a>
  </h3>

  <!-- Localized Date Display -->
  <?php if ( ! empty( $date_en['formatted'] ) ) : ?>
    <div style="font-size:0.85rem;color:var(--clr-primary, #7A2419);font-weight:600;margin-bottom:0.4rem;">
      📅
      <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $date_en['formatted'] . ( $date_en['day_of_week'] ? ' (' . $date_en['day_of_week'] . ')' : '' ) ); ?></span>
      <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( ( $date_te['day_of_week'] ? $date_te['day_of_week'] . ', ' : '' ) . $date_te['formatted'] ); ?></span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( ( $date_hi['day_of_week'] ? $date_hi['day_of_week'] . ', ' : '' ) . $date_hi['formatted'] ); ?></span>
    </div>
  <?php endif; ?>

  <?php if ( $tithi_rule ) : ?>
    <div style="font-size:0.78rem;color:var(--clr-text-muted, #7A6F68);margin-bottom:0.6rem;line-height:1.4;">
      🌙 <?php echo esc_html( $tithi_rule ); ?>
    </div>
  <?php endif; ?>

  <!-- Trilingual Short Description -->
  <p style="font-size:0.875rem;color:var(--clr-text-secondary, #55433C);line-height:1.6;margin:0 0 1rem 0;flex:1;">
    <span class="djv-lang-field" data-lang="en"><?php echo esc_html( wp_trim_words( $desc_en, 20 ) ); ?></span>
    <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu, sans-serif);"><?php echo esc_html( wp_trim_words( $desc_te, 20 ) ); ?></span>
    <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari', serif;"><?php echo esc_html( wp_trim_words( $desc_hi, 20 ) ); ?></span>
  </p>

  <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:0.75rem;border-top:1px solid #F5EFEB;">
    <a href="<?php the_permalink(); ?>" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary, #7A2419);text-decoration:none;">
      <span class="djv-lang-field" data-lang="en">Puja Vidhi &amp; Muhurat →</span>
      <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా విధానం &amp; ముహూర్తం →</span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा विधि एवं मुहूर्त →</span>
    </a>
    <?php if ( $cat_primary ) : ?>
      <span style="font-size:0.72rem;background:#FFF9F0;color:var(--clr-text-muted, #7A6F68);padding:0.2rem 0.5rem;border-radius:0.25rem;border:1px solid var(--clr-border, #E8DFD3);">
        <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $cat_en ); ?></span>
        <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( $cat_te ); ?></span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( $cat_hi ); ?></span>
      </span>
    <?php endif; ?>
  </div>
</div>
