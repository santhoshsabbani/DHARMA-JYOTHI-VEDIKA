/**
 * ============================================================
 * DJV Panchangam Engine — Validation Test Suite
 * packages/panchangam-engine/src/validation/panchangam.test.js
 *
 * Tests against known reference values from published Panchangams.
 * Run: node panchangam.test.js
 * ============================================================
 */

'use strict';

const { calculatePanchangam } = require('../core');

/* ─── Test Runner ──────────────────────────────────────────── */
let passed = 0, failed = 0, warnings = 0;

function test(name, fn) {
  try {
    fn();
    console.log(`  ✅ PASS: ${name}`);
    passed++;
  } catch (e) {
    console.log(`  ❌ FAIL: ${name}`);
    console.log(`       ${e.message}`);
    failed++;
  }
}

function warn(name, msg) {
  console.log(`  ⚠️  WARN: ${name} — ${msg}`);
  warnings++;
}

function assertEqual(actual, expected, message, tolerance = 0) {
  if (tolerance > 0) {
    if (Math.abs(actual - expected) > tolerance) {
      throw new Error(`${message}: expected ${expected} ± ${tolerance}, got ${actual}`);
    }
  } else {
    if (actual !== expected) {
      throw new Error(`${message}: expected "${expected}", got "${actual}"`);
    }
  }
}

function assertContains(actual, expected, message) {
  if (!actual.toLowerCase().includes(expected.toLowerCase())) {
    throw new Error(`${message}: "${actual}" does not contain "${expected}"`);
  }
}

/* ─── Reference Data ────────────────────────────────────────── */
/*
  IMPORTANT: These reference values must be verified against an authoritative
  published Panchangam (e.g., Sriramadandu, Meena Panchanga, or Eenadu Panchangam).
  Until verification is complete, treat engine output as PROVISIONAL.

  Reference location: Hyderabad (17.3850°N, 78.4867°E, IST = UTC+5.5)
  Ayanamsa: Lahiri (Chitrapaksha)
  Month system: Amanta

  NOTE: These test values are PLACEHOLDER REFERENCES.
  Replace with actual verified values before launch.
*/

const HYDERABAD = { lat: 17.3850, lon: 78.4867, tzOffset: 5.5 };

/* ─── Test Suite ─────────────────────────────────────────────── */

console.log('\n════════════════════════════════════════════════════════');
console.log(' DHARMA JYOTHI VEDIKA — Panchangam Validation Suite');
console.log('════════════════════════════════════════════════════════');
console.log(' ⚠️  PRE-LAUNCH VALIDATION — All values are PROVISIONAL');
console.log(' Do not use for ceremonial purposes until validated.\n');

/* ─── Core Astronomy ─────────────────────────────────────────── */
console.log('── Core Astronomy ──────────────────────────────────────');

test('Tithi calculation returns valid tithi ID (1–30)', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (p.tithi.id < 1 || p.tithi.id > 30) throw new Error(`Tithi ID out of range: ${p.tithi.id}`);
});

test('Nakshatra calculation returns valid nakshatra ID (1–27)', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (p.nakshatra.nakshatra.id < 1 || p.nakshatra.nakshatra.id > 27) throw new Error(`Nakshatra ID out of range: ${p.nakshatra.nakshatra.id}`);
});

test('Yoga calculation returns valid yoga ID (1–27)', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (p.yoga.id < 1 || p.yoga.id > 27) throw new Error(`Yoga ID out of range: ${p.yoga.id}`);
});

test('Nakshatra pada is 1–4', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (p.nakshatra.pada < 1 || p.nakshatra.pada > 4) throw new Error(`Pada out of range: ${p.nakshatra.pada}`);
});

test('Vara is Saturday for 2026-10-03', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  assertEqual(p.vara.en, 'Saturday', 'Weekday for Oct 3, 2026');
});

test('Vara is Friday for 2026-10-02', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 2, ...HYDERABAD });
  assertEqual(p.vara.en, 'Friday', 'Weekday for Oct 2, 2026');
});

/* ─── Sunrise / Sunset ──────────────────────────────────────── */
console.log('\n── Sunrise / Sunset ────────────────────────────────────');

test('Sunrise exists for Hyderabad', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.solar || !p.solar.sunrise) throw new Error('Sunrise is null');
});

test('Sunset is after sunrise', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (p.solar.sunset <= p.solar.sunrise) throw new Error('Sunset is not after sunrise');
});

test('Sunrise is between 5–8 AM IST for Hyderabad (Oct)', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  const sunriseIST = new Date(p.solar.sunrise.getTime() + 5.5 * 3600000);
  const h = sunriseIST.getUTCHours();
  if (h < 5 || h > 8) throw new Error(`Sunrise hour ${h}h IST is outside expected range [5,8]`);
});

test('Sunset is between 5–7 PM IST for Hyderabad (Oct)', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  const sunsetIST = new Date(p.solar.sunset.getTime() + 5.5 * 3600000);
  const h = sunsetIST.getUTCHours();
  if (h < 17 || h > 19) throw new Error(`Sunset hour ${h}h IST is outside expected range [17,19]`);
});

/* ─── Rahu Kalam / Yamagandam / Gulika ─────────────────────── */
console.log('\n── Inauspicious Timings ────────────────────────────────');

