<?php
/**
 * ============================================================
 * DJV Admin Dashboard
 * File: wordpress/plugins/djv-core/includes/class-djv-admin.php
 *
 * WordPress admin panel for the DJV plugin.
 * Features:
 * - Panchangam engine status dashboard
 * - Cache management (view, clear, pre-warm)
 * - Manual validation UI
 * - Festival calendar overview
 * - API endpoint test tool
 * - System requirements check
 * ============================================================
 *
 * @package DJV_Core
 * @since   1.0.0
 * @license GPL-2.0+
 */

defined('ABSPATH') || exit;

class DJV_Admin {

    /**
     * Menu slug.
     */
    const MENU_SLUG = 'djv-dashboard';

    /**
     * Constructor — registers all admin hooks.
     */
    /**
     * Output schema reference (from live engine testing 2026-10-03):
     * d.meta           — engineVersion, ruleVersion, generatedAt
     * d.vara           — id, name ("Shanivara"), nameTe, en ("Saturday"), ruler
     * d.tithi          — id, displayId, name, nameTe, paksha, pakshaId
     * d.nakshatra      — {nakshatra: {id,name,nameTe,deity,ruler}, pada, degreeInNakshatra}
     * d.yoga           — id, name, nameTe
     * d.karana         — id, name, nameTe
     * d.solar          — sunrise (UTC ISO), sunset (UTC ISO), solarNoon (UTC ISO)
     * d.timings        — rahuKalam, yamagandam, gulikaKalam, abhijitMuhurtham {start,end}
     */
    public function __construct() {
        add_action( 'admin_menu',            [ $this, 'register_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'wp_ajax_djv_clear_cache',              [ $this, 'ajax_clear_cache' ] );
        add_action( 'wp_ajax_djv_test_engine',              [ $this, 'ajax_test_engine' ] );
        add_action( 'wp_ajax_djv_precalculate',             [ $this, 'ajax_precalculate' ] );
        add_action( 'wp_ajax_djv_test_api_endpoint',        [ $this, 'ajax_test_api_endpoint' ] );
        add_action( 'wp_ajax_djv_update_submission_status', [ $this, 'ajax_update_submission_status' ] );
        add_action( 'wp_ajax_djv_retry_submission_calc',    [ $this, 'ajax_retry_submission_calc' ] );
        add_action( 'wp_ajax_djv_delete_submission',        [ $this, 'ajax_delete_submission' ] );
        add_filter( 'plugin_action_links_' . DJV_PLUGIN_BASENAME, [ $this, 'plugin_action_links' ] );
    }

    /**
     * Register the admin menu and submenu pages.
     */
    public function register_admin_menu() {
        add_menu_page(
            __( 'Dharma Jyothi Vedika', 'djv-core' ),
            __( 'DJV Platform', 'djv-core' ),
            'manage_options',
            self::MENU_SLUG,
            [ $this, 'render_dashboard' ],
            $this->get_menu_icon(),
            30
        );

        // Dedicated DJV Muhurtham Submissions Admin Menu
        add_menu_page(
            __( 'DJV Muhurtham Submissions', 'djv-core' ),
            __( 'DJV Muhurtham Submissions', 'djv-core' ),
            'manage_options',
            'djv-muhurtham-submissions',
            [ $this, 'render_submissions_dashboard' ],
            'dashicons-clipboard',
            31
        );

        add_submenu_page( self::MENU_SLUG, __( 'Dashboard', 'djv-core' ),       __( 'Dashboard', 'djv-core' ),       'manage_options', self::MENU_SLUG,           [ $this, 'render_dashboard' ] );
        add_submenu_page( self::MENU_SLUG, __( 'Muhurtham Submissions', 'djv-core' ), __( 'Submissions Registry', 'djv-core' ), 'manage_options', 'djv-muhurtham-submissions', [ $this, 'render_submissions_dashboard' ] );
        add_submenu_page( self::MENU_SLUG, __( 'Muhurtham Directory', 'djv-core' ), __( 'Muhurtham Directory', 'djv-core' ), 'manage_options', 'djv-muhurtham',     [ $this, 'render_muhurtham_manager' ] );
        add_submenu_page( self::MENU_SLUG, __( 'Cache Manager', 'djv-core' ),   __( 'Cache Manager', 'djv-core' ),   'manage_options', 'djv-cache',               [ $this, 'render_cache_manager' ] );
        add_submenu_page( self::MENU_SLUG, __( 'Engine Validator', 'djv-core' ),__( 'Engine Validator', 'djv-core' ),'manage_options', 'djv-validator',           [ $this, 'render_validator' ] );
        add_submenu_page( self::MENU_SLUG, __( 'API Test Tool', 'djv-core' ),   __( 'API Test Tool', 'djv-core' ),   'manage_options', 'djv-api-test',            [ $this, 'render_api_test' ] );
        add_submenu_page( self::MENU_SLUG, __( 'Settings', 'djv-core' ),        __( 'Settings', 'djv-core' ),        'manage_options', 'djv-settings',            [ $this, 'render_settings' ] );
        add_submenu_page( self::MENU_SLUG, __( 'System Info', 'djv-core' ),     __( 'System Info', 'djv-core' ),     'manage_options', 'djv-system',              [ $this, 'render_system_info' ] );
    }

    /**
     * Enqueue admin CSS/JS only on DJV pages.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'djv' ) === false && strpos( $hook, 'toplevel_page_djv' ) === false ) {
            return;
        }

        // Inline styles — keeps plugin self-contained without a separate CSS file
        wp_add_inline_style( 'wp-admin', $this->admin_inline_css() );
    }

    /* ─── Dashboard ──────────────────────────────────────────────── */

    /**
     * Render the main admin dashboard.
     */
    public function render_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Access denied.', 'djv-core' ) );

        $stats = $this->get_dashboard_stats();
        ?>
        <div class="wrap djv-admin">
          <div class="djv-admin-header">
            <div class="djv-logo">
              <span class="djv-logo-icon">🕉</span>
              <div>
                <h1>Dharma Jyothi Vedika</h1>
                <p>ధర్మ జ్యోతి వేదిక — Admin Dashboard</p>
              </div>
            </div>
            <div class="djv-version-badge">v<?php echo esc_html( DJV_VERSION ); ?></div>
          </div>

          <!-- Status Alerts -->
          <?php $this->render_status_alerts( $stats ); ?>

          <!-- Stats Grid -->
          <div class="djv-stats-grid">
            <div class="djv-stat-card djv-stat-<?php echo esc_attr( $stats['engine_status'] ); ?>">
              <div class="djv-stat-icon">⚙️</div>
              <div class="djv-stat-label"><?php _e( 'Engine Status', 'djv-core' ); ?></div>
              <div class="djv-stat-value"><?php echo esc_html( ucfirst( $stats['engine_status'] ) ); ?></div>
            </div>
            <div class="djv-stat-card">
              <div class="djv-stat-icon">📅</div>
              <div class="djv-stat-label"><?php _e( 'Cached Dates', 'djv-core' ); ?></div>
              <div class="djv-stat-value"><?php echo esc_html( $stats['cached_dates'] ); ?></div>
            </div>
            <div class="djv-stat-card">
              <div class="djv-stat-icon">🎊</div>
              <div class="djv-stat-label"><?php _e( 'Festivals', 'djv-core' ); ?></div>
              <div class="djv-stat-value"><?php echo esc_html( $stats['festival_count'] ); ?></div>
            </div>
            <div class="djv-stat-card">
              <div class="djv-stat-icon">🪔</div>
              <div class="djv-stat-label"><?php _e( 'Pooja Guides', 'djv-core' ); ?></div>
              <div class="djv-stat-value"><?php echo esc_html( $stats['pooja_count'] ); ?></div>
            </div>
            <div class="djv-stat-card">
              <div class="djv-stat-icon">🕉</div>
              <div class="djv-stat-label"><?php _e( 'Mantras', 'djv-core' ); ?></div>
              <div class="djv-stat-value"><?php echo esc_html( $stats['mantra_count'] ); ?></div>
            </div>
            <div class="djv-stat-card">
              <div class="djv-stat-icon">🛕</div>
              <div class="djv-stat-label"><?php _e( 'Temples', 'djv-core' ); ?></div>
              <div class="djv-stat-value"><?php echo esc_html( $stats['temple_count'] ); ?></div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="djv-section">
            <h2><?php _e( 'Quick Actions', 'djv-core' ); ?></h2>
            <div class="djv-actions-grid">
              <button class="djv-action-btn" id="djv-test-engine" data-nonce="<?php echo wp_create_nonce('djv_test_engine'); ?>">
                ⚡ <?php _e( 'Test Engine (Today)', 'djv-core' ); ?>
              </button>
              <button class="djv-action-btn djv-action-warn" id="djv-clear-cache" data-nonce="<?php echo wp_create_nonce('djv_clear_cache'); ?>">
                🗑️ <?php _e( 'Clear All Cache', 'djv-core' ); ?>
              </button>
              <button class="djv-action-btn" id="djv-prewarm" data-nonce="<?php echo wp_create_nonce('djv_precalculate'); ?>">
                🔥 <?php _e( 'Pre-warm Cache', 'djv-core' ); ?>
              </button>
              <a href="<?php echo esc_url( admin_url( 'admin.php?page=djv-validator' ) ); ?>" class="djv-action-btn">
                ✅ <?php _e( 'Open Engine Validator', 'djv-core' ); ?>
              </a>
              <a href="<?php echo esc_url( home_url( '/wp-json/djv/v1/today?latitude=17.3850&longitude=78.4867&timezone=Asia%2FKolkata' ) ); ?>" target="_blank" class="djv-action-btn">
                🌐 <?php _e( 'Test REST API', 'djv-core' ); ?>
              </a>
              <a href="<?php echo esc_url( admin_url( 'admin.php?page=djv-system' ) ); ?>" class="djv-action-btn">
                🖥️ <?php _e( 'System Info', 'djv-core' ); ?>
              </a>
            </div>
          </div>

          <!-- Engine Test Output Area -->
          <div class="djv-output-box" id="djv-engine-output" style="display:none">
            <div class="djv-output-title">Engine Output</div>
            <pre id="djv-engine-output-pre"></pre>
          </div>

          <!-- Important Notices -->
          <div class="djv-section djv-notice-section">
            <h2>⚠️ <?php _e( 'Pre-Launch Checklist', 'djv-core' ); ?></h2>
            <div class="djv-checklist">
              <label class="djv-check-item">
                <input type="checkbox" id="djv-check-validate" />
                <span><?php _e( 'Manually validate 30+ Panchangam dates against published Panchang before launch.', 'djv-core' ); ?></span>
              </label>
              <label class="djv-check-item">
                <input type="checkbox" id="djv-check-node" <?php checked( $stats['node_available'] ); ?> disabled />
                <span><?php _e( 'Node.js is available on this server.', 'djv-core' ); ?></span>
              </label>
              <label class="djv-check-item">
                <input type="checkbox" id="djv-check-cache" />
                <span><?php _e( 'Nightly cache pre-warm cron is scheduled.', 'djv-core' ); ?></span>
              </label>
              <label class="djv-check-item">
                <input type="checkbox" id="djv-check-disclaimer" />
                <span><?php _e( 'Disclaimer page is published and linked in the footer.', 'djv-core' ); ?></span>
              </label>
              <label class="djv-check-item">
                <input type="checkbox" id="djv-check-adsense" />
                <span><?php _e( 'Google AdSense verification completed and ads.txt uploaded.', 'djv-core' ); ?></span>
              </label>
            </div>
          </div>

        </div>

        <script>
        (function($){
          function djvAjax(action, nonce, data, callback) {
            $.ajax({
              url: ajaxurl,
              method: 'POST',
              data: Object.assign({ action: action, nonce: nonce }, data),
              success: callback,
              error: function(xhr) { callback({ success: false, data: { message: xhr.responseText } }); }
            });
          }

          $('#djv-test-engine').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).text('⏳ Testing…');
            djvAjax('djv_test_engine', $btn.data('nonce'), {}, function(res) {
              $btn.prop('disabled', false).text('⚡ Test Engine (Today)');
              $('#djv-engine-output').show();
              $('#djv-engine-output-pre').text(JSON.stringify(res.data, null, 2));
            });
          });

          $('#djv-clear-cache').on('click', function() {
            if (!confirm('Clear all Panchangam cache? This will cause live recalculation on next request.')) return;
            var $btn = $(this);
            $btn.prop('disabled', true).text('⏳ Clearing…');
            djvAjax('djv_clear_cache', $btn.data('nonce'), {}, function(res) {
              $btn.prop('disabled', false).text('🗑️ Clear All Cache');
              alert(res.data.message || 'Done.');
            });
          });

          $('#djv-prewarm').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).text('⏳ Pre-warming…');
            djvAjax('djv_precalculate', $btn.data('nonce'), {
              year: new Date().getFullYear(),
              month: new Date().getMonth() + 1
            }, function(res) {
              $btn.prop('disabled', false).text('🔥 Pre-warm Cache');
              $('#djv-engine-output').show();
              $('#djv-engine-output-pre').text(JSON.stringify(res.data, null, 2));
            });
          });
        })(jQuery);
        </script>
        <?php
    }

    /* ─── Cache Manager ──────────────────────────────────────────── */

    public function render_cache_manager() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        $cached_posts = get_posts([ 'post_type' => 'djv_panchangam', 'posts_per_page' => 50, 'post_status' => 'publish', 'orderby' => 'meta_value', 'meta_key' => '_djv_panchangam_date', 'order' => 'DESC' ]);
        ?>
        <div class="wrap djv-admin">
          <h1>🗄️ <?php _e( 'Panchangam Cache Manager', 'djv-core' ); ?></h1>
          <p><?php _e( 'View, manage, and pre-warm the Panchangam calculation cache.', 'djv-core' ); ?></p>

          <div class="djv-section">
            <h2><?php _e( 'Pre-warm Cache', 'djv-core' ); ?></h2>
            <p><?php _e( 'Pre-calculate Panchangam for a specific month and location. Recommended to run nightly via WP-Cron.', 'djv-core' ); ?></p>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;margin-top:1rem">
              <label><?php _e('Year', 'djv-core'); ?><br><input type="number" id="pw-year" value="<?php echo date('Y'); ?>" min="2020" max="2100" style="width:100px;margin-top:4px" /></label>
              <label><?php _e('Month', 'djv-core'); ?><br><select id="pw-month" style="margin-top:4px">
                <?php for($m=1;$m<=12;$m++) echo "<option value='$m'" . (date('n')==$m?' selected':'') . ">" . date('F', mktime(0,0,0,$m,1)) . "</option>"; ?>
              </select></label>
              <label><?php _e('Latitude', 'djv-core'); ?><br><input type="number" id="pw-lat" value="17.3850" step="0.0001" style="width:110px;margin-top:4px" /></label>
              <label><?php _e('Longitude', 'djv-core'); ?><br><input type="number" id="pw-lon" value="78.4867" step="0.0001" style="width:110px;margin-top:4px" /></label>
              <label><?php _e('Timezone', 'djv-core'); ?><br><input type="text" id="pw-tz" value="Asia/Kolkata" style="width:120px;margin-top:4px" /></label>
              <button class="button button-primary" id="pw-start" data-nonce="<?php echo wp_create_nonce('djv_precalculate'); ?>"><?php _e('Start Pre-warm', 'djv-core'); ?></button>
            </div>
            <div id="pw-result" style="margin-top:1rem"></div>
          </div>

          <div class="djv-section">
            <h2><?php printf( __( 'Cached Dates (%d entries)', 'djv-core' ), count( $cached_posts ) ); ?></h2>
            <?php if ( empty( $cached_posts ) ) : ?>
              <p><?php _e( 'No cached Panchangam data yet. Run a pre-warm to populate the cache.', 'djv-core' ); ?></p>
            <?php else : ?>
              <table class="widefat striped">
                <thead><tr><th><?php _e('Date','djv-core'); ?></th><th><?php _e('Location','djv-core'); ?></th><th><?php _e('Cached','djv-core'); ?></th><th><?php _e('Actions','djv-core'); ?></th></tr></thead>
                <tbody>
                  <?php foreach ( $cached_posts as $post ) :
                    $date    = get_post_meta( $post->ID, '_djv_panchangam_date', true );
                    $lat     = get_post_meta( $post->ID, '_djv_panchangam_lat',  true );
                    $lon     = get_post_meta( $post->ID, '_djv_panchangam_lon',  true );
                  ?>
                  <tr>
                    <td><strong><?php echo esc_html( $date ); ?></strong></td>
                    <td><?php echo esc_html( "{$lat}°N, {$lon}°E" ); ?></td>
                    <td><?php echo esc_html( get_the_date( 'Y-m-d H:i', $post ) ); ?></td>
                    <td>
                      <a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>"><?php _e('View','djv-core'); ?></a>
                      &nbsp;|&nbsp;
                      <a href="<?php echo esc_url( get_delete_post_link( $post->ID, '', true ) ); ?>" onclick="return confirm('Delete this cached entry?')" style="color:red"><?php _e('Delete','djv-core'); ?></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>

        <script>
        jQuery('#pw-start').on('click', function() {
          var $btn = jQuery(this);
          $btn.prop('disabled', true).text('⏳ Pre-warming…');
          jQuery.post(ajaxurl, {
            action: 'djv_precalculate',
            nonce:  $btn.data('nonce'),
            year:   jQuery('#pw-year').val(),
            month:  jQuery('#pw-month').val(),
            lat:    jQuery('#pw-lat').val(),
            lon:    jQuery('#pw-lon').val(),
            tz:     jQuery('#pw-tz').val()
          }, function(res) {
            $btn.prop('disabled', false).text('Start Pre-warm');
            var msg = res.success ? '✅ Done! ' + JSON.stringify(res.data) : '❌ Error: ' + (res.data && res.data.message);
            jQuery('#pw-result').html('<div class="notice notice-' + (res.success ? 'success' : 'error') + ' inline"><p>' + msg + '</p></div>');
          });
        });
        </script>
        <?php
    }

    /* ─── Engine Validator ───────────────────────────────────────── */

    public function render_validator() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        ?>
        <div class="wrap djv-admin">
          <h1>✅ <?php _e( 'Panchangam Engine Validator', 'djv-core' ); ?></h1>
          <div class="notice notice-warning"><p><strong><?php _e('MANDATORY PRE-LAUNCH STEP:', 'djv-core'); ?></strong> <?php _e('Before publishing, compare at least 30 Panchangam values below against a verified published Panchang (e.g., Telugu Panchanga from a trusted almanac or Eenadu). The engine is structurally tested, but astronomical precision must be independently validated.', 'djv-core'); ?></p></div>

          <div class="djv-section">
            <h2><?php _e('Run Live Calculation', 'djv-core'); ?></h2>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
              <label><?php _e('Date', 'djv-core'); ?><br><input type="date" id="val-date" value="<?php echo date('Y-m-d'); ?>" style="margin-top:4px" /></label>
              <label><?php _e('Latitude', 'djv-core'); ?><br><input type="number" id="val-lat" value="17.3850" step="0.0001" style="width:110px;margin-top:4px" /></label>
              <label><?php _e('Longitude', 'djv-core'); ?><br><input type="number" id="val-lon" value="78.4867" step="0.0001" style="width:110px;margin-top:4px" /></label>
              <label><?php _e('Timezone', 'djv-core'); ?><br><input type="text" id="val-tz" value="Asia/Kolkata" style="width:120px;margin-top:4px" /></label>
              <button class="button button-primary" id="val-run" data-nonce="<?php echo wp_create_nonce('djv_test_engine'); ?>"><?php _e('Calculate', 'djv-core'); ?></button>
            </div>
            <div id="val-result" style="margin-top:1.25rem"></div>
          </div>

          <div class="djv-section">
            <h2><?php _e('Validation Reference Table', 'djv-core'); ?></h2>
            <p><?php _e('Fill in the "Expected" column from a trusted Panchang, then compare with calculated values.', 'djv-core'); ?></p>
            <table class="widefat striped">
              <thead><tr><th><?php _e('Date','djv-core'); ?></th><th><?php _e('Field','djv-core'); ?></th><th><?php _e('Calculated','djv-core'); ?></th><th><?php _e('Expected (manual)','djv-core'); ?></th><th><?php _e('Match?','djv-core'); ?></th></tr></thead>
              <tbody>
                <tr><td>2026-10-03</td><td>Vara</td><td>Saturday (Shanivara)</td><td><input type="text" placeholder="Enter from Panchang" style="width:140px" /></td><td><select><option>—</option><option>✅ Match</option><option>❌ Mismatch</option></select></td></tr>
                <tr><td>2026-10-03</td><td>Tithi</td><td>Shukla Dvitiya</td><td><input type="text" placeholder="Enter from Panchang" style="width:140px" /></td><td><select><option>—</option><option>✅ Match</option><option>❌ Mismatch</option></select></td></tr>
                <tr><td>2026-10-03</td><td>Nakshatra</td><td>Uttara Phalguni</td><td><input type="text" placeholder="Enter from Panchang" style="width:140px" /></td><td><select><option>—</option><option>✅ Match</option><option>❌ Mismatch</option></select></td></tr>
                <tr><td>2026-10-03</td><td>Rahu Kalam</td><td>10:30–12:00</td><td><input type="text" placeholder="Enter from Panchang" style="width:140px" /></td><td><select><option>—</option><option>✅ Match</option><option>❌ Mismatch</option></select></td></tr>
              </tbody>
            </table>
            <p style="margin-top:.75rem"><em><?php _e('Add more rows for additional dates. Save this page\'s HTML for your records.','djv-core'); ?></em></p>
          </div>
        </div>

        <script>
        jQuery('#val-run').on('click', function() {
          var $btn = jQuery(this);
          $btn.prop('disabled', true).text('⏳ Calculating…');
          jQuery.post(ajaxurl, {
            action: 'djv_test_engine',
            nonce: $btn.data('nonce'),
            date: jQuery('#val-date').val(),
            lat: jQuery('#val-lat').val(),
            lon: jQuery('#val-lon').val(),
            tz: jQuery('#val-tz').val()
          }, function(res) {
            $btn.prop('disabled', false).text('Calculate');
            var html = res.success
              ? '<table class="widefat striped"><tbody>';
            if (res.success && res.data) {
              var d = res.data;
              // Use correct engine output field paths
              var nak = d.nakshatra ? (d.nakshatra.nakshatra || d.nakshatra) : null;
              var tz  = jQuery('#val-tz').val() || 'Asia/Kolkata';
              function utcToLocal(iso) {
                if (!iso) return '—';
                var dt = new Date(iso);
                // Simple formatting for display since Date object doesn't natively format generic IANA timezones easily without Intl
                return dt.toLocaleTimeString('en-US', { timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: false }) + ' (local)';
              }
              if (d.tithi)     html += '<tr><td><strong>Tithi</strong></td><td>' + (d.tithi.paksha||'') + ' ' + (d.tithi.name||'') + ' (' + (d.tithi.displayId||d.tithi.id||'') + ')</td></tr>';
              if (nak)         html += '<tr><td><strong>Nakshatra</strong></td><td>' + (nak.name||'') + ' (' + (nak.nameTe||'') + '), Pada ' + (d.nakshatra.pada||'—') + '</td></tr>';
              if (d.yoga)      html += '<tr><td><strong>Yoga</strong></td><td>' + (d.yoga.name||'') + ' (' + (d.yoga.nameTe||'') + ')</td></tr>';
              if (d.vara)      html += '<tr><td><strong>Vara</strong></td><td>' + (d.vara.en||d.vara.name||'') + ' — ' + (d.vara.nameTe||'') + ' (Ruler: ' + (d.vara.ruler||'') + ')</td></tr>';
              if (d.solar)     html += '<tr><td><strong>Sunrise</strong></td><td>' + utcToLocal(d.solar.sunrise) + '</td></tr>';
              if (d.solar)     html += '<tr><td><strong>Sunset</strong></td><td>' + utcToLocal(d.solar.sunset) + '</td></tr>';
              if (d.timings && d.timings.rahuKalam) html += '<tr><td><strong>Rahu Kalam</strong></td><td>' + utcToLocal(d.timings.rahuKalam.start) + ' – ' + utcToLocal(d.timings.rahuKalam.end) + '</td></tr>';
              if (d.timings && d.timings.abhijitMuhurtham) html += '<tr><td><strong>Abhijit Muhurtham</strong></td><td>' + utcToLocal(d.timings.abhijitMuhurtham.start) + ' – ' + utcToLocal(d.timings.abhijitMuhurtham.end) + '</td></tr>';
              html += '</tbody></table>';
              html += '<div style="margin-top:.5rem"><strong>Full JSON:</strong><pre style="background:#f0f0f0;padding:1rem;overflow:auto;max-height:300px">' + JSON.stringify(d, null, 2) + '</pre></div>';
            } else {
              html = '<div class="notice notice-error inline"><p>Error: ' + JSON.stringify(res.data) + '</p></div>';
            }
            jQuery('#val-result').html(html);
          });
        });
        </script>
        <?php
    }

    /* ─── API Test Tool ──────────────────────────────────────────── */

    public function render_api_test() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        $base = home_url('/wp-json/djv/v1/');
        $endpoints = [
            'Today'       => $base . 'today?latitude=17.3850&longitude=78.4867&timezone=Asia%2FKolkata',
            'Panchangam'  => $base . 'panchangam?date=' . date('Y-m-d') . '&latitude=17.3850&longitude=78.4867&timezone=Asia%2FKolkata',
            'Festivals'   => $base . 'festivals?year=' . date('Y'),
            'Mantras'     => $base . 'mantras?language=telugu',
            'Temples'     => $base . 'temples?state=telangana',
            'Pooja Guides'=> $base . 'pooja',
        ];
        ?>
        <div class="wrap djv-admin">
          <h1>🌐 <?php _e( 'REST API Test Tool', 'djv-core' ); ?></h1>
          <p><?php _e( 'Test all DJV REST API endpoints directly from the admin.', 'djv-core' ); ?></p>
          <?php foreach ( $endpoints as $label => $url ) : ?>
          <div class="djv-section" style="padding:1rem">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
              <div>
                <strong><?php echo esc_html( $label ); ?></strong><br>
                <code style="font-size:.75rem"><?php echo esc_html( $url ); ?></code>
              </div>
              <div style="display:flex;gap:.5rem">
                <a href="<?php echo esc_url( $url ); ?>" target="_blank" class="button"><?php _e('Open →', 'djv-core'); ?></a>
                <button class="button button-primary djv-test-ep" data-url="<?php echo esc_attr( $url ); ?>" data-nonce="<?php echo wp_create_nonce('djv_test_api_endpoint'); ?>"><?php _e('Test', 'djv-core'); ?></button>
              </div>
            </div>
            <div class="djv-ep-result" style="display:none;margin-top:.75rem;background:#f0f0f0;padding:.75rem;border-radius:.375rem;font-size:.75rem;max-height:200px;overflow:auto"></div>
          </div>
          <?php endforeach; ?>
        </div>
        <script>
        jQuery('.djv-test-ep').on('click', function() {
          var $btn = jQuery(this);
          var $result = $btn.closest('.djv-section').find('.djv-ep-result');
          $btn.prop('disabled', true).text('⏳');
          jQuery.get($btn.data('url'), function(data) {
            $btn.prop('disabled', false).text('Test');
            $result.show().text(JSON.stringify(data, null, 2));
          }).fail(function(xhr) {
            $btn.prop('disabled', false).text('Test');
            $result.show().text('Error ' + xhr.status + ': ' + xhr.responseText.substring(0, 500));
          });
        });
        </script>
        <?php
    }

    /* ─── Settings ───────────────────────────────────────────────── */

    public function render_settings() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        if ( isset( $_POST['djv_save_settings'] ) && check_admin_referer('djv_settings') ) {
            update_option( 'djv_default_city',    sanitize_text_field( $_POST['djv_default_city'] ?? 'Hyderabad' ) );
            update_option( 'djv_default_lat',     floatval( $_POST['djv_default_lat'] ?? 17.3850 ) );
            update_option( 'djv_default_lon',     floatval( $_POST['djv_default_lon'] ?? 78.4867 ) );
            
            $tz = sanitize_text_field( $_POST['djv_default_tz'] ?? 'Asia/Kolkata' );
            if ( in_array( $tz, timezone_identifiers_list(), true ) ) {
                update_option( 'djv_default_tz', $tz );
            }
            
            update_option( 'djv_engine_api_url',  esc_url_raw( $_POST['djv_engine_api_url'] ?? '' ) );
            update_option( 'djv_ayanamsa',        sanitize_text_field( $_POST['djv_ayanamsa'] ?? 'lahiri' ) );
            update_option( 'djv_month_system',    sanitize_text_field( $_POST['djv_month_system'] ?? 'amanta' ) );
            update_option( 'djv_cache_enabled',   isset( $_POST['djv_cache_enabled'] ) ? 1 : 0 );
            echo '<div class="notice notice-success"><p>' . __( 'Settings saved.', 'djv-core' ) . '</p></div>';
        }
        ?>
        <div class="wrap djv-admin">
          <h1>⚙️ <?php _e( 'DJV Settings', 'djv-core' ); ?></h1>
          <form method="post">
            <?php wp_nonce_field('djv_settings'); ?>
            <table class="form-table">
              <tr><th><?php _e('Default City', 'djv-core'); ?></th><td><input type="text" name="djv_default_city" value="<?php echo esc_attr(get_option('djv_default_city','Hyderabad')); ?>" class="regular-text" /></td></tr>
              <tr><th><?php _e('Default Latitude', 'djv-core'); ?></th><td><input type="number" name="djv_default_lat" value="<?php echo esc_attr(get_option('djv_default_lat',17.3850)); ?>" step="0.0001" class="small-text" /></td></tr>
              <tr><th><?php _e('Default Longitude', 'djv-core'); ?></th><td><input type="number" name="djv_default_lon" value="<?php echo esc_attr(get_option('djv_default_lon',78.4867)); ?>" step="0.0001" class="small-text" /></td></tr>
              <tr><th><?php _e('Default Timezone', 'djv-core'); ?></th><td><input type="text" name="djv_default_tz" value="<?php echo esc_attr(get_option('djv_default_tz','Asia/Kolkata')); ?>" class="regular-text" /><p class="description"><?php _e('IANA timezone identifier (e.g. Asia/Kolkata)', 'djv-core'); ?></p></td></tr>
              <tr><th><?php _e('Engine API URL', 'djv-core'); ?></th><td><input type="url" name="djv_engine_api_url" value="<?php echo esc_url(get_option('djv_engine_api_url','')); ?>" class="regular-text" /><p class="description"><?php _e('URL of the external Node.js Panchangam engine (e.g. https://api.yoursite.com)', 'djv-core'); ?></p>
              <button type="button" class="button" id="djv-test-engine-btn" data-nonce="<?php echo wp_create_nonce('djv_test_engine'); ?>">Test Connection</button> <span id="djv-engine-test-result"></span>
              </td></tr>
              <tr><th><?php _e('Ayanamsa', 'djv-core'); ?></th><td><select name="djv_ayanamsa"><option value="lahiri" <?php selected(get_option('djv_ayanamsa','lahiri'),'lahiri'); ?>>Lahiri (Chitrapaksha)</option><option value="raman" <?php selected(get_option('djv_ayanamsa'),'raman'); ?>>B.V. Raman</option></select></td></tr>
              <tr><th><?php _e('Month System', 'djv-core'); ?></th><td><select name="djv_month_system"><option value="amanta" <?php selected(get_option('djv_month_system','amanta'),'amanta'); ?>>Amanta (South Indian)</option><option value="purnimanta" <?php selected(get_option('djv_month_system'),'purnimanta'); ?>>Purnimanta (North Indian)</option></select></td></tr>
              <tr><th><?php _e('Enable Cache', 'djv-core'); ?></th><td><input type="checkbox" name="djv_cache_enabled" <?php checked(get_option('djv_cache_enabled',1)); ?> /> <span class="description"><?php _e('Recommended: ON. Disabling causes live calculation on every request.', 'djv-core'); ?></span></td></tr>
            </table>
            <p class="submit"><input type="submit" name="djv_save_settings" class="button-primary" value="<?php _e('Save Settings', 'djv-core'); ?>" /></p>
          </form>
        </div>
        
        <script>
        jQuery(document).ready(function($){
            $('#djv-test-engine-btn').click(function(){
                var url = $('input[name="djv_engine_api_url"]').val();
                if(!url) { $('#djv-engine-test-result').html('<span style="color:red">Please enter a URL first and save settings.</span>'); return; }
                $('#djv-engine-test-result').html('Testing...');
                $.post(ajaxurl, {
                    action: 'djv_test_engine',
                    nonce: $(this).data('nonce'),
                    url: url
                }, function(res) {
                    if(res.success) {
                        $('#djv-engine-test-result').html('<strong style="color:green">CONNECTED</strong>');
                    } else {
                        $('#djv-engine-test-result').html('<strong style="color:red">NOT CONNECTED</strong> (' + res.data + ')');
                    }
                }).fail(function() {
                    $('#djv-engine-test-result').html('<strong style="color:red">NOT CONNECTED</strong>');
                });
            });
        });
        </script>
        <?php
    }

    /* ─── System Info ────────────────────────────────────────────── */

    public function render_system_info() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        $api_url      = get_option( 'djv_engine_api_url', '' );
        $node_version = empty( $api_url ) ? 'Not Configured' : 'External Microservice';
        $php_version  = phpversion();
        $wp_version   = get_bloginfo('version');
        $runner_exists = false; // Subprocess architecture replaced with API
        ?>
        <div class="wrap djv-admin">
          <h1>🖥️ <?php _e( 'System Information', 'djv-core' ); ?></h1>
          <table class="widefat striped">
            <tbody>
              <tr><td><strong><?php _e('PHP Version', 'djv-core'); ?></strong></td><td><?php echo esc_html($php_version); ?> <?php echo version_compare($php_version,'8.0.0','>=') ? '✅' : '⚠️ Upgrade to PHP 8.0+'; ?></td></tr>
              <tr><td><strong><?php _e('WordPress Version', 'djv-core'); ?></strong></td><td><?php echo esc_html($wp_version); ?></td></tr>
              <tr><td><strong><?php _e('DJV Plugin Version', 'djv-core'); ?></strong></td><td><?php echo esc_html(DJV_VERSION); ?></td></tr>
              <tr><td><strong><?php _e('Node.js Version', 'djv-core'); ?></strong></td><td><?php echo $node_version ? esc_html(trim($node_version)) . ' ✅' : '❌ Not found — install Node.js on server'; ?></td></tr>
              <tr><td><strong><?php _e('Engine Runner Script', 'djv-core'); ?></strong></td><td><?php echo '✅ N/A (Using External API)'; ?></td></tr>
              <tr><td><strong><?php _e('proc_open() available', 'djv-core'); ?></strong></td><td><?php echo function_exists('proc_open') ? '✅ Yes' : '❌ No — required for live engine'; ?></td></tr>
              <tr><td><strong><?php _e('WP Cron enabled', 'djv-core'); ?></strong></td><td><?php echo !defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON ? '✅ Yes' : '⚠️ Disabled — use server cron'; ?></td></tr>
              <tr><td><strong><?php _e('Upload Dir Writable', 'djv-core'); ?></strong></td><td><?php $ud=wp_upload_dir(); echo is_writable($ud['basedir']) ? '✅ Yes' : '❌ No'; ?></td></tr>
              <tr><td><strong><?php _e('REST API Active', 'djv-core'); ?></strong></td><td><a href="<?php echo esc_url(home_url('/wp-json/djv/v1/')); ?>" target="_blank"><?php echo esc_html(home_url('/wp-json/djv/v1/')); ?></a></td></tr>
            </tbody>
          </table>
        </div>
        <?php
    }

    /* ─── AJAX Handlers ──────────────────────────────────────────── */

    public function ajax_test_engine() {
        check_ajax_referer( 'djv_test_engine', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error('Access denied.');

        $url = esc_url_raw( $_POST['url'] ?? '' );
        if ( empty( $url ) ) {
            wp_send_json_error( 'URL is empty.' );
        }

        $health_url = rtrim( $url, '/' ) . '/health';
        $response = wp_remote_get( $health_url, [ 'timeout' => 10 ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( $code !== 200 ) {
            wp_send_json_error( 'HTTP Error ' . $code );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['status'] ) && $data['status'] === 'healthy' ) {
            wp_send_json_success( 'CONNECTED' );
        } else {
            wp_send_json_error( 'Invalid health check response.' );
        }
    }

    public function ajax_clear_cache() {
        check_ajax_referer( 'djv_clear_cache', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error([ 'message' => 'Access denied.' ]);

        // Delete all djv_panchangam CPT posts
        $posts = get_posts([ 'post_type' => 'djv_panchangam', 'posts_per_page' => -1, 'post_status' => 'publish', 'fields' => 'ids' ]);
        $deleted = 0;
        foreach ( $posts as $id ) { wp_delete_post( $id, true ); $deleted++; }

        // Clear transients (matching pattern)
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_djv_pc_%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_djv_pc_%'" );

        wp_send_json_success([ 'message' => "Cleared {$deleted} cached entries and all transients." ]);
    }

    public function ajax_precalculate() {
        check_ajax_referer( 'djv_precalculate', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error([ 'message' => 'Access denied.' ]);

        if ( empty( get_option('djv_engine_api_url') ) ) {
            wp_send_json_error([ 'message' => 'Engine API URL not configured.' ]);
        }

        $lat   = floatval( $_POST['lat'] ?? 17.3850 );
        $lon   = floatval( $_POST['lon'] ?? 78.4867 );
        $tz    = sanitize_text_field( $_POST['tz'] ?? 'Asia/Kolkata' );

        set_time_limit( 120 ); // Allow longer execution for pre-warm

        $panchangam = new DJV_Panchangam();
        $result = $panchangam->precalculate_next_30_days( $lat, $lon, $tz );

        wp_send_json_success( $result );
    }

    public function ajax_test_api_endpoint() {
        check_ajax_referer( 'djv_test_api_endpoint', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error([ 'message' => 'Access denied.' ]);
        wp_send_json_success([ 'message' => 'Use browser fetch or the Test button to call endpoints.' ]);
    }

    /* ─── Helpers ────────────────────────────────────────────────── */

    private function get_dashboard_stats() {
        $api_url = get_option('djv_engine_api_url', '');
        return [
            'engine_status'  => empty( $api_url ) ? 'offline' : 'online',
            'node_available' => ! empty( $api_url ),
            'cached_dates'   => wp_count_posts('djv_panchangam')->publish ?? 0,
            'festival_count' => wp_count_posts('djv_festival')->publish ?? 0,
            'pooja_count'    => wp_count_posts('djv_pooja')->publish ?? 0,
            'mantra_count'   => wp_count_posts('djv_mantra')->publish ?? 0,
            'temple_count'   => wp_count_posts('djv_temple')->publish ?? 0,
        ];
    }

    private function render_status_alerts( $stats ) {
        if ( $stats['engine_status'] !== 'online' ) {
            echo '<div class="notice notice-error"><p><strong>❌ Panchangam Engine API Not Configured.</strong> The external Node.js microservice API URL is required. Please set it in DJV Settings before pre-warming cache or running calculations.</p></div>';
        }
        if ( $stats['cached_dates'] === 0 && $stats['engine_status'] === 'online' ) {
            echo '<div class="notice notice-warning"><p><strong>⚠️ No cached data.</strong> The Panchangam cache is empty. You may now run "Pre-warm Cache" to populate the next 30 days.</p></div>';
        }
    }

    private function get_menu_icon() {
        // Inline SVG as base64 data URI (WordPress menu icon)
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><text y="15" font-size="14">🕉</text></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function plugin_action_links( $links ) {
        $links[] = '<a href="' . admin_url('admin.php?page=djv-dashboard') . '">' . __('Dashboard','djv-core') . '</a>';
        $links[] = '<a href="' . admin_url('admin.php?page=djv-settings') . '">' . __('Settings','djv-core') . '</a>';
        return $links;
    }

    /**
     * Render Muhurtham Directory management page.
     */
    public function render_muhurtham_manager() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Access denied.', 'djv-core' ) );

        require_once DJV_PLUGIN_DIR . 'includes/data-muhurtham-events.php';
        $all_events = DJV_Muhurtham_Events::get_all();
        $categories = DJV_Muhurtham_Events::get_categories();
        $custom_opts = get_option( 'djv_muhurtham_events_custom', [] );
        $saved_notice = false;

        // Handle safe metadata updates (publication status toggle)
        if ( ! empty( $_POST['djv_save_muhurtham_admin'] ) && check_admin_referer( 'djv_muhurtham_admin_action', 'djv_muhurtham_nonce' ) ) {
            $updated_custom = is_array( $custom_opts ) ? $custom_opts : [];
            if ( isset( $_POST['event_status'] ) && is_array( $_POST['event_status'] ) ) {
                foreach ( $_POST['event_status'] as $slug => $status ) {
                    $clean_slug = sanitize_key( $slug );
                    $clean_status = sanitize_key( $status );
                    if ( isset( $all_events[ $clean_slug ] ) ) {
                        $updated_custom[ $clean_slug ]['published'] = ( $clean_status === 'draft' ) ? false : true;
                    }
                }
            }
            update_option( 'djv_muhurtham_events_custom', $updated_custom );
            $custom_opts = $updated_custom;
            $saved_notice = true;
        }
        ?>
        <div class="wrap djv-admin">
            <div class="djv-admin-header">
                <div class="djv-logo">
                    <span class="djv-logo-icon">✨</span>
                    <div>
                        <h1><?php _e( 'Muhurtham Master Directory & Event Manager', 'djv-core' ); ?></h1>
                        <p><?php printf( __( 'Configured Vedic Event Schemas (%d Total Events across 7 Categories)', 'djv-core' ), count( $all_events ) ); ?></p>
                    </div>
                </div>
                <div class="djv-version-badge">Rule v1.2.0 • Engine v1.3.0</div>
            </div>

            <?php if ( $saved_notice ) : ?>
                <div class="notice notice-success is-dismissible" style="margin-bottom:1.5rem;">
                    <p><strong>✓ <?php _e( 'Muhurtham event publication statuses and configuration successfully updated.', 'djv-core' ); ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="djv-section">
                <h2><?php _e( 'Vedic Ceremony Registry Overview', 'djv-core' ); ?></h2>
                <p style="color:#666; font-size:0.9rem; margin-bottom:1.25rem;">
                    <?php _e( 'All 58 Muhurtham ceremonies are registered with strict classical Jyotish rule-sets, unique schema forms, and verified astronomical ephemeris carving. Administrators can audit event schemas and configure frontend publication status.', 'djv-core' ); ?>
                </p>

                <form method="post" action="">
                    <?php wp_nonce_field( 'djv_muhurtham_admin_action', 'djv_muhurtham_nonce' ); ?>
                    <input type="hidden" name="djv_save_muhurtham_admin" value="1">

                    <table class="wp-list-table widefat fixed striped" style="margin-top:1rem; border-radius:6px; overflow:hidden;">
                        <thead>
                            <tr>
                                <th style="width: 45px; text-align:center;">Icon</th>
                                <th style="width: 230px;">Ceremony / Event</th>
                                <th style="width: 170px;">Category</th>
                                <th style="width: 140px;">Form Schema</th>
                                <th style="width: 130px;">Mode</th>
                                <th style="width: 100px;">Rule Ver</th>
                                <th style="width: 110px;">Status</th>
                                <th style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $all_events as $slug => $ev ) :
                                $is_pub = ! isset( $custom_opts[ $slug ]['published'] ) || $custom_opts[ $slug ]['published'] !== false;
                                $cat_info = $categories[ $ev['category'] ] ?? null;
                                $cat_label = $cat_info ? $cat_info['title']['en'] : $ev['category'];
                                $page_url = home_url( "/muhurtham/{$slug}/" );
                            ?>
                                <tr>
                                    <td style="font-size: 1.4rem; text-align: center; vertical-align: middle;"><?php echo esc_html( $ev['icon'] ?? '✨' ); ?></td>
                                    <td>
                                        <strong><?php echo esc_html( $ev['title']['en'] ); ?></strong><br>
                                        <span style="font-size:0.8rem; color:#888;"><?php echo esc_html( $ev['title']['te'] ?? '' ); ?> • <code><?php echo esc_html( $slug ); ?></code></span>
                                    </td>
                                    <td>
                                        <span style="background:#F4EDE4; color:#7A2419; padding:0.2rem 0.5rem; border-radius:4px; font-size:0.75rem; font-weight:600;">
                                            <?php echo esc_html( $cat_label ); ?>
                                        </span>
                                    </td>
                                    <td><code><?php echo esc_html( $ev['form_schema'] ?? 'general_karya' ); ?></code></td>
                                    <td>
                                        <span style="font-size:0.8rem; font-weight:600; color:<?php echo ( $ev['form_schema'] === 'marriage' ? '#2E7D5B' : '#555' ); ?>;">
                                            <?php echo ( $ev['form_schema'] === 'marriage' ? 'Personalized + Basic' : 'Basic Panchanga' ); ?>
                                        </span>
                                    </td>
                                    <td><span style="font-size:0.8rem; color:#666;">v1.2.0</span></td>
                                    <td>
                                        <select name="event_status[<?php echo esc_attr( $slug ); ?>]" style="font-size:0.8rem; padding:2px 6px;">
                                            <option value="publish" <?php selected( $is_pub, true ); ?>>Published</option>
                                            <option value="draft" <?php selected( $is_pub, false ); ?>>Draft</option>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url( $page_url ); ?>" target="_blank" class="button button-small" title="Preview live canonical page">View ↗</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 1.5rem;">
                        <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Publication Statuses', 'djv-core' ); ?>">
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render the DJV Muhurtham Submissions Dashboard.
     * Full admin panel for viewing, searching, filtering, inspecting details,
     * retrying calculations, and exporting authorized CSV records.
     */
    public function render_submissions_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Access denied. You do not have permission to access Muhurtham submissions.', 'djv-core' ) );
        }

        require_once DJV_PLUGIN_DIR . 'includes/class-djv-muhurtham-submissions.php';
        require_once DJV_PLUGIN_DIR . 'includes/data-muhurtham-events.php';

        // 1. Handle CSV Export
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'export_csv' && check_admin_referer( 'djv_export_csv', '_djv_nonce' ) ) {
            $export_filters = [
                'search'      => sanitize_text_field( $_GET['s'] ?? '' ),
                'event'       => sanitize_key( $_GET['event_filter'] ?? 'all' ),
                'status'      => sanitize_key( $_GET['status_filter'] ?? 'all' ),
                'calc_status' => sanitize_key( $_GET['calc_filter'] ?? 'all' ),
                'date_from'   => sanitize_text_field( $_GET['date_from'] ?? '' ),
                'date_to'     => sanitize_text_field( $_GET['date_to'] ?? '' ),
            ];
            $csv_data = DJV_Muhurtham_Submissions::export_csv( $export_filters );
            header( 'Content-Type: text/csv; charset=UTF-8' );
            header( 'Content-Disposition: attachment; filename=djv_muhurtham_submissions_' . date( 'Ymd_His' ) . '.csv' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            echo $csv_data;
            exit;
        }

        // 2. Handle POST Actions (Status Update, Delete, Retry)
        $action_notice = null;
        if ( ! empty( $_POST['djv_submission_action'] ) && check_admin_referer( 'djv_sub_action', '_djv_sub_nonce' ) ) {
            $sub_id = sanitize_text_field( $_POST['submission_id'] ?? '' );
            $act    = sanitize_key( $_POST['djv_submission_action'] );

            if ( $act === 'update_status' ) {
                $new_st = sanitize_key( $_POST['new_status'] ?? '' );
                if ( DJV_Muhurtham_Submissions::update_status( $sub_id, $new_st ) ) {
                    $action_notice = [ 'type' => 'success', 'msg' => sprintf( __( 'Submission %s status updated to "%s".', 'djv-core' ), $sub_id, $new_st ) ];
                }
            } elseif ( $act === 'delete' ) {
                if ( DJV_Muhurtham_Submissions::delete_submission( $sub_id ) ) {
                    $action_notice = [ 'type' => 'success', 'msg' => sprintf( __( 'Submission %s permanently deleted.', 'djv-core' ), $sub_id ) ];
                }
            } elseif ( $act === 'retry' ) {
                $ret = DJV_Muhurtham_Submissions::retry_calculation( $sub_id );
                if ( is_wp_error( $ret ) ) {
                    $action_notice = [ 'type' => 'error', 'msg' => sprintf( __( 'Calculation retry failed for %s: %s', 'djv-core' ), $sub_id, $ret->get_error_message() ) ];
                } else {
                    $action_notice = [ 'type' => 'success', 'msg' => sprintf( __( 'Calculation successfully re-evaluated for %s. Found %d auspicious timings.', 'djv-core' ), $sub_id, count( $ret['results'] ?? [] ) ) ];
                }
            }
        }

        // 3. Load Filters
        $search      = sanitize_text_field( $_GET['s'] ?? '' );
        $event_fil   = sanitize_key( $_GET['event_filter'] ?? 'all' );
        $status_fil  = sanitize_key( $_GET['status_filter'] ?? 'all' );
        $calc_fil    = sanitize_key( $_GET['calc_filter'] ?? 'all' );
        $date_from   = sanitize_text_field( $_GET['date_from'] ?? '' );
        $date_to     = sanitize_text_field( $_GET['date_to'] ?? '' );
        $paged       = max( 1, intval( $_GET['paged'] ?? 1 ) );

        $query_args = [
            'search'      => $search,
            'event'       => $event_fil,
            'status'      => $status_fil,
            'calc_status' => $calc_fil,
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'page'        => $paged,
            'per_page'    => 20,
        ];

        $submissions_data = DJV_Muhurtham_Submissions::get_submissions( $query_args );
        $items = $submissions_data['items'];
        $total_items = $submissions_data['total'];
        $total_pages = $submissions_data['pages'];

        // 4. Compute Overview Stats
        global $wpdb;
        $table_name = DJV_Muhurtham_Submissions::get_table_name();
        $stat_total   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
        $stat_today   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE DATE(created_at) = %s", date( 'Y-m-d' ) ) );
        $stat_success = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE calculation_status = 'success'" );
        $stat_failed  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE calculation_status = 'failed'" );

        $all_events = DJV_Muhurtham_Events::get_all();
        $export_url = add_query_arg( [
            'page'         => 'djv-muhurtham-submissions',
            'action'       => 'export_csv',
            's'            => $search,
            'event_filter' => $event_fil,
            'status_filter'=> $status_fil,
            'calc_filter'  => $calc_fil,
            'date_from'    => $date_from,
            'date_to'      => $date_to,
            '_djv_nonce'   => wp_create_nonce( 'djv_export_csv' ),
        ], admin_url( 'admin.php' ) );
        ?>
        <div class="wrap djv-admin">
            <div class="djv-admin-header">
                <div class="djv-logo">
                    <span class="djv-logo-icon">📋</span>
                    <div>
                        <h1><?php _e( 'DJV Muhurtham Submissions Dashboard', 'djv-core' ); ?></h1>
                        <p><?php _e( 'Universal submission registry & calculation workflow for all Vedic Muhurtham ceremonies.', 'djv-core' ); ?></p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <a href="<?php echo esc_url( $export_url ); ?>" class="button button-secondary" style="background:#FFF; color:#7A2419; font-weight:700; border:none; display:inline-flex; align-items:center; gap:0.35rem;">
                        📥 <?php _e( 'Export to CSV', 'djv-core' ); ?>
                    </a>
                    <div class="djv-version-badge">One Shared Workflow</div>
                </div>
            </div>

            <?php if ( $action_notice ) : ?>
                <div class="notice notice-<?php echo esc_attr( $action_notice['type'] ); ?> is-dismissible" style="margin-bottom:1.5rem;">
                    <p><strong><?php echo esc_html( $action_notice['msg'] ); ?></strong></p>
                </div>
            <?php endif; ?>

            <!-- Metrics Summary Grid -->
            <div class="djv-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom:1.5rem;">
                <div class="djv-stat-card">
                    <div class="djv-stat-icon">📊</div>
                    <div class="djv-stat-label"><?php _e( 'Total Submissions', 'djv-core' ); ?></div>
                    <div class="djv-stat-value"><?php echo number_format_i18n( $stat_total ); ?></div>
                </div>
                <div class="djv-stat-card">
                    <div class="djv-stat-icon">📅</div>
                    <div class="djv-stat-label"><?php _e( 'Today\'s Submissions', 'djv-core' ); ?></div>
                    <div class="djv-stat-value"><?php echo number_format_i18n( $stat_today ); ?></div>
                </div>
                <div class="djv-stat-card djv-stat-online">
                    <div class="djv-stat-icon">✅</div>
                    <div class="djv-stat-label"><?php _e( 'Calculations Succeeded', 'djv-core' ); ?></div>
                    <div class="djv-stat-value" style="color:#2E7D5B;"><?php echo number_format_i18n( $stat_success ); ?></div>
                </div>
                <div class="djv-stat-card <?php echo ( $stat_failed > 0 ) ? 'djv-stat-offline' : ''; ?>">
                    <div class="djv-stat-icon">⚠️</div>
                    <div class="djv-stat-label"><?php _e( 'Failed / Needs Retry', 'djv-core' ); ?></div>
                    <div class="djv-stat-value" style="color:<?php echo ( $stat_failed > 0 ) ? '#B52020' : '#241914'; ?>;"><?php echo number_format_i18n( $stat_failed ); ?></div>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="djv-section" style="padding:1.25rem; margin-bottom:1.5rem;">
                <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
                    <input type="hidden" name="page" value="djv-muhurtham-submissions">

                    <!-- Search Input -->
                    <div style="flex:1; min-width:220px;">
                        <label style="font-size:0.75rem; font-weight:700; color:#555; display:block; margin-bottom:0.25rem;">
                            🔍 <?php _e( 'Search by ID or Applicant', 'djv-core' ); ?>
                        </label>
                        <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'e.g. DJV-MUH... or Rajesh', 'djv-core' ); ?>" style="width:100%; height:34px; padding:0 0.5rem; border:1px solid #CCD0D4; border-radius:4px;">
                    </div>

                    <!-- Event Filter -->
                    <div style="min-width:180px;">
                        <label style="font-size:0.75rem; font-weight:700; color:#555; display:block; margin-bottom:0.25rem;">
                            🕉 <?php _e( 'Ceremony Event', 'djv-core' ); ?>
                        </label>
                        <select name="event_filter" style="width:100%; height:34px;">
                            <option value="all"><?php _e( 'All Muhurtham Events', 'djv-core' ); ?></option>
                            <?php foreach ( $all_events as $slug => $ev ) : ?>
                                <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $event_fil, $slug ); ?>>
                                    <?php echo esc_html( $ev['icon'] . ' ' . $ev['title']['en'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Submission Status Filter -->
                    <div style="min-width:140px;">
                        <label style="font-size:0.75rem; font-weight:700; color:#555; display:block; margin-bottom:0.25rem;">
                            📌 <?php _e( 'Status', 'djv-core' ); ?>
                        </label>
                        <select name="status_filter" style="width:100%; height:34px;">
                            <option value="all"><?php _e( 'All Statuses', 'djv-core' ); ?></option>
                            <option value="received" <?php selected( $status_fil, 'received' ); ?>><?php _e( 'Received', 'djv-core' ); ?></option>
                            <option value="in_review" <?php selected( $status_fil, 'in_review' ); ?>><?php _e( 'In Review', 'djv-core' ); ?></option>
                            <option value="processed" <?php selected( $status_fil, 'processed' ); ?>><?php _e( 'Processed', 'djv-core' ); ?></option>
                            <option value="cancelled" <?php selected( $status_fil, 'cancelled' ); ?>><?php _e( 'Cancelled', 'djv-core' ); ?></option>
                        </select>
                    </div>

                    <!-- Calculation Status Filter -->
                    <div style="min-width:140px;">
                        <label style="font-size:0.75rem; font-weight:700; color:#555; display:block; margin-bottom:0.25rem;">
                            ⚙️ <?php _e( 'Calculation', 'djv-core' ); ?>
                        </label>
                        <select name="calc_filter" style="width:100%; height:34px;">
                            <option value="all"><?php _e( 'All Calculation', 'djv-core' ); ?></option>
                            <option value="success" <?php selected( $calc_fil, 'success' ); ?>><?php _e( 'Success', 'djv-core' ); ?></option>
                            <option value="failed" <?php selected( $calc_fil, 'failed' ); ?>><?php _e( 'Failed', 'djv-core' ); ?></option>
                            <option value="pending" <?php selected( $calc_fil, 'pending' ); ?>><?php _e( 'Pending', 'djv-core' ); ?></option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#555; display:block; margin-bottom:0.25rem;">
                            📅 <?php _e( 'From Date', 'djv-core' ); ?>
                        </label>
                        <input type="date" name="date_from" value="<?php echo esc_attr( $date_from ); ?>" style="height:34px;">
                    </div>
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#555; display:block; margin-bottom:0.25rem;">
                            📅 <?php _e( 'To Date', 'djv-core' ); ?>
                        </label>
                        <input type="date" name="date_to" value="<?php echo esc_attr( $date_to ); ?>" style="height:34px;">
                    </div>

                    <!-- Buttons -->
                    <div style="display:flex; gap:0.5rem;">
                        <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Filter', 'djv-core' ); ?>" style="height:34px;">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=djv-muhurtham-submissions' ) ); ?>" class="button" style="height:34px; line-height:32px;">
                            <?php _e( 'Reset', 'djv-core' ); ?>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Submissions List Table -->
            <div class="djv-section" style="padding:0; overflow:hidden;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #E8D5C4; display:flex; justify-content:space-between; align-items:center; background:#FAF7F2;">
                    <strong><?php printf( __( 'Found %d Submissions', 'djv-core' ), $total_items ); ?></strong>
                    <span style="font-size:0.8rem; color:#666;">Page <?php echo esc_html( $paged ); ?> of <?php echo esc_html( max( 1, $total_pages ) ); ?></span>
                </div>

                <table class="wp-list-table widefat fixed striped" style="border:none;">
                    <thead>
                        <tr>
                            <th style="width:160px;"><?php _e( 'Reference ID', 'djv-core' ); ?></th>
                            <th style="width:130px;"><?php _e( 'Submitted At', 'djv-core' ); ?></th>
                            <th style="width:200px;"><?php _e( 'Ceremony Event', 'djv-core' ); ?></th>
                            <th style="width:180px;"><?php _e( 'Applicant', 'djv-core' ); ?></th>
                            <th style="width:160px;"><?php _e( 'Location', 'djv-core' ); ?></th>
                            <th style="width:150px;"><?php _e( 'Preferred Dates', 'djv-core' ); ?></th>
                            <th style="width:90px;"><?php _e( 'Mode', 'djv-core' ); ?></th>
                            <th style="width:100px;"><?php _e( 'Calculation', 'djv-core' ); ?></th>
                            <th style="width:110px;"><?php _e( 'Status', 'djv-core' ); ?></th>
                            <th style="width:140px; text-align:right;"><?php _e( 'Actions', 'djv-core' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $items ) ) : ?>
                            <tr>
                                <td colspan="10" style="text-align:center; padding:3rem 1rem; color:#888;">
                                    <div style="font-size:2.2rem; margin-bottom:0.5rem;">📭</div>
                                    <strong><?php _e( 'No Muhurtham submissions found matching your criteria.', 'djv-core' ); ?></strong>
                                    <p style="font-size:0.85rem; margin-top:0.25rem;"><?php _e( 'Try clearing the filters or test submitting a form from the frontend.', 'djv-core' ); ?></p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $items as $row ) :
                                $sub_id = $row['submission_id'];
                                $ev_slug = $row['event_slug'];
                                $ev_info = $all_events[ $ev_slug ] ?? null;
                                $icon = $ev_info['icon'] ?? '✨';
                                $title = $ev_info['title']['en'] ?? $row['event_title'];
                                $calc_st = $row['calculation_status'];
                                $sub_st  = $row['submission_status'];

                                $calc_badge_color = ( $calc_st === 'success' ) ? '#2E7D5B' : ( ( $calc_st === 'failed' ) ? '#B52020' : '#D9822B' );
                                $calc_badge_bg    = ( $calc_st === 'success' ) ? '#EBF8F2' : ( ( $calc_st === 'failed' ) ? '#FDF2F2' : '#FEF8F0' );

                                $sub_badge_color  = ( $sub_st === 'processed' ) ? '#2E7D5B' : ( ( $sub_st === 'in_review' ) ? '#1E6F9F' : ( ( $sub_st === 'cancelled' ) ? '#888' : '#7A2419' ) );
                                $sub_badge_bg     = ( $sub_st === 'processed' ) ? '#EBF8F2' : ( ( $sub_st === 'in_review' ) ? '#EEF6FB' : ( ( $sub_st === 'cancelled' ) ? '#F0F0F1' : '#FBF2EF' ) );
                            ?>
                                <tr id="sub-row-<?php echo esc_attr( $sub_id ); ?>" data-sub='<?php echo esc_attr( wp_json_encode( $row ) ); ?>'>
                                    <td>
                                        <code style="font-weight:700; color:#7A2419; font-size:0.85rem;"><?php echo esc_html( $sub_id ); ?></code>
                                    </td>
                                    <td>
                                        <span style="font-size:0.8rem;"><?php echo esc_html( date( 'd M Y, H:i', strtotime( $row['created_at'] ) ) ); ?></span>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:0.4rem;">
                                            <span style="font-size:1.2rem;"><?php echo esc_html( $icon ); ?></span>
                                            <div>
                                                <strong><?php echo esc_html( $title ); ?></strong><br>
                                                <span style="font-size:0.75rem; color:#888;"><?php echo esc_html( $ev_slug ); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html( $row['applicant_name'] ); ?></strong>
                                        <?php if ( ! empty( $row['contact_email'] ) ) : ?>
                                            <div style="font-size:0.75rem; color:#666;"><?php echo esc_html( $row['contact_email'] ); ?></div>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $row['phone_number'] ) ) : ?>
                                            <div style="font-size:0.75rem; color:#666;"><?php echo esc_html( $row['phone_number'] ); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-size:0.85rem; font-weight:600;"><?php echo esc_html( $row['ceremony_location'] ); ?></span><br>
                                        <span style="font-size:0.75rem; color:#888;"><?php echo esc_html( $row['city'] . ', ' . $row['state'] ); ?></span>
                                    </td>
                                    <td>
                                        <span style="font-size:0.8rem;"><?php echo esc_html( $row['date_from'] . ' → ' . $row['date_to'] ); ?></span><br>
                                        <span style="font-size:0.75rem; color:#888; text-transform:capitalize;"><?php echo esc_html( $row['time_of_day'] ); ?></span>
                                    </td>
                                    <td>
                                        <span style="font-size:0.75rem; font-weight:600; padding:0.15rem 0.45rem; border-radius:4px; background:<?php echo ( $row['calc_mode'] === 'personalized' ? '#EDE7F6' : '#E8F5E9' ); ?>; color:<?php echo ( $row['calc_mode'] === 'personalized' ? '#5E35B1' : '#2E7D32' ); ?>;">
                                            <?php echo ( $row['calc_mode'] === 'personalized' ? 'Personal' : 'Basic' ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-calc-status" style="display:inline-block; font-size:0.75rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:9999px; text-transform:capitalize; background:<?php echo esc_attr( $calc_badge_bg ); ?>; color:<?php echo esc_attr( $calc_badge_color ); ?>;">
                                            <?php echo esc_html( $calc_st ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="post" action="" style="margin:0;">
                                            <?php wp_nonce_field( 'djv_sub_action', '_djv_sub_nonce' ); ?>
                                            <input type="hidden" name="submission_id" value="<?php echo esc_attr( $sub_id ); ?>">
                                            <input type="hidden" name="djv_submission_action" value="update_status">
                                            <select name="new_status" onchange="this.form.submit()" style="font-size:0.75rem; height:26px; padding:0 4px; border-radius:4px; font-weight:600; color:<?php echo esc_attr( $sub_badge_color ); ?>; background:<?php echo esc_attr( $sub_badge_bg ); ?>;">
                                                <option value="received" <?php selected( $sub_st, 'received' ); ?>>Received</option>
                                                <option value="in_review" <?php selected( $sub_st, 'in_review' ); ?>>In Review</option>
                                                <option value="processed" <?php selected( $sub_st, 'processed' ); ?>>Processed</option>
                                                <option value="cancelled" <?php selected( $sub_st, 'cancelled' ); ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:inline-flex; gap:0.25rem;">
                                            <button type="button" class="button button-small btn-view-sub" data-sub-id="<?php echo esc_attr( $sub_id ); ?>" title="<?php esc_attr_e( 'View complete record details', 'djv-core' ); ?>">
                                                👁️
                                            </button>
                                            <form method="post" action="" style="display:inline; margin:0;" onsubmit="return confirm('Recalculate Muhurtham ephemeris for this submission?');">
                                                <?php wp_nonce_field( 'djv_sub_action', '_djv_sub_nonce' ); ?>
                                                <input type="hidden" name="submission_id" value="<?php echo esc_attr( $sub_id ); ?>">
                                                <input type="hidden" name="djv_submission_action" value="retry">
                                                <button type="submit" class="button button-small" title="<?php esc_attr_e( 'Retry calculation', 'djv-core' ); ?>">
                                                    🔄
                                                </button>
                                            </form>
                                            <form method="post" action="" style="display:inline; margin:0;" onsubmit="return confirm('Permanently delete submission <?php echo esc_js( $sub_id ); ?>?');">
                                                <?php wp_nonce_field( 'djv_sub_action', '_djv_sub_nonce' ); ?>
                                                <input type="hidden" name="submission_id" value="<?php echo esc_attr( $sub_id ); ?>">
                                                <input type="hidden" name="djv_submission_action" value="delete">
                                                <button type="submit" class="button button-small" style="color:#B52020;" title="<?php esc_attr_e( 'Delete record', 'djv-core' ); ?>">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ( $total_pages > 1 ) : ?>
                    <div style="padding:1rem 1.25rem; border-top:1px solid #E8D5C4; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <?php if ( $paged > 1 ) : ?>
                                <a href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>" class="button">&laquo; <?php _e( 'Previous', 'djv-core' ); ?></a>
                            <?php endif; ?>
                            <?php if ( $paged < $total_pages ) : ?>
                                <a href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>" class="button"><?php _e( 'Next', 'djv-core' ); ?> &raquo;</a>
                            <?php endif; ?>
                        </div>
                        <span style="font-size:0.85rem; color:#666;">
                            <?php printf( __( 'Showing page %1$d of %2$d (%3$d total items)', 'djv-core' ), $paged, $total_pages, $total_items ); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── SUBMISSION DETAIL MODAL ───────────────────────────────── -->
        <div id="djv-sub-modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:99999; backdrop-filter:blur(3px); align-items:center; justify-content:center; padding:1.5rem;">
            <div style="background:#FFF; border-radius:12px; max-width:850px; width:100%; max-height:90vh; overflow-y:auto; padding:2rem; position:relative; box-shadow:0 10px 40px rgba(0,0,0,0.3);">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E8D5C4; padding-bottom:1rem; margin-bottom:1.5rem;">
                    <div>
                        <span style="font-size:0.75rem; font-weight:700; color:#C89432; text-transform:uppercase; letter-spacing:0.05em;">DJV Universal Muhurtham Registry</span>
                        <h2 id="modal-sub-title" style="margin:0.25rem 0 0 0; font-size:1.4rem; color:#7A2419;">
                            Submission Details
                        </h2>
                    </div>
                    <button type="button" id="btn-close-sub-modal" style="background:none; border:none; font-size:1.75rem; cursor:pointer; color:#888; line-height:1;">&times;</button>
                </div>

                <div id="modal-sub-content">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>

        <script>
        (function($){
            $(document).ready(function(){
                // View Details Modal
                $('.btn-view-sub').on('click', function(){
                    var subId = $(this).data('sub-id');
                    var $row = $('#sub-row-' + subId);
                    var sub = $row.data('sub');
                    if (!sub) return;

                    var html = '<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:1.25rem; margin-bottom:1.5rem;">';
                    
                    // Box 1: Reference & Timing
                    html += '<div style="background:#FAF7F2; padding:1rem; border-radius:8px; border:1px solid #E8D5C4;">';
                    html += '<h4 style="margin:0 0 0.5rem 0; color:#7A2419;">📋 Reference & Timestamps</h4>';
                    html += '<div style="font-size:0.85rem; line-height:1.6;">';
                    html += '<div><strong>ID:</strong> <code style="color:#7A2419;">' + sub.submission_id + '</code></div>';
                    html += '<div><strong>Event:</strong> ' + sub.event_title + ' (<code>' + sub.event_slug + '</code>)</div>';
                    html += '<div><strong>Category:</strong> ' + sub.event_type + '</div>';
                    html += '<div><strong>Created:</strong> ' + sub.created_at + '</div>';
                    html += '<div><strong>Updated:</strong> ' + sub.updated_at + '</div>';
                    html += '</div></div>';

                    // Box 2: Applicant Details
                    html += '<div style="background:#FAF7F2; padding:1rem; border-radius:8px; border:1px solid #E8D5C4;">';
                    html += '<h4 style="margin:0 0 0.5rem 0; color:#7A2419;">👤 Applicant Information</h4>';
                    html += '<div style="font-size:0.85rem; line-height:1.6;">';
                    html += '<div><strong>Name:</strong> ' + sub.applicant_name + '</div>';
                    html += '<div><strong>Email:</strong> ' + (sub.contact_email || '—') + '</div>';
                    html += '<div><strong>Phone:</strong> ' + (sub.phone_number || '—') + '</div>';
                    html += '<div><strong>IP Address:</strong> ' + (sub.ip_address || '—') + '</div>';
                    html += '</div></div>';

                    // Box 3: Location & Preferences
                    html += '<div style="background:#FAF7F2; padding:1rem; border-radius:8px; border:1px solid #E8D5C4;">';
                    html += '<h4 style="margin:0 0 0.5rem 0; color:#7A2419;">📍 Ceremony & Schedule</h4>';
                    html += '<div style="font-size:0.85rem; line-height:1.6;">';
                    html += '<div><strong>Venue:</strong> ' + sub.ceremony_location + '</div>';
                    html += '<div><strong>City/State:</strong> ' + sub.city + ', ' + sub.state + '</div>';
                    html += '<div><strong>Timezone:</strong> ' + sub.timezone + ' (' + Number(sub.latitude).toFixed(4) + '°, ' + Number(sub.longitude).toFixed(4) + '°)</div>';
                    html += '<div><strong>Date Range:</strong> ' + sub.date_from + ' to ' + sub.date_to + '</div>';
                    html += '<div><strong>Time of Day:</strong> ' + sub.time_of_day + '</div>';
                    html += '<div><strong>Calc Mode:</strong> ' + sub.calc_mode + '</div>';
                    html += '</div></div>';

                    // Box 4: Engine & Rules
                    html += '<div style="background:#FAF7F2; padding:1rem; border-radius:8px; border:1px solid #E8D5C4;">';
                    html += '<h4 style="margin:0 0 0.5rem 0; color:#7A2419;">⚙️ Engine & Status</h4>';
                    html += '<div style="font-size:0.85rem; line-height:1.6;">';
                    html += '<div><strong>Rule Version:</strong> ' + sub.rule_version + '</div>';
                    html += '<div><strong>Engine Version:</strong> ' + sub.engine_version + '</div>';
                    html += '<div><strong>Submission Status:</strong> <span style="text-transform:capitalize;">' + sub.submission_status + '</span></div>';
                    html += '<div><strong>Calculation Status:</strong> <span style="text-transform:capitalize; font-weight:700;">' + sub.calculation_status + '</span></div>';
                    if (sub.error_message) {
                        html += '<div style="color:#B52020; margin-top:0.35rem;"><strong>Error:</strong> ' + sub.error_message + '</div>';
                    }
                    html += '</div></div>';

                    html += '</div>';

                    // Personal details (Bride/Groom or birth ephemeris)
                    if (sub.personal_details && typeof sub.personal_details === 'object' && Object.keys(sub.personal_details).length > 0) {
                        html += '<div style="margin-bottom:1.5rem; background:#FFF; border:1px solid #E8D5C4; border-radius:8px; padding:1rem;">';
                        html += '<h4 style="margin:0 0 0.5rem 0; color:#7A2419;">✨ Event-Specific Personal Details</h4>';
                        html += '<pre style="background:#F6F6F6; padding:0.75rem; border-radius:4px; font-size:0.75rem; overflow:auto; max-height:180px;">' + JSON.stringify(sub.personal_details, null, 2) + '</pre>';
                        html += '</div>';
                    }

                    // Calculation Results
                    html += '<div style="margin-bottom:1rem; background:#FFF; border:1px solid #E8D5C4; border-radius:8px; padding:1rem;">';
                    html += '<h4 style="margin:0 0 0.5rem 0; color:#7A2419;">🔮 Calculation Output & Auspicious Muhurthams</h4>';
                    if (sub.results_data && sub.results_data.results && sub.results_data.results.length > 0) {
                        html += '<div style="display:flex; flex-direction:column; gap:0.75rem; margin-top:0.75rem;">';
                        sub.results_data.results.forEach(function(res, i){
                            html += '<div style="background:#FAF7F2; border-left:4px solid #7A2419; padding:0.75rem 1rem; border-radius:4px;">';
                            html += '<div style="display:flex; justify-content:space-between; align-items:center;">';
                            html += '<strong>#' + (i+1) + ' — ' + res.date + ' (' + (res.weekday || '') + ')</strong>';
                            html += '<span style="font-weight:700; color:#2E7D5B;">' + (res.rank_label || res.rank) + ' (' + res.score + '/100)</span>';
                            html += '</div>';
                            html += '<div style="font-size:0.85rem; color:#555; margin-top:0.25rem;">';
                            html += '⏰ Auspicious Window: <strong>' + (res.time_window || (res.timings && res.timings.window_str) || 'Full Day') + '</strong>';
                            if (res.tithi && res.nakshatra) {
                                html += ' • Tithi: ' + res.tithi + ' • Nakshatra: ' + res.nakshatra;
                            }
                            html += '</div></div>';
                        });
                        html += '</div>';
                    } else if (sub.calculation_status === 'failed') {
                        html += '<p style="color:#B52020; font-size:0.9rem;">⚠️ Calculation did not complete successfully. Use the "Retry Calculation" action to re-run ephemeris.</p>';
                    } else {
                        html += '<p style="color:#666; font-size:0.9rem;">No candidate windows qualified under strict Vedic prohibitions for this range.</p>';
                    }
                    html += '</div>';

                    $('#modal-sub-title').text('Submission ' + sub.submission_id + ' — ' + sub.applicant_name);
                    $('#modal-sub-content').html(html);
                    $('#djv-sub-modal-backdrop').css('display', 'flex');
                });

                // Close Modal
                $('#btn-close-sub-modal, #djv-sub-modal-backdrop').on('click', function(e){
                    if (e.target === this) {
                        $('#djv-sub-modal-backdrop').hide();
                    }
                });
            });
        })(jQuery);
        </script>
        <?php
    }

    /* ─── Submissions AJAX Handlers ──────────────────────────────── */

    public function ajax_update_submission_status() {
        check_ajax_referer( 'djv_sub_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Access denied.' ] );
        }

        require_once DJV_PLUGIN_DIR . 'includes/class-djv-muhurtham-submissions.php';
        $sub_id = sanitize_text_field( $_POST['submission_id'] ?? '' );
        $status = sanitize_key( $_POST['status'] ?? '' );

        if ( DJV_Muhurtham_Submissions::update_status( $sub_id, $status ) ) {
            wp_send_json_success( [ 'submission_id' => $sub_id, 'new_status' => $status ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to update submission status.' ] );
        }
    }

    public function ajax_retry_submission_calc() {
        check_ajax_referer( 'djv_sub_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Access denied.' ] );
        }

        require_once DJV_PLUGIN_DIR . 'includes/class-djv-muhurtham-submissions.php';
        $sub_id = sanitize_text_field( $_POST['submission_id'] ?? '' );
        $res = DJV_Muhurtham_Submissions::retry_calculation( $sub_id );

        if ( is_wp_error( $res ) ) {
            wp_send_json_error( [ 'message' => $res->get_error_message() ] );
        } else {
            wp_send_json_success( [ 'submission_id' => $sub_id, 'results' => $res ] );
        }
    }

    public function ajax_delete_submission() {
        check_ajax_referer( 'djv_sub_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Access denied.' ] );
        }

        require_once DJV_PLUGIN_DIR . 'includes/class-djv-muhurtham-submissions.php';
        $sub_id = sanitize_text_field( $_POST['submission_id'] ?? '' );

        if ( DJV_Muhurtham_Submissions::delete_submission( $sub_id ) ) {
            wp_send_json_success( [ 'submission_id' => $sub_id ] );
        } else {
            wp_send_json_error( [ 'message' => 'Failed to delete submission.' ] );
        }
    }

    private function admin_inline_css() {
        return '
        .djv-admin { max-width: 1200px; }
        .djv-admin-header { display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg,#7A2419,#5C1A11); color:white; padding:1.25rem 1.5rem; border-radius:.625rem; margin-bottom:1.5rem; }
        .djv-logo { display:flex; align-items:center; gap:.875rem; }
        .djv-logo-icon { font-size:2rem; }
        .djv-logo h1 { font-size:1.25rem; margin:0; color:white; }
        .djv-logo p { font-size:.75rem; color:rgba(255,255,255,.7); margin:0; }
        .djv-version-badge { background:rgba(200,148,50,.2); border:1px solid rgba(200,148,50,.4); color:#E7B75A; padding:.3rem .75rem; border-radius:9999px; font-size:.75rem; font-weight:600; }
        .djv-stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1rem; margin-bottom:1.5rem; }
        .djv-stat-card { background:white; border:1px solid #E8D5C4; border-radius:.625rem; padding:1.25rem; text-align:center; box-shadow:0 2px 8px rgba(122,36,25,.06); }
        .djv-stat-card.djv-stat-online { border-color:#4CAF50; background:#F1FBF6; }
        .djv-stat-card.djv-stat-offline { border-color:#F44336; background:#FFF5F5; }
        .djv-stat-icon { font-size:1.5rem; margin-bottom:.5rem; }
        .djv-stat-label { font-size:.75rem; color:#A08070; font-weight:600; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.25rem; }
        .djv-stat-value { font-size:1.5rem; font-weight:700; color:#241914; }
        .djv-section { background:white; border:1px solid #E8D5C4; border-radius:.625rem; padding:1.25rem; margin-bottom:1.25rem; }
        .djv-section h2 { font-size:1rem; margin:0 0 1rem; color:#241914; }
        .djv-actions-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:.625rem; }
        .djv-action-btn { display:inline-flex; align-items:center; justify-content:center; padding:.6rem 1rem; background:#7A2419; color:white; border:none; border-radius:.5rem; font-size:.825rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .2s; font-family:inherit; }
        .djv-action-btn:hover { background:#5C1A11; color:white; transform:translateY(-1px); }
        .djv-action-btn.djv-action-warn { background:#B52020; }
        .djv-action-btn.djv-action-warn:hover { background:#8B0000; }
        .djv-output-box { background:#1E1E1E; color:#D4D4D4; border-radius:.625rem; padding:1.25rem; margin-bottom:1.25rem; }
        .djv-output-title { font-size:.75rem; color:#888; font-weight:600; margin-bottom:.5rem; text-transform:uppercase; }
        .djv-output-box pre { white-space:pre-wrap; font-size:.78rem; max-height:400px; overflow:auto; }
        .djv-notice-section h2 { color:#7A2419; }
        .djv-checklist { display:flex; flex-direction:column; gap:.625rem; }
        .djv-check-item { display:flex; align-items:flex-start; gap:.625rem; cursor:pointer; }
        .djv-check-item input { margin-top:.2rem; flex-shrink:0; }
        .djv-check-item span { font-size:.875rem; line-height:1.5; }
        ';
    }
}
