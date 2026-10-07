<?php
/**
 * Single Temple Template (djv_temple)
 *
 * @package DJV_Theme
 */

get_header();

$post_id  = get_the_ID();
$state    = get_post_meta( $post_id, '_djv_state', true );
$district = get_post_meta( $post_id, '_djv_district', true );
$address  = get_post_meta( $post_id, '_djv_address', true );
$lat      = get_post_meta( $post_id, '_djv_lat', true );
$lon      = get_post_meta( $post_id, '_djv_lon', true );
$timings  = get_post_meta( $post_id, '_djv_timings', true );
$deity    = get_post_meta( $post_id, '_djv_deity', true );
$contact  = get_post_meta( $post_id, '_djv_contact', true );
$website  = get_post_meta( $post_id, '_djv_website', true );

$loc_string = $district ? $district . ', ' . $state : $state;
?>

<div class="single-temple-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 960px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Temples', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'temple-article' ); ?>>

        <!-- Header -->
        <header style="margin-bottom: 2rem;">
          <?php if ( $loc_string ) : ?>
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
              📍 <?php echo esc_html( $loc_string ); ?>
            </div>
          <?php endif; ?>

          <h1 style="font-family: var(--font-heading); font-size: 2.6rem; color: var(--clr-primary); line-height: 1.25; margin: 0 0 0.5rem 0;">
            <?php the_title(); ?>
          </h1>

          <?php if ( $deity ) : ?>
            <div style="font-size: 1.15rem; color: var(--clr-accent); margin-bottom: 0.5rem; font-weight: 600;">
              🕉 <?php esc_html_e( 'Presiding Deity:', 'djv-theme' ); ?> <?php echo esc_html( $deity ); ?>
            </div>
          <?php endif; ?>
        </header>

        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md);">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;display:block;' ] ); ?>
          </div>
        <?php endif; ?>

        <!-- Quick Facts / Darshan Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem; background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.75rem; box-shadow: var(--shadow-sm);">
          <?php if ( $timings ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                ⏰ <?php esc_html_e( 'Darshan & Seva Timings', 'djv-theme' ); ?>
              </div>
              <div style="font-size: 0.95rem; font-weight: 600; color: var(--clr-primary); margin-top: 0.35rem;">
                <?php echo esc_html( $timings ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $address ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                🗺️ <?php esc_html_e( 'Kshetra Address', 'djv-theme' ); ?>
              </div>
              <div style="font-size: 0.95rem; color: var(--clr-text); margin-top: 0.35rem; line-height: 1.5;">
                <?php echo esc_html( $address ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $lat && $lon ) : ?>
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                🧭 <?php esc_html_e( 'GPS Coordinates', 'djv-theme' ); ?>
              </div>
              <div style="font-size: 0.9rem; color: var(--clr-text); margin-top: 0.35rem;">
                <?php echo esc_html( number_format( (float) $lat, 4 ) . '° N, ' . number_format( (float) $lon, 4 ) . '° E' ); ?><br>
                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( $lat . ',' . $lon ); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--clr-primary); font-weight: 600; text-decoration: none;">
                  ↗ <?php esc_html_e( 'Open in Maps', 'djv-theme' ); ?>
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- History & Sthala Purana -->
        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text); margin-bottom: 3.5rem;">
          <h2 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0 0 1rem 0;">
            <?php esc_html_e( 'Sthala Purana & History', 'djv-theme' ); ?>
          </h2>
          <?php the_content(); ?>
        </div>

        <!-- Contact & Official Links -->
        <?php if ( $contact || $website ) : ?>
          <div style="background: #FFF9F0; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem; margin-bottom: 3.5rem; display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center;">
            <?php if ( $contact ) : ?>
              <div>
                <strong><?php esc_html_e( 'Devasthanam Contact:', 'djv-theme' ); ?></strong>
                <a href="tel:<?php echo esc_attr( str_replace(' ', '', $contact) ); ?>" style="color: var(--clr-primary); margin-left: 0.5rem; font-weight: 600; text-decoration: none;">
                  📞 <?php echo esc_html( $contact ); ?>
                </a>
              </div>
            <?php endif; ?>

            <?php if ( $website ) : ?>
              <div>
                <strong><?php esc_html_e( 'Official Devasthanam Portal:', 'djv-theme' ); ?></strong>
                <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--clr-primary); margin-left: 0.5rem; font-weight: 600; text-decoration: none;">
                  🌐 <?php esc_html_e( 'Visit Website', 'djv-theme' ); ?> ↗
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- Related Temples -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_temple',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Sacred Kshetras & Temples', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/temple/card' ); ?>
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
