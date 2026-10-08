<?php
/**
 * Template Name: Sacred Hindu Temples Directory
 *
 * Comprehensive, location-aware, SEO-friendly India-wide Hindu Temple Directory.
 *
 * @package DJV_Theme
 */

get_header();

$lang = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';

// Query all published deities, states, and categories for dynamic filter dropdowns
$deities = get_terms([
	'taxonomy'   => 'djv_deity',
	'hide_empty' => true,
]);

$categories = get_terms([
	'taxonomy'   => 'djv_temple_category',
	'hide_empty' => true,
]);

$states = get_terms([
	'taxonomy'   => 'djv_state',
	'hide_empty' => true,
]);
if ( empty( $states ) || is_wp_error( $states ) ) {
	$states = get_terms([
		'taxonomy'   => 'djv_region',
		'hide_empty' => true,
	]);
}

$paged = max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) );

$temple_args = [
	'post_type'      => 'djv_temple',
	'posts_per_page' => 20,
	'paged'          => $paged,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
];

$temple_query = new WP_Query( $temple_args );
?>

<!-- Hero Section -->
<section class="page-hero" aria-labelledby="temples-title" style="background: linear-gradient(135deg, #241914 0%, #3D100A 100%); color: #FFF; padding: 3.5rem 0 3rem 0; position: relative;">
  <div class="container ph-inner">
    <nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: rgba(255,255,255,0.7); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
      <span> › </span>
      <span aria-current="page" style="color: var(--clr-accent, #C89432);"><?php esc_html_e( 'Sacred Temples', 'djv-theme' ); ?></span>
    </nav>

    <div class="ph-badge" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.85rem; background: rgba(200,148,50,0.2); border: 1px solid var(--clr-secondary, #C89432); border-radius: 9999px; font-size: 0.8rem; color: var(--clr-accent, #C89432); margin-bottom: 0.85rem;">
      🛕 <?php esc_html_e( 'India-Wide Hindu Temple Directory', 'djv-theme' ); ?>
    </div>

    <h1 class="ph-title" id="temples-title" style="font-family: var(--font-heading, Georgia, serif); font-size: 2.6rem; margin: 0 0 0.5rem 0; line-height: 1.2;">
      <?php esc_html_e( 'Explore Sacred Temples Across India', 'djv-theme' ); ?>
    </h1>

    <?php if ( $lang === 'te' ) : ?>
      <p class="ph-sub" style="font-family: var(--font-telugu, inherit); font-size: 1.2rem; color: var(--clr-accent, #C89432); margin: 0 0 0.75rem 0;">
        భారతదేశ పవిత్ర క్షేత్రాలు, జ్యోతిర్లింగాలు, శక్తిపీఠాలు &amp; దివ్యదేశాలు
      </p>
    <?php elseif ( $lang === 'hi' ) : ?>
      <p class="ph-sub" style="font-size: 1.2rem; color: var(--clr-accent, #C89432); margin: 0 0 0.75rem 0;">
        भारत के पवित्र तीर्थ, 12 ज्योतिर्लिंग, 51 शक्तिपीठ एवं दिव्य देशम्
      </p>
    <?php else : ?>
      <p class="ph-sub" style="font-size: 1.15rem; color: var(--clr-accent, #C89432); margin: 0 0 0.75rem 0;">
        12 Jyotirlingas · 51 Shakti Peethas · Char Dham · Divya Desams · Pancha Bhoota
      </p>
    <?php endif; ?>

    <p class="ph-desc" style="font-size: 0.95rem; color: rgba(255,255,255,0.85); max-width: 780px; margin: 0; line-height: 1.6;">
      <?php esc_html_e( "Explore India's holiest kshetras, Jyotirlingas, Shakti Peethas, and sacred Divya Desams with verified darshan timings, sthala purana, and pilgrimage travel guidance.", 'djv-theme' ); ?>
    </p>

    <!-- Location Status Bar in Hero -->
    <div style="margin-top: 1.5rem; display: inline-flex; align-items: center; gap: 0.75rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.15); padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem;">
      <span>📍 <?php esc_html_e( 'Active Location:', 'djv-theme' ); ?> <strong id="temple-hero-loc-name" style="color: var(--clr-accent, #C89432);">Hyderabad</strong></span>
      <span style="opacity: 0.5;">|</span>
      <button type="button" id="btn-near-me" style="background: var(--clr-accent, #C89432); color: #241914; border: none; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
        🧭 <?php esc_html_e( 'Temples Near Me', 'djv-theme' ); ?>
      </button>
    </div>
  </div>
</section>

<!-- Main Page Content -->
<div class="temples-page-wrapper" style="padding: 2.5rem 0 5rem 0; background: var(--clr-bg, #FAF8F5);">
  <div class="container">

    <!-- Search & Filter Controls -->
    <div class="temple-filters-bar" style="background: #FFF; padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--clr-border, #E2E8F0); box-shadow: var(--shadow-sm, 0 2px 4px rgba(0,0,0,0.05)); margin-bottom: 2.5rem;">
      <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; align-items: center;">

        <!-- Search Input -->
        <div style="position: relative;">
          <span aria-hidden="true" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1rem; color: var(--clr-text-muted, #94A3B8);">🔍</span>
          <input type="search" id="temple-search"
                 placeholder="<?php esc_attr_e( 'Search temples by name, state, or deity...', 'djv-theme' ); ?>"
                 aria-label="<?php esc_attr_e( 'Search temples', 'djv-theme' ); ?>"
                 autocomplete="off"
                 style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.6rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); font-family: inherit; font-size: 0.95rem; outline: none;" />
        </div>

        <!-- Filter by State -->
        <div>
          <select id="filter-state" aria-label="<?php esc_attr_e( 'Filter by State', 'djv-theme' ); ?>" style="width: 100%; padding: 0.75rem 0.85rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); font-family: inherit; font-size: 0.9rem; background: #FFF; outline: none;">
            <option value=""><?php esc_html_e( 'All States', 'djv-theme' ); ?></option>
            <?php if ( ! empty( $states ) && ! is_wp_error( $states ) ) : ?>
              <?php foreach ( $states as $st ) : ?>
                <option value="<?php echo esc_attr( strtolower( $st->name ) ); ?>"><?php echo esc_html( $st->name ); ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Filter by Deity -->
        <div>
          <select id="filter-deity" aria-label="<?php esc_attr_e( 'Filter by Deity', 'djv-theme' ); ?>" style="width: 100%; padding: 0.75rem 0.85rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); font-family: inherit; font-size: 0.9rem; background: #FFF; outline: none;">
            <option value=""><?php esc_html_e( 'All Deities', 'djv-theme' ); ?></option>
            <?php if ( ! empty( $deities ) && ! is_wp_error( $deities ) ) : ?>
              <?php foreach ( $deities as $d ) : ?>
                <option value="<?php echo esc_attr( strtolower( $d->name ) ); ?>"><?php echo esc_html( $d->name ); ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Filter by Category -->
        <div>
          <select id="filter-category" aria-label="<?php esc_attr_e( 'Filter by Category', 'djv-theme' ); ?>" style="width: 100%; padding: 0.75rem 0.85rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); font-family: inherit; font-size: 0.9rem; background: #FFF; outline: none;">
            <option value=""><?php esc_html_e( 'All Categories', 'djv-theme' ); ?></option>
            <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
              <?php foreach ( $categories as $c ) : ?>
                <option value="<?php echo esc_attr( strtolower( $c->name ) ); ?>"><?php echo esc_html( $c->name ); ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Reset Button -->
        <div>
          <button type="button" id="btn-reset-filters" style="padding: 0.75rem 1.15rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); background: #F8FAFC; color: #475569; font-weight: 600; cursor: pointer; white-space: nowrap;">
            ↺ <?php esc_html_e( 'Reset', 'djv-theme' ); ?>
          </button>
        </div>

      </div>

      <!-- Live Search & Results Counter -->
      <div id="filter-status-msg" style="margin-top: 1rem; font-size: 0.85rem; color: var(--clr-text-muted, #64748B); display: flex; align-items: center; justify-content: space-between;">
        <span id="results-count-text"><?php echo sprintf( esc_html__( 'Showing %d sacred temples across India', 'djv-theme' ), $temple_query->found_posts ); ?></span>
        <span id="active-sort-text" style="font-weight: 600; color: #0284C7;"></span>
      </div>
    </div>

    <!-- Temples Grid -->
    <div class="temples-grid" id="temples-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.75rem;">
      <?php if ( $temple_query->have_posts() ) : ?>
        <?php while ( $temple_query->have_posts() ) : $temple_query->the_post(); ?>
          <?php get_template_part( 'template-parts/temple/card' ); ?>
        <?php endwhile; wp_reset_postdata(); ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E2E8F0);">
          <div style="font-size: 3rem; margin-bottom: 0.75rem;">🛕</div>
          <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;"><?php esc_html_e( 'No temples found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted, #64748B); margin: 0;"><?php esc_html_e( 'Please check back shortly or explore another state.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- No Match Dynamic Message -->
    <div id="no-match-box" style="display: none; text-align: center; padding: 4rem 2rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E2E8F0); margin-top: 1.5rem;">
      <div style="font-size: 3rem; margin-bottom: 0.75rem;">🔍</div>
      <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;"><?php esc_html_e( 'No temples found matching your criteria.', 'djv-theme' ); ?></h3>
      <p style="color: var(--clr-text-muted, #64748B); margin: 0 0 1.25rem 0;"><?php esc_html_e( 'Try clearing the search text or changing the filters.', 'djv-theme' ); ?></p>
      <button type="button" id="btn-no-match-reset" style="padding: 0.5rem 1.25rem; background: var(--clr-primary, #7A2419); color: #FFF; border: none; border-radius: 9999px; font-weight: 600; cursor: pointer;">
        ↺ <?php esc_html_e( 'Reset Filters', 'djv-theme' ); ?>
      </button>
    </div>

    <!-- Pagination -->
    <?php if ( $temple_query->max_num_pages > 1 ) : ?>
      <div class="pagination-wrapper" style="margin-top: 3.5rem; text-align: center;">
        <?php
        echo paginate_links([
          'total'     => $temple_query->max_num_pages,
          'current'   => $paged,
          'prev_text' => '← ' . __( 'Previous', 'djv-theme' ),
          'next_text' => __( 'Next', 'djv-theme' ) . ' →',
        ]);
        ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<!-- Dynamic Location, Distance & Filtering JavaScript -->
<script>
(function() {
  const cards = Array.from(document.querySelectorAll('#temples-grid .temple-card'));
  const searchInput = document.getElementById('temple-search');
  const stateSelect = document.getElementById('filter-state');
  const deitySelect = document.getElementById('filter-deity');
  const catSelect = document.getElementById('filter-category');
  const resetBtn = document.getElementById('btn-reset-filters');
  const noMatchResetBtn = document.getElementById('btn-no-match-reset');
  const nearMeBtn = document.getElementById('btn-near-me');
  const noMatchBox = document.getElementById('no-match-box');
  const grid = document.getElementById('temples-grid');
  const heroLocText = document.getElementById('temple-hero-loc-name');
  const activeSortText = document.getElementById('active-sort-text');

  // Math: Haversine distance in km
  function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return Math.round(R * c);
  }

  // Math: 8-point compass bearing
  function calculateBearing(lat1, lon1, lat2, lon2) {
    const y = Math.sin((lon2 - lon1) * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180);
    const x = Math.cos(lat1 * Math.PI / 180) * Math.sin(lat2 * Math.PI / 180) -
              Math.sin(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.cos((lon2 - lon1) * Math.PI / 180);
    let brng = Math.atan2(y, x) * 180 / Math.PI;
    brng = (brng + 360) % 360;

    const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
    const idx = Math.round(brng / 45) % 8;
    return dirs[idx];
  }

  function getLang() {
    if (window.DJV_LANGUAGE && window.DJV_LANGUAGE.current) return window.DJV_LANGUAGE.current;
    const params = new URLSearchParams(window.location.search);
    return params.get('lang') || 'en';
  }

  function getCompassLabel(code, lang) {
    const labels = {
      N:  { en: 'North',     te: 'ఉత్తరం',   hi: 'उत्तर' },
      NE: { en: 'Northeast', te: 'ఈశాన్యం',  hi: 'ईशान' },
      E:  { en: 'East',      te: 'తూర్పు',    hi: 'पूर्व' },
      SE: { en: 'Southeast', te: 'ఆగ్నేయం',  hi: 'आग्नेय' },
      S:  { en: 'South',     te: 'దక్షిణం',   hi: 'दक्षिण' },
      SW: { en: 'Southwest', te: 'నైరుతి',    hi: 'नैऋत्य' },
      W:  { en: 'West',      te: 'పడమర',     hi: 'पश्चिम' },
      NW: { en: 'Northwest', te: 'వాయువ్యం', hi: 'वायव्य' }
    };
    const row = labels[code] || labels.N;
    return row[lang] || row.en;
  }

  // Update dynamic distance badges based on active location
  function updateDistances(userLoc, shouldSort) {
    if (!userLoc || !userLoc.latitude || !userLoc.longitude) return;

    if (heroLocText) {
      heroLocText.textContent = userLoc.name || userLoc.city || 'India';
    }

    const lang = getLang();

    cards.forEach(card => {
      const lat = parseFloat(card.getAttribute('data-lat'));
      const lon = parseFloat(card.getAttribute('data-lon'));
      const badge = card.querySelector('.temple-distance-badge');
      const distText = card.querySelector('.dist-text');

      if (!isNaN(lat) && !isNaN(lon) && badge && distText) {
        const dist = calculateDistance(userLoc.latitude, userLoc.longitude, lat, lon);
        const compass = calculateBearing(userLoc.latitude, userLoc.longitude, lat, lon);
        const dirLabel = getCompassLabel(compass, lang);

        card._distanceKm = dist;

        let sentence = '';
        if (lang === 'te') {
          sentence = `${dist} కి.మీ ${dirLabel} (${userLoc.name || userLoc.city} నుండి)`;
        } else if (lang === 'hi') {
          sentence = `${dist} कि.मी. ${dirLabel} (${userLoc.name || userLoc.city} से)`;
        } else {
          sentence = `${dist} km ${dirLabel} of ${userLoc.name || userLoc.city}`;
        }

        distText.textContent = sentence;
        badge.style.display = 'inline-block';
      }
    });

    if (shouldSort) {
      cards.sort((a, b) => (a._distanceKm || 999999) - (b._distanceKm || 999999));
      cards.forEach(c => grid.appendChild(c));
      if (activeSortText) {
        activeSortText.textContent = `Sorted nearest to ${userLoc.name || userLoc.city}`;
      }
    }
  }

  // Combined Live Filtering
  function filterCards() {
    const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const st = (stateSelect ? stateSelect.value : '').toLowerCase().trim();
    const dt = (deitySelect ? deitySelect.value : '').toLowerCase().trim();
    const cat = (catSelect ? catSelect.value : '').toLowerCase().trim();

    let visibleCount = 0;

    cards.forEach(card => {
      const cardText = card.textContent.toLowerCase();
      const cardState = (card.getAttribute('data-state') || '').toLowerCase();
      const cardDeity = (card.getAttribute('data-deity') || '').toLowerCase();
      const cardCat = (card.getAttribute('data-category') || '').toLowerCase();

      const matchesSearch = !q || cardText.includes(q);
      const matchesState = !st || cardState.includes(st);
      const matchesDeity = !dt || cardDeity.includes(dt);
      const matchesCat = !cat || cardCat.includes(cat);

      if (matchesSearch && matchesState && matchesDeity && matchesCat) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    const countEl = document.getElementById('results-count-text');
    if (countEl) {
      if (!q && !st && !dt && !cat) {
        countEl.textContent = '<?php echo sprintf( esc_js( __( 'Showing %d sacred temples across India', 'djv-theme' ) ), $temple_query->found_posts ); ?>';
      } else {
        countEl.textContent = `Showing ${visibleCount} matching sacred temples`;
      }
    }

    if (noMatchBox) {
      noMatchBox.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
  }

  function resetAllFilters() {
    if (searchInput) searchInput.value = '';
    if (stateSelect) stateSelect.value = '';
    if (deitySelect) deitySelect.value = '';
    if (catSelect) catSelect.value = '';
    filterCards();
  }

  // Attach Filter Listeners
  if (searchInput) searchInput.addEventListener('input', filterCards);
  if (stateSelect) stateSelect.addEventListener('change', filterCards);
  if (deitySelect) deitySelect.addEventListener('change', filterCards);
  if (catSelect) catSelect.addEventListener('change', filterCards);
  if (resetBtn) resetBtn.addEventListener('click', resetAllFilters);
  if (noMatchResetBtn) noMatchResetBtn.addEventListener('click', resetAllFilters);

  // Near Me Button Handler with Browser Geolocation Permission
  if (nearMeBtn) {
    nearMeBtn.addEventListener('click', function() {
      if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
      }
      nearMeBtn.textContent = '⏳ Locating...';
      navigator.geolocation.getCurrentPosition(
        function(pos) {
          const lat = pos.coords.latitude;
          const lon = pos.coords.longitude;
          const myLoc = {
            name: 'My GPS Location',
            city: 'My Location',
            latitude: lat,
            longitude: lon,
            lat: lat,
            lon: lon
          };
          updateDistances(myLoc, true);
          nearMeBtn.textContent = '✓ Sorted by Distance';
        },
        function(err) {
          nearMeBtn.innerHTML = '🧭 <?php esc_html_e( "Temples Near Me", "djv-theme" ); ?>';
          alert('Location access was denied or timed out. Please allow location access to see nearest temples.');
        },
        { enableHighAccuracy: true, timeout: 8000 }
      );
    });
  }

  // Initialize with DJV active location
  function init() {
    const loc = (window.DJV_LOCATION && typeof window.DJV_LOCATION.getSelected === 'function')
      ? window.DJV_LOCATION.getSelected()
      : { name: 'Hyderabad', city: 'Hyderabad', latitude: 17.3850, longitude: 78.4867 };

    updateDistances(loc, false);
  }

  // Listen to Global DJV location change events
  window.addEventListener('djv:locationChanged', function(e) {
    if (e.detail) {
      updateDistances(e.detail, false);
    }
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>

<?php
get_footer();
