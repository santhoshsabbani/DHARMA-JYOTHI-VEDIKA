<?php
/**
 * Single Festival Template (djv_festival)
 *
 * @package DJV_Theme
 */

get_header();

$post_id     = get_the_ID();
$telugu_name = get_post_meta( $post_id, '_djv_telugu_name', true );
$date_meta   = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
$end_date    = get_post_meta( $post_id, '_djv_end_date', true );
$significance= get_post_meta( $post_id, '_djv_significance', true );
$timings     = get_post_meta( $post_id, '_djv_puja_timings', true );
$samagri     = get_post_meta( $post_id, '_djv_samagri', true );
$naivedyam   = get_post_meta( $post_id, '_djv_naivedyam', true );
$mantras     = get_post_meta( $post_id, '_djv_mantras', true );

$formatted_date = '';
if ( $date_meta ) {
	$time = strtotime( $date_meta );
	if ( $time ) {
		$formatted_date = date( 'F j, Y', $time );
	}
}
?>

<div class="single-festival-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 960px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'festival-article' ); ?>>

        <!-- Header -->
        <header style="margin-bottom: 2rem;">
          <div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
            🎊 <?php esc_html_e( 'Vedic Festival & Observance', 'djv-theme' ); ?>
          </div>

          <h1 style="font-family: var(--font-heading); font-size: 2.6rem; color: var(--clr-primary); line-height: 1.2; margin: 0 0 0.5rem 0;">
            <?php the_title(); ?>
          </h1>

          <?php if ( $telugu_name ) : ?>
            <div style="font-family: var(--font-telugu); font-size: 1.5rem; color: var(--clr-accent); margin-bottom: 1rem;">
              <?php echo esc_html( $telugu_name ); ?>
            </div>
          <?php endif; ?>

          <?php if ( $formatted_date ) : ?>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: #FFF9F0; border: 1px solid var(--clr-border); padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 600; color: var(--clr-primary);">
              📅 <?php echo esc_html( $formatted_date ); ?>
              <?php if ( $end_date ) : ?>
                – <?php echo esc_html( date( 'F j, Y', strtotime( $end_date ) ) ); ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </header>

        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md);">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;display:block;' ] ); ?>
          </div>
        <?php endif; ?>

        <!-- Key Details Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem; background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem; box-shadow: var(--shadow-sm);">
          <?php if ( $timings ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase;"><?php esc_html_e( 'Auspicious Timings', 'djv-theme' ); ?></div>
              <div style="font-size: 0.95rem; font-weight: 600; color: var(--clr-primary); margin-top: 0.25rem;">⏰ <?php echo esc_html( $timings ); ?></div>
            </div>
          <?php endif; ?>

          <?php if ( $significance ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase;"><?php esc_html_e( 'Spiritual Significance', 'djv-theme' ); ?></div>
              <div style="font-size: 0.95rem; color: var(--clr-text); margin-top: 0.25rem;">✨ <?php echo esc_html( $significance ); ?></div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text); margin-bottom: 3rem;">
          <?php the_content(); ?>
        </div>

        <!-- Rituals & Samagri Section -->
        <?php if ( $samagri || $naivedyam || $mantras ) : ?>
          <div style="background: #FFF9F0; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 2rem; margin-bottom: 3rem;">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--clr-primary); margin: 0 0 1.25rem 0;">
              🪔 <?php esc_html_e( 'Pooja Vidhi & Rituals', 'djv-theme' ); ?>
            </h2>

            <?php if ( $samagri ) : ?>
              <div style="margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; color: var(--clr-primary); margin: 0 0 0.35rem 0;"><?php esc_html_e( 'Required Pooja Samagri:', 'djv-theme' ); ?></h3>
                <p style="margin: 0; color: var(--clr-text-secondary); font-size: 0.95rem;"><?php echo esc_html( $samagri ); ?></p>
              </div>
            <?php endif; ?>

            <?php if ( $naivedyam ) : ?>
              <div style="margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; color: var(--clr-primary); margin: 0 0 0.35rem 0;"><?php esc_html_e( 'Sacred Naivedyam (Prasadam):', 'djv-theme' ); ?></h3>
                <p style="margin: 0; color: var(--clr-text-secondary); font-size: 0.95rem;"><?php echo esc_html( $naivedyam ); ?></p>
              </div>
            <?php endif; ?>

            <?php if ( $mantras ) : ?>
              <div>
                <h3 style="font-size: 1rem; color: var(--clr-primary); margin: 0 0 0.35rem 0;"><?php esc_html_e( 'Chanting Mantras:', 'djv-theme' ); ?></h3>
                <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; font-family: 'Noto Sans Devanagari', serif; font-size: 1.05rem; color: var(--clr-primary); border-left: 3px solid var(--clr-primary);">
                  <?php echo esc_html( $mantras ); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Related Festivals -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_festival',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Sacred Festivals', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/festival/card' ); ?>
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
