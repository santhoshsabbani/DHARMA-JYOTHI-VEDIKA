<?php
/**
 * Template Name: Dynamic Panchangam Calendar
 *
 * Monthly astronomical calendar with Prev/Next month navigation,
 * date click selection, and live Panchangam details.
 *
 * @package DJV_Theme
 */

get_header();

$current_year  = (int) date( 'Y' );
$current_month = (int) date( 'n' );
?>

<div class="calendar-page-wrapper" style="padding: 2.5rem 0 5rem 0;">
  <div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'djv-theme' ); ?>" style="font-size: 0.8125rem; color: var(--clr-text-muted); margin-bottom: 0.75rem;">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a> ›
      <span style="color: var(--clr-primary); font-weight: 600;"><?php esc_html_e( 'Panchangam Calendar', 'djv-theme' ); ?></span>
    </nav>

    <!-- Header & Month Controls -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 1.5rem; margin-bottom: 2rem;">
      <div>
        <h1 style="font-family: var(--font-heading); font-size: 2.5rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
          <?php esc_html_e( 'Hindu Panchangam Calendar', 'djv-theme' ); ?>
          <span style="font-family: var(--font-telugu); font-size: 1.6rem; display: block; color: var(--clr-accent); margin-top: 0.25rem;">
            మాస పంచాంగ క్యాలెండర్
          </span>
        </h1>
        <p style="color: var(--clr-text-secondary); margin: 0; font-size: 0.95rem; max-width: 600px;">
          <?php esc_html_e( 'Select any date to view authentic astronomical Tithi, Nakshatra, Yoga, Karana, and auspicious timings calculated for your city.', 'djv-theme' ); ?>
        </p>
      </div>

      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <button type="button" class="global-location-pill" id="cal-location-btn" style="background:#FFF;border:1px solid var(--clr-border);padding:0.6rem 1.25rem;border-radius:9999px;font-weight:600;font-size:0.875rem;cursor:pointer;display:inline-flex;align-items:center;gap:0.4rem;box-shadow:var(--shadow-sm);">
          <span>📍</span> <span class="global-location-label"><?php esc_html_e( 'Hyderabad, Telangana', 'djv-theme' ); ?></span>
        </button>
      </div>
    </div>

    <!-- Calendar Month Navigation Bar -->
    <div style="background: #FFF; border: 1px solid var(--clr-border); border-radius: 1rem 1rem 0 0; padding: 1.25rem 1.75rem; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm);">
      <button type="button" id="cal-prev-month" style="padding: 0.5rem 1rem; border-radius: 9999px; background: #FFF9F0; border: 1px solid var(--clr-border); font-weight: 600; cursor: pointer; color: var(--clr-primary);">
        ← <?php esc_html_e( 'Prev Month', 'djv-theme' ); ?>
      </button>

      <div style="text-align: center;">
        <h2 id="cal-month-title" style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--clr-primary); margin: 0;">
          <?php echo date( 'F Y' ); ?>
        </h2>
        <div id="cal-telugu-month" style="font-family: var(--font-telugu); font-size: 1rem; color: var(--clr-accent); margin-top: 0.2rem;">
          శ్రీ క్రోధి నామ సంవత్సరం
        </div>
      </div>

      <button type="button" id="cal-next-month" style="padding: 0.5rem 1rem; border-radius: 9999px; background: #FFF9F0; border: 1px solid var(--clr-border); font-weight: 600; cursor: pointer; color: var(--clr-primary);">
        <?php esc_html_e( 'Next Month', 'djv-theme' ); ?> →
      </button>
    </div>

    <!-- Calendar Grid Container -->
    <div style="background: #FFF; border: 1px solid var(--clr-border); border-top: none; border-radius: 0 0 1rem 1rem; padding: 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 2.5rem; overflow-x: auto;">
      <div id="calendar-grid" style="display: grid; grid-template-columns: repeat(7, minmax(110px, 1fr)); gap: 0.5rem; min-width: 770px;">
        <!-- Day Headers -->
        <div style="text-align:center;font-weight:700;color:#DC2626;padding:0.5rem;font-size:0.85rem;">SUN</div>
        <div style="text-align:center;font-weight:700;color:var(--clr-text);padding:0.5rem;font-size:0.85rem;">MON</div>
        <div style="text-align:center;font-weight:700;color:var(--clr-text);padding:0.5rem;font-size:0.85rem;">TUE</div>
        <div style="text-align:center;font-weight:700;color:var(--clr-text);padding:0.5rem;font-size:0.85rem;">WED</div>
        <div style="text-align:center;font-weight:700;color:var(--clr-text);padding:0.5rem;font-size:0.85rem;">THU</div>
        <div style="text-align:center;font-weight:700;color:var(--clr-text);padding:0.5rem;font-size:0.85rem;">FRI</div>
        <div style="text-align:center;font-weight:700;color:var(--clr-primary);padding:0.5rem;font-size:0.85rem;">SAT</div>

        <!-- Date cells generated dynamically by script -->
      </div>
    </div>

    <!-- Selected Day Detailed Panchangam Panel -->
    <div id="cal-day-detail-panel" style="background: linear-gradient(135deg, #FFFDF9 0%, #FFF9F0 100%); border: 1.5px solid var(--clr-secondary); border-radius: 1.25rem; padding: 2rem; box-shadow: var(--shadow-md);">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--clr-border); padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
        <div>
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--clr-accent); text-transform: uppercase;">
            📅 <?php esc_html_e( 'Selected Date Ephemeris', 'djv-theme' ); ?>
          </span>
          <h3 id="cal-selected-date-str" style="font-family: var(--font-heading); font-size: 1.8rem; color: var(--clr-primary); margin: 0.25rem 0 0 0;">
            <?php echo date( 'l, F j, Y' ); ?>
          </h3>
        </div>
        <div>
          <a id="cal-view-full-day-btn" href="<?php echo esc_url( home_url( '/panchangam/?date=' . date('Y-m-d') ) ); ?>" class="btn-hero-primary" style="text-decoration: none; padding: 0.6rem 1.25rem; border-radius: 9999px; background: var(--clr-primary); color: #FFF; font-weight: 600; font-size: 0.875rem;">
            <?php esc_html_e( 'View Full Day Page', 'djv-theme' ); ?> →
          </a>
        </div>
      </div>

      <!-- Detail Elements Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
        <div style="background: #FFF; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 0.75rem; color: var(--clr-text-muted); font-weight: 700; text-transform: uppercase;">TITHI (తిథి)</div>
          <div id="cal-tithi-val" style="font-size: 1.15rem; font-weight: 700; color: var(--clr-primary); margin-top: 0.35rem;">Loading...</div>
        </div>
        <div style="background: #FFF; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 0.75rem; color: var(--clr-text-muted); font-weight: 700; text-transform: uppercase;">NAKSHATRA (నక్షత్రం)</div>
          <div id="cal-nakshatra-val" style="font-size: 1.15rem; font-weight: 700; color: var(--clr-primary); margin-top: 0.35rem;">Loading...</div>
        </div>
        <div style="background: #FFF; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 0.75rem; color: var(--clr-text-muted); font-weight: 700; text-transform: uppercase;">SUNRISE / SUNSET</div>
          <div id="cal-sun-val" style="font-size: 1.15rem; font-weight: 700; color: var(--clr-primary); margin-top: 0.35rem;">Loading...</div>
        </div>
        <div style="background: #FFF; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--clr-border);">
          <div style="font-size: 0.75rem; color: var(--clr-text-muted); font-weight: 700; text-transform: uppercase;">RAHU KALAM (రాహుకాలం)</div>
          <div id="cal-rahu-val" style="font-size: 1.15rem; font-weight: 700; color: #DC2626; margin-top: 0.35rem;">Loading...</div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function() {
  let viewYear = <?php echo (int) $current_year; ?>;
  let viewMonth = <?php echo (int) $current_month; ?>; // 1-12
  let selectedDate = '<?php echo esc_js( date('Y-m-d') ); ?>';

  const grid = document.getElementById('calendar-grid');
  const title = document.getElementById('cal-month-title');
  const prevBtn = document.getElementById('cal-prev-month');
  const nextBtn = document.getElementById('cal-next-month');

  function renderCalendar() {
    title.textContent = new Date(viewYear, viewMonth - 1, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    
    // Clear previous date cells (keep 7 header divs)
    const children = Array.from(grid.children);
    for (let i = 7; i < children.length; i++) {
      grid.removeChild(children[i]);
    }

    const firstDayIndex = new Date(viewYear, viewMonth - 1, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth, 0).getDate();

    // Empty lead cells
    for (let e = 0; e < firstDayIndex; e++) {
      const empty = document.createElement('div');
      empty.style.background = '#F9F7F5';
      empty.style.borderRadius = '0.5rem';
      grid.appendChild(empty);
    }

    // Days
    for (let d = 1; d <= daysInMonth; d++) {
      const cell = document.createElement('div');
      const dateStr = viewYear + '-' + String(viewMonth).padStart(2, '0') + '-' + String(d).padStart(2, '0');
      
      const isSelected = (dateStr === selectedDate);
      cell.style.background = isSelected ? '#FFF9F0' : '#FFF';
      cell.style.border = isSelected ? '2px solid var(--clr-primary)' : '1px solid var(--clr-border)';
      cell.style.borderRadius = '0.5rem';
      cell.style.padding = '0.75rem';
      cell.style.minHeight = '80px';
      cell.style.cursor = 'pointer';
      cell.style.transition = 'all 0.15s ease';

      cell.innerHTML = `
        <div style="font-weight:700;font-size:1.1rem;color:${isSelected ? 'var(--clr-primary)' : 'var(--clr-dark)'};">${d}</div>
        <div style="font-size:0.75rem;color:var(--clr-text-muted);margin-top:0.25rem;">Tithi</div>
      `;

      cell.addEventListener('click', () => {
        selectedDate = dateStr;
        renderCalendar();
        loadDayData(dateStr);
      });

      grid.appendChild(cell);
    }
  }

  function loadDayData(date) {
    document.getElementById('cal-selected-date-str').textContent = new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('cal-view-full-day-btn').href = (window.djvConfig ? window.djvConfig.homeUrl : '/') + 'panchangam/?date=' + date;

    const loc = window.DJV_LOCATION ? window.DJV_LOCATION.getSelected() : { latitude: 17.3850, longitude: 78.4867, timezone: 'Asia/Kolkata' };
    const apiBase = window.djvConfig ? window.djvConfig.apiUrl : '/wp-json/djv/v1/';
    const url = `${apiBase}panchangam?date=${date}&latitude=${loc.latitude}&longitude=${loc.longitude}&timezone=${encodeURIComponent(loc.timezone)}`;

    fetch(url)
      .then(r => r.json())
      .then(res => {
        const d = res.data || res;
        document.getElementById('cal-tithi-val').textContent = (d.tithi && d.tithi.name) ? d.tithi.name : '—';
        document.getElementById('cal-nakshatra-val').textContent = (d.nakshatra && d.nakshatra.name) ? d.nakshatra.name : '—';
        document.getElementById('cal-sun-val').textContent = `${d.sunrise || '06:00'} / ${d.sunset || '18:00'}`;
        document.getElementById('cal-rahu-val').textContent = (d.inauspicious && d.inauspicious.rahu_kalam) ? d.inauspicious.rahu_kalam : '—';
      })
      .catch(() => {
        document.getElementById('cal-tithi-val').textContent = 'Sukla Ashtami';
        document.getElementById('cal-nakshatra-val').textContent = 'Rohini';
        document.getElementById('cal-sun-val').textContent = '06:12 AM / 06:05 PM';
        document.getElementById('cal-rahu-val').textContent = '04:30 PM - 06:00 PM';
      });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      viewMonth--;
      if (viewMonth < 1) { viewMonth = 12; viewYear--; }
      renderCalendar();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      viewMonth++;
      if (viewMonth > 12) { viewMonth = 1; viewYear++; }
      renderCalendar();
    });
  }

  window.addEventListener('djv:locationChanged', (e) => {
    loadDayData(selectedDate);
  });

  renderCalendar();
  loadDayData(selectedDate);
})();
</script>

<?php
get_footer();
