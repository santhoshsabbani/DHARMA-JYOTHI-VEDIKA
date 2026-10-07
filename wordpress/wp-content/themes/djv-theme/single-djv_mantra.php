<?php
/**
 * Single Mantra Template (djv_mantra)
 *
 * Full-featured single mantra view conforming to DJV visual identity.
 * Displays breadcrumb, title, Telugu title, deity, categories,
 * Devanagari Sanskrit, Telugu script, IAST transliteration, spiritual meaning,
 * chanting procedure, counts, best time, traditional significance,
 * responsibly framed benefits, audio player, FAQ accordion, and related mantras.
 *
 * @package DJV_Theme
 */

get_header();

$post_id          = get_the_ID();
$telugu_title     = get_post_meta( $post_id, '_djv_telugu_title', true );
$sanskrit_text    = get_post_meta( $post_id, '_djv_sanskrit_text', true ) ?: get_post_meta( $post_id, '_djv_original_text', true );
$telugu_text      = get_post_meta( $post_id, '_djv_telugu_text', true );
$transliteration  = get_post_meta( $post_id, '_djv_transliteration', true );
$meaning          = get_post_meta( $post_id, '_djv_meaning', true );
$how_to_chant     = get_post_meta( $post_id, '_djv_how_to_chant', true );
$chant_count      = get_post_meta( $post_id, '_djv_chant_count', true );
$best_time        = get_post_meta( $post_id, '_djv_best_time', true );
$significance     = get_post_meta( $post_id, '_djv_significance', true );
$benefits         = get_post_meta( $post_id, '_djv_benefits', true );
$faq              = get_post_meta( $post_id, '_djv_faq', true ) ?: [];
$audio_url        = get_post_meta( $post_id, '_djv_audio_url', true ) ?: get_post_meta( $post_id, '_djv_audio', true );

$deity            = get_post_meta( $post_id, '_djv_deity', true );
if ( empty( $deity ) ) {
	$terms = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'names' ] );
	$deity = ! empty( $terms ) ? $terms[0] : '';
}
$categories = wp_get_post_terms( $post_id, 'djv_mantra_cat', [ 'fields' => 'names' ] );

// Icon mapping
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

$thumb_url = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'large' ) : '';
$share_url = urlencode( get_permalink() );
$share_txt = urlencode( get_the_title() . ' - ' . ( $telugu_title ?: '' ) . ' | Dharma Jyothi Vedika' );
?>

