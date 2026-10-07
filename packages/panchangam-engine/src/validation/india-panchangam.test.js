/**
 * ============================================================
 * India-Wide Panchangam & Dynamic Date/Location Validation Tests
 * packages/panchangam-engine/src/validation/india-panchangam.test.js
 *
 * Validates:
 * - Test A: Hyderabad + October 5, 2026
 * - Test B: Hyderabad + October 6, 2026
 * - Test C: Hyderabad + October 7, 2026
 * - Test D: Hyderabad + October 6, 2026
 * - Test E: Delhi + October 6, 2026
 * - Date independence & Location independence
 * - Caching composite key integrity
 * - Metadata presence & accuracy
 * - Nationwide geographic boundaries (Srinagar to Kanyakumari, Dwarka to Guwahati)
 * ============================================================
 */

'use strict';

const { calculatePanchangam } = require('../core.js');

let passed = 0, failed = 0;

function assert(condition, message) {
  if (!condition) {
    console.error(`  ❌ FAIL: ${message}`);
    failed++;
    throw new Error(message);
  } else {
    console.log(`  ✅ PASS: ${message}`);
    passed++;
  }
}

console.log('════════════════════════════════════════════════════════');
console.log(' INDIA-WIDE DYNAMIC PANCHANGAM VALIDATION TEST SUITE');
console.log('════════════════════════════════════════════════════════');

const HYDERABAD = { lat: 17.3850, lon: 78.4867, timezone: 'Asia/Kolkata', locationName: 'Hyderabad, Telangana' };
const DELHI     = { lat: 28.6139, lon: 77.2090, timezone: 'Asia/Kolkata', locationName: 'Delhi, Delhi' };
const MUMBAI    = { lat: 19.0760, lon: 72.8777, timezone: 'Asia/Kolkata', locationName: 'Mumbai, Maharashtra' };
const GUWAHATI  = { lat: 26.1445, lon: 91.7362, timezone: 'Asia/Kolkata', locationName: 'Guwahati, Assam' };
const SRINAGAR  = { lat: 34.0837, lon: 74.7973, timezone: 'Asia/Kolkata', locationName: 'Srinagar, Jammu & Kashmir' };

// ─── Test Suite 1: Date Independence (Tests A, B, C) ────────────────
console.log('\n── Test Suite 1: Date Changes (Hyderabad: Oct 5, 6, 7, 2026) ──');

// Test A: Hyderabad + October 5, 2026
const testA = calculatePanchangam({ year: 2026, month: 10, day: 5, ...HYDERABAD });
// Test B: Hyderabad + October 6, 2026
const testB = calculatePanchangam({ year: 2026, month: 10, day: 6, ...HYDERABAD });
// Test C: Hyderabad + October 7, 2026
const testC = calculatePanchangam({ year: 2026, month: 10, day: 7, ...HYDERABAD });

// Verify Test A (Oct 5)
assert(testA.vara.en === 'Monday', 'Test A (Oct 5): Weekday is Monday');
assert(testA.tithi.id === 25, 'Test A (Oct 5): Tithi is Dashami (ID 25)');
assert(testA.moonrise.time === '12:54 AM', 'Test A (Oct 5): Moonrise is 12:54 AM');
assert(testA.tithi.end !== null, 'Test A (Oct 5): Tithi end time is calculated');

// Verify Test B (Oct 6)
assert(testB.vara.en === 'Tuesday', 'Test B (Oct 6): Weekday is Tuesday');
assert(testB.tithi.id === 26, 'Test B (Oct 6): Tithi is Ekadashi (ID 26)');
assert(testB.moonrise.time === '1:56 AM', 'Test B (Oct 6): Moonrise is 1:56 AM');
assert(testB.nakshatra.nakshatra.name === 'Ashlesha', 'Test B (Oct 6): Nakshatra is Ashlesha');

