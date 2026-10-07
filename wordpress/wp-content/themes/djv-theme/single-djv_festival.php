<?php
/**
 * Single Festival Template (djv_festival)
 *
 * Full-featured single festival view conforming to DJV visual identity.
 * Displays breadcrumb, trilingual titles (EN, TE, HI), persistent language switcher,
 * date, dynamic Panchangam & Puja Muhurat calculation, significance, history,
 * puja vidhanam, samagri checklist, naivedyam, related mantras from Mantras CPT,
 * vrat rules, do's & don'ts, FAQ, and related entities.
 *
 * @package DJV_Theme
 */

get_header();

$post_id         = get_the_ID();
$title_en        = get_post_meta( $post_id, '_djv_title_en', true ) ?: get_the_title();
$title_te        = get_post_meta( $post_id, '_djv_title_te', true ) ?: get_post_meta( $post_id, '_djv_telugu_name', true );
$title_hi        = get_post_meta( $post_id, '_djv_title_hi', true );

$content_en      = get_post_meta( $post_id, '_djv_content_en', true ) ?: apply_filters( 'the_content', get_the_content() );
$content_te      = get_post_meta( $post_id, '_djv_content_te', true );
$content_hi      = get_post_meta( $post_id, '_djv_content_hi', true );

$date_meta       = get_post_meta( $post_id, '_djv_festival_date', true ) ?: get_post_meta( $post_id, '_djv_date', true );
$month_meta      = get_post_meta( $post_id, '_djv_month', true );
$tithi_rule      = get_post_meta( $post_id, '_djv_tithi_rule', true );

$significance    = get_post_meta( $post_id, '_djv_significance', true );
$history         = get_post_meta( $post_id, '_djv_history', true );
$timings         = get_post_meta( $post_id, '_djv_puja_timings', true );
$samagri         = get_post_meta( $post_id, '_djv_samagri', true );
$naivedyam       = get_post_meta( $post_id, '_djv_naivedyam', true );
$vrat_rules      = get_post_meta( $post_id, '_djv_vrat_rules', true );
$dos             = get_post_meta( $post_id, '_djv_dos', true );
$donts           = get_post_meta( $post_id, '_djv_donts', true );

$related_pooja_slugs  = get_post_meta( $post_id, '_djv_related_poojas', true ) ?: [];
$related_mantra_slugs = get_post_meta( $post_id, '_djv_related_mantras', true ) ?: [];

$categories = wp_get_post_terms( $post_id, 'djv_festival_cat', [ 'fields' => 'names' ] );
if ( empty( $categories ) ) {
	$categories = wp_get_post_terms( $post_id, 'djv_festival_type', [ 'fields' => 'names' ] );
}

$formatted_date = '';
$day_of_week    = '';
if ( $date_meta ) {
	$time = strtotime( $date_meta );
	if ( $time ) {
		$formatted_date = date( 'F j, Y', $time );
		$day_of_week    = date( 'l', $time );
	}
}

// Fetch dynamic Panchangam data for festival date (or current date if in past/empty)
$panchangam_data = null;
if ( class_exists( 'DJV_Panchangam' ) ) {
	$p_engine = new DJV_Panchangam();
	$calc_date = $date_meta ?: current_time( 'Y-m-d' );
	$p_res = $p_engine->get_panchangam( $calc_date, 17.3850, 78.4867, 'Asia/Kolkata' );
	if ( ! is_wp_error( $p_res ) && ! empty( $p_res['astronomy'] ) ) {
		$panchangam_data = $p_res;
	}
}
?>