<div class="single-mantra-page">
  <div class="container single-mantra-container">

    <!-- Breadcrumb -->
    <nav class="single-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
      <span class="sep">›</span>
      <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>"><?php esc_html_e( 'Mantras &amp; Slokas', 'djv-theme' ); ?></a>
      <span class="sep">›</span>
      <span class="current" aria-current="page"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="mantra-<?php the_ID(); ?>" <?php post_class( 'single-mantra-article' ); ?>>

        <!-- ── Article Header ── -->
        <header class="single-mantra-header">
          <div class="header-meta-tags">
            <span class="deity-pill">
              <span class="pill-icon"><?php echo esc_html( $icon ); ?></span>
              <span><?php echo esc_html( $deity ?: __( 'Sacred Deity', 'djv-theme' ) ); ?></span>
            </span>
            <?php if ( ! empty( $categories ) ) : ?>
              <?php foreach ( array_slice( $categories, 0, 3 ) as $cat ) : ?>
                <span class="category-pill"><?php echo esc_html( $cat ); ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <h1 class="single-mantra-title">
            <?php the_title(); ?>
          </h1>

          <?php if ( $telugu_title ) : ?>
            <div class="single-mantra-telugu-title" lang="te">
              <?php echo esc_html( $telugu_title ); ?>
            </div>
          <?php endif; ?>

          <!-- Quick Specs Bar -->
          <div class="quick-specs-bar">
            <?php if ( $chant_count ) : ?>
              <div class="spec-item">
                <span class="spec-label">🔁 <?php esc_html_e( 'Chant Count', 'djv-theme' ); ?>:</span>
                <span class="spec-val"><?php echo esc_html( $chant_count ); ?></span>
              </div>
            <?php endif; ?>
            <?php if ( $best_time ) : ?>
              <div class="spec-item">
                <span class="spec-label">🌅 <?php esc_html_e( 'Best Time', 'djv-theme' ); ?>:</span>
                <span class="spec-val"><?php echo esc_html( $best_time ); ?></span>
              </div>
            <?php endif; ?>
          </div>
        </header>

        <!-- ── Sacred Chanting Box ── -->
        <section class="sacred-box" aria-label="<?php esc_attr_e( 'Sacred Mantra Text', 'djv-theme' ); ?>">
          <div class="sacred-box-header">
            <span class="sacred-box-title">🕉️ <?php esc_html_e( 'Authentic Sacred Text', 'djv-theme' ); ?></span>
            <button type="button" class="btn-copy-mantra" id="btn-copy-mantra" title="<?php esc_attr_e( 'Copy Sanskrit Verse', 'djv-theme' ); ?>">
              📋 <?php esc_html_e( 'Copy Verse', 'djv-theme' ); ?>
            </button>
          </div>

          <!-- Sanskrit / Devanagari -->
          <?php if ( $sanskrit_text ) : ?>
            <div class="verse-group">
              <span class="verse-lang-label"><?php esc_html_e( 'Sanskrit Text (संस्कृतम्)', 'djv-theme' ); ?></span>
              <div class="sanskrit-content" id="sanskrit-text-content" lang="sa">
                <?php echo nl2br( esc_html( $sanskrit_text ) ); ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Telugu Script -->
          <?php if ( $telugu_text ) : ?>
            <div class="verse-group verse-telugu-group">
              <span class="verse-lang-label"><?php esc_html_e( 'Telugu Script (తెలుగు లిపి)', 'djv-theme' ); ?></span>
              <div class="telugu-content" lang="te">
                <?php echo nl2br( esc_html( $telugu_text ) ); ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Roman / IAST Transliteration -->
          <?php if ( $transliteration ) : ?>
            <div class="verse-group verse-translit-group">
              <span class="verse-lang-label"><?php esc_html_e( 'English / Roman Transliteration (IAST)', 'djv-theme' ); ?></span>
              <div class="transliteration-content">
                <?php echo nl2br( esc_html( $transliteration ) ); ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Audio Player if Available -->
          <?php if ( $audio_url ) : ?>
            <div class="verse-group audio-player-group">
              <span class="verse-lang-label">🎵 <?php esc_html_e( 'Sacred Audio Recitation', 'djv-theme' ); ?></span>
              <audio controls class="mantra-audio-player">
                <source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg">
                <?php esc_html_e( 'Your browser does not support the audio element.', 'djv-theme' ); ?>
              </audio>
            </div>
          <?php endif; ?>
        </section>

        <!-- ── Spiritual Meaning Section ── -->
        <?php if ( $meaning ) : ?>
          <section class="mantra-content-card meaning-card">
            <h2 class="content-card-title">
              <span class="title-icon">🌟</span>
              <span><?php esc_html_e( 'Spiritual Meaning &amp; Word-by-Word Translation', 'djv-theme' ); ?></span>
            </h2>
            <div class="content-card-body meaning-body">
              <?php echo nl2br( esc_html( $meaning ) ); ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── How to Chant Section ── -->
        <?php if ( $how_to_chant ) : ?>
          <section class="mantra-content-card how-to-chant-card">
            <h2 class="content-card-title">
              <span class="title-icon">📿</span>
              <span><?php esc_html_e( 'How to Chant: Method, Posture &amp; Purity', 'djv-theme' ); ?></span>
            </h2>
            <div class="content-card-body">
              <p><?php echo esc_html( $how_to_chant ); ?></p>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Traditional Significance Section ── -->
        <?php if ( $significance ) : ?>
          <section class="mantra-content-card significance-card">
            <h2 class="content-card-title">
              <span class="title-icon">📜</span>
              <span><?php esc_html_e( 'Traditional Significance &amp; Scriptural Origin', 'djv-theme' ); ?></span>
            </h2>
            <div class="content-card-body">
              <p><?php echo esc_html( $significance ); ?></p>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Traditional Benefits (Responsibly Framed) ── -->
        <?php if ( $benefits ) : ?>
          <section class="mantra-content-card benefits-card">
            <h2 class="content-card-title">
              <span class="title-icon">✨</span>
              <span><?php esc_html_e( 'Benefits Traditionally Associated With This Mantra', 'djv-theme' ); ?></span>
            </h2>
            <div class="content-card-body">
              <p><?php echo esc_html( $benefits ); ?></p>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Detailed Article / Commentary ── -->
        <?php if ( get_the_content() ) : ?>
          <section class="mantra-content-card detailed-text-card">
            <h2 class="content-card-title">
              <span class="title-icon">📖</span>
              <span><?php esc_html_e( 'Vedic Commentary &amp; In-Depth Context', 'djv-theme' ); ?></span>
            </h2>
            <div class="content-card-body entry-content">
              <?php the_content(); ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── FAQ Section ── -->
        <?php if ( ! empty( $faq ) && is_array( $faq ) ) : ?>
          <section class="mantra-content-card faq-card">
            <h2 class="content-card-title">
              <span class="title-icon">❓</span>
              <span><?php esc_html_e( 'Frequently Asked Questions', 'djv-theme' ); ?></span>
            </h2>
            <div class="faq-accordion-list">
              <?php foreach ( $faq as $idx => $item ) : ?>
                <details class="faq-item" <?php echo $idx === 0 ? 'open' : ''; ?>>
                  <summary class="faq-question">
                    <span><?php echo esc_html( $item['q'] ?? '' ); ?></span>
                    <span class="faq-chevron" aria-hidden="true">▾</span>
                  </summary>
                  <div class="faq-answer">
                    <p><?php echo esc_html( $item['a'] ?? '' ); ?></p>
                  </div>
                </details>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Share & Devotional Actions ── -->
        <section class="mantra-share-bar" aria-label="<?php esc_attr_e( 'Share this mantra', 'djv-theme' ); ?>">
          <span class="share-label">🕉️ <?php esc_html_e( 'Share with Devotees &amp; Family:', 'djv-theme' ); ?></span>
          <div class="share-links">
            <a href="https://api.whatsapp.com/send?text=<?php echo $share_txt . '%20' . $share_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-share whatsapp" aria-label="Share on WhatsApp">
              WhatsApp
            </a>
            <a href="https://twitter.com/intent/tweet?text=<?php echo $share_txt; ?>&url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-share twitter" aria-label="Share on X / Twitter">
              X (Twitter)
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-share facebook" aria-label="Share on Facebook">
              Facebook
            </a>
            <button type="button" class="btn-share copy-link" id="btn-copy-page-link" data-url="<?php echo esc_url( get_permalink() ); ?>">
              <?php esc_html_e( 'Copy Link', 'djv-theme' ); ?>
            </button>
          </div>
        </section>

        <!-- ── Related Mantras ── -->
        <?php
        $related_args = [
	        'post_type'      => 'djv_mantra',
	        'posts_per_page' => 3,
	        'post__not_in'   => [ $post_id ],
	        'post_status'    => 'publish',
	        'orderby'        => 'rand',
        ];
        if ( ! empty( $deity ) ) {
	        $related_args['tax_query'] = [
		        [
			        'taxonomy' => 'djv_deity',
			        'field'    => 'name',
			        'terms'    => $deity,
		        ],
	        ];
        }

        $related = new WP_Query( $related_args );
        if ( ! $related->have_posts() ) {
	        // Fallback without tax_query
	        $related = new WP_Query([
		        'post_type'      => 'djv_mantra',
		        'posts_per_page' => 3,
		        'post__not_in'   => [ $post_id ],
		        'post_status'    => 'publish',
		        'orderby'        => 'rand',
	        ]);
        }

        if ( $related->have_posts() ) :
        ?>
          <section class="related-mantras-section">
            <h2 class="related-heading">
              <?php esc_html_e( 'Related Sacred Mantras &amp; Stotras', 'djv-theme' ); ?>
              <span class="related-heading-te">సంబంధిత పవిత్ర మంత్రాలు</span>
            </h2>
            <div class="mantras-grid related-grid">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/mantra/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Related Festivals & Poojas (Reciprocal Internal Link Graph) ── -->
        <?php
        $mantra_slug = get_post_field( 'post_name', $post_id );
        $alt_slug    = ( strpos( $mantra_slug, 'maha-' ) !== false ) ? str_replace( 'maha-', 'maha', $mantra_slug ) : str_replace( 'mahamrityunjaya', 'maha-mrityunjaya', $mantra_slug );
        $all_slugs   = array_unique( [ $mantra_slug, $alt_slug ] );

        $slug_meta_conditions = [ 'relation' => 'OR' ];
        foreach ( $all_slugs as $sl ) {
          $slug_meta_conditions[] = [
            'key'     => '_djv_related_mantras',
            'value'   => $sl,
            'compare' => 'LIKE',
          ];
        }

        // 1. Query festivals prioritizing both mantra relationship and matching deity
        $deity_slugs = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'slugs' ] );
        $deity_clean = trim( preg_replace( '/\b(lord|goddess|shri|sri)\b/i', '', $deity ?: '' ) );
        $search_terms = array_unique( array_filter( array_merge(
          $deity_slugs ?: [],
          [ $deity, $deity_clean, sanitize_title( $deity_clean ), sanitize_title( $deity ) ]
        ) ) );

        $fest_args = [
          'post_type'      => 'djv_festival',
          'posts_per_page' => 2,
          'post_status'    => 'publish',
          'meta_query'     => $slug_meta_conditions,
        ];
        if ( ! empty( $search_terms ) ) {
          $fest_args['tax_query'] = [
            'relation' => 'OR',
            [
              'taxonomy' => 'djv_deity',
              'field'    => 'slug',
              'terms'    => $search_terms,
            ],
            [
              'taxonomy' => 'djv_deity',
              'field'    => 'name',
              'terms'    => $search_terms,
            ]
          ];
        }
        $rel_festivals = new WP_Query( $fest_args );

        // Fallback: if no deity-specific festival matched, query any festival referencing this mantra
        if ( ! $rel_festivals->have_posts() ) {
          $rel_festivals = new WP_Query([
            'post_type'      => 'djv_festival',
            'posts_per_page' => 2,
            'post_status'    => 'publish',
            'meta_query'     => $slug_meta_conditions,
          ]);
        }

        // 2. Query poojas prioritizing both mantra relationship and matching deity
        $pooja_args = [
          'post_type'      => 'djv_pooja',
          'posts_per_page' => 2,
          'post_status'    => 'publish',
          'meta_query'     => $slug_meta_conditions,
        ];
        if ( ! empty( $search_terms ) ) {
          $pooja_args['tax_query'] = [
            'relation' => 'OR',
            [
              'taxonomy' => 'djv_deity',
              'field'    => 'slug',
              'terms'    => $search_terms,
            ],
            [
              'taxonomy' => 'djv_deity',
              'field'    => 'name',
              'terms'    => $search_terms,
            ]
          ];
        }
        $rel_poojas = new WP_Query( $pooja_args );

        // Fallback: if no deity-specific pooja matched, query any pooja referencing this mantra
        if ( ! $rel_poojas->have_posts() ) {
          $rel_poojas = new WP_Query([
            'post_type'      => 'djv_pooja',
            'posts_per_page' => 2,
            'post_status'    => 'publish',
            'meta_query'     => $slug_meta_conditions,
          ]);
        }
        ?>
        <?php if ( $rel_festivals->have_posts() || $rel_poojas->have_posts() ) : ?>
          <section class="related-festivals-poojas-section" style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #E8DFD3);">
            <h2 class="related-heading" style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin-bottom: 1.25rem;">
              <?php esc_html_e( 'Associated Festivals &amp; Pooja Vidhis', 'djv-theme' ); ?>
              <span class="related-heading-te" style="display: block; font-family: var(--font-telugu, sans-serif); font-size: 1.1rem; color: var(--clr-accent, #C89432); margin-top: 0.25rem;">సంబంధిత పండుగలు &amp; పూజా విధానాలు</span>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
              <?php while ( $rel_festivals->have_posts() ) : $rel_festivals->the_post(); ?>
                <?php get_template_part( 'template-parts/festival/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
              <?php while ( $rel_poojas->have_posts() ) : $rel_poojas->the_post(); ?>
                <?php get_template_part( 'template-parts/pooja/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

      </article>
    <?php endwhile; endif; ?>

  </div>
</div>

<style>
/* Single Mantra Page Styling */
.single-mantra-page {
  background: var(--clr-bg, #FFF9F0);
  padding: 2.5rem 0 5rem 0;
  min-height: 80vh;
}
.single-mantra-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 1.25rem;
}
.single-breadcrumb {
  font-size: 0.8125rem;
  color: var(--clr-text-muted, #A08070);
  margin-bottom: 1.5rem;
}
.single-breadcrumb a {
  color: inherit;
  text-decoration: none;
}
.single-breadcrumb a:hover {
  color: var(--clr-primary, #7A2419);
}
.single-breadcrumb .sep {
  margin: 0 0.4rem;
}
.single-breadcrumb .current {
  color: var(--clr-primary, #7A2419);
  font-weight: 600;
}

/* Header */
.single-mantra-header {
  margin-bottom: 2.5rem;
}
.header-meta-tags {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}
.deity-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: #FFF;
  border: 1px solid var(--clr-secondary, #C89432);
  padding: 0.3rem 0.85rem;
  border-radius: 9999px;
  font-size: 0.825rem;
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
}
.category-pill {
  background: rgba(200,148,50,0.15);
  color: var(--clr-secondary-dark, #9A7020);
  border: 1px solid rgba(200,148,50,0.3);
  padding: 0.3rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.78rem;
  font-weight: 600;
}
.single-mantra-title {
  font-family: var(--font-heading, 'Playfair Display', Georgia, serif);
  font-size: clamp(2.2rem, 5vw, 3rem);
  color: var(--clr-primary, #7A2419);
  line-height: 1.2;
  margin: 0 0 0.4rem 0;
}
.single-mantra-telugu-title {
  font-family: var(--font-telugu, 'Noto Sans Telugu', sans-serif);
  font-size: clamp(1.4rem, 3.5vw, 1.85rem);
  color: var(--clr-secondary-dark, #9A7020);
  font-weight: 600;
  margin-bottom: 1.25rem;
  line-height: 1.4;
}
.quick-specs-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}
.spec-item {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: #FFF;
  border: 1px solid var(--clr-border, #E8D5C4);
  padding: 0.45rem 1rem;
  border-radius: 9999px;
  font-size: 0.85rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.spec-label {
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
}
.spec-val {
  color: var(--clr-text, #2C1A14);
}

/* Sacred Chanting Box */
.sacred-box {
  background: linear-gradient(135deg, #FFFDF9 0%, #FFF8EE 100%);
  border: 2px solid var(--clr-secondary, #C89432);
  border-radius: 1.35rem;
  padding: 2.25rem;
  margin-bottom: 2.5rem;
  box-shadow: 0 8px 24px rgba(200,148,50,0.12);
  position: relative;
}
.sacred-box-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.75rem;
  border-bottom: 1px solid rgba(200,148,50,0.3);
  padding-bottom: 0.85rem;
}
.sacred-box-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.btn-copy-mantra {
  background: #FFF;
  border: 1px solid var(--clr-border, #E8D5C4);
  padding: 0.35rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--clr-primary, #7A2419);
  cursor: pointer;
  transition: all 0.2s;
}
.btn-copy-mantra:hover {
  background: var(--clr-primary, #7A2419);
  color: #FFF;
}
.verse-group {
  margin-bottom: 2rem;
}
.verse-group:last-child {
  margin-bottom: 0;
}
.verse-lang-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--clr-text-muted, #A08070);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
}
.sanskrit-content {
  font-family: 'Noto Sans Devanagari', 'Playfair Display', serif;
  font-size: clamp(1.35rem, 3.2vw, 1.75rem);
  line-height: 1.85;
  color: var(--clr-primary, #7A2419);
  font-weight: 600;
  background: #FFF;
  padding: 1.25rem 1.5rem;
  border-radius: 0.85rem;
  border-left: 4px solid var(--clr-primary, #7A2419);
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.03);
}
.telugu-content {
  font-family: var(--font-telugu, 'Noto Sans Telugu', sans-serif);
  font-size: clamp(1.25rem, 3vw, 1.55rem);
  line-height: 1.85;
  color: var(--clr-secondary-dark, #9A7020);
  font-weight: 600;
  background: #FFF;
  padding: 1.25rem 1.5rem;
  border-radius: 0.85rem;
  border-left: 4px solid var(--clr-secondary, #C89432);
}
.transliteration-content {
  font-style: italic;
  font-size: 1.05rem;
  line-height: 1.75;
  color: var(--clr-text-secondary, #6B4C3B);
  background: rgba(255,255,255,0.7);
  padding: 1rem 1.25rem;
  border-radius: 0.75rem;
  border: 1px dashed var(--clr-border, #E8D5C4);
}
.mantra-audio-player {
  width: 100%;
  margin-top: 0.5rem;
}

/* Content Cards */
.mantra-content-card {
  background: #FFF;
  border: 1.5px solid var(--clr-border, #E8D5C4);
  border-radius: 1.25rem;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(44,26,20,0.04);
}
.content-card-title {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-family: var(--font-heading, serif);
  font-size: 1.45rem;
  color: var(--clr-primary, #7A2419);
  margin: 0 0 1.15rem 0;
  border-bottom: 1px solid var(--clr-border, #E8D5C4);
  padding-bottom: 0.75rem;
}
.content-card-body {
  font-size: 1.05rem;
  line-height: 1.8;
  color: var(--clr-text, #2C1A14);
}
.content-card-body p {
  margin: 0;
}
.meaning-body {
  font-size: 1.1rem;
  color: var(--clr-text, #2C1A14);
}

/* FAQ Accordion */
.faq-accordion-list {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}
.faq-item {
  border: 1px solid var(--clr-border, #E8D5C4);
  border-radius: 0.75rem;
  overflow: hidden;
  transition: all 0.2s;
}
.faq-item[open] {
  border-color: var(--clr-secondary, #C89432);
  background: #FFFDF9;
}
.faq-question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  font-weight: 600;
  color: var(--clr-primary, #7A2419);
  cursor: pointer;
  list-style: none;
  font-size: 1.05rem;
}
.faq-question::-webkit-details-marker {
  display: none;
}
.faq-chevron {
  font-size: 1.25rem;
  color: var(--clr-secondary, #C89432);
  transition: transform 0.2s;
}
.faq-item[open] .faq-chevron {
  transform: rotate(180deg);
}
.faq-answer {
  padding: 0 1.25rem 1.25rem 1.25rem;
  color: var(--clr-text-secondary, #6B4C3B);
  line-height: 1.7;
  font-size: 0.98rem;
}

/* Share Bar */
.mantra-share-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  background: #FFF;
  border: 1.5px dashed var(--clr-border, #E8D5C4);
  border-radius: 1rem;
  padding: 1.25rem 1.5rem;
  margin-bottom: 3.5rem;
}
.share-label {
  font-weight: 600;
  color: var(--clr-primary, #7A2419);
  font-size: 0.95rem;
}
.share-links {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.btn-share {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.95rem;
  border-radius: 9999px;
  font-size: 0.8125rem;
  font-weight: 600;
  text-decoration: none;
  border: 1px solid var(--clr-border, #E8D5C4);
  background: #FFF9F0;
  color: var(--clr-text, #2C1A14);
  cursor: pointer;
  transition: all 0.2s;
}
.btn-share.whatsapp:hover { background: #25D366; color: #FFF; border-color: #25D366; }
.btn-share.twitter:hover { background: #000; color: #FFF; border-color: #000; }
.btn-share.facebook:hover { background: #1877F2; color: #FFF; border-color: #1877F2; }
.btn-share.copy-link:hover { background: var(--clr-primary, #7A2419); color: #FFF; border-color: var(--clr-primary, #7A2419); }

/* Related Mantras */
.related-mantras-section {
  border-top: 2px solid var(--clr-border, #E8D5C4);
  padding-top: 3rem;
}
.related-heading {
  font-family: var(--font-heading, serif);
  font-size: 1.85rem;
  color: var(--clr-primary, #7A2419);
  margin: 0 0 1.5rem 0;
}
.related-heading-te {
  display: block;
  font-family: var(--font-telugu, sans-serif);
  font-size: 1.3rem;
  color: var(--clr-secondary, #C89432);
  margin-top: 0.25rem;
}
</style>

<script>
(function() {
  // Copy Verse button
  const copyBtn = document.getElementById('btn-copy-mantra');
  const verseEl = document.getElementById('sanskrit-text-content');
  if (copyBtn && verseEl) {
    copyBtn.addEventListener('click', () => {
      const text = verseEl.innerText || verseEl.textContent;
      navigator.clipboard.writeText(text).then(() => {
        const orig = copyBtn.innerHTML;
        copyBtn.innerHTML = '✅ Copied!';
        setTimeout(() => copyBtn.innerHTML = orig, 2000);
      });
    });
  }

  // Copy Page Link button
  const copyLinkBtn = document.getElementById('btn-copy-page-link');
  if (copyLinkBtn) {
    copyLinkBtn.addEventListener('click', () => {
      const url = copyLinkBtn.getAttribute('data-url') || window.location.href;
      navigator.clipboard.writeText(url).then(() => {
        const orig = copyLinkBtn.innerHTML;
        copyLinkBtn.innerHTML = '✅ Link Copied!';
        setTimeout(() => copyLinkBtn.innerHTML = orig, 2000);
      });
    });
  }
})();
</script>

<?php
get_footer();
