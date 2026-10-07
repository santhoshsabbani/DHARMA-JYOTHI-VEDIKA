/**
 * ============================================================
 * India-Wide Dynamic Panchangam Application
 * website/assets/js/panchangam-app.js
 *
 * Real-time astronomical calculation with dynamic Date & Location.
 * Zero hardcoded data. 100% dynamic recalculation.
 * ============================================================
 */

(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory(require('./panchangam-core.js'), require('./india-locations.js'));
  } else {
    root.DJVPanchangamApp = factory(root.DJVPanchangamEngine || root.DJVPanchangam, root.IndiaLocations);
  }
})(typeof self !== 'undefined' ? self : this, function (engine, locDB) {
  'use strict';

  // State Management
  const state = {
    date: getTodayIST(),
    location: {
      name: 'Hyderabad',
      state: 'Telangana',
      country: 'India',
      lat: 17.3850,
      lon: 78.4867,
      timezone: 'Asia/Kolkata',
      type: 'Metro'
    },
    timeFormat: '12h', // '12h' or '24h'
    ayanamsa: 'lahiri',
    currentPanchangam: null,
    cache: new Map()
  };

  /**
   * Get current calendar date in Indian Standard Time (IST: UTC+5:30) as YYYY-MM-DD.
   */
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

  /**
   * Format ISO date string into formatted human strings.
   */
  function formatDateDetailed(isoDate) {
    const [y, m, d] = isoDate.split('-').map(Number);
    const dateObj = new Date(Date.UTC(y, m - 1, d, 12, 0, 0));
    const en = new Intl.DateTimeFormat('en-US', {
      timeZone: 'UTC',
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }).format(dateObj);
    return { en, year: y, month: m, day: d };
  }

  /**
   * Shift ISO date string by deltaDays.
   */
  function shiftDate(isoDate, deltaDays) {
    const [y, m, d] = isoDate.split('-').map(Number);
    const dt = new Date(Date.UTC(y, m - 1, d + deltaDays, 12, 0, 0));
    const year = dt.getUTCFullYear();
    const month = String(dt.getUTCMonth() + 1).padStart(2, '0');
    const day = String(dt.getUTCDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  /**
   * Build composite cache key strictly per requirement:
   * date + latitude + longitude + timezone + calculation_method
   */
  function getCacheKey(dateStr, lat, lon, timezone, method) {
    return `${dateStr}_${lat.toFixed(4)}_${lon.toFixed(4)}_${timezone}_${method}`;
  }

  /**
   * Format a JavaScript Date to either 12-hour or 24-hour time string in the target timezone.
   */
  function formatTime(date, timezone, is12h = true) {
    if (!date) return '—';
    if (typeof date === 'string') {
      if (date.includes('T')) {
        date = new Date(date);
      } else {
        // If already formatted string like "6:05 AM" and in 24h mode, parse and convert
        if (!is12h && date.includes('M')) {
          const match = date.match(/(\d+):(\d+)\s*(AM|PM)/i);
          if (match) {
            let h = parseInt(match[1], 10);
            const m = match[2];
            const isPm = match[3].toUpperCase() === 'PM';
            if (isPm && h < 12) h += 12;
            if (!isPm && h === 12) h = 0;
            return `${String(h).padStart(2, '0')}:${m}`;
          }
        }
        return date;
      }
    }
    try {
      const d = date instanceof Date ? date : new Date(date);
      if (isNaN(d.getTime())) return '—';
      return new Intl.DateTimeFormat('en-US', {
        timeZone: timezone,
        hour: is12h ? 'numeric' : '2-digit',
        minute: '2-digit',
        hour12: is12h
      }).format(d);
    } catch (e) {
      return date.toString();
    }
  }

  /**
   * Format timing interval string according to user's 12h/24h preference.
   */
  function formatIntervalDisplay(interval, timezone, is12h) {
    if (!interval || !interval.start || !interval.end) return '—';
    const s = formatTime(interval.start, timezone, is12h);
    const e = formatTime(interval.end, timezone, is12h);
    return `${s} – ${e}`;
  }

  /**
   * Core Calculation: Computes or retrieves Panchangam dynamically.
   */
  function computePanchangam(isoDate, locationObj, ayanamsa = 'lahiri') {
    const [year, month, day] = isoDate.split('-').map(Number);
    const lat = locationObj.lat;
    const lon = locationObj.lon;
    const tz = locationObj.timezone || 'Asia/Kolkata';
    const method = 'Drik Ganita (Surya Siddhanta & Modern Ephemeris)';

    const cacheKey = getCacheKey(isoDate, lat, lon, tz, method);
    if (state.cache.has(cacheKey)) {
      return state.cache.get(cacheKey);
    }

    if (!engine || typeof engine.calculatePanchangam !== 'function') {
      throw new Error('Panchangam calculation engine is not loaded.');
    }

    const result = engine.calculatePanchangam({
      year,
      month,
      day,
      lat,
      lon,
      timezone: tz,
      ayanamsa,
      locationName: `${locationObj.name}, ${locationObj.state || 'India'}`
    });

    // Store in cache
    state.cache.set(cacheKey, result);
    return result;
  }

  /**
   * Update state and trigger recalculation and UI re-render.
   */
  function updateAndRender(newDate, newLocation) {
    if (newDate) state.date = newDate;
    if (newLocation) state.location = newLocation;

    try {
      state.currentPanchangam = computePanchangam(state.date, state.location, state.ayanamsa);
      renderUI();
    } catch (err) {
      console.error('Panchangam computation error:', err);
      showErrorState(err.message);
    }
  }

  /**
   * Render complete Panchangam UI dynamically into DOM.
   */
  function renderUI() {
    const p = state.currentPanchangam;
    if (!p) return;

    const is12h = state.timeFormat === '12h';
    const tz = state.location.timezone;
    const dateInfo = formatDateDetailed(state.date);

    // 1. Header & Location / Date Summary
    setElementText('pc-date-heading', `${dateInfo.en}`);
    setElementText('pc-location-heading', `${state.location.name}, ${state.location.state || 'India'}`);
    setElementText('pc-coord-badge', `${state.location.lat.toFixed(4)}°N, ${state.location.lon.toFixed(4)}°E`);
    setElementText('pc-tz-badge', `IST (UTC+5:30)`);
    setElementText('pc-ayanamsa-badge', `Lahiri Ayanamsa · Drik Ganita`);

    // Date Picker Input sync
    const dateInput = document.getElementById('pc-date-picker-input');
    if (dateInput) dateInput.value = state.date;

    // Previous & Next Day Button Labels
    const prevDate = shiftDate(state.date, -1);
    const nextDate = shiftDate(state.date, 1);
    const prevBtn = document.getElementById('pc-prev-day-btn');
    const nextBtn = document.getElementById('pc-next-day-btn');
    if (prevBtn) {
      const pD = formatDateDetailed(prevDate);
      prevBtn.setAttribute('title', `Previous day: ${pD.en}`);
    }
    if (nextBtn) {
      const nD = formatDateDetailed(nextDate);
      nextBtn.setAttribute('title', `Next day: ${nD.en}`);
    }

    // 2. Solar Information
    if (p.solar) {
      setElementText('val-sunrise', formatTime(p.solar.sunrise, tz, is12h));
      setElementText('val-sunset', formatTime(p.solar.sunset, tz, is12h));
      setElementText('val-solarnoon', formatTime(p.solar.solarNoon, tz, is12h));
      setElementText('val-daylength', p.solar.dayLengthStr || '—');
    }

    // 3. Lunar Information
    if (p.lunar) {
      const mrStatus = p.moonrise ? p.moonrise.status : 'normal';
      const msStatus = p.moonset ? p.moonset.status : 'normal';

      if (mrStatus === 'no_event') {
        setElementText('val-moonrise', 'No Rise Today');
      } else {
        const mrTime = p.moonrise && p.moonrise.datetime ? formatTime(p.moonrise.datetime, tz, is12h) : (p.solar ? p.solar.moonrise : '—');
        setElementText('val-moonrise', mrTime);
      }

      if (msStatus === 'no_event') {
        setElementText('val-moonset', 'No Set Today');
      } else {
        const msTime = p.moonset && p.moonset.datetime ? formatTime(p.moonset.datetime, tz, is12h) : (p.solar ? p.solar.moonset : '—');
        setElementText('val-moonset', msTime);
      }

      setElementText('val-moonphase', p.lunar.phase || '—');
      setElementText('val-moonphase-te', p.lunar.phaseTe || '');
      setElementText('val-illumination', p.lunar.illumination || '—');
    }

    // 4. Pancha Angas
    // Vara (Weekday)
    if (p.vara) {
      setElementText('val-vara-en', `${p.vara.en} (${p.vara.name})`);
      setElementText('val-vara-te', p.vara.nameTe || '');
      setElementText('val-vara-ruler', `Ruler: ${p.vara.ruler || '—'}`);
    }

    // Tithi
    if (p.tithi) {
      setElementText('val-tithi-name', `${p.tithi.name} (${p.tithi.paksha} Paksha)`);
      setElementText('val-tithi-te', p.tithi.nameTe || '');
      const tithiEndStr = p.tithi.end ? `Up to ${formatTime(p.tithi.end, tz, is12h)}` : 'Full Day';
      setElementText('val-tithi-end', tithiEndStr);
    }

    // Nakshatra
    if (p.nakshatra) {
      const nakObj = p.nakshatra.nakshatra || {};
      setElementText('val-nakshatra-name', `${nakObj.name || '—'} · Pada ${p.nakshatra.pada || 1}`);
      setElementText('val-nakshatra-te', nakObj.nameTe || '');
      const nakEndStr = p.nakshatra.end ? `Up to ${formatTime(p.nakshatra.end, tz, is12h)}` : 'Full Day';
      setElementText('val-nakshatra-end', nakEndStr);
      setElementText('val-nakshatra-meta', `Deity: ${nakObj.deity || '—'} · Ruler: ${nakObj.ruler || '—'}`);
    }

    // Yoga
    if (p.yoga) {
      setElementText('val-yoga-name', p.yoga.name || '—');
      setElementText('val-yoga-te', p.yoga.nameTe || '');
      const yogaEndStr = p.yoga.end ? `Up to ${formatTime(p.yoga.end, tz, is12h)}` : 'Full Day';
      setElementText('val-yoga-end', yogaEndStr);
    }

    // Karana
    if (p.karana) {
      setElementText('val-karana-name', p.karana.name || '—');
      const karanaEndStr = p.karana.end ? `Up to ${formatTime(p.karana.end, tz, is12h)}` : 'Full Day';
      setElementText('val-karana-end', karanaEndStr);
    }

    // 5. Inauspicious Timings
    if (p.timings) {
      setElementText('val-rahukalam', formatIntervalDisplay(p.timings.rahuKalam, tz, is12h));
      setElementText('val-yamagandam', formatIntervalDisplay(p.timings.yamagandam, tz, is12h));
      setElementText('val-gulikakalam', formatIntervalDisplay(p.timings.gulikaKalam, tz, is12h));

      // Dur Muhurtham (can have 1 or 2 slots)
      if (Array.isArray(p.timings.durMuhurtham) && p.timings.durMuhurtham.length > 0) {
        const dmText = p.timings.durMuhurtham
          .map(dm => formatIntervalDisplay(dm, tz, is12h))
          .join(' and ');
        setElementText('val-durmuhurtham', dmText);
      } else {
        setElementText('val-durmuhurtham', '—');
      }

      // Varjyam
      setElementText('val-varjyam', formatIntervalDisplay(p.timings.varjyam, tz, is12h));

      // 6. Auspicious Timings
      setElementText('val-abhijit', formatIntervalDisplay(p.timings.abhijitMuhurtham, tz, is12h));
      setElementText('val-amritkalam', formatIntervalDisplay(p.timings.amritKalam, tz, is12h));
      setElementText('val-brahmamuhurtham', formatIntervalDisplay(p.timings.brahmaMuhurtham, tz, is12h));
    }

    // 7. Metadata Inspection Display
    const metaContainer = document.getElementById('pc-meta-json');
    if (metaContainer && p.meta) {
      metaContainer.textContent = JSON.stringify(p.meta, null, 2);
    }
  }

  function setElementText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
  }

  function showErrorState(msg) {
    const errBox = document.getElementById('pc-error-banner');
    if (errBox) {
      errBox.style.display = 'block';
      errBox.textContent = `Error calculating Panchangam: ${msg}`;
    }
  }

  /**
   * Geolocation Handler: "Use My Location"
   */
  function handleUseMyLocation(onSuccess, onError) {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }

    const btn = document.getElementById('pc-btn-use-location');
    if (btn) btn.textContent = '📍 Detecting Location...';

    navigator.geolocation.getCurrentPosition(
      async position => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        try {
          let detected;
          if (locDB && typeof locDB.reverseGeocode === 'function') {
            detected = await locDB.reverseGeocode(lat, lon);
          } else {
            detected = {
              name: 'Detected Location',
              state: 'India',
              lat,
              lon,
              timezone: 'Asia/Kolkata'
            };
          }
          if (btn) btn.textContent = '📍 Use My Location';
          updateAndRender(null, detected);
          if (onSuccess) onSuccess(detected);
        } catch (e) {
          if (btn) btn.textContent = '📍 Use My Location';
          const fallback = {
            name: `${lat.toFixed(2)}°N, ${lon.toFixed(2)}°E`,
            state: 'India',
            lat,
            lon,
            timezone: 'Asia/Kolkata'
          };
          updateAndRender(null, fallback);
        }
      },
      error => {
        if (btn) btn.textContent = '📍 Use My Location';
        console.warn('Geolocation denied or failed:', error.message);
        alert('Could not access current location. Please search or pick a city from the list.');
        if (onError) onError(error);
      },
      { timeout: 10000, enableHighAccuracy: true }
    );
  }

  /**
   * Initialize DOM event listeners, search modal, and controls.
   */
  function init() {
    // 1. Prev Day Button
    const prevBtn = document.getElementById('pc-prev-day-btn');
    if (prevBtn) {
      prevBtn.addEventListener('click', e => {
        e.preventDefault();
        updateAndRender(shiftDate(state.date, -1), null);
      });
    }

    // 2. Next Day Button
    const nextBtn = document.getElementById('pc-next-day-btn');
    if (nextBtn) {
      nextBtn.addEventListener('click', e => {
        e.preventDefault();
        updateAndRender(shiftDate(state.date, 1), null);
      });
    }

    // 3. Today Button
    const todayBtn = document.getElementById('pc-today-btn');
    if (todayBtn) {
      todayBtn.addEventListener('click', e => {
        e.preventDefault();
        updateAndRender(getTodayIST(), null);
      });
    }

    // 4. Date Picker Input
    const dateInput = document.getElementById('pc-date-picker-input');
    if (dateInput) {
      dateInput.addEventListener('change', e => {
        if (e.target.value) {
          updateAndRender(e.target.value, null);
        }
      });
    }

    // 5. 12h / 24h Toggle
    const fmtBtn = document.getElementById('pc-time-format-btn');
    if (fmtBtn) {
      fmtBtn.addEventListener('click', () => {
        state.timeFormat = state.timeFormat === '12h' ? '24h' : '12h';
        fmtBtn.textContent = state.timeFormat === '12h' ? 'Switch to 24h' : 'Switch to 12h';
        renderUI();
      });
    }

    // 6. Use My Location Button
    const gpsBtn = document.getElementById('pc-btn-use-location');
    if (gpsBtn) {
      gpsBtn.addEventListener('click', e => {
        e.preventDefault();
        handleUseMyLocation();
      });
    }

    // 7. Location Search Modal / Dropdown
    initLocationSearchUI();

    // Initial render
    updateAndRender(state.date, state.location);
  }

  /**
   * Setup location search autocomplete & custom coordinates inputs.
   */
  function initLocationSearchUI() {
    const searchInput = document.getElementById('pc-loc-search-input');
    const stateFilter = document.getElementById('pc-state-filter');
    const resultsContainer = document.getElementById('pc-loc-results');
    const modal = document.getElementById('pc-location-modal');
    const openBtn = document.getElementById('pc-open-loc-modal-btn');
    const closeBtn = document.getElementById('pc-close-loc-modal-btn');

    if (openBtn && modal) {
      openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
        if (searchInput) {
          searchInput.focus();
          renderSearchResults();
        }
      });
    }

    if (closeBtn && modal) {
      closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
      });
    }

    // Populate state filter dropdown
    if (stateFilter && locDB && locDB.STATES_AND_UTS) {
      stateFilter.innerHTML = locDB.STATES_AND_UTS
        .map(st => `<option value="${st}">${st}</option>`)
        .join('');
      stateFilter.addEventListener('change', () => renderSearchResults());
    }

    function renderSearchResults() {
      if (!resultsContainer || !locDB) return;
      const q = searchInput ? searchInput.value : '';
      const st = stateFilter ? stateFilter.value : 'All States';
      const results = locDB.searchLocations(q, st, 15);

      if (results.length === 0) {
        resultsContainer.innerHTML = `
          <div style="padding:1rem;text-align:center;color:var(--clr-text-secondary,#666)">
            No locations found matching "${q}".<br>
            <small>Try another spelling or enter exact Latitude and Longitude below.</small>
          </div>
        `;
        return;
      }

      resultsContainer.innerHTML = results.map(loc => `
        <div class="loc-result-item" data-lat="${loc.lat}" data-lon="${loc.lon}" data-name="${loc.name}" data-state="${loc.state}">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <strong style="color:var(--clr-text,#222);font-size:0.95rem">${loc.name}</strong>
              <span style="color:var(--clr-text-secondary,#666);font-size:0.85rem">, ${loc.state}</span>
              ${loc.type ? `<span class="badge" style="font-size:0.7rem;margin-left:0.4rem;padding:0.15rem 0.4rem;background:#eee;border-radius:4px">${loc.type}</span>` : ''}
            </div>
            <div style="font-size:0.75rem;color:var(--clr-text-muted,#888)">
              ${loc.lat.toFixed(2)}°N, ${loc.lon.toFixed(2)}°E
            </div>
          </div>
        </div>
      `).join('');

      // Add click handlers
      const items = resultsContainer.querySelectorAll('.loc-result-item');
      items.forEach(item => {
        item.addEventListener('click', () => {
          const lat = parseFloat(item.dataset.lat);
          const lon = parseFloat(item.dataset.lon);
          const name = item.dataset.name;
          const stateName = item.dataset.state;
          const newLoc = {
            name,
            state: stateName,
            country: 'India',
            lat,
            lon,
            timezone: 'Asia/Kolkata'
          };
          updateAndRender(null, newLoc);
          if (modal) modal.style.display = 'none';
        });
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', () => renderSearchResults());
    }

    // Quick City Selection Pills
    const pills = document.querySelectorAll('.loc-quick-pill');
    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        const lat = parseFloat(pill.dataset.lat);
        const lon = parseFloat(pill.dataset.lon);
        const name = pill.dataset.name;
        const stateName = pill.dataset.state;
        const newLoc = {
          name,
          state: stateName,
          country: 'India',
          lat,
          lon,
          timezone: 'Asia/Kolkata'
        };
        updateAndRender(null, newLoc);
        if (modal) modal.style.display = 'none';
      });
    });

    // Custom Latitude & Longitude Submit
    const customCoordsBtn = document.getElementById('pc-apply-custom-coords-btn');
    if (customCoordsBtn) {
      customCoordsBtn.addEventListener('click', () => {
        const latVal = parseFloat(document.getElementById('pc-custom-lat').value);
        const lonVal = parseFloat(document.getElementById('pc-custom-lon').value);
        const labelVal = document.getElementById('pc-custom-name').value.trim() || 'Custom Coordinates';

        if (isNaN(latVal) || latVal < -90 || latVal > 90) {
          alert('Please enter a valid Latitude between -90 and 90.');
          return;
        }
        if (isNaN(lonVal) || lonVal < -180 || lonVal > 180) {
          alert('Please enter a valid Longitude between -180 and 180.');
          return;
        }

        const newLoc = {
          name: labelVal,
          state: 'Manual Entry',
          country: 'India',
          lat: latVal,
          lon: lonVal,
          timezone: 'Asia/Kolkata'
        };
        updateAndRender(null, newLoc);
        if (modal) modal.style.display = 'none';
      });
    }
  }

  // Public API
  return {
    state,
    init,
    computePanchangam,
    updateAndRender,
    setDate: isoDate => updateAndRender(isoDate, null),
    setLocation: locObj => updateAndRender(null, locObj),
    nextDay: () => updateAndRender(shiftDate(state.date, 1), null),
    prevDay: () => updateAndRender(shiftDate(state.date, -1), null),
    today: () => updateAndRender(getTodayIST(), null),
    setTimeFormat: fmt => {
      state.timeFormat = fmt;
      renderUI();
    },
    useCurrentLocation: handleUseMyLocation
  };
});
