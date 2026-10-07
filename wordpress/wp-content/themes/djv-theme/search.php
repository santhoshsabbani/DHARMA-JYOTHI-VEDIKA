<?php
/**
 * Search Results Template
 *
 * Displays multi-CPT results across festivals, temples, poojas, mantras, muhurthams, articles, and services.
 *
 * @package DJV_Theme
 */

get_header();
global $wp_query;
$query_str = get_search_query();
$count     = $wp_query->found_posts;
?>

<div class="search-results-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container" style="max-width: 900px;">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Search Results', 'djv-theme' ); ?></span>
    </nav>

    <!-- Header -->
    <header class="search-header" style="margin-bottom: 2.5rem; border-bottom: 1px solid var(--clr-border); padding-bottom: 1.5rem;">
      <h1 style="font-family: var(--font-heading); font-size: 2.25rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
        <?php printf( esc_html__( 'Search results for: “%s”', 'djv-theme' ), esc_html( $query_str ) ); ?>
      </h1>
      <p style="color: var(--clr-text-secondary); margin: 0; font-size: 1rem;">
        <?php printf( esc_html__( 'Found %d matching entries across Panchangam, Festivals, Temples, Pooja Guides, Mantras, and Articles.', 'djv-theme' ), (int) $count ); ?>
      </p>

      <!-- Search Refine Form -->
      <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-top: 1.5rem; display: flex; gap: 0.5rem; max-width: 550px;">
        <input type="search" name="s" value="<?php echo esc_attr( $query_str ); ?>" placeholder="<?php esc_attr_e( 'Search again...', 'djv-theme' ); ?>" style="flex: 1; padding: 0.75rem 1rem; border-radius: 9999px; border: 1px solid var(--clr-border); font-size: 0.95rem; outline: none;" />
        <button type="submit" style="padding: 0.75rem 1.5rem; border-radius: 9999px; background: var(--clr-primary); color: #FFF; border: none; font-weight: 600; cursor: pointer;">
          🔍 <?php esc_html_e( 'Search', 'djv-theme' ); ?>
        </button>
      </form>
    </header>

    <!-- Results List -->
    <div class="search-results-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post();
          $pt = get_post_type();
          $type_label = '';
          $type_color = 'var(--clr-primary)';

          switch ( $pt ) {
            case 'djv_festival':
              $type_label = '🎊 ' . __( 'Festival', 'djv-theme' );
              $type_color = '#C89432';
              break;
            case 'djv_temple':
              $type_label = '🛕 ' . __( 'Temple', 'djv-theme' );
              $type_color = '#7A2419';
              break;
            case 'djv_pooja':
              $type_label = '🪔 ' . __( 'Pooja Guide', 'djv-theme' );
              $type_color = '#A85A14';
              break;
            case 'djv_mantra':
              $type_label = '📿 ' . __( 'Mantra & Sloka', 'djv-theme' );
              $type_color = '#4A1209';
              break;
            case 'djv_muhurtham':
              $type_label = '⏰ ' . __( 'Shubh Muhurtham', 'djv-theme' );
              $type_color = '#8A6D3B';
              break;
            case 'djv_service':
              $type_label = '⭐ ' . __( 'Vedic Service', 'djv-theme' );
              $type_color = '#D97706';
              break;
            case 'post':
              $type_label = '📰 ' . __( 'Article', 'djv-theme' );
              $type_color = '#2563EB';
              break;
            default:
              $type_label = '📄 ' . __( 'Page', 'djv-theme' );
              $type_color = '#4B5563';
              break;
          }
        ?>
          <article class="search-result-item" style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem; padding: 1.5rem; box-shadow: var(--shadow-sm); transition: transform 0.15s, box-shadow 0.15s;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
              <span style="font-size: 0.75rem; font-weight: 700; color: <?php echo esc_attr( $type_color ); ?>; background: #FFF9F0; border: 1px solid var(--clr-border); padding: 0.2rem 0.6rem; border-radius: 9999px; text-transform: uppercase;">
                <?php echo esc_html( $type_label ); ?>
              </span>
              <span style="font-size: 0.8rem; color: var(--clr-text-muted);">
                <?php echo get_the_date(); ?>
              </span>
            </div>

            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; margin: 0 0 0.5rem 0; line-height: 1.3;">
              <a href="<?php the_permalink(); ?>" style="color: var(--clr-primary); text-decoration: none;">
                <?php the_title(); ?>
              </a>
            </h2>

            <p style="font-size: 0.9rem; color: var(--clr-text-secondary); line-height: 1.6; margin: 0 0 0.75rem 0;">
              <?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?>
            </p>

            <a href="<?php the_permalink(); ?>" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
              <?php esc_html_e( 'View Details', 'djv-theme' ); ?> →
            </a>
          </article>
        <?php endwhile; ?>
      <?php else : ?>
        <div style="text-align: center; padding: 3.5rem 1.5rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 3rem; margin-bottom: 0.75rem;">🕉</div>
          <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
            <?php esc_html_e( 'No matching records found', 'djv-theme' ); ?>
          </h2>
          <p style="color: var(--clr-text-muted); max-width: 500px; margin: 0 auto 1.5rem auto; font-size: 0.95rem;">
            <?php esc_html_e( 'Try searching for common terms like "Diwali", "Satyanarayana", "Gayatri", "Tirupati", or "Vivaha".', 'djv-theme' ); ?>
          </p>
          <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url( home_url( '/?s=Panchangam' ) ); ?>" style="padding: 0.4rem 0.85rem; background: #FFF9F0; border: 1px solid var(--clr-border); border-radius: 9999px; font-size: 0.85rem; color: var(--clr-primary); text-decoration: none;"><?php esc_html_e( 'Panchangam', 'djv-theme' ); ?></a>
            <a href="<?php echo esc_url( home_url( '/?s=Diwali' ) ); ?>" style="padding: 0.4rem 0.85rem; background: #FFF9F0; border: 1px solid var(--clr-border); border-radius: 9999px; font-size: 0.85rem; color: var(--clr-primary); text-decoration: none;"><?php esc_html_e( 'Diwali', 'djv-theme' ); ?></a>
            <a href="<?php echo esc_url( home_url( '/?s=Gayatri' ) ); ?>" style="padding: 0.4rem 0.85rem; background: #FFF9F0; border: 1px solid var(--clr-border); border-radius: 9999px; font-size: 0.85rem; color: var(--clr-primary); text-decoration: none;"><?php esc_html_e( 'Gayatri Mantra', 'djv-theme' ); ?></a>
            <a href="<?php echo esc_url( home_url( '/?s=Tirupati' ) ); ?>" style="padding: 0.4rem 0.85rem; background: #FFF9F0; border: 1px solid var(--clr-border); border-radius: 9999px; font-size: 0.85rem; color: var(--clr-primary); text-decoration: none;"><?php esc_html_e( 'Tirupati Temple', 'djv-theme' ); ?></a>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper" style="margin-top: 3rem; text-align: center;">
      <?php
      the_posts_pagination([
        'prev_text' => '← ' . __( 'Previous', 'djv-theme' ),
        'next_text' => __( 'Next', 'djv-theme' ) . ' →',
      ]);
      ?>
    </div>

  </div>
</div>

<?php
get_footer();
