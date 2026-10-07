<?php
/**
 * Single Vedic Service Template (djv_service)
 *
 * @package DJV_Theme
 */

get_header();

$post_id  = get_the_ID();
$stype    = get_post_meta( $post_id, '_djv_service_type', true );
$price    = get_post_meta( $post_id, '_djv_price', true );
$duration = get_post_meta( $post_id, '_djv_duration', true );
$contact  = get_post_meta( $post_id, '_djv_contact', true );
?>

<div class="single-service-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 960px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Services', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php the_title(); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'service-article' ); ?>>

        <!-- Header -->
        <header style="margin-bottom: 2rem;">
          <?php if ( $stype ) : ?>
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
              ⭐ <?php echo esc_html( $stype ); ?>
            </div>
          <?php endif; ?>

          <h1 style="font-family: var(--font-heading); font-size: 2.6rem; color: var(--clr-primary); line-height: 1.25; margin: 0 0 0.5rem 0;">
            <?php the_title(); ?>
          </h1>

          <?php if ( has_excerpt() ) : ?>
            <p style="font-size: 1.1rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 1.25rem 0;">
              <?php echo esc_html( get_the_excerpt() ); ?>
            </p>
          <?php endif; ?>
        </header>

        <!-- Service Specs Banner -->
        <div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.75rem; margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm);">
          <div>
            <?php if ( $duration ) : ?>
              <div style="font-size: 0.85rem; color: var(--clr-text-muted);"><?php esc_html_e( 'Estimated Duration', 'djv-theme' ); ?></div>
              <div style="font-size: 1.2rem; font-weight: 700; color: var(--clr-primary); margin-top: 0.2rem;">⏱ <?php echo esc_html( $duration ); ?></div>
            <?php endif; ?>
          </div>

          <div>
            <?php if ( $price ) : ?>
              <div style="font-size: 0.85rem; color: var(--clr-text-muted);"><?php esc_html_e( 'Suggested Dakshina / Fee', 'djv-theme' ); ?></div>
              <div style="font-size: 1.2rem; font-weight: 700; color: var(--clr-accent); margin-top: 0.2rem;">💰 <?php echo esc_html( $price ); ?></div>
            <?php endif; ?>
          </div>

          <div>
            <?php if ( $contact ) : ?>
              <a href="tel:<?php echo esc_attr( str_replace(' ', '', $contact) ); ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--clr-primary); color: #FFF; padding: 0.75rem 1.5rem; border-radius: 9999px; font-weight: 600; text-decoration: none; box-shadow: var(--shadow-sm);">
                📞 <?php esc_html_e( 'Book with Scholar', 'djv-theme' ); ?>
              </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom: 2.5rem; border-radius: 1rem; overflow: hidden; box-shadow: var(--shadow-md);">
            <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:auto;display:block;' ] ); ?>
          </div>
        <?php endif; ?>

        <!-- Detailed Description -->
        <div class="entry-content" style="font-size: 1.05rem; line-height: 1.85; color: var(--clr-text); margin-bottom: 3.5rem;">
          <h2 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0 0 1rem 0;">
            <?php esc_html_e( 'Service Description & Ritual Scope', 'djv-theme' ); ?>
          </h2>
          <?php the_content(); ?>
        </div>

        <!-- Consultation Commitment Box -->
        <div style="background: #FFF9F0; border-left: 4px solid var(--clr-primary); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 3.5rem;">
          <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
            🕉 <?php esc_html_e( 'The Dharma Jyothi Vedika Commitment', 'djv-theme' ); ?>
          </h3>
          <p style="margin: 0; color: var(--clr-text-secondary); font-size: 0.95rem; line-height: 1.6;">
            <?php esc_html_e( 'All homams and consultations are performed strictly according to Vedic scriptures by verified scholars holding formal Veda Pathashala credentials.', 'djv-theme' ); ?>
          </p>
        </div>

        <!-- Related Services -->
        <?php
        $related = new WP_Query([
          'post_type'      => 'djv_service',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related->have_posts() ) :
        ?>
          <section style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border);">
            <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin-bottom: 1.5rem;">
              <?php esc_html_e( 'Other Vedic Services & Consultations', 'djv-theme' ); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/service/card' ); ?>
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