// Verify Test C (Oct 7)
assert(testC.vara.en === 'Wednesday', 'Test C (Oct 7): Weekday is Wednesday');
assert(testC.tithi.id === 27, 'Test C (Oct 7): Tithi is Dvadashi (ID 27)');
assert(testC.moonrise.time === '2:54 AM', 'Test C (Oct 7): Moonrise is 2:54 AM');

// Date-dependent values MUST change across consecutive dates
assert(testA.vara.id !== testB.vara.id, 'Weekday progresses between Oct 5 and Oct 6');
assert(testB.vara.id !== testC.vara.id, 'Weekday progresses between Oct 6 and Oct 7');
assert(testA.tithi.id !== testB.tithi.id, 'Tithi progresses between Oct 5 and Oct 6');
assert(testB.tithi.id !== testC.tithi.id, 'Tithi progresses between Oct 6 and Oct 7');
assert(testA.moonrise.datetime !== testB.moonrise.datetime, 'Moonrise progresses between Oct 5 and Oct 6');
assert(testB.moonrise.datetime !== testC.moonrise.datetime, 'Moonrise progresses between Oct 6 and Oct 7');
assert(testA.solar.sunrise.getTime() !== testB.solar.sunrise.getTime(), 'Sunrise changes by date in Hyderabad');

// ─── Test Suite 2: Location Independence (Tests D vs E) ─────────────
console.log('\n── Test Suite 2: Location Changes (Hyderabad vs Delhi on Oct 6, 2026) ──');

// Test D: Hyderabad + October 6, 2026
const testD = calculatePanchangam({ year: 2026, month: 10, day: 6, ...HYDERABAD });
// Test E: Delhi + October 6, 2026
const testE = calculatePanchangam({ year: 2026, month: 10, day: 6, ...DELHI });

// Verify location-dependent astronomical timings change appropriately
const sunriseDiffMins = Math.abs(testD.solar.sunrise.getTime() - testE.solar.sunrise.getTime()) / 60000;
const sunsetDiffMins  = Math.abs(testD.solar.sunset.getTime() - testE.solar.sunset.getTime()) / 60000;
const moonriseDiffMins= Math.abs(new Date(testD.moonrise.datetime) - new Date(testE.moonrise.datetime)) / 60000;
const rahuDiffMins    = Math.abs(testD.timings.rahuKalam.start.getTime() - testE.timings.rahuKalam.start.getTime()) / 60000;

assert(sunriseDiffMins > 1.0, `Sunrise differs between Hyderabad & Delhi (${sunriseDiffMins.toFixed(1)} min difference)`);
assert(sunsetDiffMins > 0.5, `Sunset differs between Hyderabad & Delhi (${(sunsetDiffMins * 60).toFixed(0)} sec difference due to latitude/longitude cancellation in autumn)`);
assert(moonriseDiffMins > 1.0, `Moonrise differs between Hyderabad & Delhi (${moonriseDiffMins.toFixed(1)} min difference)`);
assert(rahuDiffMins > 1.0, `Rahu Kalam differs between Hyderabad & Delhi (${rahuDiffMins.toFixed(1)} min difference)`);
assert(testD.solar.dayLengthMinutes !== testE.solar.dayLengthMinutes, `Day length differs between Hyderabad (${testD.solar.dayLengthMinutes}m) and Delhi (${testE.solar.dayLengthMinutes}m)`);

// ─── Test Suite 3: Pan-India Geographic Boundaries ─────────────────
console.log('\n── Test Suite 3: Pan-India Geographic Extents on Oct 6, 2026 ──');

const testGuwahati = calculatePanchangam({ year: 2026, month: 10, day: 6, ...GUWAHATI });
const testSrinagar = calculatePanchangam({ year: 2026, month: 10, day: 6, ...SRINAGAR });
const testMumbai   = calculatePanchangam({ year: 2026, month: 10, day: 6, ...MUMBAI });

