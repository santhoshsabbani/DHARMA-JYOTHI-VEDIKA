<?php
/**
 * Archive Template for Mantras & Slokas (djv_mantra)
 *
 * Fully dynamic WordPress CPT archive matching DJV visual identity.
 * Features live WordPress search, category filtering, featured mantras,
 * popular chants, and dynamic paginated grid.
 *
 * @package DJV_Theme
 */

get_header();

// Fetch initial server-side query for SSR / SEO
$paged = max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) );

// Fetch Featured Mantras
$featured_query = new WP_Query([
	'post_type'      => 'djv_mantra',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'meta_query'     => [
		[
			'key'   => '_djv_is_featured',
			'value' => '1',
		],
	],
]);

// If meta query returned fewer than 6, fallback to high priority slugs
if ( ! $featured_query->have_posts() || $featured_query->found_posts < 6 ) {
	$featured_slugs = [ 'hanuman-chalisa', 'gayatri-mantra', 'mahamrityunjaya-mantra', 'om-namah-shivaya', 'ganesh-mantra', 'lakshmi-mantra' ];
	$featured_query = new WP_Query([
		'post_type'      => 'djv_mantra',
		'post_status'    => 'publish',
		'post_name__in'  => $featured_slugs,
		'posts_per_page' => 6,
		'orderby'        => 'post_name__in',
	]);
}

// Fetch Popular Mantras (10 items)
$popular_slugs = [
	'hanuman-chalisa',
	'gayatri-mantra',
	'mahamrityunjaya-mantra',
	'om-namah-shivaya',
	'ganesh-mantra',
	'hanuman-mantra',
	'lakshmi-mantra',
	'saraswati-mantra',
	'durga-mantra',
	'hare-krishna-mahamantra',
];
$popular_query = new WP_Query([
	'post_type'      => 'djv_mantra',
	'post_status'    => 'publish',
	'post_name__in'  => $popular_slugs,
	'posts_per_page' => 10,
	'orderby'        => 'post_name__in',
]);

// Fetch All Mantras for paginated list
$all_mantras_query = new WP_Query([
	'post_type'      => 'djv_mantra',
	'post_status'    => 'publish',
	'posts_per_page' => 18,
	'paged'          => $paged,
	'orderby'        => 'title',
	'order'          => 'ASC',
]);
?>

<!-- ════════════════════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════════════════ -->
<section class="mantras-hero" aria-labelledby="mantras-hero-title">
  <div class="container hero-container">
    <nav class="breadcrumb-nav" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
      <span class="sep">›</span>
      <span class="current" aria-current="page"><?php esc_html_e( 'Sacred Mantras', 'djv-theme' ); ?></span>
    </nav>

    <div class="hero-badge">
      <span class="badge-icon">🕉️</span>
      <span><?php esc_html_e( 'Sanskrit • Telugu • Transliteration & Meanings', 'djv-theme' ); ?></span>
    </div>

    <h1 class="hero-title" id="mantras-hero-title">
      <?php esc_html_e( 'Sacred Mantras', 'djv-theme' ); ?>
    </h1>

    <p class="hero-subtitle">
      <?php esc_html_e( 'Powerful Hindu mantras, stotras and sacred chants for daily devotion.', 'djv-theme' ); ?>
    </p>

    <!-- Search & Filter Controls -->
    <div class="mantras-controls-panel" role="search" aria-label="<?php esc_attr_e( 'Search and filter mantras', 'djv-theme' ); ?>">
      <!-- Search Input -->
      <div class="mantras-search-wrap">
        <span class="search-icon" aria-hidden="true">🔍</span>
        <input 
          type="search" 
          id="mantra-search-input" 
          class="mantra-search-input"
          placeholder="<?php esc_attr_e( 'Search Mantras...', 'djv-theme' ); ?>" 
          aria-label="<?php esc_attr_e( 'Search Mantras', 'djv-theme' ); ?>"
          autocomplete="off"
        />
        <button type="button" id="mantra-search-clear" class="search-clear-btn" aria-label="<?php esc_attr_e( 'Clear search', 'djv-theme' ); ?>" style="display:none;">✕</button>
      </div>

      <!-- Category Filter Pills -->
      <div class="mantras-filter-scroll" role="tablist" aria-label="<?php esc_attr_e( 'Filter by Category', 'djv-theme' ); ?>">
        <?php
        $filter_categories = [
	        'all'       => __( 'All', 'djv-theme' ),
	        'shiva'     => __( 'Shiva', 'djv-theme' ),
	        'hanuman'   => __( 'Hanuman', 'djv-theme' ),
	        'ganesha'   => __( 'Ganesha', 'djv-theme' ),
	        'lakshmi'   => __( 'Lakshmi', 'djv-theme' ),
	        'saraswati' => __( 'Saraswati', 'djv-theme' ),
	        'durga'     => __( 'Durga', 'djv-theme' ),
	        'vishnu'    => __( 'Vishnu', 'djv-theme' ),
	        'krishna'   => __( 'Krishna', 'djv-theme' ),
	        'navagraha' => __( 'Navagraha', 'djv-theme' ),
        ];
        foreach ( $filter_categories as $key => $label ) :
        ?>
          <button 
            type="button" 
            class="filter-pill <?php echo $key === 'all' ? 'active' : ''; ?>" 
            data-category="<?php echo esc_attr( $key ); ?>"
            role="tab"
            aria-selected="<?php echo $key === 'all' ? 'true' : 'false'; ?>"
          >
            <?php echo esc_html( $label ); ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     MAIN CONTENT WRAPPER
