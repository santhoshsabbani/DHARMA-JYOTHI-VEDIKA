<?php
/**
 * Archive Template for Pooja Guides (djv_pooja)
 *
 * Dedicated directory for Vedic Pooja Vidhi, 15-step generic "How to Start Puja" guide,
 * category filters, live search, and trilingual support (EN, TE, HI).
 *
 * @package DJV_Theme
 */

get_header();

// Fetch generic 15-step guide
$how_to_start = function_exists( 'djv_get_generic_how_to_start_puja' ) ? djv_get_generic_how_to_start_puja() : null;

// Fetch all pooja guides
$all_poojas = get_posts([
	'post_type'      => 'djv_pooja',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
]);
?>

<div class="archive-pooja-wrapper" style="padding: 2rem 0 5rem 0; background: var(--clr-bg, #FDFBF7);">
  <div class="container">

    <!-- ── Breadcrumb ── -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #7A6F68); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php esc_html_e( 'Pooja Guides & Vidhi', 'djv-theme' ); ?></span>
    </nav>

    <!-- ── Hero & Language Switcher Header ── -->
    <header class="pooja-hero" style="margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1.5rem; border-bottom: 1px solid var(--clr-border, #E8DFD3); padding-bottom: 2rem;">
      <div style="flex: 1; min-width: 300px;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(200,148,50,0.12); color: var(--clr-accent, #C89432); font-weight: 700; font-size: 0.8rem; padding: 0.35rem 0.85rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
          🪔 <span class="djv-lang-field" data-lang="en">Vedic Vidhanam & Ritual Guides</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">వేద పూజా విధానాలు & సంకల్పం</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">वैदिक पूजा विधि एवं संकल्प</span>
        </div>

        <h1 style="font-family: var(--font-heading, serif); font-size: 2.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0; line-height: 1.2;">
          <span class="djv-lang-field" data-lang="en">Pooja Guides &amp; Vidhi (పూజా విధానం)</span>
          <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);">పూజా విధానం, సంకల్పం &amp; సామగ్రి</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;">पूजा विधि, संकल्प एवं सामग्री</span>
        </h1>

        <p style="color: var(--clr-text-secondary, #55433C); font-size: 1.05rem; line-height: 1.6; max-width: 760px; margin: 0;">
          <span class="djv-lang-field" data-lang="en">Authentic step-by-step Vedic procedures, required samagri checklists, auspicious timings, sankalpam, and sacred naivedyam recipes for home and temple worship.</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">ఇంట్లో మరియు దేవాలయాల్లో ఆచరించదగిన సంపూర్ణ పూజా విధానాలు, సామగ్రి జాబితా, పవిత్ర సంకల్పం మరియు ప్రసాదాల వివరణ.</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">घर एवं मंदिर में पूजन हेतु प्रामाणिक वैदिक षोडशोपचार विधि, आवश्यक सामग्री सूची, संकल्प एवं नैवेद्य विधान।</span>
        </p>
      </div>

      <!-- Language Selector Bar -->
      <div class="djv-lang-bar" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.35rem 0.5rem; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.5rem; text-transform: uppercase;">🌐 Lang:</span>
        <button type="button" class="djv-lang-btn active" data-lang="en" style="border:none;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.4rem 0.85rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;">English</button>
        <button type="button" class="djv-lang-btn" data-lang="te" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.4rem 0.85rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:var(--font-telugu, sans-serif);">తెలుగు</button>
        <button type="button" class="djv-lang-btn" data-lang="hi" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.4rem 0.85rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:'Noto Sans Devanagari', serif;">हिन्दी</button>
      </div>
    </header>

    <!-- ── Universal Section: "HOW TO START A HINDU PUJA" (15 Steps) ── -->
    <?php if ( $how_to_start && ! empty( $how_to_start['steps'] ) ) : ?>
      <section class="how-to-start-puja-section" style="background: #FFF; border: 1.5px solid var(--clr-accent, #C89432); border-radius: 1.25rem; padding: 2rem; margin-bottom: 3rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <div style="color: var(--clr-accent, #C89432); font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
              📖 Foundational Vedic Protocol
            </div>
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.85rem; color: var(--clr-primary, #7A2419); margin: 0;">
              <span class="djv-lang-field" data-lang="en">How to Start a Hindu Puja (15 Canonical Steps)</span>
              <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);">హిందూ పూజను ఎలా ప్రారంభించాలి (15 ప్రాథమిక నియమాలు)</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;">हिन्दू पूजा कैसे आरंभ करें (15 शास्त्रीय चरण)</span>
            </h2>
          </div>
        </div>

        <!-- Mandatory Traditional Disclaimer -->
        <div class="puja-disclaimer-box" style="background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); padding: 0.85rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.75rem; font-size: 0.92rem; color: var(--clr-text-secondary, #55433C); font-style: italic;">
          ⚠️ <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $how_to_start['disclaimer_en'] ); ?></span>
          <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( $how_to_start['disclaimer_te'] ); ?></span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( $how_to_start['disclaimer_hi'] ); ?></span>
        </div>

        <!-- 15 Steps Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
          <?php foreach ( $how_to_start['steps'] as $st ) : ?>
            <div style="background: #FDFBF7; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1rem; display: flex; gap: 0.85rem; align-items: flex-start;">
              <div style="background: var(--clr-primary, #7A2419); color: #FFF; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0; margin-top: 0.15rem;">
                <?php echo esc_html( $st['step'] ); ?>
              </div>
              <div style="flex: 1;">
                <h4 style="font-size: 0.95rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.35rem 0; line-height: 1.3;">
                  <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $st['title_en'] ); ?></span>
                  <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $st['title_te'] ); ?></span>
                  <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $st['title_hi'] ); ?></span>
                </h4>
                <p style="font-size: 0.84rem; color: var(--clr-text-secondary, #55433C); margin: 0; line-height: 1.5;">
                  <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $st['desc_en'] ); ?></span>
                  <span class="djv-lang-field" data-lang="te" style="display:none;"><?php echo esc_html( $st['desc_te'] ); ?></span>
                  <span class="djv-lang-field" data-lang="hi" style="display:none;"><?php echo esc_html( $st['desc_hi'] ); ?></span>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- ── Search & Category Controls ── -->
    <div class="pooja-controls" style="margin-bottom: 2rem;">
      <div style="position: relative; margin-bottom: 1.25rem; max-width: 600px;">
        <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.1rem; color: var(--clr-text-muted, #7A6F68);">🔍</span>
        <input type="text" id="djv-pooja-search" placeholder="Search pooja guides (e.g., Satyanarayana, Ganapati, Shiva, Lakshmi)..."
               style="width: 100%; padding: 0.85rem 1rem 0.85rem 2.85rem; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; font-size: 0.95rem; background: #FFF; outline: none; box-sizing: border-box; transition: border-color 0.2s;">
      </div>

      <div class="pooja-filter-pills" id="djv-pooja-pills" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
        <button type="button" class="djv-pill active" data-filter="all" style="border: 1px solid var(--clr-primary, #7A2419); background: var(--clr-primary, #7A2419); color: #FFF; padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">All Poojas</button>
        <button type="button" class="djv-pill" data-filter="popular-poojas" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Popular Poojas</button>
        <button type="button" class="djv-pill" data-filter="festival-poojas" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Festival Poojas</button>
        <button type="button" class="djv-pill" data-filter="daily-poojas" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Daily Poojas</button>
        <button type="button" class="djv-pill" data-filter="deity-poojas" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Deity Poojas</button>
        <button type="button" class="djv-pill" data-filter="vratam" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Vratams</button>
      </div>
    </div>

    <!-- ── Pooja Cards Grid ── -->
    <div class="pooja-cards-grid" id="djv-pooja-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
      <?php if ( ! empty( $all_poojas ) ) : ?>
        <?php foreach ( $all_poojas as $post ) : setup_postdata( $post ); ?>
          <?php get_template_part( 'template-parts/pooja/card' ); ?>
        <?php endforeach; wp_reset_postdata(); ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
          <h3><?php esc_html_e( 'No pooja guides found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted, #7A6F68);"><?php esc_html_e( 'Database sync is in progress. Please refresh shortly.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <div id="djv-no-pooja-match" style="display: none; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3); margin-top: 1.5rem;">
      <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
      <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">No matching pooja guides found</h3>
      <p style="color: var(--clr-text-muted, #7A6F68); margin: 0;">Try adjusting your search terms or category filter.</p>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const langKey = 'djv_lang';
  let currentLang = localStorage.getItem(langKey) || 'en';

  function applyLanguage(lang) {
    currentLang = lang;
    localStorage.setItem(langKey, lang);

    document.querySelectorAll('.djv-lang-btn').forEach(btn => {
      if (btn.getAttribute('data-lang') === lang) {
        btn.classList.add('active');
        btn.style.background = 'var(--clr-primary, #7A2419)';
        btn.style.color = '#FFF';
      } else {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--clr-text, #2A1F1D)';
      }
    });

    document.querySelectorAll('.djv-lang-field').forEach(el => {
      if (el.getAttribute('data-lang') === lang) {
        el.style.display = '';
      } else {
        el.style.display = 'none';
      }
    });

    const searchInput = document.getElementById('djv-pooja-search');
    if (searchInput) {
      if (lang === 'te') {
        searchInput.placeholder = 'పూజా విధానాలను వెతకండి (ఉదా: సత్యనారాయణ, గణపతి, శివ, లక్ష్మి)...';
      } else if (lang === 'hi') {
        searchInput.placeholder = 'पूजा विधि खोजें (जैसे: सत्यनारायण, गणपति, शिव, लक्ष्मी)...';
      } else {
        searchInput.placeholder = 'Search pooja guides (e.g., Satyanarayana, Ganapati, Shiva, Lakshmi)...';
      }
    }
  }

  document.querySelectorAll('.djv-lang-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      applyLanguage(this.getAttribute('data-lang'));
    });
  });

  applyLanguage(currentLang);

  // Search & Filter
  const searchInput = document.getElementById('djv-pooja-search');
  const pills = document.querySelectorAll('#djv-pooja-pills .djv-pill');
  const cards = document.querySelectorAll('#djv-pooja-grid .pooja-card');
  const noMatch = document.getElementById('djv-no-pooja-match');
  let currentCategory = 'all';

  function filterPoojas() {
    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
    let visibleCount = 0;

    cards.forEach(card => {
      const matchCat = (currentCategory === 'all') || card.classList.contains(currentCategory);
      
      const titleEn = card.getAttribute('data-title-en') || '';
      const titleTe = card.getAttribute('data-title-te') || '';
      const titleHi = card.getAttribute('data-title-hi') || '';
      const textContent = card.textContent.toLowerCase();

      const matchSearch = !query ||
        titleEn.includes(query) ||
        titleTe.includes(query) ||
        titleHi.includes(query) ||
        textContent.includes(query);

      if (matchCat && matchSearch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (noMatch) {
      noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', filterPoojas);
  }

  pills.forEach(pill => {
    pill.addEventListener('click', function() {
      pills.forEach(p => {
        p.classList.remove('active');
        p.style.background = '#FFF';
        p.style.color = 'var(--clr-text, #2A1F1D)';
        p.style.borderColor = 'var(--clr-border, #E8DFD3)';
      });
      this.classList.add('active');
      this.style.background = 'var(--clr-primary, #7A2419)';
      this.style.color = '#FFF';
      this.style.borderColor = 'var(--clr-primary, #7A2419)';

      currentCategory = this.getAttribute('data-filter');
      filterPoojas();
    });
  });
});
</script>

<?php
get_footer();
