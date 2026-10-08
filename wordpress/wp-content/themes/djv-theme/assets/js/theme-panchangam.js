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
  let currentDate = getInitialDate();
  let currentLocation = getActiveLocation();
  const cache = new Map();

  function getInitialDate() {
    try {
      const p = new URLSearchParams(window.location.search).get('date');
      if (p && /^\d{4}-\d{2}-\d{2}$/.test(p)) {
        return p;
      }
    } catch (e) {}
    return getTodayIST();
  }

  function syncDateToUrl(date) {
    try {
      if (typeof window !== 'undefined' && window.history && window.history.replaceState) {
        const u = new URL(window.location.href);
        if (date === getTodayIST()) {
          u.searchParams.delete('date');
        } else {
          u.searchParams.set('date', date);
        }
        window.history.replaceState({}, '', u.toString());
      }
    } catch (e) {}
  }

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

  function buildCacheKey(date, lat, lon, tz, lang) {
    return `${date}_${formatCoordinate(lat, 4)}_${formatCoordinate(lon, 4)}_${tz || 'Asia/Kolkata'}_${lang || 'en'}`;
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
    if (typeof period === 'string') return decodeUnicodeEscapes(period);
    if (period.text) return decodeUnicodeEscapes(period.text);
    if (period.startStr && period.endStr) return `${decodeUnicodeEscapes(period.startStr)} – ${decodeUnicodeEscapes(period.endStr)}`;
    if (period.start && period.end) {
      return `${formatTime(period.start, timezone)} – ${formatTime(period.end, timezone)}`;
    }
    return '—';
  }

  function formatMoonEvent(evt, type, tz = 'Asia/Kolkata', lang = 'en') {
    const noEventMsg = (lang === 'hi')
      ? (type === 'moonrise' ? 'चंद्रोदय नहीं' : 'चंद्रास्त नहीं')
      : (lang === 'te'
        ? (type === 'moonrise' ? 'చంద్రోదయం లేదు' : 'చంద్రాస్తమయం లేదు')
        : (type === 'moonrise' ? 'No Moonrise' : 'No Moonset'));

    if (!evt) return noEventMsg;
    if (evt.status === 'no_event') {
      return noEventMsg;
    }
    if (evt.time) return evt.time;
    if (evt.timeStr) return evt.timeStr;
    if (evt.datetime) return formatTime(evt.datetime, tz);
    if (typeof evt === 'string' && evt !== '—') return evt;
    return noEventMsg;
  }

  const MONTHS_HI = [
    'जनवरी', 'फ़रवरी', 'मार्च', 'अप्रैल', 'मई', 'जून',
    'जुलाई', 'अगस्त', 'सितंबर', 'अक्टूबर', 'नवंबर', 'दिसंबर'
  ];
  const MONTHS_TE = [
    'జనవరి', 'ఫిబ్రవరి', 'మార్చి', 'ఏప్రిల్', 'మే', 'జూన్',
    'జూలై', 'ఆగస్టు', 'సెప్టెంబర్', 'అక్టోబర్', 'నవంబర్', 'డిసెంబర్'
  ];

  function formatLocalizedDate(isoDate, lang = 'en') {
    if (!isoDate || typeof isoDate !== 'string') return '';
    const parts = isoDate.split('-');
    if (parts.length !== 3) return isoDate;
    const year = Number(parts[0]);
    const month = Number(parts[1]);
    const day = Number(parts[2]);
    const d = new Date(Date.UTC(year, month - 1, day, 12, 0, 0));

    if (lang === 'hi') {
      return `${day} ${MONTHS_HI[month - 1]} ${year}`;
    }
    if (lang === 'te') {
      return `${day} ${MONTHS_TE[month - 1]} ${year}`;
    }
    return d.toLocaleDateString('en-US', {
      weekday: 'long',
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    });
  }

  const RULER_NAMES = {
    'Sun':     { en: 'Sun (Surya)',          te: 'రవి (సూర్యుడు)',        hi: 'सूर्य (रवि)' },
    'Moon':    { en: 'Moon (Chandra)',        te: 'చంద్రుడు',             hi: 'चन्द्र' },
    'Mars':    { en: 'Mars (Mangala)',        te: 'కుజుడు (అంగారకుడు)',   hi: 'मंगल (भौम)' },
    'Mercury': { en: 'Mercury (Budha)',       te: 'బుధుడు',               hi: 'बुध' },
    'Jupiter': { en: 'Jupiter (Brihaspati)',  te: 'గురు (బృహస్పతి)',      hi: 'बृहस्पति (गुरु)' },
    'Venus':   { en: 'Venus (Shukra)',        te: 'శుక్రుడు',              hi: 'शुक्र' },
    'Saturn':  { en: 'Saturn (Shani)',        te: 'శని భగవానుడు',         hi: 'शनि देव' },
    'Rahu':    { en: 'Rahu',                  te: 'రాహువు',               hi: 'राहु' },
    'Ketu':    { en: 'Ketu',                  te: 'కేతువు',                hi: 'केतु' }
  };
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

    const currentLang = (typeof window !== 'undefined' && window.DJV_LANGUAGE && window.DJV_LANGUAGE.current)
      || (typeof localStorage !== 'undefined' && (localStorage.getItem('djv_language') || localStorage.getItem('djv_lang')))
      || 'en';

    const key = buildCacheKey(date, latitude, longitude, timezone, currentLang);
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
      language: currentLang,
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
    let s = str;
    if (s.includes('u2013')) {
      s = s.replace(/\\?u2013/g, '–');
    }
    if (s.includes('u0c') || s.includes('u09') || s.includes('\\u')) {
      s = s.replace(/\\?u([0-9a-fA-F]{4})/g, (_, hex) => String.fromCharCode(parseInt(hex, 16)));
    }
    return s;
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
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
   * Render timing periods supporting multiple slots (e.g. 2 Dur Muhurtham slots)
   * Displays each slot clearly on its own line and defaults to '—' if empty.
   */
  function renderTimingSlotList(elementId, timingData, timezone = 'Asia/Kolkata') {
    const el = document.getElementById(elementId);
    if (!el) return;

    if (!timingData) {
      el.textContent = '—';
      return;
    }

    if (Array.isArray(timingData)) {
      const validSlots = timingData
        .map(slot => formatPeriod(slot, timezone))
        .filter(text => text && text !== '—');

      if (validSlots.length === 0) {
        el.textContent = '—';
        return;
      }

      if (validSlots.length === 1) {
        el.textContent = decodeUnicodeEscapes(validSlots[0]);
        return;
      }

      el.innerHTML = validSlots
        .map(slot => `<span class="timing-period-slot">${escapeHtml(decodeUnicodeEscapes(slot))}</span>`)
        .join('');
      return;
    }

    const formatted = formatPeriod(timingData, timezone);
    el.textContent = decodeUnicodeEscapes(formatted || '—');
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

      const currentLang = (typeof window !== 'undefined' && window.DJV_LANGUAGE && window.DJV_LANGUAGE.current)
        || (typeof localStorage !== 'undefined' && (localStorage.getItem('djv_language') || localStorage.getItem('djv_lang')))
        || 'en';

      const dateDisplay = formatLocalizedDate(date, currentLang);

      // ── Header & Hero Titles ──
      const varaTitle = data.vara ? (currentLang === 'hi' ? (data.vara.nameHi || data.vara.name) : (currentLang === 'te' ? (data.vara.nameTe || data.vara.name) : (data.vara.en || data.vara.name))) : '';
      const tithiTitle = data.tithi ? (currentLang === 'hi' ? (data.tithi.nameHi || data.tithi.name) : (currentLang === 'te' ? (data.tithi.nameTe || data.tithi.name) : data.tithi.name)) : '';
      const nObj = data.nakshatra ? (data.nakshatra.nakshatra || data.nakshatra) : null;
      const nakTitle = nObj ? (currentLang === 'hi' ? (nObj.nameHi || nObj.name) : (currentLang === 'te' ? (nObj.nameTe || nObj.name) : nObj.name)) : '';

      const summaryParts = [varaTitle, tithiTitle, nakTitle].filter(Boolean).join(', ');
      setText('pc-date-heading', `${dateDisplay}${summaryParts ? ' · ' + summaryParts : ''}`);
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
      setText('val-moonrise', formatMoonEvent(mrObj, 'moonrise', tz, currentLang));
      setText('val-moonset',  formatMoonEvent(msObj, 'moonset', tz, currentLang));

      // ── 2. Pancha Angas (5 Limbs) ──
      // Vara
      if (data.vara) {
        if (currentLang === 'hi') {
          setText('val-vara-en', data.vara.nameHi || data.vara.name);
          setText('val-vara-te', data.vara.en || data.vara.name);
        } else if (currentLang === 'te') {
          setText('val-vara-en', data.vara.nameTe || data.vara.name);
          setText('val-vara-te', data.vara.en || data.vara.name);
        } else {
          setText('val-vara-en', data.vara.en || data.vara.name);
          setText('val-vara-te', '');
        }

        if (data.vara.ruler) {
          const rulerObj = RULER_NAMES[data.vara.ruler];
          const localizedRuler = rulerObj ? (rulerObj[currentLang] || rulerObj.en) : data.vara.ruler;
          if (currentLang === 'hi') {
            setText('val-vara-ruler', `स्वामी: ${localizedRuler}`);
          } else if (currentLang === 'te') {
            setText('val-vara-ruler', `అధిపతి: ${localizedRuler}`);
          } else {
            setText('val-vara-ruler', `Ruler: ${localizedRuler}`);
          }
        }
      }

      // Tithi
      if (data.tithi) {
        let pakshaDisplay = '';
        if (currentLang === 'hi') {
          pakshaDisplay = data.tithi.pakshaHi || (data.tithi.paksha === 'Krishna' ? 'कृष्ण पक्ष' : 'शुक्ल पक्ष');
          setText('val-tithi-name', `${data.tithi.nameHi || data.tithi.name} (${pakshaDisplay})`);
          setText('val-tithi-te', `${data.tithi.name} (${data.tithi.paksha ? data.tithi.paksha + ' Paksha' : ''})`);
          setText('val-summary-paksha-pill', pakshaDisplay);
        } else if (currentLang === 'te') {
          pakshaDisplay = data.tithi.pakshaTe || (data.tithi.paksha === 'Krishna' ? 'కృష్ణ పక్షం' : 'శుక్ల పక్షం');
          setText('val-tithi-name', `${data.tithi.nameTe || data.tithi.name} (${pakshaDisplay})`);
          setText('val-tithi-te', `${data.tithi.name} (${data.tithi.paksha ? data.tithi.paksha + ' Paksha' : ''})`);
          setText('val-summary-paksha-pill', pakshaDisplay);
        } else {
          pakshaDisplay = data.tithi.paksha ? `${data.tithi.paksha} Paksha` : '';
          setText('val-tithi-name', `${data.tithi.name} (${pakshaDisplay})`);
          setText('val-tithi-te', '');
          setText('val-summary-paksha-pill', pakshaDisplay || 'Panchangam');
        }

        const endTimeStr = data.tithi.endStr || (data.tithi.endTime ? formatTime(data.tithi.endTime, tz) : '');
        let tEnd = '';
        if (endTimeStr) {
          if (currentLang === 'hi') {
            tEnd = `समाप्त: ${endTimeStr}`;
          } else if (currentLang === 'te') {
            tEnd = `ముగింపు: ${endTimeStr}`;
          } else {
            tEnd = `Up to ${endTimeStr}`;
          }
        } else if (data.tithi.spanStr) {
          tEnd = data.tithi.spanStr;
        }
        setText('val-tithi-end', tEnd);
      }

      // Nakshatra
      if (data.nakshatra) {
        const nObj = data.nakshatra.nakshatra || data.nakshatra;
        if (currentLang === 'hi') {
          setText('val-nakshatra-name', nObj.nameHi || nObj.name || '—');
          setText('val-nakshatra-te', nObj.name || '');
        } else if (currentLang === 'te') {
          setText('val-nakshatra-name', nObj.nameTe || nObj.name || '—');
          setText('val-nakshatra-te', nObj.name || '');
        } else {
          setText('val-nakshatra-name', nObj.name || '—');
          setText('val-nakshatra-te', '');
        }

        let padaStr = '';
        if (data.nakshatra.pada) {
          if (currentLang === 'hi') {
            padaStr = `${data.nakshatra.pada}वां चरण`;
          } else if (currentLang === 'te') {
            padaStr = `${data.nakshatra.pada}వ పాదం`;
          } else {
            padaStr = `${data.nakshatra.pada}${getOrdinal(data.nakshatra.pada)} Pada`;
          }
        }

        const endTimeStr = data.nakshatra.endStr || (data.nakshatra.endTime ? formatTime(data.nakshatra.endTime, tz) : '');
        let nEnd = '';
        if (endTimeStr) {
          if (currentLang === 'hi') {
            nEnd = `समाप्त: ${endTimeStr}`;
          } else if (currentLang === 'te') {
            nEnd = `ముగింపు: ${endTimeStr}`;
          } else {
            nEnd = `Up to ${endTimeStr}`;
          }
        } else if (data.nakshatra.spanStr) {
          nEnd = data.nakshatra.spanStr;
        }

        setText('val-nakshatra-end', [padaStr, nEnd].filter(Boolean).join(' · '));

        if (nObj.ruler || nObj.deity) {
          if (currentLang === 'hi') {
            setText('val-nakshatra-meta', `देवता: ${nObj.deityHi || nObj.deity || '—'} · स्वामी: ${nObj.rulerHi || nObj.ruler || '—'}`);
          } else if (currentLang === 'te') {
            setText('val-nakshatra-meta', `దేవత: ${nObj.deityTe || nObj.deity || '—'} · అధిపతి: ${nObj.rulerTe || nObj.ruler || '—'}`);
          } else {
            setText('val-nakshatra-meta', `Deity: ${nObj.deity || '—'} · Ruler: ${nObj.ruler || '—'}`);
          }
        }
      }

      // Yoga
      if (data.yoga) {
        if (currentLang === 'hi') {
          setText('val-yoga-name', data.yoga.nameHi || data.yoga.name || '—');
          setText('val-yoga-te', data.yoga.name || '');
        } else if (currentLang === 'te') {
          setText('val-yoga-name', data.yoga.nameTe || data.yoga.name || '—');
          setText('val-yoga-te', data.yoga.name || '');
        } else {
          setText('val-yoga-name', data.yoga.name || '—');
          setText('val-yoga-te', '');
        }

        const endTimeStr = data.yoga.endStr || (data.yoga.endTime ? formatTime(data.yoga.endTime, tz) : '');
        let yEnd = '';
        if (endTimeStr) {
          if (currentLang === 'hi') {
            yEnd = `समाप्त: ${endTimeStr}`;
          } else if (currentLang === 'te') {
            yEnd = `ముగింపు: ${endTimeStr}`;
          } else {
            yEnd = `Up to ${endTimeStr}`;
          }
        } else if (data.yoga.spanStr) {
          yEnd = data.yoga.spanStr;
        }
        setText('val-yoga-end', yEnd);
      }

      // Karana
      if (data.karana) {
        if (currentLang === 'hi') {
          setText('val-karana-name', data.karana.nameHi || data.karana.name || '—');
          setText('val-karana-te', data.karana.name || '');
        } else if (currentLang === 'te') {
          setText('val-karana-name', data.karana.nameTe || data.karana.name || '—');
          setText('val-karana-te', data.karana.name || '');
        } else {
          setText('val-karana-name', data.karana.name || '—');
          setText('val-karana-te', '');
        }

        const endTimeStr = data.karana.endStr || (data.karana.endTime ? formatTime(data.karana.endTime, tz) : '');
        let kEnd = '';
        if (endTimeStr) {
          if (currentLang === 'hi') {
            kEnd = `समाप्त: ${endTimeStr}`;
          } else if (currentLang === 'te') {
            kEnd = `ముగింపు: ${endTimeStr}`;
          } else {
            kEnd = `Up to ${endTimeStr}`;
          }
        } else if (data.karana.spanStr) {
          kEnd = data.karana.spanStr;
        }
        setText('val-karana-end', kEnd);
      }

      // ── 3. Auspicious Timings ──
      const timings = data.timings || {};

      const abhijit = timings.abhijitMuhurtham || timings.abhijit || timings.abhijit_muhurtham || data.abhijitMuhurtham;
      const amrit = timings.amritKalam || timings.amrit_kalam || timings.amritakalam || data.amritKalam;
      const brahma = timings.brahmaMuhurtham || timings.brahma_muhurtham || timings.brahmamuhurtham || data.brahmaMuhurtham;

      renderTimingSlotList('val-abhijit', abhijit, tz);
      renderTimingSlotList('val-amritkalam', amrit, tz);
      renderTimingSlotList('val-brahmamuhurtham', brahma, tz);

      // ── 4. Inauspicious Timings ──
      const rahu = timings.rahuKalam || timings.rahukalam || timings.rahu_kalam || data.rahuKalam;
      const yama = timings.yamagandam || timings.yamaGandam || timings.yamaganda || data.yamagandam;
      const gulika = timings.gulikaKalam || timings.gulikakalam || timings.gulika || data.gulikaKalam;
      const dur = timings.durMuhurtham || timings.durMuhurtam || timings.durmuhurtham || timings.dur_muhurtam || timings.durmuhurta || data.durMuhurtham || data.durmuhurtham;
      const varj = timings.varjyam || timings.tyajyam || timings.varjyamPeriods || timings.varjam || data.varjyam;

      renderTimingSlotList('val-rahukalam', rahu, tz);
      renderTimingSlotList('val-yamagandam', yama, tz);
      renderTimingSlotList('val-gulikakalam', gulika, tz);
      renderTimingSlotList('val-durmuhurtham', dur, tz);
      renderTimingSlotList('val-varjyam', varj, tz);

      // ── 5. Lunar Phase & Illumination ──
      if (data.lunar) {
        if (currentLang === 'hi') {
          setText('val-moonphase', data.lunar.phaseHi || data.lunar.phase || '—');
          setText('val-moonphase-te', data.lunar.phase || '—');
          setText('lbl-lunar-desc', 'विवरण');
        } else if (currentLang === 'te') {
          setText('val-moonphase', data.lunar.phaseTe || data.lunar.phase || '—');
          setText('val-moonphase-te', data.lunar.phase || '—');
          setText('lbl-lunar-desc', 'వివరణ');
        } else {
          setText('val-moonphase', data.lunar.phase || '—');
          setText('val-moonphase-te', data.lunar.phase || '—');
          setText('lbl-lunar-desc', 'Description');
        }
        setText('val-illumination', data.lunar.illuminationPercent ? `${data.lunar.illuminationPercent}%` : (data.lunar.illumination ? `${data.lunar.illumination}` : '—'));
      }

      // ── 6. Engine Metadata Box ──
      const metaJsonEl = document.getElementById('pc-meta-json');
      if (metaJsonEl && data.meta) {
        metaJsonEl.textContent = JSON.stringify(data.meta, null, 2);
      }

      // ── 7. Dynamic SEO Updates ──
      if (currentLang === 'hi') {
        document.title = `आज का पंचांग | ${loc.name} | ${dateDisplay} | धर्म ज्योति वेदिका`;
      } else if (currentLang === 'te') {
        document.title = `నేటి పంచాంగం | ${loc.name} | ${dateDisplay} | ధర్మ జ్యోతి వేదిక`;
      } else {
        document.title = `Today's Panchangam | ${loc.name} | ${dateDisplay} | Dharma Jyothi Vedika`;
      }

      const metaDesc = document.querySelector('meta[name="description"]');
      if (metaDesc) {
        metaDesc.setAttribute('content', `Today's complete Hindu Panchangam for ${dateDisplay} in ${loc.name}, ${loc.state}. Tithi: ${data.tithi ? (data.tithi.nameHi || data.tithi.name) : ''}, Nakshatra: ${nObj ? (nObj.nameHi || nObj.name) : ''}.`);
      }

      // Also render Homepage Hero Quick Card if present on page
      renderHeroCard(data, date, loc, currentLang);

      // Also render Homepage Full Panchangam Grid if present on page
      renderHomepageGrid(data, date, loc, currentLang);

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
  function renderHeroCard(data, date, location, currentLang = 'en') {
    const content = document.getElementById('hero-pc-data');
    if (!content) return;

    try {
      const tz = location.timezone || 'Asia/Kolkata';
      const dateDisplay = formatLocalizedDate(date, currentLang);

      setText('hero-date-display', dateDisplay);
      setText('hero-loc-name', location.name);

      if (data.tithi) {
        const tName = currentLang === 'hi' ? (data.tithi.nameHi || data.tithi.name) : (currentLang === 'te' ? (data.tithi.nameTe || data.tithi.name) : data.tithi.name);
        setText('hero-tithi', tName || '—');
        const endTimeStr = data.tithi.endStr || (data.tithi.endTime ? formatTime(data.tithi.endTime, tz) : '');
        let tEnd = '';
        if (endTimeStr) {
          tEnd = currentLang === 'hi' ? `समाप्त: ${endTimeStr}` : (currentLang === 'te' ? `ముగింపు: ${endTimeStr}` : `Up to ${endTimeStr}`);
        }
        setText('hero-tithi-end', tEnd);
      }

      if (data.nakshatra) {
        const nObj = data.nakshatra.nakshatra || data.nakshatra;
        const nName = currentLang === 'hi' ? (nObj.nameHi || nObj.name) : (currentLang === 'te' ? (nObj.nameTe || nObj.name) : nObj.name);
        setText('hero-nakshatra', nName || '—');
        const endTimeStr = data.nakshatra.endStr || (data.nakshatra.endTime ? formatTime(data.nakshatra.endTime, tz) : '');
        let nEnd = '';
        if (endTimeStr) {
          nEnd = currentLang === 'hi' ? `समाप्त: ${endTimeStr}` : (currentLang === 'te' ? `ముగింపు: ${endTimeStr}` : `Up to ${endTimeStr}`);
        }
        setText('hero-nakshatra-end', nEnd);
      }

      if (data.yoga) {
        const yName = currentLang === 'hi' ? (data.yoga.nameHi || data.yoga.name) : (currentLang === 'te' ? (data.yoga.nameTe || data.yoga.name) : data.yoga.name);
        setText('hero-yoga', yName || '—');
        const endTimeStr = data.yoga.endStr || (data.yoga.endTime ? formatTime(data.yoga.endTime, tz) : '');
        let yEnd = '';
        if (endTimeStr) {
          yEnd = currentLang === 'hi' ? `समाप्त: ${endTimeStr}` : (currentLang === 'te' ? `ముగింపు: ${endTimeStr}` : `Up to ${endTimeStr}`);
        }
        setText('hero-yoga-end', yEnd);
      }

      if (data.karana) {
        const kName = currentLang === 'hi' ? (data.karana.nameHi || data.karana.name) : (currentLang === 'te' ? (data.karana.nameTe || data.karana.name) : data.karana.name);
        setText('hero-karana', kName || '—');
        const endTimeStr = data.karana.endStr || (data.karana.endTime ? formatTime(data.karana.endTime, tz) : '');
        let kEnd = '';
        if (endTimeStr) {
          kEnd = currentLang === 'hi' ? `समाप्त: ${endTimeStr}` : (currentLang === 'te' ? `ముగింపు: ${endTimeStr}` : `Up to ${endTimeStr}`);
        }
        setText('hero-karana-end', kEnd);
      }

      if (data.solar) {
        setText('hero-sunrise', data.solar.sunriseStr || formatTime(data.solar.sunrise, tz));
        setText('hero-sunset',  data.solar.sunsetStr  || formatTime(data.solar.sunset, tz));
      }

      const mrObj = data.moonrise || (data.lunar ? data.lunar.moonrise : (data.solar ? data.solar.moonrise : null));
      setText('hero-moonrise', formatMoonEvent(mrObj, 'moonrise', tz, currentLang));

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
  function renderHomepageGrid(data, date, location, currentLang = 'en') {
    const card = document.getElementById('full-panchangam-card');
    if (!card) return;

    try {
      const tz = location.timezone || 'Asia/Kolkata';
      const dateDisplay = formatLocalizedDate(date, currentLang);

      // Headers & metadata
      const titlePrefix = currentLang === 'hi' ? 'पंचांग' : (currentLang === 'te' ? 'పంచాంగం' : 'Panchangam');
      setText('full-pc-title', `${titlePrefix} — ${dateDisplay}`);
      setText('full-pc-vara', dateDisplay);
      setText('fp-header-location', `${location.name}, ${location.state}`);

      // Pancha Angas
      if (data.vara) {
        const vName = currentLang === 'hi' ? (data.vara.nameHi || data.vara.name) : (currentLang === 'te' ? (data.vara.nameTe || data.vara.name) : (data.vara.en || data.vara.name));
        setText('fp-vara', vName || '—');
      }
      if (data.tithi) {
        const tName = currentLang === 'hi' ? (data.tithi.nameHi || data.tithi.name) : (currentLang === 'te' ? (data.tithi.nameTe || data.tithi.name) : data.tithi.name);
        const pName = currentLang === 'hi' ? (data.tithi.pakshaHi || data.tithi.paksha) : (currentLang === 'te' ? (data.tithi.pakshaTe || data.tithi.paksha) : data.tithi.paksha);
        setText('fp-tithi', tName || '—');
        setText('fp-paksha', pName || '—');
      }
      if (data.nakshatra) {
        const nObj = data.nakshatra.nakshatra || data.nakshatra;
        const nName = currentLang === 'hi' ? (nObj.nameHi || nObj.name) : (currentLang === 'te' ? (nObj.nameTe || nObj.name) : nObj.name);
        setText('fp-nakshatra', nName || '—');
        let padaText = '';
        if (data.nakshatra.pada) {
          padaText = currentLang === 'hi' ? `${data.nakshatra.pada}वां चरण` : (currentLang === 'te' ? `${data.nakshatra.pada}వ పాదం` : `Pada ${data.nakshatra.pada}`);
        }
        setText('fp-nakshatra-pada', padaText);
      }
      if (data.yoga) {
        const yName = currentLang === 'hi' ? (data.yoga.nameHi || data.yoga.name) : (currentLang === 'te' ? (data.yoga.nameTe || data.yoga.name) : data.yoga.name);
        setText('fp-yoga', yName || '—');
      }
      if (data.karana) {
        const kName = currentLang === 'hi' ? (data.karana.nameHi || data.karana.name) : (currentLang === 'te' ? (data.karana.nameTe || data.karana.name) : data.karana.name);
        setText('fp-karana', kName || '—');
      }

      // Solar & Lunar
      if (data.solar) {
        setText('fp-sunrise', data.solar.sunriseStr || formatTime(data.solar.sunrise, tz));
        setText('fp-sunset',  data.solar.sunsetStr  || formatTime(data.solar.sunset, tz));
      }
      const mrObj = data.moonrise || (data.lunar ? data.lunar.moonrise : (data.solar ? data.solar.moonrise : null));
      setText('fp-moonrise', formatMoonEvent(mrObj, 'moonrise', tz, currentLang));

      // Auspicious & Inauspicious Timings
      const timings = data.timings || {};
      const fpAbhijit = timings.abhijitMuhurtham || timings.abhijit || timings.abhijit_muhurtham || data.abhijitMuhurtham;
      const fpRahu = timings.rahuKalam || timings.rahukalam || timings.rahu_kalam || data.rahuKalam;
      const fpYama = timings.yamagandam || timings.yamaGandam || timings.yamaganda || data.yamagandam;
      const fpGulika = timings.gulikaKalam || timings.gulikakalam || timings.gulika || data.gulikaKalam;

      renderTimingSlotList('fp-abhijit', fpAbhijit, tz);
      renderTimingSlotList('fp-rahu', fpRahu, tz);
      renderTimingSlotList('fp-yamagandam', fpYama, tz);
      renderTimingSlotList('fp-gulika', fpGulika, tz);

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
          syncDateToUrl(currentDate);
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
          syncDateToUrl(currentDate);
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
          syncDateToUrl(currentDate);
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
          syncDateToUrl(currentDate);
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
        syncDateToUrl(currentDate);
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

    // Global language change event listener
    window.addEventListener('djv:languageChanged', function (e) {
      updateAllPanchangamViews();
    });

    // Initial fetch on DOM ready
    updateAllPanchangamViews();
  });
})();
