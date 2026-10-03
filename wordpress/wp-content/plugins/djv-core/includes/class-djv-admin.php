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
        add_action( 'wp_ajax_djv_clear_cache',      [ $this, 'ajax_clear_cache' ] );
        add_action( 'wp_ajax_djv_test_engine',      [ $this, 'ajax_test_engine' ] );
        add_action( 'wp_ajax_djv_precalculate',     [ $this, 'ajax_precalculate' ] );
        add_action( 'wp_ajax_djv_test_api_endpoint',[ $this, 'ajax_test_api_endpoint' ] );
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

        add_submenu_page( self::MENU_SLUG, __( 'Dashboard', 'djv-core' ),       __( 'Dashboard', 'djv-core' ),       'manage_options', self::MENU_SLUG,           [ $this, 'render_dashboard' ] );
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
                🔥 <?php _e( 'Pre-warm This Month', 'djv-core' ); ?>
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
              $btn.prop('disabled', false).text('🔥 Pre-warm This Month');
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
            update_option( 'djv_default_tz',      floatval( $_POST['djv_default_tz']  ?? 5.5 ) );
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
              <tr><th><?php _e('Default TZ Offset', 'djv-core'); ?></th><td><input type="number" name="djv_default_tz" value="<?php echo esc_attr(get_option('djv_default_tz',5.5)); ?>" step="0.25" min="-14" max="14" class="small-text" /><p class="description"><?php _e('Hours from UTC (IST = 5.5)', 'djv-core'); ?></p></td></tr>
              <tr><th><?php _e('Ayanamsa', 'djv-core'); ?></th><td><select name="djv_ayanamsa"><option value="lahiri" <?php selected(get_option('djv_ayanamsa','lahiri'),'lahiri'); ?>>Lahiri (Chitrapaksha)</option><option value="raman" <?php selected(get_option('djv_ayanamsa'),'raman'); ?>>B.V. Raman</option></select></td></tr>
              <tr><th><?php _e('Month System', 'djv-core'); ?></th><td><select name="djv_month_system"><option value="amanta" <?php selected(get_option('djv_month_system','amanta'),'amanta'); ?>>Amanta (South Indian)</option><option value="purnimanta" <?php selected(get_option('djv_month_system'),'purnimanta'); ?>>Purnimanta (North Indian)</option></select></td></tr>
              <tr><th><?php _e('Enable Cache', 'djv-core'); ?></th><td><input type="checkbox" name="djv_cache_enabled" <?php checked(get_option('djv_cache_enabled',1)); ?> /> <span class="description"><?php _e('Recommended: ON. Disabling causes live calculation on every request.', 'djv-core'); ?></span></td></tr>
            </table>
            <p class="submit"><input type="submit" name="djv_save_settings" class="button-primary" value="<?php _e('Save Settings', 'djv-core'); ?>" /></p>
          </form>
        </div>
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
              <tr><td><strong><?php _e('Engine Runner Script', 'djv-core'); ?></strong></td><td><?php echo $runner_exists ? '✅ Found' : '❌ Not found at expected path'; ?></td></tr>
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
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error([ 'message' => 'Access denied.' ]);

        $date = sanitize_text_field( $_POST['date'] ?? date('Y-m-d') );
        $lat  = floatval( $_POST['lat'] ?? 17.3850 );
        $lon  = floatval( $_POST['lon'] ?? 78.4867 );
        $tz   = floatval( $_POST['tz']  ?? 5.5 );

        $panchangam = new DJV_Panchangam();
        $result = $panchangam->get_panchangam( $date, $lat, $lon, $tz );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error([ 'message' => $result->get_error_message() ]);
        } else {
            wp_send_json_success( $result );
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

        $year  = intval( $_POST['year']  ?? date('Y') );
        $month = intval( $_POST['month'] ?? date('n') );
        $lat   = floatval( $_POST['lat'] ?? 17.3850 );
        $lon   = floatval( $_POST['lon'] ?? 78.4867 );
        $tz    = floatval( $_POST['tz']  ?? 5.5 );

        set_time_limit( 120 ); // Allow longer execution for pre-warm

        $panchangam = new DJV_Panchangam();
        $result = $panchangam->precalculate_month( $year, $month, $lat, $lon, $tz );

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
            echo '<div class="notice notice-error"><p><strong>⚠️ Node.js not found.</strong> The Panchangam engine requires Node.js on the server. Install Node.js or define <code>DJV_NODE_PATH</code> in wp-config.php.</p></div>';
        }
        if ( $stats['cached_dates'] === 0 ) {
            echo '<div class="notice notice-warning"><p><strong>📭 No cached data.</strong> The Panchangam cache is empty. Run "Pre-warm This Month" to populate it.</p></div>';
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
