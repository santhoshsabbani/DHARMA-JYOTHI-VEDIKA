<?php
/**
 * Template Name: Mantras & Slokas Library
 *
 * Fully dynamic page template querying djv_mantra CPT.
 *
 * @package DJV_Theme
 */

get_header();
?>

<section class="page-hero" aria-labelledby="mantras-title" style="background: linear-gradient(135deg, #241914 0%, #3D100A 100%); color: #FFF; padding: 3rem 0 2.5rem 0;">
  <div class="container ph-inner">
    <nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: rgba(255,255,255,0.7); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> <span>›</span> <span aria-current="page" style="color: var(--clr-accent);"><?php esc_html_e( 'Mantras &amp; Slokas', 'djv-theme' ); ?></span>
    </nav>
    <div class="ph-badge" style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(200,148,50,0.2); border: 1px solid var(--clr-secondary); border-radius: 9999px; font-size: 0.8rem; color: var(--clr-accent); margin-bottom: 0.75rem;">
      🕉 Sanskrit · Telugu · Transliteration
    </div>
    <h1 class="ph-title" id="mantras-title" style="font-family: var(--font-heading); font-size: 2.5rem; margin: 0 0 0.5rem 0;"><?php esc_html_e( 'Mantras &amp; Slokas Library', 'djv-theme' ); ?></h1>
    <p class="ph-sub" style="font-family: var(--font-telugu); font-size: 1.15rem; color: var(--clr-accent); margin: 0 0 0.5rem 0;">మంత్రాలు మరియు శ్లోకాల భండారం</p>
    <p class="ph-desc" style="font-size: 0.95rem; color: rgba(255,255,255,0.8); max-width: 650px; margin: 0; line-height: 1.6;">
      <?php esc_html_e( 'Sacred mantras, slokas, and stotras in Sanskrit with Telugu transliteration and meaning. For daily worship, meditation, and devotional chanting.', 'djv-theme' ); ?>
    </p>
  </div>
</section>

<div class="mantras-page-wrapper" style="padding: 2.5rem 0 4.5rem 0; background: var(--clr-bg);">
  <div class="container">

    <!-- Search + Filter Bar -->
    <div class="search-filter-bar" role="search" aria-label="<?php esc_attr_e( 'Filter mantras', 'djv-theme' ); ?>" style="background:#FFF; padding:1.25rem; border-radius:1rem; border:1px solid var(--clr-border); box-shadow:var(--shadow-sm); margin-bottom: 2rem;">
      <div class="search-input-wrap" style="position:relative; margin-bottom: 1rem;">
        <span class="search-icon" aria-hidden="true" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);font-size:1rem;color:var(--clr-text-muted);">🔍</span>
        <input type="search" id="mantra-search" placeholder="<?php esc_attr_e( 'Search mantras by name, deity, or prayer...', 'djv-theme' ); ?>" aria-label="<?php esc_attr_e( 'Search mantras', 'djv-theme' ); ?>" autocomplete="off" style="width:100%;padding:0.75rem 1rem 0.75rem 2.5rem;border-radius:9999px;border:1px solid var(--clr-border);font-family:inherit;font-size:0.95rem;outline:none;" />
      </div>
    </div>

    <!-- Dynamic Mantra Grid -->
    <div class="mantras-grid" id="mantras-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:1.5rem;">
      <?php
      $mantra_query = new WP_Query([
        'post_type'      => 'djv_mantra',
        'posts_per_page' => 24,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
      ]);

      if ( $mantra_query->have_posts() ) :
        while ( $mantra_query->have_posts() ) : $mantra_query->the_post();
          get_template_part( 'template-parts/mantra/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📿</div>
          <h3><?php esc_html_e( 'No mantras found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted);"><?php esc_html_e( 'Please check back shortly or explore our pooja guides.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
(function() {
  const searchInput = document.getElementById('mantra-search');
  const cards = document.querySelectorAll('#mantras-grid .mantra-card');

  if (searchInput && cards.length) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = (!q || text.includes(q)) ? '' : 'none';
      });
    });
  }
})();
</script>

<?php
get_footer();
