/**
 * ============================================================
 * DJV Festival Engine v1.0
 * packages/festival-engine/src/festivals.js
 *
 * Rule-based Hindu festival calculation system.
 * Festival dates are computed from Panchangam rules,
 * NOT stored as hard-coded Gregorian dates.
 *
 * Architecture: Each festival is defined as a rule object
 * with a calculator function that returns the festival date(s)
 * for a given year from astronomical/Panchangam data.
 * ============================================================
 */

'use strict';

const { calculatePanchangam, gregorianToJD, jdToGregorian } = require('../../panchangam-engine/src/core');

/**
 * Festival Rule types:
 *
 * type TithiRule = { paksha: 'shukla'|'krishna', tithi: number, masa: string, approxMonth: number }
 * type DateRule  = { month: number, day: number } (for Makar Sankranti etc.)
 * type MoonRule  = { moonPhase: 'full'|'new', afterMonth: number }
 */

/* ─── Hindu Month Names (Amanta system) ─────────────────────── */
const MASA_NAMES = [
  'Chaitra', 'Vaishakha', 'Jyeshtha', 'Ashadha',
  'Shravana', 'Bhadrapada', 'Ashwin', 'Karthika',
  'Margashirsha', 'Pausha', 'Magha', 'Phalguna'
];

/* ─── Festival Rules ─────────────────────────────────────────── */

/**
 * Each festival has:
 * - id: Unique string ID
 * - name: English name
 * - nameTe: Telugu name
 * - description: Short description
 * - type: 'tithi' | 'solar' | 'moon_phase' | 'recurring'
 * - rule: Calculation rule
 * - calculator: Function(year, region) => [{ date, timings, ... }]
 */
