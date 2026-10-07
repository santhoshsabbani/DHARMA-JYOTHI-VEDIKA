<?php
/**
 * Single Pooja Guide Template (djv_pooja)
 *
 * @package DJV_Theme
 */

get_header();

$post_id   = get_the_ID();
$samagri   = get_post_meta( $post_id, '_djv_samagri', true );
$naivedyam = get_post_meta( $post_id, '_djv_naivedyam', true );
$mantras   = get_post_meta( $post_id, '_djv_mantras', true );
$duration  = get_post_meta( $post_id, '_djv_duration', true );
$timings   = get_post_meta( $post_id, '_djv_timings', true );
?>

<div class="single-pooja-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 960px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/pooja/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Pooja Guides', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'pooja-article' ); ?>>

        <!-- Header -->
        <header style="margin-bottom: 2rem;">
          <div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
            🪔 <?php esc_html_e( 'Vedic Pooja Vidhi & Samagri', 'djv-theme' ); ?>
          </div>

          <h1 style="font-family: var(--font-heading); font-size: 2.6rem; color: var(--clr-primary); line-height: 1.25; margin: 0 0 0.75rem 0;">
            <?php the_title(); ?>
          </h1>

          <?php if ( has_excerpt() ) : ?>
            <p style="font-size: 1.1rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1.25rem 0;">
              <?php echo esc_html( get_the_excerpt() ); ?>
            </p>
          <?php endif; ?>

          <?php if ( $duration || $timings ) : ?>
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
              <?php if ( $duration ) : ?>
                <span style="background: #FFF9F0; border: 1px solid var(--clr-border); padding: 0.4rem 0.85rem; border-radius: 9999px; font-weight: 600; font-size: 0.85rem; color: var(--clr-primary);">
                  ⏱ <?php esc_html_e( 'Duration:', 'djv-theme' ); ?> <?php echo esc_html( $duration ); ?>
                </span>
              <?php endif; ?>
              <?php if ( $timings ) : ?>
                <span style="background: #FFF9F0; border: 1px solid var(--clr-border); padding: 0.4rem 0.85rem; border-radius: 9999px; font-weight: 600; font-size: 0.85rem; color: var(--clr-primary);">
                  ⏰ <?php echo esc_html( $timings ); ?>
                </span>
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

        <!-- Samagri & Naivedyam Box -->
        <?php if ( $samagri || $naivedyam ) : ?>
          <div style="background: #FFF9F0; border: 1.5px solid var(--clr-secondary); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem;">
            <h2 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--clr-primary); margin: 0 0 1.25rem 0;">
              📦 <?php esc_html_e( 'Essential Samagri & Naivedyam', 'djv-theme' ); ?>
            </h2>

            <?php if ( $samagri ) : ?>
              <div style="margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; color: var(--clr-primary); margin: 0 0 0.35rem 0;">
                  <?php esc_html_e( 'Required Pooja Samagri List:', 'djv-theme' ); ?>
                </h3>
                <p style="margin: 0; color: var(--clr-text-secondary); line-height: 1.6; font-size: 0.95rem;">
                  <?php echo esc_html( $samagri ); ?>
                </p>
              </div>
            <?php endif; ?>

            <?php if ( $naivedyam ) : ?>
              <div>
                <h3 style="font-size: 1rem; color: var(--clr-primary); margin: 0 0 0.35rem 0;">
                  <?php esc_html_e( 'Sacred Naivedyam (Prasadam):', 'djv-theme' ); ?>
                </h3>
                <p style="margin: 0; color: var(--clr-text-secondary); line-height: 1.6; font-size: 0.95rem;">
                  <?php echo esc_html( $naivedyam ); ?>
                </p>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Content (Procedure) -->
        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text); margin-bottom: 3rem;">
          <h2 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0 0 1rem 0;">
            <?php esc_html_e( 'Step-by-Step Pooja Vidhi (విధానం)', 'djv-theme' ); ?>
          </h2>
          <?php the_content(); ?>
        </div>

        <!-- Sacred Mantras Chanting Box -->
        <?php if ( $mantras ) : ?>
          <div style="background: #FFF; border: 1px solid var(--clr-border); border-left: 4px solid var(--clr-primary); border-radius: 0.75rem; padding: 1.75rem; margin-bottom: 3.5rem; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--clr-primary); margin: 0 0 0.75rem 0;">
              📿 <?php esc_html_e( 'Associated Chanting Mantras & Slokas', 'djv-theme' ); ?>
            </h3>
            <div style="font-family: 'Noto Sans Devanagari', serif; font-size: 1.15rem; color: var(--clr-primary); line-height: 1.8;">
              <?php echo esc_html( $mantras ); ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Related Pooja Guides -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_pooja',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Sacred Pooja Guides', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/pooja/card' ); ?>
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