════════════════════════════════════════════════════════════ -->
<main class="mantras-page-main" id="mantras-main-content">
  <div class="container">

    <!-- Active Filter Banner (Visible when searching / filtering) -->
    <div id="mantra-active-filter-bar" class="active-filter-bar" style="display:none;">
      <div class="filter-status-info">
        <span class="status-icon">🔎</span>
        <span id="filter-status-text"><?php esc_html_e( 'Showing search results', 'djv-theme' ); ?></span>
      </div>
      <button type="button" id="btn-reset-filters" class="btn-reset-filters">
        <?php esc_html_e( 'Clear Filter', 'djv-theme' ); ?> ✕
      </button>
    </div>

    <!-- ── Featured Mantras Section (Shown on default landing state) ── -->
    <section class="mantras-section featured-section" id="featured-mantras-section">
      <div class="section-header">
        <div class="section-title-wrap">
          <span class="section-badge">⭐ <?php esc_html_e( 'High Priority', 'djv-theme' ); ?></span>
          <h2 class="section-title">
            <?php esc_html_e( 'Featured Mantras', 'djv-theme' ); ?>
            <span class="section-title-te">ముఖ్యమైన పవిత్ర మంత్రాలు</span>
          </h2>
        </div>
        <p class="section-desc">
          <?php esc_html_e( 'The most revered foundational invocations of Sanatana Dharma for peace, health, and enlightenment.', 'djv-theme' ); ?>
        </p>
      </div>

      <div class="mantras-grid featured-grid">
        <?php
        if ( $featured_query->have_posts() ) :
	        while ( $featured_query->have_posts() ) : $featured_query->the_post();
		        get_template_part( 'template-parts/mantra/card' );
	        endwhile;
	        wp_reset_postdata();
        endif;
        ?>
      </div>
    </section>

    <!-- ── Popular Mantras Section (Shown on default landing state) ── -->
    <section class="mantras-section popular-section" id="popular-mantras-section">
      <div class="section-header">
        <div class="section-title-wrap">
          <span class="section-badge">🔥 <?php esc_html_e( 'Devotee Favorites', 'djv-theme' ); ?></span>
          <h2 class="section-title">
            <?php esc_html_e( 'Popular Mantras', 'djv-theme' ); ?>
            <span class="section-title-te">అత్యధికంగా జపించే మంత్రాలు</span>
          </h2>
        </div>
        <p class="section-desc">
          <?php esc_html_e( 'Widely chanted daily stotras and powerful mantras across India and the global Vedic community.', 'djv-theme' ); ?>
        </p>
      </div>

      <div class="mantras-grid popular-grid">
        <?php
        if ( $popular_query->have_posts() ) :
	        while ( $popular_query->have_posts() ) : $popular_query->the_post();
		        get_template_part( 'template-parts/mantra/card' );
	        endwhile;
	        wp_reset_postdata();
        endif;
        ?>
      </div>
    </section>

    <!-- ── All Mantras Dynamic Grid (Source of Truth) ── -->
    <section class="mantras-section all-mantras-section" id="all-mantras-section">
      <div class="section-header all-mantras-header">
        <div class="section-title-wrap">
          <span class="section-badge">📿 <?php esc_html_e( 'Complete Library', 'djv-theme' ); ?></span>
          <h2 class="section-title" id="all-mantras-heading">
            <?php esc_html_e( 'All Sacred Mantras', 'djv-theme' ); ?>
          </h2>
        </div>
        <div class="results-counter" id="mantras-counter" aria-live="polite">
          <?php printf( esc_html__( 'Total %d Mantras Available', 'djv-theme' ), (int) $all_mantras_query->found_posts ); ?>
        </div>
      </div>

      <!-- Live Loading State -->
      <div id="mantras-loading" class="mantras-loading-indicator" style="display:none;" aria-hidden="true">
        <div class="spinner"></div>
        <span><?php esc_html_e( 'Loading sacred mantras from WordPress...', 'djv-theme' ); ?></span>
      </div>

      <!-- Dynamic Mantra Cards Grid -->
      <div class="mantras-grid main-grid" id="mantras-main-grid">
        <?php
        if ( $all_mantras_query->have_posts() ) :
	        while ( $all_mantras_query->have_posts() ) : $all_mantras_query->the_post();
		        get_template_part( 'template-parts/mantra/card' );
	        endwhile;
        else :
        ?>
          <div class="empty-mantras-state">
            <div class="empty-icon">📿</div>
            <h3><?php esc_html_e( 'No mantras found matching your criteria', 'djv-theme' ); ?></h3>
            <p><?php esc_html_e( 'Try clearing your search query or selecting a different category.', 'djv-theme' ); ?></p>
            <button type="button" class="btn btn-primary" onclick="window.location.reload();">
              <?php esc_html_e( 'View All Mantras', 'djv-theme' ); ?>
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pagination / Dynamic Controls -->
      <div class="mantras-pagination-area" id="mantras-pagination-area">
        <?php
        echo paginate_links([
	        'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
	        'format'    => '?paged=%#%',
	        'current'   => $paged,
	        'total'     => $all_mantras_query->max_num_pages,
	        'prev_text' => '← ' . __( 'Previous', 'djv-theme' ),
	        'next_text' => __( 'Next', 'djv-theme' ) . ' →',
	        'type'      => 'list',
        ]);
        wp_reset_postdata();
        ?>
      </div>
    </section>

  </div>