const FESTIVAL_RULES = [

  /* ─────────────────────────────────────────────────────────
     UGADI — Telugu New Year
     Shukla Pratipada (Tithi 1) of Chaitra masa
  ───────────────────────────────────────────────────────── */
  {
    id: 'ugadi',
    name: 'Ugadi',
    nameTe: 'ఉగాది',
    description: 'Telugu & Kannada New Year. First day of Chaitra Shukla Paksha.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 1, masa: 'Chaitra' },
    approxGregorianMonth: 3, // March-April
    region: ['telugu', 'kannada', 'all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     MAHA SHIVARATRI
     Krishna Chaturdashi (Tithi 14) of Magha/Phalguna masa
  ───────────────────────────────────────────────────────── */
  {
    id: 'maha-shivaratri',
    name: 'Maha Shivaratri',
    nameTe: 'మహాశివరాత్రి',
    description: 'Great night of Shiva. Krishna Chaturdashi of Phalguna/Magha masa.',
    type: 'tithi',
    rule: { paksha: 'krishna', tithi: 14, masa: 'Magha' },
    approxGregorianMonth: 2,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     HOLI — Phalguna Purnima
     Shukla Purnima of Phalguna masa
  ───────────────────────────────────────────────────────── */
  {
    id: 'holi',
    name: 'Holi',
    nameTe: 'హోలీ',
    description: 'Festival of colors. Phalguna Purnima.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 15, masa: 'Phalguna' },
    approxGregorianMonth: 3,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     MAKAR SANKRANTI — Solar event (Capricorn ingress)
     The day the Sun enters Capricorn (Makar Rashi)
     in the sidereal zodiac (approximately Jan 14 each year,
     but may shift; calculated from Sun position)
  ───────────────────────────────────────────────────────── */
  {
    id: 'makar-sankranti',
    name: 'Makar Sankranti',
    nameTe: 'మకర సంక్రాంతి',
    description: 'Festival marking the Sun entering Capricorn. Harvest festival.',
    type: 'solar_ingress',
    rule: { siderealRashi: 9 }, // Capricorn = 9th Rashi (0-indexed from Aries)
    approxGregorianMonth: 1,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     SRI RAMA NAVAMI
     Shukla Navami of Chaitra masa
  ───────────────────────────────────────────────────────── */
  {
    id: 'rama-navami',
    name: 'Sri Rama Navami',
    nameTe: 'శ్రీ రామ నవమి',
    description: 'Birthday of Lord Rama. Chaitra Shukla Navami.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 9, masa: 'Chaitra' },
    approxGregorianMonth: 4,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     HANUMAN JAYANTI
     Chaitra Purnima (regional variation exists)
  ───────────────────────────────────────────────────────── */
  {
    id: 'hanuman-jayanti',
    name: 'Hanuman Jayanti',
    nameTe: 'హనుమాన్ జయంతి',
    description: 'Birthday of Lord Hanuman. Chaitra Purnima (Telugu tradition).',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 15, masa: 'Chaitra' },
    approxGregorianMonth: 4,
    region: ['telugu', 'all'],
    importance: 'major',
    note: 'Regional variation: some follow Karthika Shukla Chaturdashi'
  },

  /* ─────────────────────────────────────────────────────────
     AKSHAYA TRITIYA
     Vaishakha Shukla Tritiya
  ───────────────────────────────────────────────────────── */
  {
    id: 'akshaya-tritiya',
    name: 'Akshaya Tritiya',
    nameTe: 'అక్షయ తృతీయ',
    description: 'Auspicious day for new beginnings. Vaishakha Shukla Tritiya.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 3, masa: 'Vaishakha' },
    approxGregorianMonth: 5,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     GURU PURNIMA
     Ashadha Purnima
  ───────────────────────────────────────────────────────── */
  {
    id: 'guru-purnima',
    name: 'Guru Purnima',
    nameTe: 'గురు పూర్ణిమ',
    description: 'Full moon of Ashadha. Day to honor one\'s spiritual teacher.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 15, masa: 'Ashadha' },
    approxGregorianMonth: 7,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     KRISHNA JANMASHTAMI
     Bhadrapada Krishna Ashtami at midnight (Rohini Nakshatra)
  ───────────────────────────────────────────────────────── */
  {
    id: 'janmashtami',
    name: 'Krishna Janmashtami',
    nameTe: 'శ్రీ కృష్ణ జన్మాష్టమి',
    description: 'Birthday of Lord Krishna. Bhadrapada Krishna Ashtami.',
    type: 'tithi',
    rule: { paksha: 'krishna', tithi: 8, masa: 'Bhadrapada' },
    approxGregorianMonth: 8,
    region: ['all'],
    importance: 'major',
    note: 'Traditional celebration at midnight when the 8th tithi prevails in Rohini nakshatra'
  },

  /* ─────────────────────────────────────────────────────────
     GANESH CHATURTHI
     Bhadrapada Shukla Chaturthi
  ───────────────────────────────────────────────────────── */
  {
    id: 'ganesh-chaturthi',
    name: 'Ganesh Chaturthi',
    nameTe: 'వినాయక చవితి',
    description: 'Birthday of Lord Ganesha. Bhadrapada Shukla Chaturthi.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 4, masa: 'Bhadrapada' },
    approxGregorianMonth: 8,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     NAVARATRI — Nine nights starting Ashwin Shukla Pratipada
  ───────────────────────────────────────────────────────── */
  {
    id: 'navaratri',
    name: 'Sharada Navaratri',
    nameTe: 'శారదా నవరాత్రి',
    description: 'Nine nights of Goddess Durga. Ashwin Shukla Pratipada to Navami.',
    type: 'tithi_range',
    rule: { paksha: 'shukla', tithiStart: 1, tithiEnd: 9, masa: 'Ashwin' },
    approxGregorianMonth: 10,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     DUSSEHRA / VIJAYADASAMI
     Ashwin Shukla Dashami
  ───────────────────────────────────────────────────────── */
  {
    id: 'dussehra',
    name: 'Dussehra / Vijayadasami',
    nameTe: 'దసరా / విజయదశమి',
    description: 'Victory of Rama over Ravana. Ashwin Shukla Dashami.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 10, masa: 'Ashwin' },
    approxGregorianMonth: 10,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     DIWALI
     Ashwin Krishna Chaturdashi / Amavasya
  ───────────────────────────────────────────────────────── */
  {
    id: 'diwali',
    name: 'Diwali',
    nameTe: 'దీపావళి',
    description: 'Festival of lights. Ashwin Krishna Amavasya (Amanta) / Karthika Amavasya (Purnimanta).',
    type: 'tithi',
    rule: { paksha: 'krishna', tithi: 30, masa: 'Ashwin' },
    approxGregorianMonth: 10,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     KARTHIKA PURNIMA
     Karthika Shukla Purnima
  ───────────────────────────────────────────────────────── */
  {
    id: 'karthika-purnima',
    name: 'Karthika Purnima',
    nameTe: 'కార్తీక పౌర్ణమి',
    description: 'Sacred full moon of Karthika. Lighting of diyas and river bathing.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 15, masa: 'Karthika' },
    approxGregorianMonth: 11,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     VAIKUNTHA EKADASHI
     Margashirsha Shukla Ekadashi
  ───────────────────────────────────────────────────────── */
  {
    id: 'vaikuntha-ekadashi',
    name: 'Vaikuntha Ekadashi',
    nameTe: 'వైకుంఠ ఏకాదశి',
    description: 'Most sacred Ekadashi. Margashirsha Shukla Ekadashi.',
    type: 'tithi',
    rule: { paksha: 'shukla', tithi: 11, masa: 'Margashirsha' },
    approxGregorianMonth: 12,
    region: ['all'],
    importance: 'major',
  },

  /* ─────────────────────────────────────────────────────────
     EKADASHI — Monthly (24 per year: Shukla + Krishna of each masa)
  ───────────────────────────────────────────────────────── */
  {
    id: 'ekadashi-shukla',
    name: 'Ekadashi (Shukla)',
    nameTe: 'శుక్ల ఏకాదశి',
    description: 'Shukla Paksha Ekadashi — fasting day for Vishnu devotees.',
    type: 'recurring_tithi',
    rule: { paksha: 'shukla', tithi: 11 },
    frequency: 'monthly',
    importance: 'regular',
  },
  {
    id: 'ekadashi-krishna',
    name: 'Ekadashi (Krishna)',
    nameTe: 'కృష్ణ ఏకాదశి',
    description: 'Krishna Paksha Ekadashi — fasting day.',
    type: 'recurring_tithi',
    rule: { paksha: 'krishna', tithi: 26 },
    frequency: 'monthly',
    importance: 'regular',
  },

  /* ─────────────────────────────────────────────────────────
     POURNAMI (Full Moon) — Monthly
  ───────────────────────────────────────────────────────── */
  {
    id: 'pournami',
    name: 'Pournami',
    nameTe: 'పౌర్ణమి',
    description: 'Full moon day. Auspicious for puja and spiritual practices.',
    type: 'recurring_tithi',
    rule: { tithi: 15 }, // Tithi 15 = Purnima
    frequency: 'monthly',
    importance: 'regular',
  },

  /* ─────────────────────────────────────────────────────────
     AMAVASYA (New Moon) — Monthly
  ───────────────────────────────────────────────────────── */
  {
    id: 'amavasya',
    name: 'Amavasya',
    nameTe: 'అమావాస్య',
    description: 'New moon day. Important for ancestral rituals (Pitru Tarpana).',
    type: 'recurring_tithi',
    rule: { tithi: 30 }, // Tithi 30 = Amavasya
    frequency: 'monthly',
    importance: 'regular',
  },

  /* ─────────────────────────────────────────────────────────
     SANKASHTI CHATURTHI — Monthly (Krishna Chaturthi)
  ───────────────────────────────────────────────────────── */
  {
    id: 'sankashti-chaturthi',
    name: 'Sankashti Chaturthi',
    nameTe: 'సంకష్టీ చతుర్థి',
    description: 'Monthly Krishna Chaturthi. Fasting for Lord Ganesha.',
    type: 'recurring_tithi',
    rule: { paksha: 'krishna', tithi: 4 },
    frequency: 'monthly',
    importance: 'regular',
  },

];

/* ─── Festival Calculator ────────────────────────────────────── */

/**
 * Find all dates in a year where a specific tithi occurs.
 * Scans day by day. This is computationally intensive for full years;
 * use caching in production.
 *
 * @param {number} year
 * @param {object} rule - { paksha, tithi } or { tithi }
 * @param {object} location
 * @returns {Array<{date: string, panchangam: object}>}
 */
function findTithiDates(year, rule, location = { lat: 17.3850, lon: 78.4867, tzOffset: 5.5 }) {
  const results = [];

  // Scan from Jan 1 to Dec 31
  const startJan = new Date(year, 0, 1);
  const endDec   = new Date(year, 11, 31);

  for (let d = new Date(startJan); d <= endDec; d.setDate(d.getDate() + 1)) {
    const yy = d.getFullYear();
    const mm = d.getMonth() + 1;
    const dd = d.getDate();

    const p = calculatePanchangam({ year: yy, month: mm, day: dd, ...location });

    const matchesTithi  = rule.tithi ? p.tithi.id === rule.tithi : true;
    const matchesPaksha = rule.paksha ? p.tithi.pakshaId === rule.paksha : true;

    if (matchesTithi && matchesPaksha) {
      results.push({
        date:       `${yy}-${String(mm).padStart(2,'0')}-${String(dd).padStart(2,'0')}`,
        tithi:      p.tithi,
        nakshatra:  p.nakshatra,
        vara:       p.vara,
        panchangam: p,
      });
    }
  }

  return results;
}

/**
 * Get all festival dates for a given year.
 * Returns structured festival data with calculated dates.
 *
 * @param {number} year
 * @param {string} region - 'telugu' | 'all'
 * @param {object} location
 * @returns {Array<object>}
 */
function getFestivalsForYear(year, region = 'telugu', location) {
  // NOTE: In production, this result must be CACHED.
  // Computing for all festivals in a year is expensive.
  // Pre-calculate and cache at startup / nightly cron.

  const results = [];

  for (const festival of FESTIVAL_RULES) {
    // Filter by region
    if (festival.region && !festival.region.includes('all') && !festival.region.includes(region)) {
      continue;
    }

    if (festival.type === 'tithi' || festival.type === 'recurring_tithi') {
      const dates = findTithiDates(year, festival.rule, location);

      dates.forEach(d => {
        results.push({
          festival_id:  festival.id,
          name:         festival.name,
          nameTe:       festival.nameTe,
          description:  festival.description,
          importance:   festival.importance,
          date:         d.date,
          tithi:        d.tithi,
          nakshatra:    d.nakshatra,
          vara:         d.vara,
          note:         festival.note || null,
          rule:         festival.rule,
          calculatedAt: new Date().toISOString(),
          engineVersion: '1.0.0',
          ruleVersion:   'telugu-1.0',
        });
      });
    }
    // NOTE: Solar ingress (Makar Sankranti) and other types would be
    // implemented here with their specific calculators.
    // Marked as TODO for Phase 2 completion.
  }

  // Sort by date
  results.sort((a, b) => a.date.localeCompare(b.date));

  return results;
}

/**
 * Get festival data for a specific festival by ID.
 * @param {string} id
 * @param {number} year
 * @returns {object|null}
 */
function getFestivalById(id, year = new Date().getFullYear()) {
  const rule = FESTIVAL_RULES.find(f => f.id === id);
  if (!rule) return null;
  return { ...rule, ruleVersion: 'telugu-1.0' };
}

/**
 * Get upcoming festivals from today.
 * @param {number} daysAhead - Number of days to look ahead
 * @param {object} location
 * @returns {Array}
 */
function getUpcomingFestivals(daysAhead = 30, location) {
  const today = new Date();
  const endDate = new Date();
  endDate.setDate(today.getDate() + daysAhead);

  const year1 = today.getFullYear();
  const year2 = endDate.getFullYear();

  let allFestivals = getFestivalsForYear(year1, 'all', location);
  if (year2 !== year1) {
    allFestivals = allFestivals.concat(getFestivalsForYear(year2, 'all', location));
  }

  const todayStr = today.toISOString().split('T')[0];
  const endStr   = endDate.toISOString().split('T')[0];

  return allFestivals.filter(f => f.date >= todayStr && f.date <= endStr);
}

/* ─── Exports ───────────────────────────────────────────────── */
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    FESTIVAL_RULES,
    MASA_NAMES,
    findTithiDates,
    getFestivalsForYear,
    getFestivalById,
    getUpcomingFestivals,
  };
}
