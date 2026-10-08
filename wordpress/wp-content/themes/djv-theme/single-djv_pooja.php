<?php
/**
 * Single Pooja Guide Template (djv_pooja)
 *
 * Full-featured traditional 12-step Pooja Vidhanam view.
 * Features trilingual support (EN, TE, HI), 15-step foundational protocol,
 * authentic Sanskrit Sankalpam and Dhyana shlokas, samagri checklist,
 * and direct links to Mantras and Festivals CPTs.
 *
 * @package DJV_Theme
 */

get_header();

$post_id         = get_the_ID();
$title_en        = get_post_meta( $post_id, '_djv_title_en', true ) ?: get_the_title();
$title_te        = get_post_meta( $post_id, '_djv_title_te', true );
$title_hi        = get_post_meta( $post_id, '_djv_title_hi', true );

$current_lang    = function_exists( 'djv_get_current_language' ) ? djv_get_current_language() : 'en';
$intro_en        = get_post_meta( $post_id, '_djv_intro_en', true ) ?: apply_filters( 'the_content', get_the_content() );
$intro_te        = get_post_meta( $post_id, '_djv_intro_te', true );
$intro_hi        = get_post_meta( $post_id, '_djv_intro_hi', true );

$duration        = get_post_meta( $post_id, '_djv_duration', true );
$samagri         = get_post_meta( $post_id, '_djv_samagri', true );

// 12 Canonical Steps
$preparation      = get_post_meta( $post_id, '_djv_preparation', true );
$sankalpam        = get_post_meta( $post_id, '_djv_sankalpam', true );
$kalasha_sthapana = get_post_meta( $post_id, '_djv_kalasha_sthapana', true );
$avahanam         = get_post_meta( $post_id, '_djv_avahanam', true );
$dhyana           = get_post_meta( $post_id, '_djv_dhyana', true );
$main_puja        = get_post_meta( $post_id, '_djv_main_puja', true );
$mantra_japa      = get_post_meta( $post_id, '_djv_mantra_japa', true );
$naivedyam        = get_post_meta( $post_id, '_djv_naivedyam', true );
$aarti            = get_post_meta( $post_id, '_djv_aarti', true );
$prarthana        = get_post_meta( $post_id, '_djv_prarthana', true );
$prasadam         = get_post_meta( $post_id, '_djv_prasadam', true );
$visarjan         = get_post_meta( $post_id, '_djv_visarjan', true );
$vrat_rules       = get_post_meta( $post_id, '_djv_vrat_rules', true );
$faq              = get_post_meta( $post_id, '_djv_faq', true ) ?: [];

$related_mantras_slugs   = get_post_meta( $post_id, '_djv_related_mantras', true ) ?: [];
$related_festivals_slugs = get_post_meta( $post_id, '_djv_related_festivals', true ) ?: [];

$categories = wp_get_post_terms( $post_id, 'djv_pooja_cat', [ 'fields' => 'names' ] );
$deity      = wp_get_post_terms( $post_id, 'djv_deity', [ 'fields' => 'names' ] );

// Universal "How to Start Puja" helper
$how_to_start = function_exists( 'djv_get_generic_how_to_start_puja' ) ? djv_get_generic_how_to_start_puja() : null;
?>

