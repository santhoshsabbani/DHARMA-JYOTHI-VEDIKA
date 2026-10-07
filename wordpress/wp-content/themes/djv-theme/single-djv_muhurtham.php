<?php
/**
 * Single Muhurtham Template (djv_muhurtham)
 *
 * @package DJV_Theme
 */

get_header();

$post_id     = get_the_ID();
$category    = get_post_meta( $post_id, '_djv_category', true );
$auspicious  = get_post_meta( $post_id, '_djv_auspicious', true );
$description = get_post_meta( $post_id, '_djv_description', true );
$tithi_pref  = get_post_meta( $post_id, '_djv_tithi', true );
$timings     = get_post_meta( $post_id, '_djv_timings', true );
?>

<div class="single-muhurtham-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 960px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/muhurtham/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Muhurtham', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'muhurtham-article' ); ?>>

        <!-- Header -->
        <header style="margin-bottom: 2rem;">
          <?php if ( $category ) : ?>
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
              ✨ <?php echo esc_html( $category ); ?> <?php esc_html_e( 'Muhurtham Guide', 'djv-theme' ); ?>
            </div>
          <?php endif; ?>

          <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); line-height: 1.25; margin: 0 0 0.75rem 0;">
            <?php the_title(); ?>
          </h1>

          <?php if ( $description ) : ?>
            <p style="font-size: 1.1rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1.5rem 0;">
              <?php echo esc_html( $description ); ?>
            </p>
          <?php endif; ?>
        </header>

        <!-- Auspicious Factors Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem; background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.75rem; box-shadow: var(--shadow-sm);">
          <?php if ( $auspicious ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                🌟 <?php esc_html_e( 'Favorable Nakshatras & Months', 'djv-theme' ); ?>
              </div>
              <div style="font-size: 1rem; font-weight: 600; color: var(--clr-primary); margin-top: 0.35rem;">
                <?php echo esc_html( $auspicious ); ?>
              </div>
            </div>
          <?php endif; ?>

          <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
              📍 <?php esc_html_e( 'Location Dependency', 'djv-theme' ); ?>
            </div>
            <div style="font-size: 0.95rem; color: var(--clr-text); margin-top: 0.35rem;">
              <?php esc_html_e( 'Muhurtham lagna and timings depend strictly on local sunrise and latitude/longitude.', 'djv-theme' ); ?>
            </div>
          </div>
        </div>

        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md);">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;display:block;' ] ); ?>
          </div>
        <?php endif; ?>

        <!-- Detailed Content -->
        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text); margin-bottom: 3rem;">
          <?php the_content(); ?>
        </div>

        <!-- Dynamic Panchangam Link CTA -->
        <div style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF 100%); border: 1px solid var(--clr-secondary); border-radius: 1rem; padding: 2rem; margin-bottom: 3.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
          <div>
            <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
              📅 <?php esc_html_e( 'Verify Against Live Panchangam', 'djv-theme' ); ?>
            </h3>
            <p style="margin: 0; color: var(--clr-text-secondary); font-size: 0.95rem; max-width: 550px;">
              <?php esc_html_e( 'Check current daily Tithi, Nakshatra, Rahu Kalam, Yamagandam, and Dur Muhurtham for your chosen city.', 'djv-theme' ); ?>
            </p>
          </div>
          <div>
            <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="btn-hero-primary" style="text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 9999px; background: var(--clr-primary); color: #FFF; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
              <span>🗓</span> <?php esc_html_e( "View Today's Panchangam", 'djv-theme' ); ?>
            </a>
          </div>
        </div>

        <!-- Related Muhurtham Guides -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_muhurtham',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Sacred Muhurtham Categories', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/muhurtham/card' ); ?>
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
