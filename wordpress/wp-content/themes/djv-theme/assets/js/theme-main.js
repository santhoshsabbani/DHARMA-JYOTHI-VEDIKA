/**
 * Dharma Jyothi Vedika — Theme Main UI Scripts
 * Handles Header Navigation, Mobile Drawer, Search Overlay, and Pan-India Location Modal.
 */

(function () {
  'use strict';

  const STORAGE_PRIMARY  = 'djv_selected_location';
  const STORAGE_FALLBACK = 'djv_user_location';

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

  const defaultLoc = Object.freeze({
    name: 'Hyderabad',
    city: 'Hyderabad',
    state: 'Telangana',
    latitude: 17.3850,
    longitude: 78.4867,
    lat: 17.3850,
    lon: 78.4867,
    timezone: 'Asia/Kolkata'
  });

  /**
   * Retrieve active location from localStorage or default.
   * Seamlessly handles legacy string formats and normalizes into numeric coordinates.
   */
  function getStoredLocation() {
    try {
      const raw = localStorage.getItem(STORAGE_PRIMARY) || localStorage.getItem(STORAGE_FALLBACK);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === 'object') {
          return normalizeLocation(parsed, defaultLoc);
        }
      }
    } catch (e) {}
    return normalizeLocation(defaultLoc);
  }

  /**
   * Persist location and dispatch global event to update Panchangam.
   */
  function setStoredLocation(loc) {
    const normalized = normalizeLocation(loc, defaultLoc);

    try {
      localStorage.setItem(STORAGE_PRIMARY, JSON.stringify(normalized));
      localStorage.setItem(STORAGE_FALLBACK, JSON.stringify(normalized));
    } catch (e) {}

    // Dispatch global event for all listeners (theme-panchangam.js, etc.)
    window.dispatchEvent(new CustomEvent('djv:locationChanged', { detail: normalized }));
    updateLocationDisplays(normalized);
  }

  /**
   * Update all header and card location badges across the DOM.
   */
  function updateLocationDisplays(loc) {
    const normalized = normalizeLocation(loc, defaultLoc);

    document.querySelectorAll('.global-location-name, #hero-loc-name').forEach(el => {
      el.textContent = normalized.name;
    });

    const subText = document.getElementById('pc-current-location-text');
    if (subText) {
      subText.textContent = `${normalized.name}${normalized.state ? ', ' + normalized.state : ''}`;
    }

    const badge = document.getElementById('pc-detail-location-badge');
    if (badge) {
      badge.textContent = `${normalized.name.toUpperCase()}, ${(normalized.state || 'INDIA').toUpperCase()} · ${formatCoordinate(normalized.latitude, 4)}° N, ${formatCoordinate(normalized.longitude, 4)}° E`;
    }
  }

  if (typeof window !== 'undefined') {
    window.DJV_LOCATION = {
      getSelected: getStoredLocation,
      setSelected: setStoredLocation,
      normalizeLocation: normalizeLocation,
      formatCoordinate: formatCoordinate
    };
  }

  document.addEventListener('DOMContentLoaded', function () {
    const activeLoc = getStoredLocation();
    updateLocationDisplays(activeLoc);

    // ── Mobile Navigation Drawer ─────────────────────────────
    const menuToggle      = document.getElementById('menu-toggle-btn');
    const mobileNav       = document.getElementById('mobile-nav');
    const mobileNavClose  = document.getElementById('mobile-nav-close-btn');

    if (menuToggle && mobileNav) {
      menuToggle.addEventListener('click', function () {
        mobileNav.classList.add('open');
        menuToggle.setAttribute('aria-expanded', 'true');
      });
      if (mobileNavClose) {
        mobileNavClose.addEventListener('click', function () {
          mobileNav.classList.remove('open');
          menuToggle.setAttribute('aria-expanded', 'false');
        });
      }
      mobileNav.addEventListener('click', function (e) {
        if (e.target === mobileNav) {
          mobileNav.classList.remove('open');
          menuToggle.setAttribute('aria-expanded', 'false');
        }
      });
    }

    // ── Search Overlay ───────────────────────────────────────
    const searchOpenBtn  = document.getElementById('search-open-btn');
    const searchOverlay  = document.getElementById('search-overlay');
    const searchCloseBtn = document.getElementById('search-close-btn');
    const searchInput    = document.getElementById('search-input');

    if (searchOpenBtn && searchOverlay) {
      searchOpenBtn.addEventListener('click', function () {
        searchOverlay.classList.add('open');
        if (searchInput) searchInput.focus();
      });
      if (searchCloseBtn) {
        searchCloseBtn.addEventListener('click', function () {
          searchOverlay.classList.remove('open');
        });
      }
      searchOverlay.addEventListener('click', function (e) {
        if (e.target === searchOverlay) {
          searchOverlay.classList.remove('open');
        }
      });
      document.querySelectorAll('.search-chip').forEach(chip => {
        chip.addEventListener('click', function () {
          const q = this.getAttribute('data-search') || this.textContent;
          if (searchInput) {
            searchInput.value = q;
            searchInput.closest('form').submit();
          }
        });
      });
    }

    // ── Pan-India Location Modal ──────────────────────────────
    const locModal         = document.getElementById('location-modal-backdrop');
    const locCloseBtn      = document.getElementById('loc-modal-close-btn');
    const locSearchInput   = document.getElementById('loc-search-input');
    const locStateFilter   = document.getElementById('loc-state-filter');
    const locGpsBtn        = document.getElementById('loc-gps-btn');
    const locStatusBanner  = document.getElementById('loc-status-banner');
    const popularContainer = document.getElementById('popular-cities-list');
    const resultsContainer = document.getElementById('loc-results-container');
    const quickGpsBtn      = document.getElementById('pc-gps-quick-btn');

    function openLocationModal() {
      if (!locModal) return;
      locModal.classList.add('open');
      locModal.style.display = 'flex';
      initLocationModal();
      if (locSearchInput) {
        locSearchInput.focus();
      }
    }

    function closeLocationModal() {
      if (!locModal) return;
      locModal.classList.remove('open');
      locModal.style.display = 'none';
      hideModalStatus();
    }

    function showModalStatus(htmlContent, type = 'info', focusSearchOnAction = false) {
      if (!locStatusBanner) return;
      locStatusBanner.className = `loc-status-banner ${type}`;
      locStatusBanner.innerHTML = htmlContent;
      locStatusBanner.style.display = 'flex';

      const manualLink = locStatusBanner.querySelector('.loc-manual-link');
      if (manualLink) {
        manualLink.addEventListener('click', function (e) {
          e.preventDefault();
          hideModalStatus();
          if (locSearchInput) {
            locSearchInput.focus();
            locSearchInput.select();
          }
        });
      }

      if (focusSearchOnAction && locSearchInput) {
        locSearchInput.focus();
      }
    }

    function hideModalStatus() {
      if (!locStatusBanner) return;
      locStatusBanner.style.display = 'none';
      locStatusBanner.innerHTML = '';
      locStatusBanner.className = 'loc-status-banner';
    }

    // Modal Triggers
    document.querySelectorAll('.global-location-pill, #hero-location-btn, #change-location-btn, #pc-location-pill-btn').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        openLocationModal();
      });
    });

    if (locCloseBtn) {
      locCloseBtn.addEventListener('click', closeLocationModal);
    }

    if (locModal) {
      locModal.addEventListener('click', function (e) {
        if (e.target === locModal) {
          closeLocationModal();
        }
      });
    }

    // Populate Location Modal Options
    function initLocationModal() {
      if (!window.IndiaLocations) return;
      const db = window.IndiaLocations;

      // Populate States filter dropdown if empty
      if (locStateFilter && locStateFilter.options.length <= 1) {
        const states = db.getStates ? db.getStates() : (db.STATES_AND_UTS ? db.STATES_AND_UTS.filter(s => s !== 'All States') : []);
        states.forEach(st => {
          const opt = document.createElement('option');
          opt.value = st;
          opt.textContent = st;
          locStateFilter.appendChild(opt);
        });
      }

      // Populate Popular Vedic Centers
      if (popularContainer && popularContainer.children.length === 0) {
        const popular = [
          { name: 'Hyderabad',     state: 'Telangana',      lat: 17.3850, lon: 78.4867 },
          { name: 'Ghatkesar',     state: 'Telangana',      lat: 17.4475, lon: 78.6833 },
          { name: 'Vijayawada',    state: 'Andhra Pradesh', lat: 16.5062, lon: 80.6480 },
          { name: 'Varanasi',      state: 'Uttar Pradesh',  lat: 25.3176, lon: 82.9739 },
          { name: 'Tirupati',      state: 'Andhra Pradesh', lat: 13.6288, lon: 79.4192 },
          { name: 'Visakhapatnam', state: 'Andhra Pradesh', lat: 17.6868, lon: 83.2185 },
          { name: 'Bengaluru',     state: 'Karnataka',      lat: 12.9716, lon: 77.5946 },
          { name: 'Chennai',       state: 'Tamil Nadu',     lat: 13.0827, lon: 80.2707 },
          { name: 'Mumbai',        state: 'Maharashtra',    lat: 19.0760, lon: 72.8777 },
          { name: 'Delhi',         state: 'Delhi',          lat: 28.6139, lon: 77.2090 }
        ].map(c => normalizeLocation(c));

        popular.forEach(c => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'loc-chip-btn';
          btn.textContent = c.name;
          btn.addEventListener('click', function () {
            setStoredLocation(normalizeLocation(c));
            closeLocationModal();
          });
          popularContainer.appendChild(btn);
        });
      }

      renderSearchResults(locSearchInput ? locSearchInput.value.trim() : '', locStateFilter ? locStateFilter.value : '');
    }

    function renderSearchResults(query, stateFilter = '') {
      if (!resultsContainer || !window.IndiaLocations) return;
      const db = window.IndiaLocations;
      const results = db.searchLocations ? db.searchLocations(query, stateFilter, 40) : [];

      if (results.length === 0) {
        resultsContainer.innerHTML = '<div style="padding:1.25rem;text-align:center;color:var(--clr-text-muted);font-size:0.875rem;">No matching Indian cities found. Please try another name or filter by State.</div>';
        return;
      }

      resultsContainer.innerHTML = '';
      results.forEach(loc => {
        const item = document.createElement('div');
        item.className = 'loc-result-item';

        item.innerHTML = `
          <div>
            <strong>${loc.name}</strong>
            <span style="margin-left:0.35rem;">(${loc.state})</span>
          </div>
          <div style="font-size:0.75rem;color:var(--clr-text-muted);">
            ${formatCoordinate(loc.latitude ?? loc.lat, 2)}° N, ${formatCoordinate(loc.longitude ?? loc.lon, 2)}° E
          </div>
        `;

        item.addEventListener('click', function () {
          setStoredLocation(normalizeLocation(loc));
          closeLocationModal();
        });

        resultsContainer.appendChild(item);
      });
    }

    if (locSearchInput) {
      locSearchInput.addEventListener('input', function () {
        renderSearchResults(this.value.trim(), locStateFilter ? locStateFilter.value : '');
      });
    }

    if (locStateFilter) {
      locStateFilter.addEventListener('change', function () {
        renderSearchResults(locSearchInput ? locSearchInput.value.trim() : '', this.value);
      });
    }

    // ── Browser Geolocation: "Use My Current Location" ────────
    function executeGeolocation(btnEl) {
      if (!navigator.geolocation) {
        showModalStatus(
          'Geolocation is not supported by your browser. Please <a class="loc-manual-link">choose a location manually</a>.',
          'error'
        );
        return;
      }

      const originalHtml = btnEl ? btnEl.innerHTML : '🎯 Use My Current Location';
      if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = '⏳ Detecting your location…';
      }
      showModalStatus('Detecting your location…', 'info');

      const geoOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 300000
      };

      navigator.geolocation.getCurrentPosition(
        function (pos) {
          try {
            const rawLat = Number(pos.coords.latitude);
            const rawLon = Number(pos.coords.longitude);

            if (!Number.isFinite(rawLat) || !Number.isFinite(rawLon)) {
              throw new Error('Invalid GPS coordinates received from browser.');
            }

            const db = window.IndiaLocations;
            if (!db) {
              throw new Error('Location database not ready.');
            }

            // Check if coordinates fall within supported Indian territory
            const isInside = db.isWithinIndia
              ? db.isWithinIndia(rawLat, rawLon)
              : (rawLat >= 6.0 && rawLat <= 37.5 && rawLon >= 68.0 && rawLon <= 97.5);

            if (!isInside) {
              if (btnEl) {
                btnEl.disabled = false;
                btnEl.innerHTML = originalHtml;
              }
              showModalStatus(
                'Your current location is outside the supported India locations. <br><a class="loc-manual-link">Choose Location Manually</a>',
                'warning'
              );
              return;
            }

            // Find nearest supported Indian city using Haversine calculation
            const nearestRes = db.findNearestLocation(rawLat, rawLon);
            if (!nearestRes || !nearestRes.location) {
              throw new Error('Nearest city lookup failed.');
            }

            const selectedLocation = normalizeLocation(nearestRes.location);

            showModalStatus(
              `✓ Location detected: <strong>${selectedLocation.name}, ${selectedLocation.state}</strong>`,
              'success'
            );
            if (btnEl) {
              btnEl.innerHTML = '✓ Location detected';
            }

            // Persist and broadcast location change
            setStoredLocation(selectedLocation);

            // Close modal after brief visual confirmation
            setTimeout(function () {
              closeLocationModal();
              if (btnEl) {
                btnEl.disabled = false;
                btnEl.innerHTML = originalHtml;
              }
            }, 800);

          } catch (err) {
            if (btnEl) {
              btnEl.disabled = false;
              btnEl.innerHTML = originalHtml;
            }
            showModalStatus(
              'Unable to match nearest supported city. Please <a class="loc-manual-link">choose a location manually</a>.',
              'error'
            );
          }
        },
        function (err) {
          if (btnEl) {
            btnEl.disabled = false;
            btnEl.innerHTML = originalHtml;
          }

          switch (err.code) {
            case err.PERMISSION_DENIED:
              showModalStatus(
                'Location permission was denied. Please allow location access in your browser or <a class="loc-manual-link">choose a location manually</a>.',
                'error'
              );
              break;
            case err.TIMEOUT:
              showModalStatus(
                'Unable to detect your location. Please try again or <a class="loc-manual-link">choose a location manually</a>.',
                'warning'
              );
              break;
            case err.POSITION_UNAVAILABLE:
              showModalStatus(
                'Your location could not be detected. Please <a class="loc-manual-link">choose a location manually</a>.',
                'error'
              );
              break;
            default:
              showModalStatus(
                'Unable to detect your location. Please <a class="loc-manual-link">choose a location manually</a>.',
                'error'
              );
          }
        },
        geoOptions
      );
    }

    if (locGpsBtn) {
      locGpsBtn.addEventListener('click', function (e) {
        e.preventDefault();
        executeGeolocation(locGpsBtn);
      });
    }

    if (quickGpsBtn) {
      quickGpsBtn.addEventListener('click', function (e) {
        e.preventDefault();
        openLocationModal();
        executeGeolocation(locGpsBtn);
      });
    }

    // ESC key closes modals
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        if (searchOverlay) searchOverlay.classList.remove('open');
        closeLocationModal();
        if (mobileNav) mobileNav.classList.remove('open');
      }
    });
  });
})();
