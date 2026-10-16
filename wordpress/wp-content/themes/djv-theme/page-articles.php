<?php
/**
 * Template Name: Vedic Articles & Knowledge
 * Description: Dedicated template for Articles archive (/articles/).
 *
 * @package DJV_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$current_lang = djv_get_current_language();
$filter_lang  = isset( $_GET['lang'] ) && in_array( $_GET['lang'], [ 'en', 'te', 'hi' ], true ) ? sanitize_key( $_GET['lang'] ) : '';

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$query_args = [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'paged'          => $paged,
	'posts_per_page' => 12,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( ! empty( $filter_lang ) ) {
	$query_args['meta_query'] = [
		'relation' => 'OR',
		[
			'key'   => 'djv_language',
			'value' => $filter_lang,
		],
		[
			'key'   => '_lang',
			'value' => $filter_lang,
		],
	];
	if ( $filter_lang === 'en' ) {
		$query_args['meta_query'][] = [
			'key'     => 'djv_language',
			'compare' => 'NOT EXISTS',
		];
	}
}

$articles_query = new WP_Query( $query_args );

// Total post count helper for badges
$count_all = wp_count_posts()->publish;
?>

<div class="archive-wrapper" style="padding: calc(var(--header-h, 72px) + 2rem) 0 5rem 0; background: var(--clr-bg, #fdfaf6);">
  <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.25rem;">

    <header class="archive-header" style="margin-bottom: 2.5rem; text-align: center;">
      <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #78716c); margin-bottom: 0.5rem;">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
        <span style="margin: 0 0.4rem;">›</span>
        <span style="color: var(--clr-primary, #7a2419); font-weight: 600;"><?php esc_html_e( 'Articles', 'djv-theme' ); ?></span>
      </nav>
      <h1 style="font-family: var(--font-heading, serif); font-size: 2.5rem; color: var(--clr-primary, #7a2419); margin: 0 0 0.5rem 0; line-height: 1.2;">
        <?php esc_html_e( 'Vedic Articles & Knowledge', 'djv-theme' ); ?>
      </h1>
      <p style="color: var(--clr-text-secondary, #57534e); margin: 0 auto 1.75rem auto; font-size: 1.05rem; max-width: 680px; line-height: 1.6;">
        <?php esc_html_e( 'Authentic Jyotish insights, traditional Muhurtham planning guides, ritual procedures, and Sanatana Dharma teachings across languages.', 'djv-theme' ); ?>
      </p>

      <!-- Multilingual Filter Tabs -->
      <div class="djv-lang-filter-bar" style="display: inline-flex; background: #fff; padding: 0.35rem; border-radius: 9999px; border: 1px solid var(--clr-border, #e7e5e4); box-shadow: 0 2px 6px rgba(0,0,0,0.04); gap: 0.25rem;">
        <a href="<?php echo esc_url( remove_query_arg( 'lang' ) ); ?>" class="djv-filter-btn <?php echo empty( $filter_lang ) ? 'active' : ''; ?>" style="padding: 0.45rem 1.1rem; border-radius: 9999px; text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: all 0.2s ease; <?php echo empty( $filter_lang ) ? 'background: var(--clr-primary, #7a2419); color: #fff;' : 'color: var(--clr-text, #444);'; ?>">
          🌐 <?php esc_html_e( 'All Languages', 'djv-theme' ); ?>
        </a>
        <a href="<?php echo esc_url( add_query_arg( 'lang', 'en' ) ); ?>" class="djv-filter-btn <?php echo $filter_lang === 'en' ? 'active' : ''; ?>" style="padding: 0.45rem 1.1rem; border-radius: 9999px; text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: all 0.2s ease; <?php echo $filter_lang === 'en' ? 'background: var(--clr-primary, #7a2419); color: #fff;' : 'color: var(--clr-text, #444);'; ?>">
          English
        </a>
        <a href="<?php echo esc_url( add_query_arg( 'lang', 'te' ) ); ?>" class="djv-filter-btn <?php echo $filter_lang === 'te' ? 'active' : ''; ?>" style="padding: 0.45rem 1.1rem; border-radius: 9999px; text-decoration: none; font-size: 0.875rem; font-weight: 600; font-family: var(--font-telugu, sans-serif); transition: all 0.2s ease; <?php echo $filter_lang === 'te' ? 'background: var(--clr-primary, #7a2419); color: #fff;' : 'color: var(--clr-text, #444);'; ?>">
          తెలుగు
        </a>
        <a href="<?php echo esc_url( add_query_arg( 'lang', 'hi' ) ); ?>" class="djv-filter-btn <?php echo $filter_lang === 'hi' ? 'active' : ''; ?>" style="padding: 0.45rem 1.1rem; border-radius: 9999px; text-decoration: none; font-size: 0.875rem; font-weight: 600; font-family: var(--font-hindi, sans-serif); transition: all 0.2s ease; <?php echo $filter_lang === 'hi' ? 'background: var(--clr-primary, #7a2419); color: #fff;' : 'color: var(--clr-text, #444);'; ?>">
          हिन्दी
        </a>
      </div>
    </header>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:2rem;">
      <?php if ( $articles_query->have_posts() ) : while ( $articles_query->have_posts() ) : $articles_query->the_post(); 
        $post_id   = get_the_ID();
        $post_lang = get_post_meta( $post_id, 'djv_language', true ) ?: get_post_meta( $post_id, '_lang', true ) ?: 'en';
        
        $lang_label = 'English';
        $badge_bg   = '#eff6ff';
        $badge_clr  = '#1e40af';
        $font_style = '';

        if ( $post_lang === 'te' ) {
            $lang_label = 'తెలుగు';
            $badge_bg   = '#fef3c7';
            $badge_clr  = '#92400e';
            $font_style = 'font-family: var(--font-telugu, sans-serif);';
        } elseif ( $post_lang === 'hi' ) {
            $lang_label = 'हिन्दी';
            $badge_bg   = '#fce7f3';
            $badge_clr  = '#9d174d';
            $font_style = 'font-family: var(--font-hindi, sans-serif);';
        }
      ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:#FFF; border-radius:var(--radius-xl, 1rem); border:1px solid var(--clr-border, #e8d5c4); overflow:hidden; box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04)); display:flex; flex-direction:column; transition:transform 0.2s ease, box-shadow 0.2s ease;">
          <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>" style="display:block; overflow:hidden; aspect-ratio:16/9;">
              <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.3s ease;' ] ); ?>
            </a>
          <?php else : ?>
            <div style="aspect-ratio:16/9; background:linear-gradient(135deg, #7a2419 0%, #b45309 100%); display:flex; align-items:center; justify-content:center; color:#fff;">
              <span style="font-size:3.5rem; opacity:0.85;">🕉️</span>
            </div>
          <?php endif; ?>

          <div style="padding:1.5rem; flex:1; display:flex; flex-direction:column;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.6rem;">
              <div style="font-size:0.75rem; color:var(--clr-accent, #c89432); font-weight:700; text-transform:uppercase; letter-spacing:0.04em;">
                <?php the_category( ', ' ); ?>
              </div>
              <span style="display:inline-block; font-size:0.75rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:9999px; background:<?php echo esc_attr( $badge_bg ); ?>; color:<?php echo esc_attr( $badge_clr ); ?>; <?php echo esc_attr( $font_style ); ?>">
                <?php echo esc_html( $lang_label ); ?>
              </span>
            </div>

            <h2 style="font-family:var(--font-heading, serif); font-size:1.35rem; color:var(--clr-primary, #7a2419); margin:0 0 0.75rem 0; line-height:1.3; <?php echo esc_attr( $font_style ); ?>">
              <a href="<?php the_permalink(); ?>" style="color:inherit; text-decoration:none;"><?php the_title(); ?></a>
            </h2>

            <div style="font-size:0.9rem; color:var(--clr-text-secondary, #666); line-height:1.6; margin-bottom:1.25rem; flex:1; <?php echo esc_attr( $font_style ); ?>">
              <?php echo wp_trim_words( get_the_excerpt(), 22 ); ?>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8125rem; color:var(--clr-text-muted, #888); border-top:1px solid var(--clr-border, #f0f0f0); padding-top:0.75rem;">
              <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
              <a href="<?php the_permalink(); ?>" style="font-weight:600; color:var(--clr-primary, #7a2419); text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem;">
                <?php esc_html_e( 'Read Article →', 'djv-theme' ); ?>
              </a>
            </div>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); else : ?>
        <div style="grid-column:1/-1; text-align:center; padding:4rem 1.5rem; background:#fff; border-radius:1rem; border:1px solid var(--clr-border, #e8d5c4);">
          <div style="font-size:3rem; margin-bottom:1rem;">📜</div>
          <h3 style="font-family:var(--font-heading, serif); color:var(--clr-primary); margin-bottom:0.5rem;">
            <?php esc_html_e( 'No Articles Found', 'djv-theme' ); ?>
          </h3>
          <p style="color:var(--clr-text-muted); font-size:0.95rem; max-width:480px; margin:0 auto 1.5rem auto;">
            <?php esc_html_e( 'No published articles match the selected language filter. Try viewing all languages.', 'djv-theme' ); ?>
          </p>
          <a href="<?php echo esc_url( remove_query_arg( 'lang' ) ); ?>" style="display:inline-block; background:var(--clr-primary, #7a2419); color:#fff; padding:0.6rem 1.25rem; border-radius:0.5rem; text-decoration:none; font-weight:600; font-size:0.9rem;">
            <?php esc_html_e( 'View All Articles', 'djv-theme' ); ?>
          </a>
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
