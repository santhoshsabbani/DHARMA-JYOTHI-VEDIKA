<?php
/**
 * Taxonomy Template: States & Regions (djv_state)
 *
 * Displays all sacred temples located in a specific state or region.
 * Uses the existing djv_state taxonomy as the single source of truth.
 *
 * @package DJV_Theme
 */

get_header();

$lang = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';

$term = get_queried_object();
if ( ! ( $term instanceof WP_Term ) ) {
	$term_slug = get_query_var( 'djv_state' ) ?: get_query_var( 'term' );
	$term      = get_term_by( 'slug', $term_slug, 'djv_state' );
}

$state_name = $term ? $term->name : __( 'State / Region', 'djv-theme' );
$state_slug = $term ? $term->slug : '';

// Taxonomy Term Count vs Query Count
$term_count  = $term ? (int) $term->count : 0;
$total_found = (int) $wp_query->found_posts;
$max_pages   = max( 1, (int) $wp_query->max_num_pages );
$paged       = max( 1, (int) ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) ) );

// Fetch filter options for dropdowns
$deities = get_terms([
	'taxonomy'   => 'djv_deity',
	'hide_empty' => true,
]);

$categories = get_terms([
	'taxonomy'   => 'djv_temple_category',
	'hide_empty' => true,
]);

$all_states = get_terms([
	'taxonomy'   => 'djv_state',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
]);

// Current filter values from GET parameters
$current_search   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
$current_deity    = isset( $_GET['deity'] ) ? sanitize_title( wp_unslash( $_GET['deity'] ) ) : '';
$current_category = isset( $_GET['category'] ) ? sanitize_title( wp_unslash( $_GET['category'] ) ) : '';
?>

