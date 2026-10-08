<?php
/**
 * Dharma Jyothi Vedika — Front Page Template
 *
 * 100% Dynamic WordPress Front Page.
 * Panchangam rendered via DJV Core / REST API ephemeris.
 * Content populated via WP_Query on WordPress Custom Post Types.
 *
 * @package DJV_Theme
 */

get_header();
?>

<!-- ════════════════════════════════════════════════════════════
     1. HERO SECTION
════════════════════════════════════════════════════════════ -->
<section class="hero" aria-labelledby="hero-heading">
  <div class="hero-bg" aria-hidden="true"></div>
  <div class="hero-pattern" aria-hidden="true"></div>
  <div class="hero-orb hero-orb--1" aria-hidden="true"></div>
  <div class="hero-orb hero-orb--2" aria-hidden="true"></div>
  <div class="hero-orb hero-orb--3" aria-hidden="true"></div>

  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge">
        <span aria-hidden="true">🕉</span>
        <span><?php esc_html_e( 'Hindu Panchangam & Devotional Platform', 'djv-theme' ); ?></span>
      </div>

      <h1 class="hero-title" id="hero-heading">
        <?php esc_html_e( "Today's Hindu Panchangam", 'djv-theme' ); ?>
      </h1>

      <p class="hero-desc">
        <?php esc_html_e( 'Accurate daily Panchangam, festivals, Muhurtham and devotional guidance for devotees across India. Grounded in tradition, built for modern life.', 'djv-theme' ); ?>
      </p>

      <div class="hero-tagline" aria-label="<?php esc_attr_e( 'Platform features', 'djv-theme' ); ?>">
        <span class="hero-tag"><?php esc_html_e( 'Panchangam', 'djv-theme' ); ?></span>
        <span class="hero-tag"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></span>
        <span class="hero-tag"><?php esc_html_e( 'Muhurtham', 'djv-theme' ); ?></span>
        <span class="hero-tag"><?php esc_html_e( 'Pooja', 'djv-theme' ); ?></span>
        <span class="hero-tag"><?php esc_html_e( 'Mantras', 'djv-theme' ); ?></span>
        <span class="hero-tag"><?php esc_html_e( 'Temples', 'djv-theme' ); ?></span>
      </div>

      <div class="hero-actions">
        <a href="<?php echo esc_url( home_url( '/panchangam/' ) ); ?>" class="btn-hero-primary" id="hero-panchangam-btn">
          <span aria-hidden="true">📅</span>
          <?php esc_html_e( 'View Full Panchangam', 'djv-theme' ); ?>
        </a>
        <button type="button" class="btn-hero-secondary global-location-pill" id="hero-location-btn">
          <span aria-hidden="true">📍</span>
          <?php esc_html_e( 'Change Location', 'djv-theme' ); ?>
        </button>
      </div>
    </div>

    <!-- Reusable Quick Panchangam Card in Hero -->
    <?php get_template_part( 'template-parts/panchangam/hero-card' ); ?>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     2. QUICK ACCESS NAVIGATION
════════════════════════════════════════════════════════════ -->
<?php get_template_part( 'template-parts/cards/quick-access' ); ?>

<!-- ════════════════════════════════════════════════════════════
     3. TODAY'S COMPLETE PANCHANGAM (LIVE CALCULATION)
════════════════════════════════════════════════════════════ -->
<?php get_template_part( 'template-parts/panchangam/homepage-grid' ); ?>

<!-- ════════════════════════════════════════════════════════════
     4. UPCOMING FESTIVALS SECTION (DYNAMIC CPT)