<div class="single-festival-wrapper" style="padding: 2rem 0 5rem 0; background: var(--clr-bg, #FDFBF7);">
  <div class="container" style="max-width: 1040px;">

    <!-- ── Breadcrumb ── -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #7A6F68); margin-bottom: 1.25rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php echo esc_html( $title_en ); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="festival-<?php the_ID(); ?>" <?php post_class( 'single-festival-article' ); ?>>

        <!-- ── Header Banner with Language Switcher ── -->
        <header style="margin-bottom: 2.25rem; border-bottom: 1px solid var(--clr-border, #E8DFD3); padding-bottom: 1.75rem;">
          <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
              <span style="background: rgba(200,148,50,0.12); color: var(--clr-accent, #C89432); font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase;">
                🎊 <?php esc_html_e( 'Vedic Festival', 'djv-theme' ); ?>
              </span>
              <?php if ( ! empty( $categories ) ) : ?>
                <?php foreach ( array_slice( $categories, 0, 2 ) as $c ) : ?>
                  <span style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); color: var(--clr-text-muted, #7A6F68); font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                    <?php echo esc_html( $c ); ?>
                  </span>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <!-- Language Switcher Bar -->
            <div class="djv-lang-bar" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.25rem 0.4rem; display: inline-flex; align-items: center; gap: 0.25rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.4rem; text-transform: uppercase;">🌐 Script:</span>
              <button type="button" class="djv-lang-btn active" data-lang="en" style="border:none;background:var(--clr-primary, #7A2419);color:#FFF;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;">English</button>
              <button type="button" class="djv-lang-btn" data-lang="te" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:var(--font-telugu, sans-serif);">తెలుగు</button>
              <button type="button" class="djv-lang-btn" data-lang="hi" style="border:none;background:transparent;color:var(--clr-text, #2A1F1D);padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari', serif;">हिन्दी</button>
            </div>
          </div>

          <!-- Trilingual Titles -->
          <h1 style="font-family: var(--font-heading, serif); font-size: 2.75rem; color: var(--clr-primary, #7A2419); line-height: 1.2; margin: 0 0 0.5rem 0;">
            <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $title_en ); ?></span>
            <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $title_te ?: $title_en ); ?></span>
            <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $title_hi ?: $title_en ); ?></span>
          </h1>

          <?php if ( $title_te ) : ?>
            <div style="font-family: var(--font-telugu, sans-serif); font-size: 1.4rem; color: var(--clr-accent, #C89432); font-weight: 600; margin-bottom: 0.85rem;">
              <?php echo esc_html( $title_te ); ?>
            </div>
          <?php endif; ?>

          <!-- Date & Astronomical Info Strip -->
          <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); padding: 0.85rem 1.25rem; border-radius: 0.75rem; font-size: 0.92rem; margin-top: 1rem;">
            <?php if ( $formatted_date ) : ?>
              <div style="font-weight: 700; color: var(--clr-primary, #7A2419);">
                📅 <?php echo esc_html( $formatted_date ); ?><?php echo $day_of_week ? ' (' . esc_html( $day_of_week ) . ')' : ''; ?>
              </div>
            <?php endif; ?>
            <?php if ( $tithi_rule ) : ?>
              <div style="color: var(--clr-text-secondary, #55433C);">
                🌙 <strong>Tithi:</strong> <?php echo esc_html( $tithi_rule ); ?>
              </div>
            <?php endif; ?>
            <div style="color: var(--clr-text-muted, #7A6F68); margin-left: auto; font-size: 0.82rem;">
              📍 Calculations for <strong>Hyderabad / Telangana &amp; AP</strong>
            </div>
          </div>
        </header>

        <!-- ── Dynamic Panchangam & Puja Muhurat Box ── -->
        <section class="panchangam-muhurat-box" style="background: linear-gradient(135deg, #FFF9F0 0%, #FFF3E0 100%); border: 1.5px solid var(--clr-accent, #C89432); border-radius: 1rem; padding: 1.75rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(200,148,50,0.3); padding-bottom: 0.75rem;">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.35rem; color: var(--clr-primary, #7A2419); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
              ⏰ <span class="djv-lang-field" data-lang="en">Auspicious Puja Muhurat &amp; Panchangam</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">శుభ పూజా ముహూర్తం &amp; పంచాంగ వివరాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">शुभ पूजा मुहूर्त एवं पंचांग विवरण</span>
            </h2>
            <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" style="font-size: 0.82rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
              Detailed Today's Panchangam →
            </a>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
            <?php if ( $timings ) : ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">
                  Recommended Puja Timings
                </div>
                <div style="font-size: 1.05rem; font-weight: 700; color: var(--clr-primary, #7A2419); margin-top: 0.25rem;">
                  <?php echo esc_html( $timings ); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $panchangam_data['astronomy'] ) ) : ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Solar Timings (Hyderabad)
                </div>
                <div style="font-size: 0.92rem; color: var(--clr-text, #2A1F1D); margin-top: 0.25rem;">
                  🌅 Sunrise: <strong><?php echo esc_html( $panchangam_data['astronomy']['sunrise'] ?? '06:12 AM' ); ?></strong> | 
                  🌇 Sunset: <strong><?php echo esc_html( $panchangam_data['astronomy']['sunset'] ?? '06:05 PM' ); ?></strong>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $panchangam_data['timings']['abhijitMuhurtham'] ) ) : ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Abhijit Muhurtham
                </div>
                <div style="font-size: 0.95rem; font-weight: 600; color: #1E6B38; margin-top: 0.25rem;">
                  ✨ <?php echo esc_html( $panchangam_data['timings']['abhijitMuhurtham']['start'] . ' – ' . $panchangam_data['timings']['abhijitMuhurtham']['end'] ); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $panchangam_data['timings']['rahuKalam'] ) ) : ?>
              <div style="background: #FFF; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--clr-border, #E8DFD3);">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); text-transform: uppercase;">
                  Rahu Kalam (Inauspicious)
                </div>
                <div style="font-size: 0.95rem; font-weight: 600; color: #B3261E; margin-top: 0.25rem;">
                  ⚠️ <?php echo esc_html( $panchangam_data['timings']['rahuKalam']['start'] . ' – ' . $panchangam_data['timings']['rahuKalam']['end'] ); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <!-- ── Spiritual Significance & History ── -->
        <section class="festival-significance" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1rem 0;">
            🌟 <span class="djv-lang-field" data-lang="en">Significance &amp; Spiritual Importance</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">పండుగ విశిష్టత &amp; ఆధ్యాత్మిక ప్రాముఖ్యత</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">त्योहार का महत्व एवं आध्यात्मिक प्रभाव</span>
          </h2>
          
          <div style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text, #2A1F1D); margin-bottom: 1.5rem;">
            <div class="djv-lang-field" data-lang="en"><?php echo nl2br( esc_html( $content_en ) ); ?></div>
            <?php if ( $content_te ) : ?>
              <div class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo nl2br( esc_html( $content_te ) ); ?></div>
            <?php endif; ?>
            <?php if ( $content_hi ) : ?>
              <div class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo nl2br( esc_html( $content_hi ) ); ?></div>
            <?php endif; ?>
          </div>

          <?php if ( $history ) : ?>
            <div style="border-top: 1px solid var(--clr-border, #E8DFD3); padding-top: 1.25rem;">
              <h3 style="font-size: 1.15rem; color: var(--clr-accent, #C89432); margin: 0 0 0.5rem 0;">
                📜 <span class="djv-lang-field" data-lang="en">Historical Background &amp; Puranic Origins</span>
                <span class="djv-lang-field" data-lang="te" style="display:none;">పురాణ గాథ &amp; చారిత్రక నేపథ్యం</span>
                <span class="djv-lang-field" data-lang="hi" style="display:none;">पौराणिक कथा एवं ऐतिहासिक संदर्भ</span>
              </h3>
              <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.95rem;">
                <?php echo nl2br( esc_html( $history ) ); ?>
              </p>
            </div>
          <?php endif; ?>
        </section>

        <!-- ── Direct Link to Associated Pooja Guide CPT ── -->
        <?php if ( ! empty( $related_pooja_slugs ) ) :
          $first_pooja_slug = $related_pooja_slugs[0];
          $p_post = get_page_by_path( $first_pooja_slug, OBJECT, 'djv_pooja' );
          if ( $p_post ) :
        ?>
          <div class="pooja-cta-card" style="background: linear-gradient(135deg, #7A2419 0%, #5B160E 100%); color: #FFF; border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem; box-shadow: var(--shadow-md, 0 4px 16px rgba(0,0,0,0.12));">
            <div style="flex: 1; min-width: 280px;">
              <div style="color: var(--clr-accent, #C89432); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.35rem;">
                🕉️ Dedicated Pooja Vidhanam Available
              </div>
              <h3 style="font-family: var(--font-heading, serif); font-size: 1.6rem; margin: 0 0 0.5rem 0; color: #FFF;">
                <?php echo esc_html( get_the_title( $p_post ) ); ?>
              </h3>
              <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem; margin: 0; line-height: 1.6;">
                Follow the authentic 12-step traditional procedure, Sankalpam, Kalasha Sthapana, and Naivedyam offerings.
              </p>
            </div>
            <a href="<?php echo esc_url( get_permalink( $p_post ) ); ?>" style="background: var(--clr-accent, #C89432); color: #2A1F1D; padding: 0.85rem 1.6rem; border-radius: 0.5rem; text-decoration: none; font-weight: 700; font-size: 0.95rem; white-space: nowrap; transition: opacity 0.2s;">
              View Complete Puja Vidhi →
            </a>
          </div>
        <?php endif; endif; ?>

        <!-- ── Puja Samagri & Naivedyam ── -->
        <?php if ( $samagri || $naivedyam ) : ?>
          <section class="festival-samagri-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
              📦 <span class="djv-lang-field" data-lang="en">Puja Samagri Checklist &amp; Sacred Naivedyam</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా సామగ్రి జాబితా &amp; నైవేద్యం</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा सामग्री सूची एवं पवित्र नैवेद्य</span>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
              <?php if ( $samagri ) : ?>
                <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.5rem;">
                  <h3 style="font-size: 1.05rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                    🌿 <?php esc_html_e( 'Required Puja Samagri:', 'djv-theme' ); ?>
                  </h3>
                  <div style="color: var(--clr-text-secondary, #55433C); line-height: 1.8; font-size: 0.95rem;">
                    <?php echo nl2br( esc_html( $samagri ) ); ?>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ( $naivedyam ) : ?>
                <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.5rem;">
                  <h3 style="font-size: 1.05rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.75rem 0;">
                    🍲 <?php esc_html_e( 'Traditional Naivedyam (Prasadam):', 'djv-theme' ); ?>
                  </h3>
                  <div style="color: var(--clr-text-secondary, #55433C); line-height: 1.8; font-size: 0.95rem;">
                    <?php echo nl2br( esc_html( $naivedyam ) ); ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Associated Mantras (Direct Relationships to Mantras CPT) ── -->
        <?php if ( ! empty( $related_mantra_slugs ) ) : ?>
          <section class="festival-mantras-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem;">
              <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0;">
                📿 <span class="djv-lang-field" data-lang="en">Associated Sacred Mantras &amp; Stotras</span>
                <span class="djv-lang-field" data-lang="te" style="display:none;">సంబంధిత పవిత్ర మంత్రాలు &amp; స్తోత్రాలు</span>
                <span class="djv-lang-field" data-lang="hi" style="display:none;">संबंधित पावन मंत्र एवं स्तोत्र</span>
              </h2>
              <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
                Browse All Mantras →
              </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 1.25rem;">
              <?php
              foreach ( $related_mantra_slugs as $m_slug ) :
                $m_post = get_page_by_path( $m_slug, OBJECT, 'djv_mantra' );
                if ( ! $m_post && strpos( $m_slug, 'mrityunjaya' ) !== false ) {
                  $alt = ( strpos( $m_slug, 'maha-' ) !== false ) ? str_replace( 'maha-', 'maha', $m_slug ) : str_replace( 'mahamrityunjaya', 'maha-mrityunjaya', $m_slug );
                  $m_post = get_page_by_path( $alt, OBJECT, 'djv_mantra' );
                }
                if ( $m_post ) :
                  $m_sans = get_post_meta( $m_post->ID, '_djv_sanskrit_text', true ) ?: get_post_meta( $m_post->ID, '_djv_original_text', true );
                  $m_tel  = get_post_meta( $m_post->ID, '_djv_telugu_title', true );
              ?>
                <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column;">
                  <h3 style="font-family: var(--font-heading, serif); font-size: 1.15rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.25rem 0;">
                    <a href="<?php echo esc_url( get_permalink( $m_post ) ); ?>" style="color: inherit; text-decoration: none;">
                      <?php echo esc_html( get_the_title( $m_post ) ); ?>
                    </a>
                  </h3>
                  <?php if ( $m_tel ) : ?>
                    <div style="font-family: var(--font-telugu, sans-serif); font-size: 0.9rem; color: var(--clr-accent, #C89432); margin-bottom: 0.5rem;">
                      <?php echo esc_html( $m_tel ); ?>
                    </div>
                  <?php endif; ?>
                  <?php if ( $m_sans ) : ?>
                    <div style="background: #FFF; padding: 0.75rem; border-radius: 0.5rem; font-family: 'Noto Sans Devanagari', serif; font-size: 0.95rem; color: var(--clr-primary, #7A2419); border-left: 3px solid var(--clr-primary, #7A2419); margin-bottom: 0.75rem; line-height: 1.6;">
                      <?php echo nl2br( esc_html( wp_trim_words( $m_sans, 18 ) ) ); ?>
                    </div>
                  <?php endif; ?>
                  <div style="margin-top: auto; padding-top: 0.5rem;">
                    <a href="<?php echo esc_url( get_permalink( $m_post ) ); ?>" style="font-size: 0.85rem; font-weight: 600; color: var(--clr-primary, #7A2419); text-decoration: none;">
                      Read Full Mantra &amp; Audio →
                    </a>
                  </div>
                </div>
              <?php endif; endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Vrat Rules, Do's & Don'ts ── -->
        <?php if ( $vrat_rules || $dos || $donts ) : ?>
          <section class="festival-rules-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
              ⚖️ <span class="djv-lang-field" data-lang="en">Vrat Rules, Do's &amp; Don'ts</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">వ్రత నియమాలు, చేయవలసినవి &amp; చేయకూడనివి</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">व्रत के नियम, क्या करें और क्या न करें</span>
            </h2>

            <?php if ( $vrat_rules ) : ?>
              <div style="background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); padding: 1rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <strong style="color: var(--clr-primary, #7A2419);">Fasting Guidelines:</strong>
                <p style="margin: 0.25rem 0 0 0; color: var(--clr-text-secondary, #55433C); line-height: 1.6; font-size: 0.95rem;">
                  <?php echo esc_html( $vrat_rules ); ?>
                </p>
              </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
              <?php if ( $dos ) : ?>
                <div style="background: #F1F8F3; border: 1px solid #C3E6CB; border-radius: 0.75rem; padding: 1.25rem;">
                  <h3 style="font-size: 1.05rem; color: #1E6B38; margin: 0 0 0.5rem 0;">
                    ✅ <?php esc_html_e( 'Do’s (ఆచరించవలసినవి / क्या करें):', 'djv-theme' ); ?>
                  </h3>
                  <p style="color: var(--clr-text-secondary, #55433C); margin: 0; line-height: 1.7; font-size: 0.92rem;">
                    <?php echo esc_html( $dos ); ?>
                  </p>
                </div>
              <?php endif; ?>

              <?php if ( $donts ) : ?>
                <div style="background: #FDF2F2; border: 1px solid #F5C6CB; border-radius: 0.75rem; padding: 1.25rem;">
                  <h3 style="font-size: 1.05rem; color: #B3261E; margin: 0 0 0.5rem 0;">
                    ❌ <?php esc_html_e( 'Don’ts (నిషేధించబడినవి / क्या न करें):', 'djv-theme' ); ?>
                  </h3>
                  <p style="color: var(--clr-text-secondary, #55433C); margin: 0; line-height: 1.7; font-size: 0.92rem;">
                    <?php echo esc_html( $donts ); ?>
                  </p>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Related Festivals ── -->
        <?php
        $related_festivals = new WP_Query([
          'post_type'      => 'djv_festival',
          'posts_per_page' => 3,
          'post__not_in'   => [ $post_id ],
          'post_status'    => 'publish',
        ]);

        if ( $related_festivals->have_posts() ) :
        ?>
          <section class="related-festivals-section" style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #E8DFD3);">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.75rem; color: var(--clr-primary, #7A2419); margin-bottom: 1.5rem;">
              <span class="djv-lang-field" data-lang="en">Other Major Festivals &amp; Vrats</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">ఇతర ముఖ్యమైన పండుగలు &amp; వ్రతాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">अन्य प्रमुख त्योहार एवं व्रत</span>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
              <?php while ( $related_festivals->have_posts() ) : $related_festivals->the_post(); ?>
                <?php get_template_part( 'template-parts/festival/card' ); ?>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          </section>
        <?php endif; ?>

      </article>
    <?php endwhile; endif; ?>

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
  }

  document.querySelectorAll('.djv-lang-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      applyLanguage(this.getAttribute('data-lang'));
    });
  });

  applyLanguage(currentLang);
});
</script>

<?php
get_footer();