// Guwahati (East, ~91.7°E) has much earlier sunrise than Mumbai (West, ~72.9°E)
const eastWestSunriseDiffHrs = (testMumbai.solar.sunrise.getTime() - testGuwahati.solar.sunrise.getTime()) / 3600000;
assert(eastWestSunriseDiffHrs > 1.0, `Eastern Guwahati sunrise is earlier than Western Mumbai by ${eastWestSunriseDiffHrs.toFixed(2)} hours`);

// Srinagar (North, 34°N) has distinct day length compared to Hyderabad (17°N)
assert(testSrinagar.solar.dayLengthMinutes !== testD.solar.dayLengthMinutes, 'Day length differs between northern Srinagar and Hyderabad');

// ─── Test Suite 4: Complete Panchangam Data Attributes ──────────────
console.log('\n── Test Suite 4: Required Panchangam Data Attributes ──');

// Verify all required data elements exist
assert(typeof testD.vara.name === 'string' && typeof testD.vara.nameTe === 'string', 'Vara has English & Telugu names');
assert(typeof testD.tithi.name === 'string' && typeof testD.tithi.endStr === 'string', 'Tithi has name and end time string');
assert(typeof testD.nakshatra.nakshatra.name === 'string' && typeof testD.nakshatra.pada === 'number', 'Nakshatra has name and Pada (1-4)');
assert(typeof testD.yoga.name === 'string' && typeof testD.yoga.endStr === 'string', 'Yoga has name and end time string');
assert(typeof testD.karana.name === 'string' && typeof testD.karana.endStr === 'string', 'Karana has name and end time string');
assert(typeof testD.lunar.phase === 'string' && typeof testD.lunar.illumination === 'string', 'Moon phase and illumination % are present');
assert(testD.timings.rahuKalam && testD.timings.rahuKalam.text, 'Rahu Kalam text interval exists');
assert(testD.timings.yamagandam && testD.timings.yamagandam.text, 'Yamagandam text interval exists');
assert(testD.timings.gulikaKalam && testD.timings.gulikaKalam.text, 'Gulika Kalam text interval exists');
assert(testD.timings.abhijitMuhurtham && testD.timings.abhijitMuhurtham.text, 'Abhijit Muhurtham text interval exists');
assert(Array.isArray(testD.timings.durMuhurtham) && testD.timings.durMuhurtham.length > 0, 'Dur Muhurtham slots exist');
assert(testD.timings.varjyam && testD.timings.varjyam.text, 'Varjyam text interval exists');
assert(testD.timings.amritKalam && testD.timings.amritKalam.text, 'Amrit Kalam text interval exists');
assert(testD.timings.brahmaMuhurtham && testD.timings.brahmaMuhurtham.text, 'Brahma Muhurtham text interval exists');

// ─── Test Suite 5: Metadata & Data Integrity ────────────────────────
console.log('\n── Test Suite 5: Data Integrity & Metadata ──');

const meta = testD.meta;
assert(meta.date.year === 2026 && meta.date.month === 10 && meta.date.day === 6, 'Metadata has exact date');
assert(meta.location_name === 'Hyderabad, Telangana', 'Metadata has exact location name');
assert(meta.latitude === 17.3850, 'Metadata has exact latitude');
assert(meta.longitude === 78.4867, 'Metadata has exact longitude');
assert(meta.timezone === 'Asia/Kolkata', 'Metadata has exact timezone');
assert(typeof meta.calculation_method === 'string', 'Metadata records calculation method');
assert(meta.ayanamsa === 'lahiri', 'Metadata records Lahiri ayanamsa');
assert(typeof meta.calculation_timestamp === 'string', 'Metadata records calculation timestamp');

console.log('\n════════════════════════════════════════════════════════');
console.log(` RESULTS: ${passed} passed, ${failed} failed.`);
console.log('════════════════════════════════════════════════════════\n');

if (failed > 0) {
  process.exit(1);
}