<!-- Hero Section -->
<section class="page-hero" aria-labelledby="state-title" style="background: linear-gradient(135deg, #241914 0%, #3D100A 100%); color: #FFF; padding: 3.5rem 0 3rem 0; position: relative;">
  <div class="container ph-inner">
    <nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: rgba(255,255,255,0.7); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
      <span> › </span>
      <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Sacred Temples', 'djv-theme' ); ?></a>
      <span> › </span>
      <span aria-current="page" style="color: var(--clr-accent, #C89432);"><?php echo esc_html( $state_name ); ?></span>
    </nav>

    <div class="ph-badge" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.85rem; background: rgba(200,148,50,0.2); border: 1px solid var(--clr-secondary, #C89432); border-radius: 9999px; font-size: 0.8rem; color: var(--clr-accent, #C89432); margin-bottom: 0.85rem;">
      🛕 <?php printf( esc_html__( 'Sacred Temples in %s', 'djv-theme' ), esc_html( $state_name ) ); ?>
    </div>

    <h1 class="ph-title" id="state-title" style="font-family: var(--font-heading, Georgia, serif); font-size: 2.6rem; margin: 0 0 0.5rem 0; line-height: 1.2;">
      <?php printf( esc_html__( 'Temples in %s', 'djv-theme' ), esc_html( $state_name ) ); ?>
    </h1>

    <?php if ( $lang === 'te' ) : ?>
      <p class="ph-sub" style="font-family: var(--font-telugu, inherit); font-size: 1.2rem; color: var(--clr-accent, #C89432); margin: 0 0 0.75rem 0;">
        <?php echo esc_html( $state_name ); ?> పవిత్ర దేవాలయాలు, పుణ్యక్షేత్రాలు &amp; దర్శన సమాచారం
      </p>
    <?php elseif ( $lang === 'hi' ) : ?>
      <p class="ph-sub" style="font-size: 1.2rem; color: var(--clr-accent, #C89432); margin: 0 0 0.75rem 0;">
        <?php echo esc_html( $state_name ); ?> के पवित्र मंदिर, तीर्थ स्थल एवं दर्शन विवरण
      </p>
    <?php else : ?>
      <p class="ph-sub" style="font-size: 1.15rem; color: var(--clr-accent, #C89432); margin: 0 0 0.75rem 0;">
        <?php printf( esc_html__( 'Explore all %d sacred Hindu temples and pilgrimage kshetras across %s', 'djv-theme' ), $total_found, esc_html( $state_name ) ); ?>
      </p>
    <?php endif; ?>

    <p class="ph-desc" style="font-size: 0.95rem; color: rgba(255,255,255,0.85); max-width: 780px; margin: 0; line-height: 1.6;">
      <?php printf( esc_html__( 'Comprehensive pilgrim directory of historical and sacred temples in %s with verified timings, sthala purana, festivals, and pilgrimage travel guidance.', 'djv-theme' ), esc_html( $state_name ) ); ?>
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
  <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.25rem;">

    <!-- Search & Filter Controls -->
    <div class="temple-filters-bar" style="background: #FFF; padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--clr-border, #E2E8F0); box-shadow: var(--shadow-sm, 0 2px 4px rgba(0,0,0,0.05)); margin-bottom: 2.5rem;">
      <div style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr auto; gap: 1rem; align-items: center;">

        <!-- Search Input -->
        <div style="position: relative;">
          <span aria-hidden="true" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1rem; color: var(--clr-text-muted, #94A3B8);">🔍</span>
          <input type="search" id="temple-search"
                 placeholder="<?php printf( esc_attr__( 'Search temples in %s...', 'djv-theme' ), esc_attr( $state_name ) ); ?>"
                 value="<?php echo esc_attr( $current_search ); ?>"
                 aria-label="<?php printf( esc_attr__( 'Search temples in %s', 'djv-theme' ), esc_attr( $state_name ) ); ?>"
                 autocomplete="off"
                 style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.6rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); font-family: inherit; font-size: 0.95rem; outline: none;" />
        </div>

        <!-- Switch State / Region Dropdown -->
        <div>
          <select id="filter-state" aria-label="<?php esc_attr_e( 'Switch State / Region', 'djv-theme' ); ?>" style="width: 100%; padding: 0.75rem 0.85rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); font-family: inherit; font-size: 0.9rem; background: #FFF; outline: none; font-weight: 600; color: var(--clr-primary, #7A2419);">
            <?php if ( ! empty( $all_states ) && ! is_wp_error( $all_states ) ) : ?>
              <?php foreach ( $all_states as $st ) : ?>
                <option value="<?php echo esc_attr( $st->slug ); ?>"
                        data-url="<?php echo esc_url( get_term_link( $st, 'djv_state' ) ); ?>"
                        <?php selected( $st->slug, $state_slug ); ?>>
                  📍 <?php echo esc_html( $st->name ); ?> (<?php echo (int) $st->count; ?>)
                </option>
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
                <option value="<?php echo esc_attr( $d->slug ); ?>" <?php selected( $d->slug, $current_deity ); ?>>
                  🕉 <?php echo esc_html( $d->name ); ?>
                </option>
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
                <option value="<?php echo esc_attr( $c->slug ); ?>" <?php selected( $c->slug, $current_category ); ?>>
                  🛕 <?php echo esc_html( $c->name ); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Reset Button -->
        <div>
          <a href="<?php echo esc_url( get_term_link( $term, 'djv_state' ) ); ?>"
             id="btn-reset-filters"
             style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.75rem 1.15rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #CBD5E1); background: #F8FAFC; color: #475569; font-weight: 600; text-decoration: none; white-space: nowrap;">
            ↺ <?php esc_html_e( 'Reset', 'djv-theme' ); ?>
          </a>
        </div>

      </div>

      <!-- Live Search & Results Counter -->
      <div id="filter-status-msg" style="margin-top: 1rem; font-size: 0.875rem; color: var(--clr-text-muted, #64748B); display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.75rem;">
        <span id="results-count-text">
          <strong>
            <?php
            if ( $max_pages > 1 ) {
              printf(
                esc_html__( 'Showing %1$d sacred temples in %2$s · Page %3$d of %4$d', 'djv-theme' ),
                $total_found,
                esc_html( $state_name ),
                $paged,
                $max_pages
              );
            } else {
              printf(
                esc_html__( 'Showing %1$d sacred temples in %2$s', 'djv-theme' ),
                $total_found,
                esc_html( $state_name )
              );
            }
            ?>
          </strong>
        </span>
        <span id="active-sort-text" style="font-weight: 600; color: #0284C7;"></span>
      </div>
    </div>

    <!-- Temples Grid -->
    <div class="temples-grid" id="temples-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.75rem;">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'template-parts/temple/card' ); ?>
        <?php endwhile; ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E2E8F0);">
          <div style="font-size: 3rem; margin-bottom: 0.75rem;">🛕</div>
          <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;"><?php esc_html_e( 'No temples found matching criteria', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted, #64748B); margin: 0 0 1.25rem 0;"><?php printf( esc_html__( 'No temples found in %s matching your current search or filters.', 'djv-theme' ), esc_html( $state_name ) ); ?></p>
          <a href="<?php echo esc_url( get_term_link( $term, 'djv_state' ) ); ?>" style="display: inline-block; padding: 0.5rem 1.25rem; background: var(--clr-primary, #7A2419); color: #FFF; border-radius: 9999px; font-weight: 600; text-decoration: none;">
            ↺ <?php esc_html_e( 'Reset Filters', 'djv-theme' ); ?>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Dynamic No Match Box for JS Filtering -->
    <div id="no-match-box" style="display: none; text-align: center; padding: 4rem 2rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E2E8F0); margin-top: 1.5rem;">
      <div style="font-size: 3rem; margin-bottom: 0.75rem;">🔍</div>
      <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;"><?php esc_html_e( 'No temples found matching your criteria.', 'djv-theme' ); ?></h3>
      <p style="color: var(--clr-text-muted, #64748B); margin: 0 0 1.25rem 0;"><?php esc_html_e( 'Try clearing the search text or changing the filters.', 'djv-theme' ); ?></p>
      <a href="<?php echo esc_url( get_term_link( $term, 'djv_state' ) ); ?>" style="display: inline-block; padding: 0.5rem 1.25rem; background: var(--clr-primary, #7A2419); color: #FFF; border-radius: 9999px; font-weight: 600; text-decoration: none;">
        ↺ <?php esc_html_e( 'Reset Filters', 'djv-theme' ); ?>
      </a>
    </div>

    <!-- Pagination -->
    <?php if ( $max_pages > 1 ) : ?>
      <div class="pagination-wrapper" style="margin-top: 3.5rem; text-align: center;">
        <?php
        echo paginate_links([
          'total'     => $max_pages,
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
  const nearMeBtn = document.getElementById('btn-near-me');
  const noMatchBox = document.getElementById('no-match-box');
  const grid = document.getElementById('temples-grid');
  const heroLocText = document.getElementById('temple-hero-loc-name');
  const activeSortText = document.getElementById('active-sort-text');
  const countEl = document.getElementById('results-count-text');

  const defaultTotal = <?php echo (int) $total_found; ?>;
  const stateName = <?php echo json_encode( $state_name ); ?>;
  const paged = <?php echo (int) $paged; ?>;
  const maxPages = <?php echo (int) $max_pages; ?>;

  // When state is changed in dropdown, navigate to that state's dedicated URL
  if (stateSelect) {
    stateSelect.addEventListener('change', function() {
      const selectedOption = stateSelect.options[stateSelect.selectedIndex];
      const targetUrl = selectedOption ? selectedOption.getAttribute('data-url') : '';
      if (targetUrl) {
        window.location.href = targetUrl;
      }
    });
  }

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

    if (shouldSort && grid) {
      cards.sort((a, b) => (a._distanceKm || 999999) - (b._distanceKm || 999999));
      cards.forEach(c => grid.appendChild(c));
      if (activeSortText) {
        activeSortText.textContent = `Sorted nearest to ${userLoc.name || userLoc.city}`;
      }
    }
  }

  // Client-Side Live Filter while on current State page
  function filterCards() {
    const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const dt = (deitySelect ? deitySelect.value : '').toLowerCase().trim();
    const cat = (catSelect ? catSelect.value : '').toLowerCase().trim();

    let visibleCount = 0;

    cards.forEach(card => {
      const cardText = card.textContent.toLowerCase();
      const cardDeity = (card.getAttribute('data-deity') || '').toLowerCase();
      const cardCat = (card.getAttribute('data-category') || '').toLowerCase();

      const matchesSearch = !q || cardText.includes(q);
      const matchesDeity = !dt || cardDeity.includes(dt);
      const matchesCat = !cat || cardCat.includes(cat);

      if (matchesSearch && matchesDeity && matchesCat) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (countEl) {
      if (!q && !dt && !cat) {
        if (maxPages > 1) {
          countEl.innerHTML = `<strong>Showing ${defaultTotal} sacred temples in ${stateName} · Page ${paged} of ${maxPages}</strong>`;
        } else {
          countEl.innerHTML = `<strong>Showing ${defaultTotal} sacred temples in ${stateName}</strong>`;
        }
      } else {
        countEl.innerHTML = `<strong>Showing ${visibleCount} matching sacred temples in ${stateName}</strong>`;
      }
    }

    if (noMatchBox) {
      noMatchBox.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
  }

  if (searchInput) searchInput.addEventListener('input', filterCards);
  if (deitySelect) deitySelect.addEventListener('change', filterCards);
  if (catSelect) catSelect.addEventListener('change', filterCards);

  // Near Me Button Handler with Browser Geolocation
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