════════════════════════════════════════════════════════════ -->
<section class="festivals-section" aria-labelledby="festivals-heading" style="padding:4.5rem 0;">
  <div class="container" style="position:relative;z-index:1;">
    <div class="section-header">
      <div class="section-badge" style="background:rgba(200,148,50,0.15);color:var(--clr-accent);">🎊 <?php esc_html_e( 'Upcoming', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="festivals-heading"><?php esc_html_e( 'Hindu Festivals & Holy Days', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Festival dates calculated dynamically from Panchangam rules and lunar tithis.', 'djv-theme' ); ?>
      </p>
    </div>

    <div class="festival-cards" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1.5rem;">
      <?php
      $fest_query = new WP_Query([
        'post_type'      => 'djv_festival',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value',
        'meta_key'       => '_djv_festival_date',
        'order'          => 'ASC',
      ]);

      if ( $fest_query->have_posts() ) :
        while ( $fest_query->have_posts() ) : $fest_query->the_post();
          get_template_part( 'template-parts/festival/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'No festivals scheduled at this time.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/festivals/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;border:1px solid rgba(200,148,50,0.5);color:var(--clr-accent);border-radius:9999px;font-weight:600;text-decoration:none;transition:all 0.2s;">
        <?php esc_html_e( 'View All Festivals & Vrats', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     5. AUSPICIOUS MUHURTHAM SECTION (DYNAMIC CPT)
════════════════════════════════════════════════════════════ -->
<section class="muhurtham-section" aria-labelledby="muhurtham-heading" style="padding:4.5rem 0;background:#FFF9F0;">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">⏰ <?php esc_html_e( 'Shubh Timings', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="muhurtham-heading"><?php esc_html_e( 'Auspicious Muhurtham Finder', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Find auspicious Lagna, Tithi, and Nakshatra alignments for life milestones.', 'djv-theme' ); ?>
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1.5rem;">
      <?php
      $muh_query = new WP_Query([
        'post_type'      => 'djv_muhurtham',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
      ]);

      if ( $muh_query->have_posts() ) :
        while ( $muh_query->have_posts() ) : $muh_query->the_post();
          get_template_part( 'template-parts/muhurtham/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'No muhurtham entries found.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/muhurtham/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;background:var(--clr-primary);color:#FFF;border-radius:9999px;font-weight:600;text-decoration:none;box-shadow:var(--shadow-sm);">
        <?php esc_html_e( 'Explore All Muhurtham Categories', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     6. POOJA GUIDES SECTION (DYNAMIC CPT)
════════════════════════════════════════════════════════════ -->
<section class="pooja-section" aria-labelledby="pooja-heading" style="padding:4.5rem 0;">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">🪔 <?php esc_html_e( 'Guides', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="pooja-heading"><?php esc_html_e( 'Vedic Pooja Guides & Vidhi', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Step-by-step procedures, samagri lists, mantras, and naivedyam for every holy ritual.', 'djv-theme' ); ?>
      </p>
    </div>

    <div class="pooja-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1.5rem;">
      <?php
      $pooja_query = new WP_Query([
        'post_type'      => 'djv_pooja',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
      ]);

      if ( $pooja_query->have_posts() ) :
        while ( $pooja_query->have_posts() ) : $pooja_query->the_post();
          get_template_part( 'template-parts/pooja/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'No pooja guides published yet.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/pooja/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;border:1px solid var(--clr-primary);color:var(--clr-primary);border-radius:9999px;font-weight:600;text-decoration:none;">
        <?php esc_html_e( 'View All Pooja Guides', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     7. SACRED MANTRAS & STOTRAMS (DYNAMIC CPT)
════════════════════════════════════════════════════════════ -->
<section class="mantras-section" aria-labelledby="mantras-heading" style="padding:4.5rem 0;background:#FFF9F0;">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">📿 <?php esc_html_e( 'Sacred Chants', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="mantras-heading"><?php esc_html_e( 'Sacred Mantras & Slokas', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Ancient Vedic chants with Sanskrit original, Telugu script, transliteration, and spiritual meanings.', 'djv-theme' ); ?>
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1.5rem;">
      <?php
      $mantra_query = new WP_Query([
        'post_type'      => 'djv_mantra',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
      ]);

      if ( $mantra_query->have_posts() ) :
        while ( $mantra_query->have_posts() ) : $mantra_query->the_post();
          get_template_part( 'template-parts/mantra/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'No mantras published yet.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/mantras/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;background:var(--clr-primary);color:#FFF;border-radius:9999px;font-weight:600;text-decoration:none;">
        <?php esc_html_e( 'Explore All Mantras & Stotrams', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     8. TEMPLES OF BHARAT (DYNAMIC CPT)
════════════════════════════════════════════════════════════ -->
<section class="temples-section" aria-labelledby="temples-heading" style="padding:4.5rem 0;">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">🛕 <?php esc_html_e( 'Tirtha & Kshetra', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="temples-heading"><?php esc_html_e( 'Temples of Bharat', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Explore sacred shrines, darshan timings, history, and festive traditions.', 'djv-theme' ); ?>
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1.5rem;">
      <?php
      $temple_query = new WP_Query([
        'post_type'      => 'djv_temple',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
      ]);

      if ( $temple_query->have_posts() ) :
        while ( $temple_query->have_posts() ) : $temple_query->the_post();
          get_template_part( 'template-parts/temple/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'No temples listed yet.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/temples/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;border:1px solid var(--clr-primary);color:var(--clr-primary);border-radius:9999px;font-weight:600;text-decoration:none;">
        <?php esc_html_e( 'Discover All Temples', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     9. VEDIC SERVICES & CONSULTATION (DYNAMIC CPT)
════════════════════════════════════════════════════════════ -->
<section class="services-section" aria-labelledby="services-heading" style="padding:4.5rem 0;background:#FFF9F0;">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">⭐ <?php esc_html_e( 'Pandit & Consultation', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="services-heading"><?php esc_html_e( 'Vedic Services & Seva', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Personalized homams, pooja bookings, astrology consultations, and Vastu guidance.', 'djv-theme' ); ?>
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1.5rem;">
      <?php
      $service_query = new WP_Query([
        'post_type'      => 'djv_service',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
      ]);

      if ( $service_query->have_posts() ) :
        while ( $service_query->have_posts() ) : $service_query->the_post();
          get_template_part( 'template-parts/service/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'No services published yet.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;background:var(--clr-primary);color:#FFF;border-radius:9999px;font-weight:600;text-decoration:none;">
        <?php esc_html_e( 'View All Vedic Services', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════
     10. VEDIC ARTICLES & WISDOM (WORDPRESS POSTS)
════════════════════════════════════════════════════════════ -->
<section class="articles-section" aria-labelledby="articles-heading" style="padding:4.5rem 0;">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">📰 <?php esc_html_e( 'Vedic Insights', 'djv-theme' ); ?></div>
      <h2 class="section-title" id="articles-heading"><?php esc_html_e( 'Sacred Knowledge & Articles', 'djv-theme' ); ?></h2>
      <p class="section-desc">
        <?php esc_html_e( 'Articles exploring Hindu philosophy, Jyotish, traditions, and spiritual observances.', 'djv-theme' ); ?>
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));gap:1.5rem;">
      <?php
      $article_query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
      ]);

      if ( $article_query->have_posts() ) :
        while ( $article_query->have_posts() ) : $article_query->the_post();
          get_template_part( 'template-parts/article/card' );
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--clr-text-muted);">
          <?php esc_html_e( 'Welcome to Dharma Jyothi Vedika. Publish your first Vedic article in WordPress Admin to display here.', 'djv-theme' ); ?>
        </p>
      <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem;">
      <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;border:1px solid var(--clr-primary);color:var(--clr-primary);border-radius:9999px;font-weight:600;text-decoration:none;">
        <?php esc_html_e( 'Browse All Articles & Guides', 'djv-theme' ); ?> →
      </a>
    </div>
  </div>
</section>

<?php
get_footer();
