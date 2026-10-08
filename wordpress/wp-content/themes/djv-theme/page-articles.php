<?php
/**
 * Template Name: Vedic Articles & Knowledge
 * Description: Dedicated template for Articles archive (/articles/).
 *
 * @package DJV_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$articles_query = new WP_Query( [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'paged'          => $paged,
	'posts_per_page' => 9,
] );
?>

<div class="archive-wrapper" style="padding: 3rem 0 5rem 0; background: var(--clr-bg, #fdfaf6);">
  <div class="container">

    <header class="archive-header" style="margin-bottom: 2.5rem; text-align: center;">
      <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.5rem;">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
        <span style="margin: 0 0.4rem;">›</span>
        <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Articles', 'djv-theme' ); ?></span>
      </nav>
      <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php esc_html_e( 'Vedic Articles & Knowledge', 'djv-theme' ); ?>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem; max-width: 680px; margin: 0 auto;">
        <?php esc_html_e( 'Authentic teachings, Jyotish insights, Sanatana Dharma traditions, and devotional spiritual guides.', 'djv-theme' ); ?>
      </p>
    </header>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:2rem;">
      <?php if ( $articles_query->have_posts() ) : while ( $articles_query->have_posts() ) : $articles_query->the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:#FFF; border-radius:var(--radius-xl, 1rem); border:1px solid var(--clr-border, #e8d5c4); overflow:hidden; box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04)); display:flex; flex-direction:column; transition:transform 0.2s ease, box-shadow 0.2s ease;">
          <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%; height:200px; object-fit:cover; display:block;' ] ); ?>
            </a>
          <?php endif; ?>

          <div style="padding:1.5rem; flex:1; display:flex; flex-direction:column;">
            <div style="font-size:0.75rem; color:var(--clr-accent, #c89432); font-weight:700; margin-bottom:0.5rem; text-transform:uppercase; letter-spacing:0.04em;">
              <?php the_category( ', ' ); ?>
            </div>
            <h2 style="font-family:var(--font-heading); font-size:1.35rem; color:var(--clr-primary, #7a2419); margin:0 0 0.75rem 0; line-height:1.3;">
              <a href="<?php the_permalink(); ?>" style="color:inherit; text-decoration:none;"><?php the_title(); ?></a>
            </h2>
            <div style="font-size:0.9rem; color:var(--clr-text-secondary, #666); line-height:1.6; margin-bottom:1.25rem; flex:1;">
              <?php echo wp_trim_words( get_the_excerpt(), 22 ); ?>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8125rem; color:var(--clr-text-muted); border-top:1px solid var(--clr-border, #f0f0f0); padding-top:0.75rem;">
              <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
              <a href="<?php the_permalink(); ?>" style="font-weight:600; color:var(--clr-primary, #7a2419); text-decoration:none;">
                <?php esc_html_e( 'Read Article →', 'djv-theme' ); ?>
              </a>
            </div>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); else : ?>
        <div style="grid-column:1/-1; text-align:center; padding:4rem 1.5rem; background:#fff; border-radius:1rem; border:1px solid var(--clr-border, #e8d5c4);">
          <div style="font-size:3rem; margin-bottom:1rem;">📜</div>
          <h3 style="font-family:var(--font-heading); color:var(--clr-primary); margin-bottom:0.5rem;">
            <?php esc_html_e( 'No Articles Published Yet', 'djv-theme' ); ?>
          </h3>
          <p style="color:var(--clr-text-muted); font-size:0.95rem; max-width:480px; margin:0 auto;">
            <?php esc_html_e( 'Vedic articles and Jyotish guides are currently being prepared by the editorial team. Please check back soon.', 'djv-theme' ); ?>
          </p>
        </div>
      <?php endif; ?>
    </div>

    <?php if ( $articles_query->max_num_pages > 1 ) : ?>
      <div class="pagination" style="margin-top: 3rem; text-align: center;">
        <?php
        echo paginate_links( [
          'total'   => $articles_query->max_num_pages,
          'current' => $paged,
        ] );
        ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php
get_footer();
