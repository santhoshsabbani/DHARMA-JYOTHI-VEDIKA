/**
 * ============================================================
 * DJV Panchangam Engine — Node.js CLI Runner
 * packages/panchangam-engine/src/runner.js
 *
 * Called by the WordPress PHP bridge (class-djv-panchangam.php)
 * as a subprocess for live calculations.
 *
 * Usage:
 *   node runner.js <date> <latitude> <longitude> <tz_offset>
 *   node runner.js 2026-10-03 17.3850 78.4867 5.5
 *
 * Output: JSON to stdout (single line)
 * Errors: JSON { "error": "..." } to stdout
 * ============================================================
 */

'use strict';

const { calculatePanchangam } = require('./core');

// Parse CLI arguments
const args = process.argv.slice(2);

if (args.length < 4) {
  process.stdout.write(JSON.stringify({
    error: 'Usage: node runner.js <YYYY-MM-DD> <latitude> <longitude> <tz_offset>'
  }));
  process.exit(1);
}

const [dateStr, latStr, lonStr, tzStr] = args;

// Validate date
const dateMatch = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
if (!dateMatch) {
  process.stdout.write(JSON.stringify({ error: 'Invalid date format. Use YYYY-MM-DD.' }));
  process.exit(1);
}

const year  = parseInt(dateMatch[1], 10);
const month = parseInt(dateMatch[2], 10);
const day   = parseInt(dateMatch[3], 10);

// Validate ranges
if (year < 1900 || year > 2100 || month < 1 || month > 12 || day < 1 || day > 31) {
  process.stdout.write(JSON.stringify({ error: 'Date out of valid range (1900–2100).' }));
  process.exit(1);
}

const lat      = parseFloat(latStr);
const lon      = parseFloat(lonStr);
const tzOffset = parseFloat(tzStr);

if (isNaN(lat) || lat < -90  || lat >  90) { process.stdout.write(JSON.stringify({ error: 'Invalid latitude.' }));  process.exit(1); }
if (isNaN(lon) || lon < -180 || lon >  180) { process.stdout.write(JSON.stringify({ error: 'Invalid longitude.' })); process.exit(1); }
if (isNaN(tzOffset) || tzOffset < -14 || tzOffset > 14) { process.stdout.write(JSON.stringify({ error: 'Invalid timezone offset.' })); process.exit(1); }

// Calculate
try {
  const result = calculatePanchangam({
    year, month, day,
    lat, lon, tzOffset
  });

  // Output single-line JSON (important: PHP reads from stdout)
  process.stdout.write(JSON.stringify(result));
  process.exit(0);

} catch (err) {
  process.stdout.write(JSON.stringify({
    error: err.message || 'Unknown calculation error',
    stack: process.env.NODE_ENV === 'development' ? err.stack : undefined
  }));
  process.exit(1);
}