</main>

<!-- ════════════════════════════════════════════════════════════
     INLINE STYLES FOR MANTRAS PAGE (DJV VISUAL IDENTITY)
════════════════════════════════════════════════════════════ -->
<style>
/* Mantras Page Hero */
.mantras-hero {
  background: linear-gradient(135deg, #241914 0%, #3D100A 50%, #1A0D08 100%);
  color: #FFF;
  padding: 3.5rem 0 3rem 0;
  position: relative;
  overflow: hidden;
  border-bottom: 2px solid var(--clr-secondary, #C89432);
}
.mantras-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -20%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(200,148,50,0.12) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.hero-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.25rem;
  position: relative;
  z-index: 1;
}
.breadcrumb-nav {
  font-size: 0.8125rem;
  color: rgba(255,255,255,0.7);
  margin-bottom: 0.85rem;
}
.breadcrumb-nav a {
  color: inherit;
  text-decoration: none;
  transition: color 0.2s;
}
.breadcrumb-nav a:hover {
  color: var(--clr-accent, #E7B75A);
}
.breadcrumb-nav .sep {
  margin: 0 0.4rem;
  opacity: 0.6;
}
.breadcrumb-nav .current {
  color: var(--clr-accent, #E7B75A);
  font-weight: 600;
}
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(200,148,50,0.18);
  border: 1px solid rgba(231,183,90,0.4);
  padding: 0.35rem 0.95rem;
  border-radius: 9999px;
  font-size: 0.8125rem;
  color: var(--clr-accent, #E7B75A);
  margin-bottom: 0.85rem;
  font-weight: 500;
}
.hero-title {
  font-family: var(--font-heading, 'Playfair Display', Georgia, serif);
  font-size: clamp(2.2rem, 5vw, 3.25rem);
  font-weight: 700;
  color: #FFF;
  line-height: 1.2;
  margin: 0 0 0.5rem 0;
}
.hero-title-telugu {
  display: block;
  font-family: var(--font-telugu, 'Noto Sans Telugu', sans-serif);
  font-size: clamp(1.35rem, 3.5vw, 1.85rem);
  color: var(--clr-accent, #E7B75A);
  font-weight: 600;
  margin-top: 0.25rem;
}
.hero-subtitle {
  font-size: 1.05rem;
  color: rgba(255,255,255,0.85);
  max-width: 680px;
  line-height: 1.6;
  margin: 0 0 2rem 0;
}

/* Controls Panel: Search & Filter Pills */
.mantras-controls-panel {
  background: rgba(255,255,255,0.06);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 1.25rem;
  padding: 1.25rem;
  box-shadow: 0 8px 30px rgba(0,0,0,0.25);
}
.mantras-search-wrap {
  position: relative;
  margin-bottom: 1rem;
}
.search-icon {
  position: absolute;
  left: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.15rem;
  color: var(--clr-text-muted, #A08070);
  pointer-events: none;
}
.mantra-search-input {
  width: 100%;
  padding: 0.9rem 2.75rem 0.9rem 3.1rem;
  border-radius: 9999px;
  border: 1.5px solid rgba(231,183,90,0.3);
  background: #FFF;
  color: var(--clr-dark, #241914);
  font-family: inherit;
  font-size: 1rem;
  outline: none;
  box-sizing: border-box;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.2s ease;
}
.mantra-search-input:focus {
  border-color: var(--clr-accent, #E7B75A);
  box-shadow: 0 0 0 3px rgba(231,183,90,0.25);
}
.search-clear-btn {
  position: absolute;
  right: 1.15rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 1.1rem;
  color: #777;
  cursor: pointer;
  padding: 0.25rem;
}
.search-clear-btn:hover {
  color: var(--clr-primary, #7A2419);
}

/* Filter Pills */
.mantras-filter-scroll {
  display: flex;
  gap: 0.6rem;
  overflow-x: auto;
  padding-bottom: 0.4rem;
  scrollbar-width: thin;
}
.mantras-filter-scroll::-webkit-scrollbar {
  height: 4px;
}
.mantras-filter-scroll::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.2);
  border-radius: 9999px;
}
.filter-pill {
  flex-shrink: 0;
  padding: 0.55rem 1.15rem;
  border-radius: 9999px;
  border: 1px solid rgba(255,255,255,0.2);
  background: rgba(255,255,255,0.08);
  color: #FFF;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.filter-pill:hover {
  background: rgba(231,183,90,0.2);
  border-color: var(--clr-accent, #E7B75A);
  color: var(--clr-accent, #E7B75A);
}
.filter-pill.active {
  background: var(--clr-accent, #E7B75A);
  color: #241914;
  border-color: var(--clr-accent, #E7B75A);
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(231,183,90,0.4);
}

/* Active Filter Banner */
.active-filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #FFF9F0;
  border: 1.5px solid var(--clr-secondary, #C89432);
  border-radius: 0.85rem;
  padding: 0.85rem 1.25rem;
  margin-bottom: 2rem;
}
.filter-status-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--clr-primary, #7A2419);
  font-size: 0.95rem;
}
.btn-reset-filters {
  background: var(--clr-primary, #7A2419);
  color: #FFF;
  border: none;
  padding: 0.4rem 0.85rem;
  border-radius: 9999px;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-reset-filters:hover {
  background: var(--clr-primary-dark, #5C1A11);
}

/* Main Content Area */
.mantras-page-main {
  background: var(--clr-bg, #FFF9F0);
  padding: 3rem 0 5rem 0;
  min-height: 60vh;
}
.mantras-section {
  margin-bottom: 4rem;
}
.section-header {
  margin-bottom: 2rem;
}
.all-mantras-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  flex-wrap: wrap;
  gap: 1rem;
  border-bottom: 2px solid var(--clr-border, #E8D5C4);
  padding-bottom: 1rem;
}
.section-badge {
  display: inline-block;
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--clr-primary, #7A2419);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.35rem;
}
.section-title {
  font-family: var(--font-heading, 'Playfair Display', Georgia, serif);
  font-size: 2rem;
  color: var(--clr-primary, #7A2419);
  margin: 0;
  line-height: 1.25;
}
.section-title-te {
  display: inline-block;
  margin-left: 0.5rem;
  font-family: var(--font-telugu, 'Noto Sans Telugu', sans-serif);
  font-size: 1.35rem;
  color: var(--clr-secondary, #C89432);
  font-weight: 600;
}
.section-desc {
  font-size: 0.95rem;
  color: var(--clr-text-secondary, #6B4C3B);
  margin: 0.4rem 0 0 0;
}
.results-counter {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--clr-text-muted, #A08070);
  background: #FFF;
  padding: 0.35rem 0.85rem;
  border-radius: 9999px;
  border: 1px solid var(--clr-border, #E8D5C4);
}

/* Responsive Grid System */
.mantras-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}
@media (min-width: 640px) {
  .mantras-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (min-width: 1024px) {
  .mantras-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .featured-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .popular-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
@media (min-width: 1280px) {
  .main-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* Card Styling (DJV Standard) */
.mantra-card {
  background: #FFF;
  border: 1.5px solid var(--clr-border, #E8D5C4);
  border-radius: 1.15rem;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 14px rgba(44,26,20,0.05);
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.mantra-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(122,36,25,0.12);
  border-color: var(--clr-secondary, #C89432);
}
.mantra-card-media {
  background: linear-gradient(135deg, #FFFDF9 0%, #FFF4E5 100%);
  border-bottom: 1px solid var(--clr-border, #E8D5C4);
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.mantra-card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.mantra-icon-placeholder {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #FFF;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 10px rgba(200,148,50,0.2);
  border: 1.5px solid var(--clr-secondary, #C89432);
}
.mantra-deity-symbol {
  font-size: 2.2rem;
  line-height: 1;
}
.mantra-card-body {
  padding: 1.35rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}
.mantra-category-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--clr-primary, #7A2419);
  margin-bottom: 0.5rem;
}
.badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--clr-secondary, #C89432);
}
.mantra-title {
  font-family: var(--font-heading, 'Playfair Display', Georgia, serif);
  font-size: 1.35rem;
  line-height: 1.3;
  margin: 0 0 0.35rem 0;
}
.mantra-title-link {
  color: var(--clr-primary, #7A2419);
  text-decoration: none;
  transition: color 0.2s;
}
.mantra-title-link:hover {
  color: var(--clr-secondary-dark, #9A7020);
}
.mantra-telugu-title {
  font-family: var(--font-telugu, 'Noto Sans Telugu', sans-serif);
  font-size: 1.15rem;
  font-weight: 600;
  color: var(--clr-secondary-dark, #9A7020);
  margin-bottom: 0.75rem;
  line-height: 1.4;
}
.mantra-excerpt {
  font-size: 0.88rem;
  line-height: 1.6;
  color: var(--clr-text-secondary, #6B4C3B);
  margin: 0 0 1.25rem 0;
  flex: 1;
}
.mantra-card-footer {
  margin-top: auto;
  border-top: 1px dashed var(--clr-border, #E8D5C4);
  padding-top: 1rem;
}
.btn-read-mantra {
  display: inline-flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  background: #FFF9F0;
  color: var(--clr-primary, #7A2419);
  border: 1.5px solid var(--clr-primary, #7A2419);
  border-radius: 0.6rem;
  padding: 0.6rem 1rem;
  font-size: 0.88rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
}
.btn-read-mantra .arrow {
  transition: transform 0.2s ease;
}
.btn-read-mantra:hover {
  background: var(--clr-primary, #7A2419);
  color: #FFF;
}
.btn-read-mantra:hover .arrow {
  transform: translateX(4px);
}

/* Loading & Empty States */
.mantras-loading-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.85rem;
  padding: 3rem 1rem;
  color: var(--clr-primary, #7A2419);
  font-weight: 600;
}
.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(122,36,25,0.2);
  border-top-color: var(--clr-primary, #7A2419);
  border-radius: 50%;
  animation: djvSpin 0.7s linear infinite;
}
@keyframes djvSpin {
  to { transform: rotate(360deg); }
}
.empty-mantras-state {
  grid-column: 1 / -1;
  text-align: center;
  background: #FFF;
  border: 1.5px dashed var(--clr-border, #E8D5C4);
  border-radius: 1.25rem;
  padding: 3.5rem 1.5rem;
}
.empty-mantras-state .empty-icon {
  font-size: 3rem;
  margin-bottom: 0.75rem;
}
.empty-mantras-state h3 {
  font-family: var(--font-heading, serif);
  font-size: 1.5rem;
  color: var(--clr-primary, #7A2419);
  margin: 0 0 0.5rem 0;
}
.empty-mantras-state p {
  color: var(--clr-text-muted, #A08070);
  max-width: 450px;
  margin: 0 auto 1.5rem auto;
}

/* Pagination */
.mantras-pagination-area {
  margin-top: 3.5rem;
  text-align: center;
}
.mantras-pagination-area ul.page-numbers {
  display: inline-flex;
  gap: 0.4rem;
  list-style: none;
  padding: 0;
  margin: 0;
}
.mantras-pagination-area .page-numbers {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  height: 40px;
  padding: 0 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid var(--clr-border, #E8D5C4);
  background: #FFF;
  color: var(--clr-primary, #7A2419);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
}
.mantras-pagination-area .page-numbers.current,
.mantras-pagination-area .page-numbers:hover {
  background: var(--clr-primary, #7A2419);
  color: #FFF;
  border-color: var(--clr-primary, #7A2419);
}
</style>

<!-- ════════════════════════════════════════════════════════════
     DYNAMIC REST API JAVASCRIPT LOGIC
════════════════════════════════════════════════════════════ -->
<script>
(function() {
  const restUrl = '<?php echo esc_url_raw( rest_url( 'djv/v1/mantras' ) ); ?>';
  const searchInput = document.getElementById('mantra-search-input');
  const clearBtn = document.getElementById('mantra-search-clear');
  const filterPills = document.querySelectorAll('.filter-pill');
  const mainGrid = document.getElementById('mantras-main-grid');
  const counter = document.getElementById('mantras-counter');
  const loadingIndicator = document.getElementById('mantras-loading');
  const activeFilterBar = document.getElementById('mantra-active-filter-bar');
  const filterStatusText = document.getElementById('filter-status-text');
  const resetBtn = document.getElementById('btn-reset-filters');
  const featuredSection = document.getElementById('featured-mantras-section');
  const popularSection = document.getElementById('popular-mantras-section');
  const paginationArea = document.getElementById('mantras-pagination-area');
  const allMantrasHeading = document.getElementById('all-mantras-heading');

  let activeCategory = 'all';
  let searchQuery = '';
  let currentPage = 1;
  let debounceTimer = null;

  function getDeityIcon(deity, title) {
    const text = ((deity || '') + ' ' + (title || '')).toLowerCase();
    if (text.includes('hanuman') || text.includes('anjaneya')) return '🚩';
    if (text.includes('shiva') || text.includes('mrityunjaya')) return '🔱';
    if (text.includes('ganesh') || text.includes('ganapati') || text.includes('vakratunda')) return '🐘';
    if (text.includes('lakshmi') || text.includes('kuber') || text.includes('shreem')) return '🪷';
    if (text.includes('saraswati') || text.includes('vidya')) return '🪕';
    if (text.includes('durga') || text.includes('devi') || text.includes('chandi')) return '🦁';
    if (text.includes('krishna')) return '🦚';
    if (text.includes('vishnu') || text.includes('narayana')) return '🐚';
    if (text.includes('surya')) return '☀️';
    if (text.includes('shani') || text.includes('navagraha') || text.includes('chandra') || text.includes('rahu')) return '🪐';
    return '🕉️';
  }

  function renderCards(mantras) {
    if (!mantras || !mantras.length) {
      mainGrid.innerHTML = `
        <div class="empty-mantras-state">
          <div class="empty-icon">📿</div>
          <h3>${<?php echo wp_json_encode( __( 'No mantras found matching your criteria', 'djv-theme' ) ); ?>}</h3>
          <p>${<?php echo wp_json_encode( __( 'Try a different deity or clear the search keywords.', 'djv-theme' ) ); ?>}</p>
          <button type="button" class="btn-reset-filters" id="empty-state-reset">
            ${<?php echo wp_json_encode( __( 'View All Mantras', 'djv-theme' ) ); ?>}
          </button>
        </div>
      `;
      const btn = document.getElementById('empty-state-reset');
      if (btn) btn.addEventListener('click', resetAllFilters);
      return;
    }

    const html = mantras.map(m => {
      const icon = getDeityIcon(m.deity, m.title);
      const catLabel = m.categories && m.categories.length 
        ? m.categories.slice(0, 2).join(' • ') 
        : (m.deity || 'Sacred Chant');
      const excerpt = m.excerpt || m.meaning || '';
      const cleanExcerpt = excerpt.replace(/<[^>]*>?/gm, '').split(' ').slice(0, 16).join(' ') + '...';
      const teluguHtml = m.telugu_title ? `<div class="mantra-telugu-title" lang="te">${escapeHtml(m.telugu_title)}</div>` : '';

      return `
        <article class="mantra-card" id="mantra-card-${m.id}">
          <div class="mantra-card-media">
            ${m.featured_image ? `<img src="${m.featured_image}" alt="${escapeHtml(m.title)}" class="mantra-card-img" loading="lazy" />` : `
              <div class="mantra-icon-placeholder" aria-hidden="true">
                <span class="mantra-deity-symbol">${icon}</span>
              </div>
            `}
          </div>
          <div class="mantra-card-body">
            <div class="mantra-category-tag">
              <span class="badge-dot" aria-hidden="true"></span>
              <span class="badge-text">${escapeHtml(catLabel)}</span>
            </div>
            <h3 class="mantra-title">
              <a href="${m.link}" class="mantra-title-link">${escapeHtml(m.title)}</a>
            </h3>
            ${teluguHtml}
            <p class="mantra-excerpt">${escapeHtml(cleanExcerpt)}</p>
            <div class="mantra-card-footer">
              <a href="${m.link}" class="btn-read-mantra" aria-label="Read Mantra: ${escapeHtml(m.title)}">
                <span>${<?php echo wp_json_encode( __( 'Read Mantra', 'djv-theme' ) ); ?>}</span>
                <span class="arrow" aria-hidden="true">→</span>
              </a>
            </div>
          </div>
        </article>
      `;
    }).join('');

    mainGrid.innerHTML = html;
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  async function fetchMantras(page = 1) {
    const isFiltered = (activeCategory !== 'all' || searchQuery.length > 0);

    // Toggle featured/popular sections visibility when actively filtering
    if (featuredSection) featuredSection.style.display = isFiltered ? 'none' : '';
    if (popularSection) popularSection.style.display = isFiltered ? 'none' : '';
    if (activeFilterBar) activeFilterBar.style.display = isFiltered ? 'flex' : 'none';

    if (isFiltered) {
      const parts = [];
      if (searchQuery) parts.push(`"${searchQuery}"`);
      if (activeCategory !== 'all') parts.push(`Category: ${activeCategory.toUpperCase()}`);
      filterStatusText.textContent = `Showing results for ${parts.join(' & ')}`;
    }

    if (loadingIndicator) loadingIndicator.style.display = 'flex';
    mainGrid.style.opacity = '0.5';

    try {
      const params = new URLSearchParams();
      params.set('per_page', '18');
      params.set('page', page);
      if (activeCategory !== 'all') params.set('category', activeCategory);
      if (searchQuery) params.set('search', searchQuery);

      const res = await fetch(`${restUrl}?${params.toString()}`);
      if (!res.ok) throw new Error('Failed to fetch');
      const json = await res.json();

      const mantras = json.data || [];
      const total = json.meta?.total || mantras.length;
      const totalPages = json.meta?.total_pages || 1;

      renderCards(mantras);

      if (counter) {
        counter.textContent = isFiltered 
          ? `Found ${total} Mantras` 
          : `Total ${total} Mantras Available`;
      }

      renderPagination(page, totalPages);

    } catch (err) {
      console.error('DJV Mantras Error:', err);
      mainGrid.innerHTML = `
        <div class="empty-mantras-state">
          <p style="color:var(--clr-error);">Could not load mantras at this moment. Please refresh the page.</p>
        </div>
      `;
    } finally {
      if (loadingIndicator) loadingIndicator.style.display = 'none';
      mainGrid.style.opacity = '1';
    }
  }

  function renderPagination(current, totalPages) {
    if (!paginationArea) return;
    if (totalPages <= 1) {
      paginationArea.innerHTML = '';
      return;
    }

    let html = '<ul class="page-numbers">';
    if (current > 1) {
      html += `<li><a href="#" class="page-numbers prev" data-page="${current - 1}">← Previous</a></li>`;
    }
    for (let p = 1; p <= totalPages; p++) {
      if (p === current) {
        html += `<li><span class="page-numbers current">${p}</span></li>`;
      } else if (p === 1 || p === totalPages || Math.abs(p - current) <= 2) {
        html += `<li><a href="#" class="page-numbers" data-page="${p}">${p}</a></li>`;
      } else if (p === current - 3 || p === current + 3) {
        html += `<li><span class="page-numbers dots">…</span></li>`;
      }
    }
    if (current < totalPages) {
      html += `<li><a href="#" class="page-numbers next" data-page="${current + 1}">Next →</a></li>`;
    }
    html += '</ul>';

    paginationArea.innerHTML = html;

    paginationArea.querySelectorAll('a[data-page]').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const p = parseInt(link.getAttribute('data-page'), 10);
        currentPage = p;
        fetchMantras(p);
        allMantrasHeading.scrollIntoView({ behavior: 'smooth' });
      });
    });
  }

  function resetAllFilters() {
    activeCategory = 'all';
    searchQuery = '';
    if (searchInput) searchInput.value = '';
    if (clearBtn) clearBtn.style.display = 'none';

    filterPills.forEach(pill => {
      const cat = pill.getAttribute('data-category');
      const isActive = cat === 'all';
      pill.classList.toggle('active', isActive);
      pill.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    fetchMantras(1);
  }

  // Event Listeners
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      searchQuery = this.value.trim();
      if (clearBtn) clearBtn.style.display = searchQuery ? 'block' : 'none';

      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        currentPage = 1;
        fetchMantras(1);
      }, 350);
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      searchInput.value = '';
      clearBtn.style.display = 'none';
      searchQuery = '';
      fetchMantras(1);
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener('click', resetAllFilters);
  }

  filterPills.forEach(pill => {
    pill.addEventListener('click', function() {
      const cat = this.getAttribute('data-category');
      if (activeCategory === cat) return;

      activeCategory = cat;
      filterPills.forEach(p => {
        const active = p === pill;
        p.classList.toggle('active', active);
        p.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      currentPage = 1;
      fetchMantras(1);
    });
  });

})();
</script>

<?php
get_footer();
