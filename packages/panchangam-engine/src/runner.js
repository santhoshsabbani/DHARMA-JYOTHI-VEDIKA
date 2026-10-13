/**
 * ============================================================
 * DJV Panchangam Engine — Node.js CLI Runner
 * packages/panchangam-engine/src/runner.js
 *
 * Called by the WordPress PHP bridge (class-djv-panchangam.php)
 * as a subprocess for live calculations.
 *
 * Usage:
 *   node runner.js <date> <latitude> <longitude> <timezone>
 *   node runner.js 2026-10-03 17.3850 78.4867 Asia/Kolkata
 *
 * Output: JSON to stdout (single line)
 * Errors: JSON { "error": "..." } to stdout
 * ============================================================
 */

'use strict';

const { calculatePanchangam } = require('./core');

// Parse CLI arguments
const args = process.argv.slice(2);

// Check for --range mode
if (args[0] === '--range') {
  if (args.length < 6) {
    process.stdout.write(JSON.stringify({
      error: 'Usage: node runner.js --range <start YYYY-MM-DD> <end YYYY-MM-DD> <latitude> <longitude> <timezone>'
    }));
    process.exit(1);
  }
  const [, startDateStr, endDateStr, latStr, lonStr, timezone] = args;
  const lat = parseFloat(latStr);
  const lon = parseFloat(lonStr);
  if (isNaN(lat) || isNaN(lon)) {
    process.stdout.write(JSON.stringify({ error: 'Invalid coordinates' }));
    process.exit(1);
  }
  try {
    new Intl.DateTimeFormat('en-US', { timeZone: timezone });
  } catch (e) {
    process.stdout.write(JSON.stringify({ error: 'Invalid timezone' }));
    process.exit(1);
  }

  const results = {};
  const curr = new Date(startDateStr + 'T00:00:00Z');
  const end = new Date(endDateStr + 'T00:00:00Z');

  // Limit max range to 120 days for safety
  let count = 0;
  while (curr <= end && count < 120) {
    const y = curr.getUTCFullYear();
    const m = curr.getUTCMonth() + 1;
    const d = curr.getUTCDate();
    const dStr = `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    try {
      results[dStr] = calculatePanchangam({ year: y, month: m, day: d, lat, lon, timezone });
    } catch (err) {
      results[dStr] = { error: err.message };
    }
    curr.setUTCDate(curr.getUTCDate() + 1);
    count++;
  }

  process.stdout.write(JSON.stringify({ status: 'success', days: results }));
  process.exit(0);
}

const [dateStr, latStr, lonStr, timezone] = args;

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

if (isNaN(lat) || lat < -90  || lat >  90) { process.stdout.write(JSON.stringify({ error: 'Invalid latitude.' }));  process.exit(1); }
if (isNaN(lon) || lon < -180 || lon >  180) { process.stdout.write(JSON.stringify({ error: 'Invalid longitude.' })); process.exit(1); }

try {
  new Intl.DateTimeFormat('en-US', { timeZone: timezone });
} catch (e) {
  process.stdout.write(JSON.stringify({ error: 'Invalid timezone identifier.' })); process.exit(1);
}

// Calculate
try {
  const result = calculatePanchangam({
    year, month, day,
    lat, lon, timezone
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
