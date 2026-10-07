<?php
/**
 * Single Mantra Template (djv_mantra)
 *
 * @package DJV_Theme
 */

get_header();

$post_id         = get_the_ID();
$sanskrit_text   = get_post_meta( $post_id, '_djv_original_text', true );
$telugu_text     = get_post_meta( $post_id, '_djv_telugu_text', true );
$transliteration = get_post_meta( $post_id, '_djv_transliteration', true );
$meaning         = get_post_meta( $post_id, '_djv_meaning', true );
$chant_count     = get_post_meta( $post_id, '_djv_chant_count', true );
$audio_url       = get_post_meta( $post_id, '_djv_audio', true );
$deity           = get_post_meta( $post_id, '_djv_deity', true );
?>

<div class="single-mantra-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 920px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Mantras & Slokas', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'mantra-article' ); ?>>

        <!-- Header -->
        <header style="margin-bottom: 2rem;">
          <div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
            📿 <?php echo $deity ? esc_html( $deity ) . ' • ' : ''; ?><?php esc_html_e( 'Sacred Stotram & Mantra', 'djv-theme' ); ?>
          </div>

          <h1 style="font-family: var(--font-heading); font-size: 2.6rem; color: var(--clr-primary); line-height: 1.25; margin: 0 0 0.5rem 0;">
            <?php the_title(); ?>
          </h1>

          <?php if ( $chant_count ) : ?>
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #FFF9F0; border: 1px solid var(--clr-border); padding: 0.4rem 0.85rem; border-radius: 9999px; font-weight: 600; font-size: 0.85rem; color: var(--clr-primary); margin-top: 0.5rem;">
              🔁 <?php esc_html_e( 'Chant Count:', 'djv-theme' ); ?> <?php echo esc_html( $chant_count ); ?>
            </div>
          <?php endif; ?>
        </header>

        <!-- Sacred Chanting Box -->
        <div style="background: linear-gradient(135deg, #FFFDF9 0%, #FFF9F0 100%); border: 1.5px solid var(--clr-secondary); border-radius: 1.25rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm);">
          
          <?php if ( $sanskrit_text ) : ?>
            <div style="margin-bottom: 1.75rem;">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                <?php esc_html_e( 'Sanskrit (संस्कृतम्):', 'djv-theme' ); ?>
              </span>
              <div style="font-family: 'Noto Sans Devanagari', serif; font-size: 1.5rem; color: var(--clr-primary); line-height: 1.8; margin-top: 0.35rem; font-weight: 600;">
                <?php echo nl2br( esc_html( $sanskrit_text ) ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $telugu_text ) : ?>
            <div style="margin-bottom: 1.75rem; border-top: 1px dashed var(--clr-border); padding-top: 1.25rem;">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                <?php esc_html_e( 'Telugu Script (తెలుగు లిపి):', 'djv-theme' ); ?>
              </span>
              <div style="font-family: var(--font-telugu), sans-serif; font-size: 1.35rem; color: var(--clr-accent); line-height: 1.8; margin-top: 0.35rem; font-weight: 600;">
                <?php echo nl2br( esc_html( $telugu_text ) ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $transliteration ) : ?>
            <div style="margin-bottom: 1.75rem; border-top: 1px dashed var(--clr-border); padding-top: 1.25rem;">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                <?php esc_html_e( 'IAST Roman Transliteration:', 'djv-theme' ); ?>
              </span>
              <div style="font-style: italic; font-size: 1.05rem; color: var(--clr-text-secondary); line-height: 1.7; margin-top: 0.35rem;">
                <?php echo nl2br( esc_html( $transliteration ) ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $meaning ) : ?>
            <div style="border-top: 1px solid var(--clr-border); padding-top: 1.25rem;">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                🌟 <?php esc_html_e( 'Spiritual Meaning & Significance:', 'djv-theme' ); ?>
              </span>
              <div style="font-size: 1.05rem; color: var(--clr-text); line-height: 1.75; margin-top: 0.35rem;">
                <?php echo esc_html( $meaning ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $audio_url ) : ?>
            <div style="margin-top: 1.5rem; border-top: 1px solid var(--clr-border); padding-top: 1.25rem;">
              <audio controls style="width: 100%;">
                <source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mpeg">
                <?php esc_html_e( 'Your browser does not support the audio element.', 'djv-theme' ); ?>
              </audio>
            </div>
          <?php endif; ?>

        </div>

        <!-- Detailed Commentary / Background -->
        <?php if ( get_the_content() ) : ?>
          <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text); margin-bottom: 3.5rem;">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--clr-primary); margin: 0 0 1rem 0;">
              <?php esc_html_e( 'Vedic Context & Practical Benefits', 'djv-theme' ); ?>
            </h2>
            <?php the_content(); ?>
          </div>
        <?php endif; ?>

        <!-- Related Mantras -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_mantra',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Sacred Mantras & Stotras', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/mantra/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

      </article>
    <?php endwhile; endif; ?>

  </div>
</div>

<?php
get_footer();
