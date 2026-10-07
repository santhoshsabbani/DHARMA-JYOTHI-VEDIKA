/**
 * Dharma Jyothi Vedika — Panchangam REST API Client
 *
 * Connects the WordPress theme directly to DJV Core REST API (/wp-json/djv/v1/).
 * Consumes Panchangam calculated dynamically by DJV Core & Node.js Engine.
 * NEVER calls the Node.js engine directly from the browser.
 */

(function () {
  'use strict';

  // Config from wp_localize_script or fallback defaults
  const config = window.djvConfig || {
    apiUrl: '/wp-json/djv/v1/',
    defaultLat: 17.3850,
    defaultLon: 78.4867,
    defaultTz: 'Asia/Kolkata',
    defaultCity: 'Hyderabad',
    defaultState: 'Telangana'
  };

  // Helper normalization functions
  const normalizeLocation = (typeof window !== 'undefined' && window.IndiaLocations && window.IndiaLocations.normalizeLocation)
    || (typeof window !== 'undefined' && window.djvNormalizeLocation)
    || function (loc, fallback) {
      const fb = fallback || {
        name: 'Hyderabad',
        city: 'Hyderabad',
        state: 'Telangana',
        latitude: 17.3850,
        longitude: 78.4867,
        lat: 17.3850,
        lon: 78.4867,
        timezone: 'Asia/Kolkata'
      };
      if (!loc || typeof loc !== 'object') return { ...fb };
      const rawLat = (loc.latitude !== undefined && loc.latitude !== null && loc.latitude !== '')
        ? loc.latitude
        : ((loc.lat !== undefined && loc.lat !== null && loc.lat !== '') ? loc.lat : (fb.latitude ?? fb.lat));
      const rawLon = (loc.longitude !== undefined && loc.longitude !== null && loc.longitude !== '')
        ? loc.longitude
        : ((loc.lon !== undefined && loc.lon !== null && loc.lon !== '') ? loc.lon : (fb.longitude ?? fb.lon));
      const latitude = Number(rawLat);
      const longitude = Number(rawLon);
      if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
        if (fallback) return { ...fb };
        throw new Error('Invalid location coordinates');
      }
      const name = typeof (loc.name || loc.city) === 'string' && (loc.name || loc.city).trim()
        ? (loc.name || loc.city).trim() : (fb.name || 'Hyderabad');
      const state = typeof loc.state === 'string' && loc.state.trim()
        ? loc.state.trim() : (fb.state || 'Telangana');
      let timezone = typeof loc.timezone === 'string' && loc.timezone.trim() ? loc.timezone.trim() : '';
      if (!timezone || /^[0-9.+-]+$/.test(timezone)) {
        timezone = fb.timezone || 'Asia/Kolkata';
      }
      return {
        ...loc,
        name,
        city: name,
        state,
        latitude,
        longitude,
        lat: latitude,
        lon: longitude,
        timezone
      };
    };

  const formatCoordinate = (typeof window !== 'undefined' && window.IndiaLocations && window.IndiaLocations.formatCoordinate)
    || (typeof window !== 'undefined' && window.djvFormatCoordinate)
    || function (value, digits = 4) {
      const number = Number(value);
      if (!Number.isFinite(number)) return '—';
      return number.toFixed(digits);
    };

  // State
  let currentDate = getTodayIST();
  let currentLocation = getActiveLocation();
  const cache = new Map();

  function getTodayIST() {
    try {
      const parts = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Asia/Kolkata',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      }).formatToParts(new Date());
      const map = {};
      for (const p of parts) map[p.type] = p.value;
      return `${map.year}-${map.month}-${map.day}`;
    } catch (e) {
      const d = new Date(Date.now() + 5.5 * 3600000);
      return d.toISOString().split('T')[0];
    }
  }

  function getActiveLocation() {
    const fallbackLoc = {
      name: config.defaultCity || 'Hyderabad',
      city: config.defaultCity || 'Hyderabad',
      state: config.defaultState || 'Telangana',
      latitude: Number(config.defaultLat ?? 17.3850),
      longitude: Number(config.defaultLon ?? 78.4867),
      lat: Number(config.defaultLat ?? 17.3850),
      lon: Number(config.defaultLon ?? 78.4867),
      timezone: (config.defaultTz && !/^[0-9.+-]+$/.test(config.defaultTz)) ? config.defaultTz : 'Asia/Kolkata'
    };

    try {
      const raw = localStorage.getItem('djv_selected_location') || localStorage.getItem('djv_user_location');
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === 'object') {
          return normalizeLocation(parsed, fallbackLoc);
        }
      }
    } catch (e) {}

    return normalizeLocation(fallbackLoc);
  }

  function buildCacheKey(date, lat, lon, tz) {
    return `${date}_${formatCoordinate(lat, 4)}_${formatCoordinate(lon, 4)}_${tz || 'Asia/Kolkata'}`;
  }

  /**
   * Universal formatters for dates and times
   */
  function formatTime(val, timezone = 'Asia/Kolkata') {
    if (!val) return '—';
    if (typeof val === 'string' && (val.includes('AM') || val.includes('PM'))) return val;
    try {
      const d = new Date(val);
      if (isNaN(d.getTime())) return String(val);
      return new Intl.DateTimeFormat('en-US', {
        timeZone: timezone,
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      }).format(d);
    } catch (e) {
      return String(val);
    }
  }

  function formatPeriod(period, timezone = 'Asia/Kolkata') {
    if (!period) return '—';
    if (typeof period === 'string') return period;
    if (period.text) return period.text;
    if (period.startStr && period.endStr) return `${period.startStr} – ${period.endStr}`;
    if (period.start && period.end) {
      return `${formatTime(period.start, timezone)} – ${formatTime(period.end, timezone)}`;
    }
    return '—';
  }

  function formatMoonEvent(evt, type, tz = 'Asia/Kolkata') {
    if (!evt) return type === 'moonrise' ? 'No Moonrise' : 'No Moonset';
    if (evt.status === 'no_event') {
      return type === 'moonrise' ? 'No Moonrise' : 'No Moonset';
    }
    if (evt.time) return evt.time;
    if (evt.timeStr) return evt.timeStr;
    if (evt.datetime) return formatTime(evt.datetime, tz);
    if (typeof evt === 'string' && evt !== '—') return evt;
    return type === 'moonrise' ? 'No Moonrise' : 'No Moonset';
  }

  const RULER_TELUGU = {
    'Sun': 'రవి (సూర్యుడు)',
    'Moon': 'చంద్రుడు',
    'Mars': 'కుజుడు (అంగారకుడు)',
    'Mercury': 'బుధుడు',
    'Jupiter': 'గురు (బృహస్పతి)',
    'Venus': 'శుక్రుడు',
    'Saturn': 'శని భగవానుడు',
    'Rahu': 'రాహువు',
    'Ketu': 'కేతువు'
  };

  function unwrapPanchangamData(raw) {
    if (!raw) return null;
    let p = raw;
    if (p.data && typeof p.data === 'object' && !p.vara) {
      p = p.data;
    }
    if (p.panchangam && typeof p.panchangam === 'object') {
      p = p.panchangam;
    }
    if (p.data && typeof p.data === 'object' && !p.vara) {
      p = p.data;
    }
    return p;
  }

  /**
   * Fetch Panchangam from WordPress REST API.
   */
  async function fetchPanchangamData(date, location) {
    const normalized = normalizeLocation(location);
    const latitude = Number(normalized.latitude);
    const longitude = Number(normalized.longitude);
    const timezone = normalized.timezone || 'Asia/Kolkata';

    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
      throw new Error('Invalid location coordinates');
    }

    const key = buildCacheKey(date, latitude, longitude, timezone);
    if (cache.has(key)) {
      return cache.get(key);
    }

    const endpoint = `${config.apiUrl.replace(/\/$/, '')}/panchangam`;
    const params = new URLSearchParams({
      date: date,
      latitude: latitude.toString(),
      longitude: longitude.toString(),
      timezone: timezone,
      region: 'telugu',
      language: 'en',
      ayanamsa: 'lahiri'
    });

    const response = await fetch(`${endpoint}?${params.toString()}`, {
      headers: {
        'Accept': 'application/json',
        'X-WP-Nonce': config.nonce || ''
      }
    });

    if (!response.ok) {
      let errorMsg = `WordPress REST API returned HTTP ${response.status}`;
      try {
        const errJson = await response.json();
        if (errJson && errJson.message) errorMsg = errJson.message;
        else if (errJson && errJson.error && errJson.error.message) errorMsg = errJson.error.message;
      } catch (_) {}
      throw new Error(errorMsg);
    }

    const json = await response.json();
    if (!json.success || !json.data) {
      throw new Error(json.error ? (json.error.message || json.error) : 'Invalid response from Panchangam API');
    }

    const panchangam = unwrapPanchangamData(json.data);
    cache.set(key, panchangam);
    return panchangam;
  }

  function decodeUnicodeEscapes(str) {
    if (!str || typeof str !== 'string') return str;
    if (str.includes('u0c') || str.includes('u09') || str.includes('\\u')) {
      return str.replace(/\\?u([0-9a-fA-F]{4})/g, (_, hex) => String.fromCharCode(parseInt(hex, 16)));
    }
    return str;
  }

  /**
   * Helper to set text content safely
   */
  function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = decodeUnicodeEscapes(text);
  }

  function setHTML(id, html) {
    const el = document.getElementById(id);
    if (el) el.innerHTML = html;
  }

  /**
   * Clear display to prevent stale data flashing during loading
   */
  function setViewsToLoading() {
    // Show loading indicators
    const fullLoading = document.getElementById('pc-loading-state');
    if (fullLoading) fullLoading.style.display = 'block';

    const fullContent = document.getElementById('pc-content-container');
    if (fullContent) fullContent.style.opacity = '0.4';

    const heroLoading = document.getElementById('hero-pc-loading');
    if (heroLoading) heroLoading.style.display = 'block';

    const errorBanner = document.getElementById('pc-error-banner');
    if (errorBanner) errorBanner.style.display = 'none';

    const heroError = document.getElementById('hero-pc-error');
    if (heroError) heroError.style.display = 'none';
  }

  function setViewsLoaded() {
    const fullLoading = document.getElementById('pc-loading-state');
    if (fullLoading) fullLoading.style.display = 'none';

    const fullContent = document.getElementById('pc-content-container');
    if (fullContent) fullContent.style.opacity = '1';

    const heroLoading = document.getElementById('hero-pc-loading');
    if (heroLoading) heroLoading.style.display = 'none';

    const heroContent = document.getElementById('hero-pc-data');
    if (heroContent) heroContent.style.display = '';

    const fpLoading = document.getElementById('full-pc-loading');
    if (fpLoading) fpLoading.style.display = 'none';

    const fpContent = document.getElementById('full-pc-data');
    if (fpContent) fpContent.style.display = '';
  }

  /**
   * Render Today's Panchangam Page & Template Views.
   */
  function renderTodayPanchangamView(data, date, location) {
    try {
      const loc = normalizeLocation(location);
      const tz = loc.timezone || 'Asia/Kolkata';

      const dObj = new Date(date + 'T12:00:00Z');
      const formattedEn = dObj.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      });

      // ── Header & Hero Titles ──
      const varaTe = data.vara ? (data.vara.nameTe || '') : '';
      const tithiTe = data.tithi ? (data.tithi.nameTe || '') : '';
      const nakTe = data.nakshatra ? ((data.nakshatra.nakshatra ? data.nakshatra.nakshatra.nameTe : data.nakshatra.nameTe) || '') : '';

      setText('pc-date-heading', `${formattedEn} · ${varaTe ? varaTe + ', ' : ''}${tithiTe ? tithiTe + ', ' : ''}${nakTe}`);
      setText('pc-location-heading', `📍 ${loc.name}, ${loc.state}`);
      setText('pc-coord-badge', `${formatCoordinate(loc.latitude, 4)}° N, ${formatCoordinate(loc.longitude, 4)}° E`);
      setText('pc-tz-badge', `Timezone: ${tz}`);
      setText('pc-current-location-text', `${loc.name}, ${loc.state}`);
      setText('pc-loc-btn-label', `${loc.name}, ${loc.state}`);

      const locPill = document.getElementById('global-location-name');
      if (locPill) locPill.textContent = loc.name;

      // ── 1. Sun & Moon Timings ──
      if (data.solar) {
        setText('val-sunrise', data.solar.sunriseStr || formatTime(data.solar.sunrise, tz));
        setText('val-sunset',  data.solar.sunsetStr  || formatTime(data.solar.sunset, tz));
        setText('val-solarnoon', data.solar.solarNoonStr || formatTime(data.solar.solarNoon, tz));
        setText('val-daylength', data.solar.dayLengthStr || '—');
      }

      const mrObj = data.moonrise || (data.lunar ? data.lunar.moonrise : null);
      const msObj = data.moonset  || (data.lunar ? data.lunar.moonset : null);
      setText('val-moonrise', formatMoonEvent(mrObj, 'moonrise', tz));
      setText('val-moonset',  formatMoonEvent(msObj, 'moonset', tz));

      // ── 2. Pancha Angas (5 Limbs) ──
      // Vara
      if (data.vara) {
        setText('val-vara-en', data.vara.en || data.vara.name);
        setText('val-vara-te', data.vara.nameTe || '');
        if (data.vara.ruler) {
          const rulerTe = RULER_TELUGU[data.vara.ruler] || data.vara.ruler;
          setText('val-vara-ruler', `Ruler: ${data.vara.ruler} (${rulerTe})`);
        }
      }

      // Tithi
      if (data.tithi) {
        const pakshaName = data.tithi.paksha ? `${data.tithi.paksha} Paksha` : '';
        setText('val-tithi-name', `${data.tithi.name} (${pakshaName})`);
        setText('val-tithi-te', data.tithi.nameTe || '');
        const tEnd = data.tithi.spanStr || (data.tithi.endStr ? `Ends at ${data.tithi.endStr}` : (data.tithi.endTime ? `Ends at ${formatTime(data.tithi.endTime, tz)}` : ''));
        setText('val-tithi-end', tEnd);
        setText('val-summary-paksha-pill', pakshaName || 'Panchangam');
      }

      // Nakshatra
      if (data.nakshatra) {
        const nObj = data.nakshatra.nakshatra || data.nakshatra;
        setText('val-nakshatra-name', nObj.name || '—');
        setText('val-nakshatra-te', nObj.nameTe || '');
        const padaStr = data.nakshatra.pada ? `${data.nakshatra.pada}${getOrdinal(data.nakshatra.pada)} Pada (${data.nakshatra.pada}వ పాదం)` : '';
        const nEnd = data.nakshatra.spanStr || (data.nakshatra.endStr ? `Ends at ${data.nakshatra.endStr}` : (data.nakshatra.endTime ? `Ends at ${formatTime(data.nakshatra.endTime, tz)}` : ''));
        setText('val-nakshatra-end', [padaStr, nEnd].filter(Boolean).join(' · '));
        if (nObj.ruler || nObj.deity) {
          setText('val-nakshatra-meta', `Deity: ${nObj.deity || '—'} · Ruler: ${nObj.ruler || '—'}`);
        }
      }

      // Yoga
      if (data.yoga) {
        setText('val-yoga-name', data.yoga.name || '—');
        setText('val-yoga-te', data.yoga.nameTe || '');
        const yEnd = data.yoga.spanStr || (data.yoga.endStr ? `Ends at ${data.yoga.endStr}` : '');
        setText('val-yoga-end', yEnd);
      }

      // Karana
      if (data.karana) {
        setText('val-karana-name', data.karana.name || '—');
        setText('val-karana-te', data.karana.nameTe || '');
        const kEnd = data.karana.spanStr || (data.karana.endStr ? `Ends at ${data.karana.endStr}` : '');
        setText('val-karana-end', kEnd);
      }

      // ── 3. Auspicious Timings ──
      if (data.timings) {
        setText('val-abhijit', formatPeriod(data.timings.abhijitMuhurtham, tz));
        setText('val-amritkalam', formatPeriod(data.timings.amritKalam, tz));
        setText('val-brahmamuhurtham', formatPeriod(data.timings.brahmaMuhurtham, tz));

        // ── 4. Inauspicious Timings ──
        setText('val-rahukalam', formatPeriod(data.timings.rahuKalam, tz));
        setText('val-yamagandam', formatPeriod(data.timings.yamagandam, tz));
        setText('val-gulikakalam', formatPeriod(data.timings.gulikaKalam, tz));

        if (data.timings.durMuhurtham) {
          if (Array.isArray(data.timings.durMuhurtham)) {
            const periods = data.timings.durMuhurtham.map(dm => formatPeriod(dm, tz)).filter(Boolean);
            setText('val-durmuhurtham', periods.join(' & ') || '—');
          } else {
            setText('val-durmuhurtham', formatPeriod(data.timings.durMuhurtham, tz));
          }
        }
        setText('val-varjyam', formatPeriod(data.timings.varjyam, tz));
      }

      // ── 5. Lunar Phase & Illumination ──
      if (data.lunar) {
        setText('val-moonphase', data.lunar.phase || '—');
        setText('val-moonphase-te', data.lunar.phaseTe || '—');
        setText('val-illumination', data.lunar.illumination ? `${data.lunar.illumination}` : '—');
      }

      // ── 6. Engine Metadata Box ──
      const metaJsonEl = document.getElementById('pc-meta-json');
      if (metaJsonEl && data.meta) {
        metaJsonEl.textContent = JSON.stringify(data.meta, null, 2);
      }

      // ── 7. Dynamic SEO Updates ──
      document.title = `Today's Panchangam | ${location.name} | ${formattedEn} | Dharma Jyothi Vedika`;
      const metaDesc = document.querySelector('meta[name="description"]');
      if (metaDesc) {
        metaDesc.setAttribute('content', `Today's complete Hindu Panchangam for ${formattedEn} in ${location.name}, ${location.state}. Tithi: ${data.tithi ? data.tithi.name : ''}, Nakshatra: ${data.nakshatra ? (data.nakshatra.nakshatra ? data.nakshatra.nakshatra.name : data.nakshatra.name) : ''}, Rahu Kalam: ${data.timings && data.timings.rahuKalam ? formatPeriod(data.timings.rahuKalam, tz) : ''}.`);
      }

      // Also render Homepage Hero Quick Card if present on page
      renderHeroCard(data, date, location);

      // Also render Homepage Full Panchangam Grid if present on page
      renderHomepageGrid(data, date, location);

    } catch (e) {
      console.error('Error rendering panchangam view:', e);
    }
  }

  function getOrdinal(n) {
    const s = ['th', 'st', 'nd', 'rd'];
    const v = n % 100;
    return s[(v - 20) % 10] || s[v] || s[0];
  }

  /**
   * Render Homepage Hero Quick Card if present.
   */
  function renderHeroCard(data, date, location) {
    const content = document.getElementById('hero-pc-data');
    if (!content) return;

    try {
      const tz = location.timezone || 'Asia/Kolkata';

      const dObj = new Date(date + 'T12:00:00Z');
      const formattedEn = dObj.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      });

      setText('hero-date-display', formattedEn);
      setText('hero-loc-name', location.name);

      if (data.tithi) {
        setText('hero-tithi', data.tithi.name || '—');
        setText('hero-tithi-end', data.tithi.spanStr || (data.tithi.endStr ? `Up to ${data.tithi.endStr}` : ''));
      }

      if (data.nakshatra) {
        const nObj = data.nakshatra.nakshatra || data.nakshatra;
        setText('hero-nakshatra', nObj.name || '—');
        setText('hero-nakshatra-end', data.nakshatra.spanStr || (data.nakshatra.endStr ? `Up to ${data.nakshatra.endStr}` : ''));
      }

      if (data.yoga) {
        setText('hero-yoga', data.yoga.name || '—');
        setText('hero-yoga-end', data.yoga.spanStr || (data.yoga.endStr ? `Up to ${data.yoga.endStr}` : ''));
      }

      if (data.karana) {
        setText('hero-karana', data.karana.name || '—');
        setText('hero-karana-end', data.karana.spanStr || (data.karana.endStr ? `Up to ${data.karana.endStr}` : ''));
      }

      if (data.solar) {
        setText('hero-sunrise', data.solar.sunriseStr || formatTime(data.solar.sunrise, tz));
        setText('hero-sunset',  data.solar.sunsetStr  || formatTime(data.solar.sunset, tz));
      }

      const mrObj = data.moonrise || (data.lunar ? data.lunar.moonrise : (data.solar ? data.solar.moonrise : null));
      setText('hero-moonrise', formatMoonEvent(mrObj, 'moonrise', tz));

      if (data.timings && data.timings.rahuKalam) {
        setText('hero-rahu', formatPeriod(data.timings.rahuKalam, tz));
      }
    } catch (e) {
      console.error('Error rendering hero card:', e);
    }
  }

  /**
   * Render Homepage Full Panchangam Section Grid if present.
   */
  function renderHomepageGrid(data, date, location) {
    const card = document.getElementById('full-panchangam-card');
    if (!card) return;

    try {
      const tz = location.timezone || 'Asia/Kolkata';

      const dObj = new Date(date + 'T12:00:00Z');
      const formattedEn = dObj.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      });

      // Headers & metadata
      setText('full-pc-title', `Panchangam — ${date}`);
      setText('full-pc-vara', formattedEn);
      setText('fp-header-location', `${location.name}, ${location.state}`);

      // Pancha Angas
      if (data.vara) {
        setText('fp-vara', data.vara.name || '—');
      }
      if (data.tithi) {
        setText('fp-tithi', data.tithi.name || '—');
        setText('fp-paksha', data.tithi.paksha || '—');
      }
      if (data.nakshatra) {
        const nObj = data.nakshatra.nakshatra || data.nakshatra;
        setText('fp-nakshatra', nObj.name || '—');
        const padaText = data.nakshatra.pada ? `Pada ${data.nakshatra.pada}` : '';
        setText('fp-nakshatra-pada', padaText);
      }
      if (data.yoga) {
        setText('fp-yoga', data.yoga.name || '—');
      }
      if (data.karana) {
        setText('fp-karana', data.karana.name || '—');
      }

      // Solar & Lunar
      if (data.solar) {
        setText('fp-sunrise', data.solar.sunriseStr || formatTime(data.solar.sunrise, tz));
        setText('fp-sunset',  data.solar.sunsetStr  || formatTime(data.solar.sunset, tz));
      }
      const mrObj = data.moonrise || (data.lunar ? data.lunar.moonrise : (data.solar ? data.solar.moonrise : null));
      setText('fp-moonrise', formatMoonEvent(mrObj, 'moonrise', tz));

      // Auspicious & Inauspicious Timings
      if (data.timings) {
        if (data.timings.abhijitMuhurtham) {
          setText('fp-abhijit', formatPeriod(data.timings.abhijitMuhurtham, tz));
        }
        if (data.timings.rahuKalam) {
          setText('fp-rahu', formatPeriod(data.timings.rahuKalam, tz));
        }
        if (data.timings.yamagandam) {
          setText('fp-yamagandam', formatPeriod(data.timings.yamagandam, tz));
        }
        if (data.timings.gulikaKalam) {
          setText('fp-gulika', formatPeriod(data.timings.gulikaKalam, tz));
        }
      }

      // Footer Location info
      const metaLoc = document.getElementById('fp-meta-location');
      if (metaLoc) {
        metaLoc.innerHTML = `📍 <span class="global-location-name">${location.name}</span> (${formatCoordinate(location.latitude, 4)}°N, ${formatCoordinate(location.longitude, 4)}°E)`;
      }

      const fullLoading = document.getElementById('full-pc-loading');
      if (fullLoading) fullLoading.style.display = 'none';
      const fullData = document.getElementById('full-pc-data');
      if (fullData) fullData.style.display = '';

    } catch (e) {
      console.error('Error rendering homepage grid:', e);
    }
  }

  /**
   * Main reload function: called on init, date change, or location change.
   */
  async function updateAllPanchangamViews() {
    setViewsToLoading();
    try {
      currentLocation = normalizeLocation(currentLocation);
      const data = await fetchPanchangamData(currentDate, currentLocation);
      renderTodayPanchangamView(data, currentDate, currentLocation);
      setViewsLoaded();
    } catch (err) {
      console.error('Panchangam update error:', err);
      setViewsLoaded();

      let displayMsg = 'Unable to load Panchangam from server. Please check your connection and try again.';
      if (err && err.message) {
        if (err.message.includes('toFixed') || err.message.includes('TypeError') || err.message.includes('ReferenceError')) {
          displayMsg = 'Unable to load Panchangam due to a location format issue. Please select your location again.';
        } else {
          displayMsg = err.message;
        }
      }

      const errorBanner = document.getElementById('pc-error-banner');
      const errorMsg = document.getElementById('pc-error-banner-msg');
      if (errorBanner) errorBanner.style.display = 'block';
      if (errorMsg) errorMsg.textContent = displayMsg;

      const heroLoading = document.getElementById('hero-pc-loading');
      const heroError   = document.getElementById('hero-pc-error');
      const heroErrMsg  = document.getElementById('hero-pc-error-msg');
      if (heroLoading) heroLoading.style.display = 'none';
      if (heroError) heroError.style.display = '';
      if (heroErrMsg) heroErrMsg.textContent = displayMsg;
    }
  }

  // Shift date helper
  function shiftDate(isoDate, deltaDays) {
    const [y, m, d] = isoDate.split('-').map(Number);
    const dt = new Date(Date.UTC(y, m - 1, d + deltaDays, 12, 0, 0));
    const year = dt.getUTCFullYear();
    const month = String(dt.getUTCMonth() + 1).padStart(2, '0');
    const day = String(dt.getUTCDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  /**
   * GPS detection via navigator.geolocation and Haversine nearest city matching
   */
  function handleGpsDetection() {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }

    const gpsBtn = document.getElementById('pc-gps-quick-btn');
    const origText = gpsBtn ? gpsBtn.textContent : '';
    if (gpsBtn) gpsBtn.textContent = '⏳ Locating…';

    navigator.geolocation.getCurrentPosition(
      function (pos) {
        const uLat = Number(pos.coords.latitude);
        const uLon = Number(pos.coords.longitude);

        if (!Number.isFinite(uLat) || !Number.isFinite(uLon)) {
          alert('Could not determine valid GPS coordinates.');
          if (gpsBtn) gpsBtn.textContent = origText;
          return;
        }

        let closest = null;
        let minDistance = Infinity;

        const db = window.IndiaLocations;
        if (db && typeof db.findNearestLocation === 'function') {
          const res = db.findNearestLocation(uLat, uLon);
          if (res && res.location) {
            closest = res.location;
          }
        } else if (window.indiaLocations && Array.isArray(window.indiaLocations)) {
          for (const loc of window.indiaLocations) {
            const locNorm = normalizeLocation(loc);
            const d = haversineDistance(uLat, uLon, locNorm.latitude, locNorm.longitude);
            if (d < minDistance) {
              minDistance = d;
              closest = locNorm;
            }
          }
        }

        const newLoc = normalizeLocation(closest || {
          name: 'Detected Location',
          state: 'India',
          latitude: uLat,
          longitude: uLon,
          timezone: 'Asia/Kolkata'
        });

        try {
          localStorage.setItem('djv_selected_location', JSON.stringify(newLoc));
          localStorage.setItem('djv_user_location', JSON.stringify(newLoc));
        } catch (e) {}

        currentLocation = newLoc;
        if (gpsBtn) gpsBtn.textContent = origText;

        window.dispatchEvent(new CustomEvent('djv:locationChanged', { detail: newLoc }));
        updateAllPanchangamViews();
      },
      function (err) {
        if (gpsBtn) gpsBtn.textContent = origText;
        console.warn('Geolocation error:', err);
        // If GPS permission is denied or fails, open the manual location modal
        const modal = document.getElementById('pc-location-modal') || document.getElementById('location-modal');
        if (modal) {
          modal.style.display = 'flex';
        } else {
          alert('GPS detection unavailable. Please select your city from the location picker.');
        }
      },
      { timeout: 10000, enableHighAccuracy: true }
    );
  }

  function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  // DOM Ready
  document.addEventListener('DOMContentLoaded', function () {

    // Jump Date Picker inputs
    const datePickers = [
      document.getElementById('pc-date-picker-input'),
      document.getElementById('pc-date-picker')
    ];
    datePickers.forEach(dp => {
      if (dp) {
        dp.value = currentDate;
        dp.addEventListener('change', function () {
          currentDate = this.value;
          datePickers.forEach(p => { if (p) p.value = currentDate; });
          updateAllPanchangamViews();
        });
      }
    });

    // Previous Day buttons
    const prevBtns = [
      document.getElementById('pc-prev-day-btn')
    ];
    prevBtns.forEach(btn => {
      if (btn) {
        btn.addEventListener('click', function () {
          currentDate = shiftDate(currentDate, -1);
          datePickers.forEach(p => { if (p) p.value = currentDate; });
          updateAllPanchangamViews();
        });
      }
    });

    // Next Day buttons
    const nextBtns = [
      document.getElementById('pc-next-day-btn')
    ];
    nextBtns.forEach(btn => {
      if (btn) {
        btn.addEventListener('click', function () {
          currentDate = shiftDate(currentDate, 1);
          datePickers.forEach(p => { if (p) p.value = currentDate; });
          updateAllPanchangamViews();
        });
      }
    });

    // Today buttons
    const todayBtns = [
      document.getElementById('pc-today-btn')
    ];
    todayBtns.forEach(btn => {
      if (btn) {
        btn.addEventListener('click', function () {
          currentDate = getTodayIST();
          datePickers.forEach(p => { if (p) p.value = currentDate; });
          updateAllPanchangamViews();
        });
      }
    });

    // Quick Presets
    document.querySelectorAll('.pc-preset-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const preset = this.getAttribute('data-preset');
        if (preset === 'today') {
          currentDate = getTodayIST();
        } else if (preset === 'tomorrow') {
          currentDate = shiftDate(getTodayIST(), 1);
        } else if (preset === 'yesterday') {
          currentDate = shiftDate(getTodayIST(), -1);
        }
        datePickers.forEach(p => { if (p) p.value = currentDate; });
        updateAllPanchangamViews();
      });
    });

    // GPS Buttons
    const gpsBtns = [
      document.getElementById('pc-gps-quick-btn'),
      document.getElementById('pc-btn-use-location')
    ];
    gpsBtns.forEach(btn => {
      if (btn) btn.addEventListener('click', handleGpsDetection);
    });

    // Location Pill Click -> Open Modal
    const locPillBtns = [
      document.getElementById('pc-location-pill-btn'),
      document.getElementById('pc-open-loc-modal-btn')
    ];
    locPillBtns.forEach(btn => {
      if (btn) {
        btn.addEventListener('click', function () {
          const modal = document.getElementById('location-modal') || document.getElementById('pc-location-modal');
          if (modal) modal.style.display = 'flex';
        });
      }
    });

    // Print Panchangam button
    const printBtn = document.getElementById('pc-print-btn');
    if (printBtn) {
      printBtn.addEventListener('click', function () {
        window.print();
      });
    }

    // Share Panchangam button
    const shareBtn = document.getElementById('pc-share-btn');
    if (shareBtn) {
      shareBtn.addEventListener('click', function () {
        const shareUrl = window.location.origin + window.location.pathname;
        if (navigator.share) {
          navigator.share({
            title: document.title,
            text: `Check today's Hindu Panchangam for ${currentLocation.name}`,
            url: shareUrl
          }).catch(() => {});
        } else {
          if (navigator.clipboard) {
            navigator.clipboard.writeText(shareUrl).then(() => {
              alert('Panchangam link copied to clipboard:\n' + shareUrl);
            });
          } else {
            prompt('Copy Panchangam URL:', shareUrl);
          }
        }
      });
    }

    // Retry buttons
    const retryBtn = document.getElementById('pc-retry-btn');
    if (retryBtn) retryBtn.addEventListener('click', updateAllPanchangamViews);
    const retryHero = document.getElementById('hero-pc-retry-btn');
    if (retryHero) retryHero.addEventListener('click', updateAllPanchangamViews);

    // Global location change event listener (fired by location modal in theme-main.js)
    window.addEventListener('djv:locationChanged', function (e) {
      const loc = e.detail;
      if (!loc) return;
      currentLocation = normalizeLocation(loc);
      updateAllPanchangamViews();
    });

    // Initial fetch on DOM ready
    updateAllPanchangamViews();
  });
})();
