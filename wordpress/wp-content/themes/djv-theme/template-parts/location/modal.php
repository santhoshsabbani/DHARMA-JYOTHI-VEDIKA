<?php
/**
 * Template Part: Location Selection Modal (Pan-India)
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="pc-modal-backdrop" id="location-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="loc-modal-title">
  <div class="pc-modal-dialog">
    <div class="pc-modal-header">
      <h3 class="pc-modal-title" id="loc-modal-title">📍 <?php esc_html_e( 'Select Location', 'djv-theme' ); ?></h3>
      <button type="button" class="pc-close-btn" id="loc-modal-close-btn" aria-label="<?php esc_attr_e( 'Close modal', 'djv-theme' ); ?>">✕</button>
    </div>
    
    <div class="pc-modal-body">
      <div>
        <label for="loc-search-input" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.35rem;color:var(--clr-dark);">
          <?php esc_html_e( 'Search City, Town, or Village in India:', 'djv-theme' ); ?>
        </label>
        <input type="text" id="loc-search-input" class="loc-input-field" placeholder="<?php esc_attr_e( 'Search city (e.g. Hyderabad, Ghatkesar, Vijayawada, Varanasi)...', 'djv-theme' ); ?>" autocomplete="off" />
      </div>

      <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <button type="button" class="loc-gps-btn" id="loc-gps-btn" style="flex:1;min-width:180px;">
          🎯 <?php esc_html_e( 'Use My Current Location', 'djv-theme' ); ?>
        </button>
        <select id="loc-state-filter" class="loc-select-field" aria-label="<?php esc_attr_e( 'Filter by State or Union Territory', 'djv-theme' ); ?>">
          <option value=""><?php esc_html_e( 'All States & UTs (36)', 'djv-theme' ); ?></option>
        </select>
      </div>

      <!-- Real-time Status / Feedback Banner -->
      <div id="loc-status-banner" class="loc-status-banner" style="display:none;" role="status" aria-live="polite"></div>

      <!-- Popular Vedic Centers -->
      <div>
        <div style="font-size:0.75rem;font-weight:600;color:var(--clr-text-muted);margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.05em;">
          <?php esc_html_e( 'Popular Vedic Centers:', 'djv-theme' ); ?>
        </div>
        <div class="loc-quick-chips" id="popular-cities-list">
          <!-- Populated dynamically by JS -->
        </div>
      </div>

      <!-- Autocomplete/Search Results list -->
      <div id="loc-results-container" class="loc-results-list">
        <div style="padding:1rem;text-align:center;color:var(--clr-text-muted);font-size:0.85rem;">
          <?php esc_html_e( 'Type to search among 350+ Indian cities and towns with exact astronomical coordinates.', 'djv-theme' ); ?>
        </div>
      </div>
    </div>
  </div>
</div>