test('Rahu Kalam is calculated for Saturday', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.timings.rahuKalam) throw new Error('Rahu Kalam not calculated');
  if (p.timings.rahuKalam.end <= p.timings.rahuKalam.start) throw new Error('Rahu Kalam end is before start');
});

test('Rahu Kalam duration is approximately 1.5 hours', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  const durationMs = p.timings.rahuKalam.end - p.timings.rahuKalam.start;
  const durationHrs = durationMs / 3600000;
  // Allow ±20 min tolerance
  if (durationHrs < 1.2 || durationHrs > 1.8) throw new Error(`Rahu Kalam duration ${durationHrs.toFixed(2)}h outside expected range`);
});

test('Abhijit Muhurtham is near solar noon', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.timings.abhijitMuhurtham) throw new Error('Abhijit not calculated');
  const abhijitIST = new Date(p.timings.abhijitMuhurtham.solarNoon.getTime() + 5.5 * 3600000);
  const h = abhijitIST.getUTCHours();
  // Solar noon should be around 11:30 AM - 12:30 PM IST for Hyderabad
  if (h < 11 || h > 13) throw new Error(`Solar noon hour ${h}h IST is outside expected range [11,13]`);
});

/* ─── Output Structure ──────────────────────────────────────── */
console.log('\n── Output Structure ────────────────────────────────────');

test('Result contains engineVersion', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.meta.engineVersion) throw new Error('engineVersion missing');
});

test('Result contains ruleVersion', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.meta.ruleVersion) throw new Error('ruleVersion missing');
});

test('Result contains generatedAt timestamp', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.meta.generatedAt) throw new Error('generatedAt missing');
});

test('Tithi has English name', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.tithi.name || p.tithi.name.length < 3) throw new Error('Tithi name missing or too short');
});

test('Nakshatra has Telugu name', () => {
  const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...HYDERABAD });
  if (!p.nakshatra.nakshatra.nameTe) throw new Error('Nakshatra Telugu name missing');
});

/* ─── Regional Variations ───────────────────────────────────── */
console.log('\n── Multi-city Sanity Checks ────────────────────────────');

const CITIES = [
  { name: 'Hyderabad',      lat: 17.3850, lon: 78.4867, tzOffset: 5.5 },
  { name: 'Vijayawada',     lat: 16.5062, lon: 80.6480, tzOffset: 5.5 },
  { name: 'Visakhapatnam',  lat: 17.6868, lon: 83.2185, tzOffset: 5.5 },
  { name: 'Tirupati',       lat: 13.6288, lon: 79.4192, tzOffset: 5.5 },
  { name: 'Bengaluru',      lat: 12.9716, lon: 77.5946, tzOffset: 5.5 },
  { name: 'Chennai',        lat: 13.0827, lon: 80.2707, tzOffset: 5.5 },
  { name: 'Mumbai',         lat: 19.0760, lon: 72.8777, tzOffset: 5.5 },
  { name: 'New Delhi',      lat: 28.6139, lon: 77.2090, tzOffset: 5.5 },
  { name: 'New York',       lat: 40.7128, lon: -74.0060, tzOffset: -5 },
  { name: 'London',         lat: 51.5074, lon: -0.1278,  tzOffset: 0  },
  { name: 'Sydney',         lat: -33.8688,lon: 151.2093, tzOffset: 10 },
];

CITIES.forEach(city => {
  test(`Sunrise/sunset calculated for ${city.name}`, () => {
    const p = calculatePanchangam({ year: 2026, month: 10, day: 3, ...city });
    if (!p.solar) throw new Error(`Solar times null for ${city.name}`);
    if (p.solar.sunset <= p.solar.sunrise) throw new Error(`Sunset before sunrise for ${city.name}`);
  });
});

/* ─── Special Days ──────────────────────────────────────────── */
console.log('\n── Special Days ────────────────────────────────────────');

test('Amavasya (New Moon) — Tithi ID = 30', () => {
  // This is approximate. The actual Amavasya day in a given month
  // depends on exact timing. We test that an Amavasya day we know
  // returns Tithi 30 or near it.
  // PLACEHOLDER: Replace with a verified Amavasya date
  warn('Amavasya date', 'Replace with verified Amavasya date from reference Panchangam before launch');
});

test('Ekadashi — Tithi ID = 11 or 26', () => {
  warn('Ekadashi date', 'Replace with verified Ekadashi date from reference Panchangam before launch');
});

/* ─── Summary ────────────────────────────────────────────────── */
console.log('\n════════════════════════════════════════════════════════');
console.log(` Results: ${passed} passed, ${failed} failed, ${warnings} warnings`);
if (failed > 0) {
  console.log(' ❌ Some tests FAILED. Engine needs review before launch.');
} else if (warnings > 0) {
  console.log(' ⚠️  All tests passed but manual validation still required.');
  console.log('    Replace placeholder reference values with verified data.');
} else {
  console.log(' ✅ All tests passed!');
}
console.log('');
console.log(' IMPORTANT: Automated tests check calculation structure only.');
console.log(' MANDATORY: Manually compare output against published Panchangam');
console.log(' for at least 30 reference dates before public launch.');
console.log('════════════════════════════════════════════════════════\n');

if (failed > 0) process.exit(1);