<div class="single-pooja-wrapper" style="padding: 2rem 0 5rem 0; background: var(--clr-bg, #FDFBF7);">
  <div class="container" style="max-width: 1040px;">

    <!-- ── Breadcrumb ── -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted, #7A6F68); margin-bottom: 1.25rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <a href="<?php echo esc_url( home_url( '/pooja/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Pooja Guides', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary, #7A2419); font-weight: 600;"><?php echo esc_html( $title_en ); ?></span>
    </nav>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="pooja-<?php the_ID(); ?>" <?php post_class( 'single-pooja-article' ); ?>>

        <!-- ── Header Banner with Language Switcher ── -->
        <header style="margin-bottom: 2.25rem; border-bottom: 1px solid var(--clr-border, #E8DFD3); padding-bottom: 1.75rem;">
          <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
              <span style="background: rgba(200,148,50,0.12); color: var(--clr-accent, #C89432); font-weight: 700; font-size: 0.78rem; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase;">
                🪔 <?php esc_html_e( 'Vedic Pooja Vidhi', 'djv-theme' ); ?>
              </span>
              <?php if ( $duration ) : ?>
                <span style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); color: var(--clr-primary, #7A2419); font-weight: 600; font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                  ⏱ <?php echo esc_html( $duration ); ?>
                </span>
              <?php endif; ?>
              <?php if ( ! empty( $deity[0] ) ) : ?>
                <span style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); color: var(--clr-text-muted, #7A6F68); font-size: 0.78rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                  🕉️ <?php echo esc_html( $deity[0] ); ?>
                </span>
              <?php endif; ?>
            </div>

            <!-- Language Switcher Bar -->
            <div class="djv-lang-bar" style="background: #FFF; border: 1.5px solid var(--clr-border, #E8DFD3); border-radius: 9999px; padding: 0.25rem 0.4rem; display: inline-flex; align-items: center; gap: 0.25rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
              <span style="font-size: 0.75rem; font-weight: 700; color: var(--clr-text-muted, #7A6F68); padding: 0 0.4rem; text-transform: uppercase;">🌐 Script:</span>
              <button type="button" class="djv-lang-btn <?php echo $current_lang === 'en' ? 'active' : ''; ?>" data-lang="en" style="border:none;background:<?php echo $current_lang === 'en' ? 'var(--clr-primary, #7A2419)' : 'transparent'; ?>;color:<?php echo $current_lang === 'en' ? '#FFF' : 'var(--clr-text, #2A1F1D)'; ?>;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;">English</button>
              <button type="button" class="djv-lang-btn <?php echo $current_lang === 'te' ? 'active' : ''; ?>" data-lang="te" style="border:none;background:<?php echo $current_lang === 'te' ? 'var(--clr-primary, #7A2419)' : 'transparent'; ?>;color:<?php echo $current_lang === 'te' ? '#FFF' : 'var(--clr-text, #2A1F1D)'; ?>;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:var(--font-telugu, sans-serif);">తెలుగు</button>
              <button type="button" class="djv-lang-btn <?php echo $current_lang === 'hi' ? 'active' : ''; ?>" data-lang="hi" style="border:none;background:<?php echo $current_lang === 'hi' ? 'var(--clr-primary, #7A2419)' : 'transparent'; ?>;color:<?php echo $current_lang === 'hi' ? '#FFF' : 'var(--clr-text, #2A1F1D)'; ?>;padding:0.35rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari', serif;">हिन्दी</button>
            </div>
          </div>

          <!-- Trilingual Titles -->
          <h1 style="font-family: var(--font-heading, serif); font-size: 2.6rem; color: var(--clr-primary, #7A2419); line-height: 1.25; margin: 0 0 0.5rem 0;">
            <span class="djv-lang-field" data-lang="en"><?php echo esc_html( $title_en ); ?></span>
            <span class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo esc_html( $title_te ?: $title_en ); ?></span>
            <span class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo esc_html( $title_hi ?: $title_en ); ?></span>
          </h1>
        </header>

        <!-- ── Universal 15-Step Overview & Traditional Disclaimer ── -->
        <section class="puja-universal-intro" style="background: #FFF; border: 1.5px solid var(--clr-accent, #C89432); border-radius: 1rem; padding: 1.75rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.3rem; color: var(--clr-primary, #7A2419); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
              📜 <span class="djv-lang-field" data-lang="en">How to Start a Hindu Puja (15 Canonical Steps)</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా ప్రారంభ ప్రాథమిక నియమాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा आरंभ करने के शास्त्रीय नियम</span>
            </h2>
            <span style="font-size: 0.8rem; background: #FFF9F0; color: var(--clr-accent, #C89432); padding: 0.25rem 0.65rem; border-radius: 9999px; font-weight: 700;">
              Universal Vedic Rule
            </span>
          </div>

          <!-- Mandatory Traditional Disclaimer -->
          <div style="background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); padding: 0.75rem 1rem; border-radius: 0.4rem; margin-bottom: 1.25rem; font-size: 0.9rem; color: var(--clr-text-secondary, #55433C); font-style: italic;">
            ⚠️ <span class="djv-lang-field" data-lang="en">Procedure may vary according to family tradition, sampradaya and regional practice.</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా విధానం కుటుంబ ఆచారం, సాంప్రదాయం మరియు ప్రాంతీయ నియమాల ప్రకారం మారవచ్చు.</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">पूजा की विधि पारिवारिक परम्परा, सम्प्रदाय और क्षेत्रीय रीति-रिवाजों के अनुसार भिन्न हो सकती है।</span>
          </div>

          <!-- Compact 15 Step Checklist -->
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 0.65rem; font-size: 0.86rem; color: var(--clr-text, #2A1F1D);">
            <div>1. Clean the Puja Sthalam (పూజా స్థల శుద్ధి)</div>
            <div>2. Bathe &amp; Wear Clean Clothes (స్నానం)</div>
            <div>3. Prepare the Puja Samagri (సామగ్రి సిద్ధం)</div>
            <div>4. Place the Deity (ఆసనం / ప్రతిష్ఠ)</div>
            <div>5. Light the Lamp (దీపారాధన)</div>
            <div>6. Light Incense (ధూపం)</div>
            <div>7. Perform Achamana (ఆచమనం)</div>
            <div>8. Take Sankalpa (సంకల్పం)</div>
            <div>9. Invoke the Deity (ఆవాహనం)</div>
            <div>10. Perform Prescribed Puja (షోడశోపచారాలు)</div>
            <div>11. Chant Associated Mantra (మంత్ర జపం)</div>
            <div>12. Offer Naivedyam (నైవేద్యం)</div>
            <div>13. Perform Aarti (మంగళ హారతి)</div>
            <div>14. Offer Prarthana (ప్రార్థన &amp; ప్రదక్షిణ)</div>
            <div>15. Take Prasadam (తీర్థ ప్రసాద స్వీకరణ)</div>
          </div>
        </section>

        <!-- ── Section: Puja Introduction ── -->
        <section class="puja-intro-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
          <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1rem 0;">
            🌟 <span class="djv-lang-field" data-lang="en">Introduction &amp; Spiritual Essence</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">పరిచయం &amp; పూజా విశిష్టత</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">परिचय एवं आध्यात्मिक महत्व</span>
          </h2>
          <div style="font-size: 1.05rem; line-height: 1.8; color: var(--clr-text, #2A1F1D);">
            <div class="djv-lang-field" data-lang="en"><?php echo nl2br( esc_html( $intro_en ) ); ?></div>
            <?php if ( $intro_te ) : ?>
              <div class="djv-lang-field" data-lang="te" style="display:none; font-family: var(--font-telugu, sans-serif);"><?php echo nl2br( esc_html( $intro_te ) ); ?></div>
            <?php endif; ?>
            <?php if ( $intro_hi ) : ?>
              <div class="djv-lang-field" data-lang="hi" style="display:none; font-family: 'Noto Sans Devanagari', serif;"><?php echo nl2br( esc_html( $intro_hi ) ); ?></div>
            <?php endif; ?>
          </div>
        </section>

        <!-- ── Section: Puja Samagri Checklist ── -->
        <?php if ( $samagri ) : ?>
          <section class="puja-samagri-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.5rem; color: var(--clr-primary, #7A2419); margin: 0 0 1rem 0;">
              📦 <span class="djv-lang-field" data-lang="en">Required Puja Samagri Checklist</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">పూజా సామగ్రి జాబితా</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">आवश्यक पूजा सामग्री चेकलिस्ट</span>
            </h2>
            <div style="background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.5rem; font-size: 0.98rem; line-height: 1.9; color: var(--clr-text-secondary, #55433C);">
              <?php echo nl2br( esc_html( $samagri ) ); ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Section: 12 Canonical Steps ── -->
        <section class="puja-steps-section" style="margin-bottom: 3rem;">
          <h2 style="font-family: var(--font-heading, serif); font-size: 1.85rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.5rem 0;">
            🪔 <span class="djv-lang-field" data-lang="en">12-Step Traditional Puja Vidhanam</span>
            <span class="djv-lang-field" data-lang="te" style="display:none;">12 సంప్రదాయ పూజా విధానాలు</span>
            <span class="djv-lang-field" data-lang="hi" style="display:none;">12 शास्त्रीय पूजा विधान चरण</span>
          </h2>

          <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- Step 1: Preparation -->
            <?php if ( $preparation ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 1</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Preparation &amp; Asanam (శుచి &amp; పీఠ ప్రతిష్ఠ / शुद्धि एवं आसन)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $preparation ) ); ?>
                </p>
              </div>
            <?php endif; ?>

            <!-- Step 2: Sankalpam -->
            <?php if ( $sankalpam ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-accent, #C89432); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 2</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Sankalpam (సంకల్పం / संकल्प)
                </h3>
                <div style="background: #FFF9F0; border-radius: 0.5rem; padding: 1rem; font-family: 'Noto Sans Devanagari', serif; font-size: 1.05rem; color: var(--clr-primary, #7A2419); line-height: 1.8; margin-bottom: 0.5rem;">
                  <?php echo nl2br( esc_html( $sankalpam ) ); ?>
                </div>
                <small style="color: var(--clr-text-muted, #7A6F68); font-size: 0.85rem;">
                  Hold water, flower, and akshatas in the right palm while reciting your Gotra, Nakshatra, and Name.
                </small>
              </div>
            <?php endif; ?>

            <!-- Step 3: Kalasha Sthapana -->
            <?php if ( $kalasha_sthapana ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 3</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Kalasha Sthapana (కలశ స్థాపన / कलश स्थापना)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $kalasha_sthapana ) ); ?>
                </p>
              </div>
            <?php endif; ?>

            <!-- Step 4: Avahanam -->
            <?php if ( $avahanam ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 4</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Avahanam &amp; Prana Pratishtha (ఆవాహనం &amp; ప్రాణ ప్రతిష్ఠ / आवाहन)
                </h3>
                <div style="background: #FFF9F0; border-radius: 0.5rem; padding: 1rem; font-family: 'Noto Sans Devanagari', serif; font-size: 1.05rem; color: var(--clr-primary, #7A2419); line-height: 1.8;">
                  <?php echo nl2br( esc_html( $avahanam ) ); ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Step 5: Dhyana -->
            <?php if ( $dhyana ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-accent, #C89432); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 5</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Dhyana Shlokas (ధ్యానం / ध्यान)
                </h3>
                <div style="background: #FFF9F0; border-radius: 0.5rem; padding: 1rem; font-family: 'Noto Sans Devanagari', serif; font-size: 1.05rem; color: var(--clr-primary, #7A2419); line-height: 1.8;">
                  <?php echo nl2br( esc_html( $dhyana ) ); ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Step 6: Main Puja -->
            <?php if ( $main_puja ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 6</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Main Puja &amp; Shodashopachara (షోడశోపచార పూజ &amp; అష్టోత్తర శతనామ పూజ / मुख्य पूजा)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $main_puja ) ); ?>
                </p>
              </div>
            <?php endif; ?>

            <!-- Step 7: Mantra Japa -->
            <?php if ( $mantra_japa ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-accent, #C89432); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 7</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Mantra Japa &amp; Stotra Patha (మంత్ర జపం / मंत्र जप)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0 0 1rem 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $mantra_japa ) ); ?>
                </p>
                <?php if ( ! empty( $related_mantras_slugs ) ) : ?>
                  <div style="border-top: 1px solid var(--clr-border, #E8DFD3); padding-top: 0.75rem; display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--clr-primary, #7A2419);">Linked Mantras:</span>
                    <?php foreach ( $related_mantras_slugs as $ms ) :
                      $mp = get_page_by_path( $ms, OBJECT, 'djv_mantra' );
                      if ( $mp ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $mp ) ); ?>" style="font-size: 0.82rem; background: #FFF9F0; border: 1px solid var(--clr-border, #E8DFD3); color: var(--clr-primary, #7A2419); padding: 0.2rem 0.5rem; border-radius: 0.25rem; text-decoration: none; font-weight: 600;">
                          <?php echo esc_html( get_the_title( $mp ) ); ?> →
                        </a>
                    <?php endif; endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <!-- Step 8: Naivedyam -->
            <?php if ( $naivedyam ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 8</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Naivedyam &amp; Food Offering (నైవేద్య సమర్పణ / नैवेद्य अर्पण)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $naivedyam ) ); ?>
                </p>
              </div>
            <?php endif; ?>

            <!-- Step 9: Aarti -->
            <?php if ( $aarti ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-accent, #C89432); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 9</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Aarti &amp; Karpura Neerajanam (మంగళ హారతి / आरती)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $aarti ) ); ?>
                </p>
              </div>
            <?php endif; ?>

            <!-- Step 10: Prarthana -->
            <?php if ( $prarthana ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 10</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Prarthana &amp; Circumambulation (ప్రార్థన &amp; ఆత్మప్రదక్షిణ / प्रार्थना)
                </h3>
                <div style="background: #FFF9F0; border-radius: 0.5rem; padding: 1rem; font-family: 'Noto Sans Devanagari', serif; font-size: 1.05rem; color: var(--clr-primary, #7A2419); line-height: 1.8;">
                  <?php echo nl2br( esc_html( $prarthana ) ); ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Step 11: Prasadam -->
            <?php if ( $prasadam ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-primary, #7A2419); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 11</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Theertham &amp; Prasadam Acceptance (తీర్థ ప్రసాద స్వీకరణ / प्रसाद वितरण)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $prasadam ) ); ?>
                </p>
              </div>
            <?php endif; ?>

            <!-- Step 12: Visarjan -->
            <?php if ( $visarjan ) : ?>
              <div class="puja-step-card" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-left: 5px solid var(--clr-accent, #C89432); border-radius: 0.75rem; padding: 1.5rem;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent, #C89432); text-transform: uppercase;">Step 12</div>
                <h3 style="font-family: var(--font-heading, serif); font-size: 1.25rem; color: var(--clr-primary, #7A2419); margin: 0.25rem 0 0.75rem 0;">
                  Visarjan / Udvasana (ఉద్వాసన / విసర్జన / विसर्जन)
                </h3>
                <p style="color: var(--clr-text-secondary, #55433C); line-height: 1.7; margin: 0; font-size: 0.98rem;">
                  <?php echo nl2br( esc_html( $visarjan ) ); ?>
                </p>
              </div>
            <?php endif; ?>

          </div>
        </section>

        <!-- ── Section: Vrat Rules & Guidelines ── -->
        <?php if ( $vrat_rules ) : ?>
          <section class="puja-vrat-rules" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.45rem; color: var(--clr-primary, #7A2419); margin: 0 0 1rem 0;">
              ⚖️ <span class="djv-lang-field" data-lang="en">Vrat Rules &amp; Observance Code</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">వ్రత నియమాలు &amp; పాటించవలసిన పద్ధతులు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">व्रत के नियम एवं सावधानियां</span>
            </h2>
            <div style="background: #FFF9F0; border-left: 4px solid var(--clr-accent, #C89432); padding: 1.25rem; border-radius: 0.5rem; font-size: 0.98rem; line-height: 1.8; color: var(--clr-text-secondary, #55433C);">
              <?php echo nl2br( esc_html( $vrat_rules ) ); ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Section: FAQ Accordion ── -->
        <?php if ( ! empty( $faq ) ) : ?>
          <section class="puja-faq-section" style="background: #FFF; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 1rem; padding: 2rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.06));">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.45rem; color: var(--clr-primary, #7A2419); margin: 0 0 1.25rem 0;">
              ❓ <span class="djv-lang-field" data-lang="en">Frequently Asked Questions (FAQ)</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">తరచుగా అడిగే ప్రశ్నలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">अक्सर पूछे जाने वाले प्रश्न</span>
            </h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
              <?php foreach ( $faq as $item ) : ?>
                <div style="background: #FDFBF7; border: 1px solid var(--clr-border, #E8DFD3); border-radius: 0.75rem; padding: 1.25rem;">
                  <h3 style="font-size: 1.05rem; color: var(--clr-primary, #7A2419); margin: 0 0 0.5rem 0;">
                    <?php echo esc_html( $item['q'] ); ?>
                  </h3>
                  <p style="margin: 0; color: var(--clr-text-secondary, #55433C); line-height: 1.6; font-size: 0.92rem;">
                    <?php echo esc_html( $item['a'] ); ?>
                  </p>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <!-- ── Section: Related Festivals (Internal Graph) ── -->
        <?php if ( ! empty( $related_festivals_slugs ) ) : ?>
          <section class="puja-related-festivals" style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid var(--clr-border, #E8DFD3);">
            <h2 style="font-family: var(--font-heading, serif); font-size: 1.7rem; color: var(--clr-primary, #7A2419); margin-bottom: 1.25rem;">
              🎊 <span class="djv-lang-field" data-lang="en">Related Sacred Festivals &amp; Vrats</span>
              <span class="djv-lang-field" data-lang="te" style="display:none;">సంబంధిత పండుగలు &amp; వ్రతాలు</span>
              <span class="djv-lang-field" data-lang="hi" style="display:none;">संबंधित पावन पर्व एवं व्रत</span>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
              <?php foreach ( $related_festivals_slugs as $fs ) :
                $fp = get_page_by_path( $fs, OBJECT, 'djv_festival' );
                if ( $fp ) :
                  $post = $fp; setup_postdata( $post );
                  get_template_part( 'template-parts/festival/card' );
                  wp_reset_postdata();
                endif;
              endforeach; ?>
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
