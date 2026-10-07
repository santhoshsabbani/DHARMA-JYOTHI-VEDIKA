<?php
/**
 * Template Part: Dynamic Pooja Guide Card
 *
 * Supports trilingual titles (EN, TE, HI), duration, procedure tags,
 * and seamless client-side language switching.
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

$post_id   = get_the_ID();
$title_en  = get_post_meta( $post_id, '_djv_title_en', true ) ?: get_the_title();
$title_te  = get_post_meta( $post_id, '_djv_title_te', true );
$title_hi  = get_post_meta( $post_id, '_djv_title_hi', true );
$duration  = get_post_meta( $post_id, '_djv_duration', true );
$categories= wp_get_post_terms( $post_id, 'djv_pooja_cat', [ 'fields' => 'names' ] );
$deity     = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'names' ] );

$cat_slugs = wp_get_post_terms( $post_id, 'djv_pooja_cat', [ 'fields' => 'slugs' ] );
$deity_slugs = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'slugs' ] );
$all_classes = array_merge( $cat_slugs ?: [], $deity_slugs ?: [] );
$filter_class_str = esc_attr( implode( ' ', array_unique( $all_classes ) ) );
?>
<a href="<?php the_permalink(); ?>" class="pooja-card djv-filter-item <?php echo $filter_class_str; ?>" id="pooja-card-<?php echo esc_attr( $post_id ); ?>"
   data-title-en="<?php echo esc_attr( strtolower( $title_en ) ); ?>"
   data-title-te="<?php echo esc_attr( strtolower( $title_te ?: '' ) ); ?>"
   data-title-hi="<?php echo esc_attr( strtolower( $title_hi ?: '' ) ); ?>"
   style="text-decoration:none;display:flex;align-items:flex-start;gap:1.25rem;background:#FFF;border:1px solid var(--clr-border, #E8DFD3);border-radius:1rem;padding:1.25rem;box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));transition:transform 0.2s,box-shadow 0.2s;">

  <div class="pooja-card-icon" style="font-size:2rem;background:rgba(200,148,50,0.12);width:56px;height:56px;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;" aria-hidden="true">
    <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( [56, 56], [ 'style' => 'width:100%;height:100%;object-fit:cover;border-radius:0.75rem;' ] ); ?>
    <?php else : ?>
      🪔
    <?php endif; ?>
  </div>

  <div class="pooja-card-content" style="flex:1;min-width:0;">
    <h3 class="pooja-card-title" style="font-family:var(--font-heading, serif);font-size:1.15rem;color:var(--clr-primary, #7A2419);margin:0 0 0.25rem 0;line-height:1.35;">
      <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $title_en ); ?></span>
      <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu, sans-serif);"><?php echo esc_html( $title_te ?: $title_en ); ?></span>
      <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari', serif;"><?php echo esc_html( $title_hi ?: $title_en ); ?></span>
    </h3>

    <?php if ( $title_te ) : ?>
      <div class="pooja-card-te-sub djv-lang-field" data-lang="en" style="font-family:var(--font-telugu, sans-serif);font-size:0.88rem;color:var(--clr-accent, #C89432);font-weight:600;margin-bottom:0.4rem;">
        <?php echo esc_html( $title_te ); ?>
      </div>
    <?php endif; ?>
    
    <p style="font-size:0.85rem;color:var(--clr-text-secondary, #55433C);margin:0 0 0.65rem 0;line-height:1.5;">
      <?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?>
    </p>

    <div class="pooja-card-tags" style="display:flex;gap:0.4rem;flex-wrap:wrap;align-items:center;">
      <span class="pooja-tag" style="background:#FFF9F0;color:var(--clr-primary, #7A2419);border:1px solid var(--clr-border, #E8DFD3);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
        <span class="djv-lang-field" data-lang="en">12-Step Vidhi</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">12 విధానాలు</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">12 चरण विधि</span>
      </span>
      <span class="pooja-tag" style="background:#FFF9F0;color:var(--clr-primary, #7A2419);border:1px solid var(--clr-border, #E8DFD3);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
        <span class="djv-lang-field" data-lang="en">Samagri</span>
        <span class="djv-lang-field" data-lang="te" style="display:none;">సామగ్రి</span>
        <span class="djv-lang-field" data-lang="hi" style="display:none;">सामग्री</span>
      </span>
      <?php if ( $duration ) : ?>
        <span class="pooja-tag" style="background:rgba(122,36,25,0.08);color:var(--clr-primary, #7A2419);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
          ⏱ <?php echo esc_html( $duration ); ?>
        </span>
      <?php endif; ?>
      <?php if ( ! empty( $deity[0] ) ) : ?>
        <span class="pooja-tag" style="background:rgba(200,148,50,0.12);color:var(--clr-accent, #C89432);padding:0.2rem 0.5rem;border-radius:0.25rem;font-size:0.75rem;font-weight:600;">
          <?php echo esc_html( $deity[0] ); ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
</a>
