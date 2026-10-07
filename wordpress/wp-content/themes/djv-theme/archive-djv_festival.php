<?php
/**
 * Archive Template for Festivals (djv_festival)
 *
 * Dedicated directory for Hindu & Telugu festivals with live search,
 * category filters, upcoming spotlight, and trilingual support (EN, TE, HI).
 *
 * @package DJV_Theme
 */

get_header();

// Fetch all published festivals ordered by date
$all_festivals = get_posts([
	'post_type'      => 'djv_festival',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'meta_value',
	'meta_key'       => '_djv_festival_date',
	'order'          => 'ASC',
]);

// Determine upcoming / spotlight festival based on current date
$today_str = current_time( 'Y-m-d' );
$spotlight_festival = null;
foreach ( $all_festivals as $f ) {
	$f_date = get_post_meta( $f->ID, '_djv_festival_date', true );
	if ( $f_date && $f_date >= $today_str ) {
		$spotlight_festival = $f;
		break;
	}
}
if ( ! $spotlight_festival && ! empty( $all_festivals ) ) {
	$spotlight_festival = $all_festivals[0];
}
?>

<div class="archive-festival-wrapper" style="padding: 2rem 0 5rem 0; background: var(--clr-bg, #FDFBF7);">
  <div class="container">

    <!-- ── Breadcrumb ── -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #7A6F68); margin-bottom: 1rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php esc_html_e( 'Festivals & Vrats', 'djv-theme' ); ?></span>
    </nav>

    <!-- ── Hero & Language Switcher Header ── -->
    <header class="festival-hero" style="margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1.5rem; border-bottom: 1px solid var(--clr-border, #E8DFD3); padding-bottom: 2rem;">
      <div style="flex: 1; min-width: 300px;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(200,148,50,0.12); color: var(--clr-accent, #C89432); font-weight: 700; font-size: 0.8rem; padding: 0.35rem 0.85rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
          🪔 <span class="djv-lang-field" data-lang="en">Vedic Calendar & Celebrations</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">వేద క్యాలెండర్ & పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">वैदिक पंचांग एवं प्रमुख पर्व</span>
        </div>

        <h1 style="font-family: var(--font-heading, serif); font-size: 2.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0; line-height: 1.2;">
          <span class="djv-lang-field" data-lang="en">Hindu Festivals &amp; Vrats Calendar</span>
          <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);">హిందూ పండుగలు &amp; వ్రతాల క్యాలెండర్</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;">हिन्दू व्रत एवं त्योहार कैलेंडर</span>
        </h1>

        <p style="color: var(--clr-text-secondary, #55433C); font-size: 1.05rem; line-height: 1.6; max-width: 760px; margin: 0;">
          <span class="djv-lang-field" data-lang="en">Calculated dynamically according to authentic Vedic tithi, nakshatra, and solar transit rules. Complete puja vidhi, muhurats, and mantras.</span>
          <span class="djv-lang-field" data-lang="te" style="display:none;">శాస్త్రీయ పంచాంగ తిథి, నక్షత్రాలు, సౌర పరివర్తనల ఆధారంగా లెక్కించబడిన పండుగలు, పూజా ముహూర్తాలు, విశిష్టత మరియు విధానాలు.</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">वैदिक पंचांग, तिथि, नक्षत्र एवं सौर संक्रांति के अनुसार गणना किए गए प्रामाणिक त्योहार, शुभ पूजा मुहूर्त एवं संपूर्ण विधि।</span>
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

    <!-- ── Spotlight / Upcoming Festival Card ── -->
    <?php if ( $spotlight_festival ) :
      $sp_id       = $spotlight_festival->ID;
      $sp_title_en = get_post_meta( $sp_id, '_djv_title_en', true ) ?: $spotlight_festival->post_title;
      $sp_title_te = get_post_meta( $sp_id, '_djv_title_te', true ) ?: get_post_meta( $sp_id, '_djv_telugu_name', true );
      $sp_title_hi = get_post_meta( $sp_id, '_djv_title_hi', true );
      $sp_date     = get_post_meta( $sp_id, '_djv_festival_date', true );
      $sp_timings  = get_post_meta( $sp_id, '_djv_puja_timings', true );
      $sp_tithi    = get_post_meta( $sp_id, '_djv_tithi_rule', true );
      $sp_link     = get_permalink( $sp_id );
      $sp_fmt_date = $sp_date ? date( 'l, F j, Y', strtotime( $sp_date ) ) : '';
    ?>
      <section class="festival-spotlight" style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF4E5 100%); border: 1.5px solid var(--clr-accent, #C89432); border-radius: 1.25rem; padding: 2rem; margin-bottom: 3rem; box-shadow: var(--shadow-md, 0 4px 16px rgba(0,0,0,0.08));">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem;">
          <div style="flex: 1; min-width: 280px;">
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--clr-primary, #7A2419); font-weight: 700; font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase;">
              ⭐ <span class="djv-lang-field" data-lang="en">Featured / Upcoming Observance</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">రాబోయే ప్రధాన పండుగ విశేషాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">आगामी प्रमुख व्रत एवं पर्व</span>
            </div>
            <h2 style="font-family: var(--font-heading, serif); font-size: 2rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">
              <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $sp_title_en ); ?></span>
              <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $sp_title_te ?: $sp_title_en ); ?></span>
              <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $sp_title_hi ?: $sp_title_en ); ?></span>
            </h2>
            <?php if ( $sp_title_te ) : ?>
              <div style="font-family: var(--font-telugu, sans-serif); font-size: 1.15rem; color: var(--clr-accent, #C89432); font-weight: 600; margin-bottom: 0.5rem;">
                <?php echo esc_html( $sp_title_te ); ?>
              </div>
            <?php endif; ?>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; font-size: 0.95rem; color: var(--clr-text, #2A1F1D); margin-bottom: 1rem;">
              <?php if ( $sp_fmt_date ) : ?>
                <span style="font-weight: 700; color: var(--clr-primary, #7A2419);">📅 <?php echo esc_html( $sp_fmt_date ); ?></span>
              <?php endif; ?>
              <?php if ( $sp_tithi ) : ?>
                <span style="color: var(--clr-text-secondary, #55433C);">🌙 <?php echo esc_html( $sp_tithi ); ?></span>
              <?php endif; ?>
              <?php if ( $sp_timings ) : ?>
                <span style="color: var(--clr-accent, #C89432); font-weight: 600;">⏰ <?php echo esc_html( $sp_timings ); ?></span>
              <?php endif; ?>
            </div>
            <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.6; margin: 0 0 1.25rem 0; font-size: 0.95rem;">
              <?php echo esc_html( wp_trim_words( $spotlight_festival->post_excerpt ?: $spotlight_festival->post_content, 35 ) ); ?>
            </p>
            <a href="<?php echo esc_url( $sp_link ); ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--clr-primary, #7A2419); color: #FFF; padding: 0.7rem 1.4rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">
              <span class="djv-lang-field" data-lang="en">Explore Puja Vidhi & Muhurat →</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">సంపూర్ణ పూజా విధానం & ముహూర్తం చూడండి →</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">संपूर्ण पूजा विधि एवं मुहूर्त देखें →</span>
            </a>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ── Filter & Search Controls ── -->
    <div class="festival-controls" style="margin-bottom: 2rem;">

      <!-- ── Year Selector Bar: ‹ 2025  2026  2027  2028  2029  2030 › ── -->
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
        <div class="djv-year-bar" id="djv-year-selector" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.35rem 0.6rem; display: inline-flex; align-items: center; gap: 0.3rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.4rem; text-transform: uppercase;">📅 Year:</span>
          <button type="button" class="djv-year-nav" id="djv-year-prev-btn" style="border:none;background:transparent;color:var(--clr-primary, #7A2419);font-size:1.2rem;font-weight:700;cursor:pointer;padding:0.2rem 0.5rem;border-radius:9999px;line-height:1;" title="Previous Year">‹</button>
          <?php
          $current_page_year = (int) date( 'Y' );
          foreach ( [ 2025, 2026, 2027, 2028, 2029, 2030 ] as $y_btn ) :
            $is_active = ( $y_btn === $current_page_year );
          ?>
            <button type="button" class="djv-year-btn <?php echo $is_active ? 'active' : ''; ?>" data-year="<?php echo esc_attr( $y_btn ); ?>"
                    style="border:none;background:<?php echo $is_active ? 'var(--clr-primary, #7A2419)' : 'transparent'; ?>;color:<?php echo $is_active ? '#FFF' : 'var(--clr-text, #2A1F1D)'; ?>;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.85rem;font-weight:700;cursor:pointer;transition:all 0.2s;">
              <?php echo esc_html( $y_btn ); ?>
            </button>
          <?php endforeach; ?>
          <button type="button" class="djv-year-nav" id="djv-year-next-btn" style="border:none;background:transparent;color:var(--clr-primary, #7A2419);font-size:1.2rem;font-weight:700;cursor:pointer;padding:0.2rem 0.5rem;border-radius:9999px;line-height:1;" title="Next Year">›</button>
        </div>

        <div id="djv-year-indicator" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-text-muted, #7A6F68);">
          <span class="djv-lang-field" data-lang="en">Calculated for Year <strong id="djv-current-year-label" style="color:var(--clr-primary, #7A2419);"><?php echo esc_html( $current_page_year ); ?></strong></span>
          <span class="djv-lang-field" data-lang="te" style="display:none;"><strong id="djv-current-year-label-te" style="color:var(--clr-primary, #7A2419);"><?php echo esc_html( $current_page_year ); ?></strong> సంవత్సర పంచాంగ పండుగలు</span>
          <span class="djv-lang-field" data-lang="hi" style="display:none;">वर्ष <strong id="djv-current-year-label-hi" style="color:var(--clr-primary, #7A2419);"><?php echo esc_html( $current_page_year ); ?></strong> के प्रामाणिक व्रत एवं त्योहार</span>
        </div>
      </div>

      <!-- Search Box -->
      <div style="position: relative; margin-bottom: 1.25rem; max-width: 600px;">
        <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.1rem; color: var(--clr-text-muted, #7A6F68);">🔍</span>
        <input type="text" id="djv-festival-search" placeholder="Search festivals (e.g., Ugadi, Diwali, Shivaratri, Ekadashi)..."
               style="width: 100%; padding: 0.85rem 1rem 0.85rem 2.85rem; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; font-size: 0.95rem; background: #FFF; outline: none; box-sizing: border-box; transition: border-color 0.2s;">
      </div>

      <!-- Category Filter Pills -->
      <div class="festival-filter-pills" id="djv-festival-pills" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
        <button type="button" class="djv-pill active" data-filter="all" style="border: 1px solid var(--clr-primary, #7A2419); background: var(--clr-primary, #7A2419); color: #FFF; padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">All Festivals</button>
        <button type="button" class="djv-pill" data-filter="major-festivals" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Major Festivals</button>
        <button type="button" class="djv-pill" data-filter="regional" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Telugu / Regional</button>
        <button type="button" class="djv-pill" data-filter="shiva" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Shiva</button>
        <button type="button" class="djv-pill" data-filter="vishnu" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Vishnu</button>
        <button type="button" class="djv-pill" data-filter="krishna" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Krishna</button>
        <button type="button" class="djv-pill" data-filter="ganesha" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Ganesha</button>
        <button type="button" class="djv-pill" data-filter="hanuman" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Hanuman</button>
        <button type="button" class="djv-pill" data-filter="devi" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Devi / Durga</button>
        <button type="button" class="djv-pill" data-filter="lakshmi" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Lakshmi</button>
        <button type="button" class="djv-pill" data-filter="saraswati" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Saraswati</button>
        <button type="button" class="djv-pill" data-filter="sankranti" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Sankranti</button>
        <button type="button" class="djv-pill" data-filter="fasting" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Fasting &amp; Ekadashi</button>
        <button type="button" class="djv-pill" data-filter="purnima" style="border: 1px solid var(--clr-border, #E8DFD3); background: #FFF; color: var(--clr-text, #2A1F1D); padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">Purnima</button>
      </div>
    </div>

    <!-- ── Festival Cards Grid (3 Columns Desktop, 2 Columns Tablet, 1 Column Mobile) ── -->
    <div class="festival-cards-grid" id="djv-festival-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
      <?php if ( ! empty( $all_festivals ) ) : ?>
        <?php foreach ( $all_festivals as $post ) : setup_postdata( $post ); ?>
          <?php get_template_part( 'template-parts/festival/card' ); ?>
        <?php endforeach; wp_reset_postdata(); ?>
      <?php else : ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
          <h3><?php esc_html_e( 'No festivals found', 'djv-theme' ); ?></h3>
          <p style="color: var(--clr-text-muted, #7A6F68);"><?php esc_html_e( 'Database sync is in progress. Please refresh shortly.', 'djv-theme' ); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <div id="djv-no-festivals-match" style="display: none; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3); margin-top: 1.5rem;">
      <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
      <h3 style="color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">No matching festivals found</h3>
      <p style="color: var(--clr-text-muted, #7A6F68); margin: 0;">Try adjusting your keyword or category filter.</p>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // ── Language Switcher Persistence ──
  const langKey = 'djv_lang';
  let currentLang = localStorage.getItem(langKey) || 'en';

  function applyLanguage(lang) {
    currentLang = lang;
    localStorage.setItem(langKey, lang);

    // Update switcher buttons
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

    // Toggle fields
    document.querySelectorAll('.djv-lang-field').forEach(el => {
      if (el.getAttribute('data-lang') === lang) {
        el.style.display = '';
      } else {
        el.style.display = 'none';
      }
    });

    // Update search placeholder based on language
    const searchInput = document.getElementById('djv-festival-search');
    if (searchInput) {
      if (lang === 'te') {
        searchInput.placeholder = 'పండుగలను వెతకండి (ఉదా: ఉగాది, దీపావళి, వినాయక చవితి)...';
      } else if (lang === 'hi') {
        searchInput.placeholder = 'त्योहार खोजें (जैसे: उगादि, दिवाली, गणेश चतुर्थी)...';
      } else {
        searchInput.placeholder = 'Search festivals (e.g., Ugadi, Diwali, Shivaratri, Ekadashi)...';
      }
    }
  }

  // Bind switcher buttons
  document.querySelectorAll('.djv-lang-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      applyLanguage(this.getAttribute('data-lang'));
    });
  });

  applyLanguage(currentLang);

  // ── Search & Category Filter ──
  const searchInput = document.getElementById('djv-festival-search');
  const pills = document.querySelectorAll('#djv-festival-pills .djv-pill');
  const cards = document.querySelectorAll('#djv-festival-grid .festival-card');
  const noMatch = document.getElementById('djv-no-festivals-match');
  let currentCategory = 'all';

  function filterFestivals() {
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
    searchInput.addEventListener('input', filterFestivals);
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
      filterFestivals();
    });
  });

  // ── Dynamic Year Selector Engine ──
  const availableYears = [ 2025, 2026, 2027, 2028, 2029, 2030 ];
  let currentYear = <?php echo (int) $current_page_year; ?>;
  const grid = document.getElementById('djv-festival-grid');

  function updateYearButtonStyles(year) {
    document.querySelectorAll('.djv-year-btn').forEach(btn => {
      const btnYear = parseInt(btn.getAttribute('data-year'), 10);
      if (btnYear === year) {
        btn.classList.add('active');
        btn.style.background = 'var(--clr-primary, #7A2419)';
        btn.style.color = '#FFF';
      } else {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--clr-text, #2A1F1D)';
      }
    });

    const lEn = document.getElementById('djv-current-year-label');
    const lTe = document.getElementById('djv-current-year-label-te');
    const lHi = document.getElementById('djv-current-year-label-hi');
    if (lEn) lEn.textContent = year;
    if (lTe) lTe.textContent = year;
    if (lHi) lHi.textContent = year;
  }

  function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function renderFestivalCard(f) {
    const titleEn = f.title_en || f.title || '';
    const titleTe = f.title_te || '';
    const titleHi = f.title_hi || '';
    const dateFormatted = f.formatted_date || (f.date ? f.date : '');
    const tithiRule = f.tithi_rule || '';
    const link = f.link || '#';
    const excerpt = f.excerpt || f.content_en || '';
    const category = (f.categories && f.categories.length) ? f.categories[0] : '';

    // Date badge (e.g. "OCT 20")
    let badge = '';
    if (f.date) {
      const dParts = f.date.split('-');
      if (dParts.length === 3) {
        const dObj = new Date(parseInt(dParts[0]), parseInt(dParts[1]) - 1, parseInt(dParts[2]));
        badge = dObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }).toUpperCase();
      }
    }

    // Filter classes
    const classes = ['festival-card', 'djv-filter-item'];
    if (f.deity_slug) classes.push(f.deity_slug);
    if (f.is_major) classes.push('major-festivals');
    if (f.is_telugu) classes.push('regional');
    if (f.categories) {
      f.categories.forEach(c => {
        const cSlug = c.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        classes.push(cSlug);
      });
    }
    const filterClassStr = Array.from(new Set(classes)).join(' ');

    return `
      <div class="${filterClassStr}" id="festival-card-${f.id || f.slug}"
           data-title-en="${escapeHtml(titleEn.toLowerCase())}"
           data-title-te="${escapeHtml(titleTe.toLowerCase())}"
           data-title-hi="${escapeHtml(titleHi.toLowerCase())}"
           style="border:1px solid var(--clr-border, #E8DFD3);background:#FFF;border-radius:1rem;padding:1.5rem;box-shadow:var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));position:relative;display:flex;flex-direction:column;transition:transform 0.2s,box-shadow 0.2s;">
        
        ${badge ? `
          <span class="festival-card-badge" style="position:absolute;top:1rem;right:1rem;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.25rem 0.65rem;border-radius:0.4rem;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;">
            ${escapeHtml(badge)}
          </span>
        ` : ''}

        <div style="width:48px;height:48px;background:rgba(200,148,50,0.12);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;font-size:1.75rem;margin-bottom:0.85rem;" aria-hidden="true">
          🪔
        </div>

        <h3 class="festival-card-title" style="font-family:var(--font-heading, serif);font-size:1.25rem;color:var(--clr-primary, #7A2419);margin:0 0 0.35rem 0;line-height:1.35;">
          <a href="${escapeHtml(link)}" style="color:inherit;text-decoration:none;">
            <span class="djv-lang-field" data-lang="en">${escapeHtml(titleEn)}</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;font-family:var(--font-telugu, sans-serif);">${escapeHtml(titleTe || titleEn)}</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;font-family:'Noto Sans Devanagari', serif;">${escapeHtml(titleHi || titleEn)}</span>
          </a>
        </h3>

        ${titleTe ? `
          <div class="festival-card-te-sub djv-lang-field" data-lang="en" style="font-family:var(--font-telugu, sans-serif);font-size:0.92rem;color:var(--clr-accent, #C89432);font-weight:600;margin-bottom:0.4rem;">
            ${escapeHtml(titleTe)}
          </div>
        ` : ''}

        ${dateFormatted ? `
          <div style="font-size:0.85rem;color:var(--clr-primary, #7A2419);font-weight:600;margin-bottom:0.4rem;">
            📅 ${escapeHtml(dateFormatted)}
          </div>
        ` : ''}

        ${tithiRule ? `
          <div style="font-size:0.78rem;color:var(--clr-text-muted, #7A6F68);margin-bottom:0.6rem;line-height:1.4;">
            🌙 ${escapeHtml(tithiRule)}
          </div>
        ` : ''}

        <p style="font-size:0.875rem;color:var(--clr-text-secondary, #55433C);line-height:1.6;margin:0 0 1rem 0;flex:1;">
          ${escapeHtml(excerpt)}
        </p>

        <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:0.75rem;border-top:1px solid #F5EFEB;">
          <a href="${escapeHtml(link)}" style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.875rem;font-weight:600;color:var(--clr-primary, #7A2419);text-decoration:none;">
            <span class="djv-lang-field" data-lang="en">View Vidhi &amp; Muhurat →</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా విధానం &amp; ముహూర్తం →</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा विधि एवं मुहूर्त →</span>
          </a>
          ${category ? `
            <span style="font-size:0.72rem;background:#FFF9F0;color:var(--clr-text-muted, #7A6F68);padding:0.2rem 0.5rem;border-radius:0.25rem;border:1px solid var(--clr-border, #E8DFD3);">
              ${escapeHtml(category)}
            </span>
          ` : ''}
        </div>
      </div>
    `;
  }

  function switchYear(targetYear) {
    currentYear = targetYear;
    updateYearButtonStyles(targetYear);

    if (grid) {
      grid.style.opacity = '0.5';
    }

    fetch(`/wp-json/djv/v1/festivals?year=${targetYear}&language=${currentLang}`)
      .then(res => res.json())
      .then(payload => {
        if (!payload.success || !Array.isArray(payload.data)) {
          console.warn('DJV: Could not fetch year occurrences', payload);
          if (grid) grid.style.opacity = '1';
          return;
        }

        const occurrences = payload.data;
        if (grid) {
          if (occurrences.length === 0) {
            grid.innerHTML = `
              <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #FFF; border-radius: 1rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🪔</div>
                <h3>No festivals calculated for ${targetYear}</h3>
                <p style="color: var(--clr-text-muted, #7A6F68);">Please select another year.</p>
              </div>
            `;
          } else {
            grid.innerHTML = occurrences.map(renderFestivalCard).join('');
          }
          grid.style.opacity = '1';
        }

        // Re-apply language and category filter on newly rendered cards
        applyLanguage(currentLang);
        filterFestivals();
      })
      .catch(err => {
        console.error('DJV: Year fetch error:', err);
        if (grid) grid.style.opacity = '1';
      });
  }

  // Bind year buttons
  document.querySelectorAll('.djv-year-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const yr = parseInt(this.getAttribute('data-year'), 10);
      if (yr && yr !== currentYear) {
        switchYear(yr);
      }
    });
  });

  const prevYearBtn = document.getElementById('djv-year-prev-btn');
  if (prevYearBtn) {
    prevYearBtn.addEventListener('click', function() {
      const prevIdx = availableYears.indexOf(currentYear) - 1;
      if (prevIdx >= 0) {
        switchYear(availableYears[prevIdx]);
      }
    });
  }

  const nextYearBtn = document.getElementById('djv-year-next-btn');
  if (nextYearBtn) {
    nextYearBtn.addEventListener('click', function() {
      const nextIdx = availableYears.indexOf(currentYear) + 1;
      if (nextIdx < availableYears.length) {
        switchYear(availableYears[nextIdx]);
      }
    });
  }
});
</script>

<?php
get_footer();
