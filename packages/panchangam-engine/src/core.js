/**
 * ============================================================
 * DHARMA JYOTHI VEDIKA — Panchangam Engine v1.0
 * packages/panchangam-engine/src/astronomy/core.js
 *
 * ASTRONOMICAL CALCULATIONS — Jean Meeus Algorithm Implementation
 *
 * Based on: Jean Meeus, "Astronomical Algorithms" (2nd Edition)
 * Willmann-Bell, Inc., 1998. ISBN 978-0-943396-61-3
 *
 * The mathematical algorithms from Meeus are applied to derive
 * planetary positions, sunrise/sunset, and related astronomical events.
 *
 * All code is original work by the DJV Engineering Team.
 * Algorithms are mathematical formulas — not copyrightable.
 *
 * License: See LICENSE-CHECK.md
 * ============================================================
 */

'use strict';

/* ─── Constants ─────────────────────────────────────────────── */
const DEG2RAD = Math.PI / 180;
const RAD2DEG = 180 / Math.PI;
const J2000   = 2451545.0;          // Julian Day for J2000.0 epoch
const JULIAN_CENTURY = 36525.0;     // Days per Julian century

/**
 * Convert a Gregorian calendar date (UTC) to a Julian Day Number.
 * Meeus, Chapter 7, p.61
 *
 * @param {number} year  - Full year (e.g., 2026)
 * @param {number} month - Month (1–12)
 * @param {number} day   - Day with fractional part for time
 * @returns {number} Julian Day Number
 */
function gregorianToJD(year, month, day) {
  if (month <= 2) { year -= 1; month += 12; }
  const A = Math.floor(year / 100);
  const B = 2 - A + Math.floor(A / 4);
  return Math.floor(365.25 * (year + 4716)) +
         Math.floor(30.6001 * (month + 1)) +
         day + B - 1524.5;
}

/**
 * Convert a Julian Day Number to a Gregorian calendar date (UTC).
 * Meeus, Chapter 7, p.63
 *
 * @param {number} jd - Julian Day Number
 * @returns {{ year: number, month: number, day: number, hour: number, minute: number, second: number }}
 */
function jdToGregorian(jd) {
  const jd0 = jd + 0.5;
  const Z   = Math.floor(jd0);
  const F   = jd0 - Z;
  let A;
  if (Z < 2299161) {
    A = Z;
  } else {
    const alpha = Math.floor((Z - 1867216.25) / 36524.25);
    A = Z + 1 + alpha - Math.floor(alpha / 4);
  }
  const B  = A + 1524;
  const C  = Math.floor((B - 122.1) / 365.25);
  const D  = Math.floor(365.25 * C);
  const E  = Math.floor((B - D) / 30.6001);

  const day   = B - D - Math.floor(30.6001 * E);
  const month = E < 14 ? E - 1 : E - 13;
  const year  = month > 2 ? C - 4716 : C - 4715;

  const fracDay     = F * 24;
  const hour        = Math.floor(fracDay);
  const fracHour    = (fracDay - hour) * 60;
  const minute      = Math.floor(fracHour);
  const second      = Math.round((fracHour - minute) * 60);

  return { year, month, day, hour, minute, second };
}

/**
 * Compute T = Julian centuries since J2000.0 from a JD.
 * @param {number} jd
 * @returns {number} T
 */
function jdToT(jd) {
  return (jd - J2000) / JULIAN_CENTURY;
}

/**
 * Normalize an angle to [0, 360) degrees.
 * @param {number} deg
 * @returns {number}
 */
function normalizeDeg(deg) {
  deg = deg % 360;
  return deg < 0 ? deg + 360 : deg;
}

/**
 * Normalize an angle to [-180, 180) degrees.
 */
function normalizeDeg180(deg) {
  deg = normalizeDeg(deg);
  return deg > 180 ? deg - 360 : deg;
}

/* ─── Helpers ─────────────────────────────────────────────────── */

/**
 * Calculate timezone offset in hours for a specific date in an IANA timezone.
 */
function getTzOffsetHours(timezone, year, month, day) {
  const dt = new Date(Date.UTC(year, month - 1, day, 12, 0, 0));
  const utcDate = new Date(dt.toLocaleString('en-US', { timeZone: 'UTC' }));
  const tzDate = new Date(dt.toLocaleString('en-US', { timeZone: timezone }));
  return (tzDate.getTime() - utcDate.getTime()) / 3600000;
}

/* ─── Sun Position ──────────────────────────────────────────── */

/**
 * Calculate the Sun's apparent ecliptic longitude (degrees).
 * Meeus, Chapter 25 (Low accuracy, sufficient for Panchangam).
 *
 * @param {number} T - Julian centuries since J2000.0
 * @returns {{ longitude: number, anomaly: number }}
 */
function sunEclipticLongitude(T) {
  // Geometric mean longitude of the Sun (degrees)
  const L0 = normalizeDeg(280.46646 + 36000.76983 * T + 0.0003032 * T * T);

  // Mean anomaly of the Sun (degrees)
  const M = normalizeDeg(357.52911 + 35999.05029 * T - 0.0001537 * T * T);
  const Mrad = M * DEG2RAD;

  // Equation of center
  const C =
    (1.914602 - 0.004817 * T - 0.000014 * T * T) * Math.sin(Mrad) +
    (0.019993 - 0.000101 * T) * Math.sin(2 * Mrad) +
    0.000289 * Math.sin(3 * Mrad);

  // Sun's true longitude
  const sunLon = L0 + C;

  // Apparent longitude (correction for nutation and aberration)
  const omega   = 125.04 - 1934.136 * T;
  const lambdaApp = sunLon - 0.00569 - 0.00478 * Math.sin(omega * DEG2RAD);

  return { longitude: normalizeDeg(lambdaApp), anomaly: M };
}

/* ─── Moon Position ─────────────────────────────────────────── */

/**
 * Calculate the Moon's apparent ecliptic longitude (degrees).
 * Meeus, Chapter 47 (Simplified). Returns tropical longitude.
 *
 * @param {number} T - Julian centuries since J2000.0
 * @returns {{ longitude: number, latitude: number }}
 */
function moonEclipticLongitude(T) {
  const T2 = T * T;
  const T3 = T2 * T;
  const T4 = T3 * T;

  // Moon's mean longitude
  const Lp = normalizeDeg(
    218.3164477 + 481267.88123421 * T - 0.0015786 * T2 +
    T3 / 538841 - T4 / 65194000
  );

  // Moon's mean elongation
  const D = normalizeDeg(
    297.8501921 + 445267.1114034 * T - 0.0018819 * T2 +
    T3 / 545868 - T4 / 113065000
  );

  // Sun's mean anomaly
  const M = normalizeDeg(
    357.5291092 + 35999.0502909 * T - 0.0001536 * T2 + T3 / 24490000
  );

  // Moon's mean anomaly
  const Mp = normalizeDeg(
    134.9633964 + 477198.8675055 * T + 0.0087414 * T2 +
    T3 / 69699 - T4 / 14712000
  );

  // Moon's argument of latitude
  const F = normalizeDeg(
    93.2720950 + 483202.0175233 * T - 0.0036539 * T2 -
    T3 / 3526000 + T4 / 863310000
  );

  // Additional arguments
  const A1 = normalizeDeg(119.75 + 131.849 * T);
  const A2 = normalizeDeg(53.09 + 479264.290 * T);
  const A3 = normalizeDeg(313.45 + 481266.484 * T);
  const E  = 1 - 0.002516 * T - 0.0000074 * T2;

  // Convert to radians
  const Dr   = D  * DEG2RAD;
  const Mr   = M  * DEG2RAD;
  const Mpr  = Mp * DEG2RAD;
  const Fr   = F  * DEG2RAD;
  const A1r  = A1 * DEG2RAD;
  const A2r  = A2 * DEG2RAD;
  const A3r  = A3 * DEG2RAD;

  // Longitude perturbation terms (Meeus Table 47.A — top terms only)
  let SumL =
    6288774 * Math.sin(Mpr) +
    1274027 * Math.sin(2*Dr - Mpr) +
     658314 * Math.sin(2*Dr) +
     213618 * Math.sin(2*Mpr) -
     185116 * E * Math.sin(Mr) -
     114332 * Math.sin(2*Fr) +
      58793 * Math.sin(2*Dr - 2*Mpr) +
      57066 * E * Math.sin(2*Dr - Mr - Mpr) +
      53322 * Math.sin(2*Dr + Mpr) +
      45758 * E * Math.sin(2*Dr - Mr) -
      40923 * E * Math.sin(Mr - Mpr) -
      34720 * Math.sin(Dr) -
      30383 * E * Math.sin(Mr + Mpr) +
      15327 * Math.sin(2*Dr - 2*Fr) -
      12528 * Math.sin(Mpr + 2*Fr) +
      10980 * Math.sin(Mpr - 2*Fr) +
      10675 * Math.sin(4*Dr - Mpr) +
      10034 * Math.sin(3*Mpr) +
       8548 * Math.sin(4*Dr - 2*Mpr) -
       7888 * E * Math.sin(2*Dr + Mr - Mpr) -
       6766 * E * Math.sin(2*Dr + Mr) -
       5163 * Math.sin(Dr - Mpr);

  // Additional terms for latitude
  let SumB =
    5128122 * Math.sin(Fr) +
     280602 * Math.sin(Mpr + Fr) +
     277693 * Math.sin(Mpr - Fr) +
     173237 * Math.sin(2*Dr - Fr) +
      55413 * Math.sin(2*Dr - Mpr + Fr) +
      46271 * Math.sin(2*Dr - Mpr - Fr) +
      32573 * Math.sin(2*Dr + Fr) +
      17198 * Math.sin(2*Mpr + Fr) +
       9266 * Math.sin(2*Dr + Mpr - Fr) +
       8822 * Math.sin(2*Mpr - Fr) -
       8216 * E * Math.sin(2*Dr - Mr - Fr) -
       4324 * Math.sin(2*Dr - 2*Mpr - Fr) -
       4200 * Math.sin(2*Dr + Mpr + Fr);

  // Additive terms
  SumL += 3958 * Math.sin(A1r) + 1962 * Math.sin(Lp * DEG2RAD - Fr) + 318 * Math.sin(A2r);
  SumB += -2235 * Math.sin(Lp * DEG2RAD) + 382 * Math.sin(A3r) + 175 * Math.sin(A1r - Fr) +
           175 * Math.sin(A1r + Fr) + 127 * Math.sin(Lp * DEG2RAD - Mpr) - 115 * Math.sin(Lp * DEG2RAD + Mpr);

  const longitude = normalizeDeg(Lp + SumL / 1000000);
  const latitude  = SumB / 1000000;

  return { longitude, latitude };
}

/* ─── Ayanamsa ──────────────────────────────────────────────── */

/**
 * Calculate Lahiri (Chitrapaksha) Ayanamsa for a given JD.
 * Formula based on the standard Lahiri Ayanamsa definition.
 * Reference: Indian Astronomical Ephemeris, Rashtriya Panchang.
 *
 * @param {number} jd
 * @returns {number} Ayanamsa in degrees
 */
function lahiriAyanamsa(jd) {
  const T = jdToT(jd);
  // Standard Lahiri ayanamsa formula
  // Reference position: Ayanamsa = 23°15' on 1956-01-01
  const base = 23.25 + (jd - 2435553.5) * (50.2388475 / 3600) / 365.25;
  return base;
}

/**
 * Get sidereal position from tropical longitude using the selected Ayanamsa.
 *
 * @param {number} tropicalLongitude - Tropical ecliptic longitude (degrees)
 * @param {number} jd                - Julian Day
 * @param {string} ayanamsa          - Ayanamsa system: 'lahiri' | 'raman' | 'kp'
 * @returns {number} Sidereal longitude (degrees)
 */
function toSidereal(tropicalLongitude, jd, ayanamsa = 'lahiri') {
  let ayVal;
  switch (ayanamsa) {
    case 'lahiri':
    default:
      ayVal = lahiriAyanamsa(jd);
      break;
  }
  return normalizeDeg(tropicalLongitude - ayVal);
}

/* ─── Tithi Calculation ─────────────────────────────────────── */

const TITHI_NAMES = [
  'Pratipada', 'Dvitiya', 'Tritiya', 'Chaturthi', 'Panchami',
  'Shashthi', 'Saptami', 'Ashtami', 'Navami', 'Dashami',
  'Ekadashi', 'Dvadashi', 'Trayodashi', 'Chaturdashi', 'Purnima / Amavasya'
];

const TITHI_NAMES_TE = [
  'పాడ్యమి', 'విదియ', 'తదియ', 'చవితి', 'పంచమి',
  'షష్ఠి', 'సప్తమి', 'అష్టమి', 'నవమి', 'దశమి',
  'ఏకాదశి', 'ద్వాదశి', 'త్రయోదశి', 'చతుర్దశి', 'పౌర్ణమి / అమావాస్య'
];

/**
 * Calculate Tithi from the Moon–Sun longitude difference.
 * One Tithi = 12° of separation.
 * Tithi ID: 1–15 (Shukla Paksha), 16–30 (Krishna Paksha)
 * Tithi 15 = Purnima, Tithi 30 = Amavasya
 *
 * @param {number} moonLon - Sidereal Moon longitude (degrees)
 * @param {number} sunLon  - Sidereal Sun longitude (degrees)
 * @returns {{ id: number, name: string, nameTe: string, paksha: string, pakshaId: string, degreeStart: number }}
 */
function calcTithi(moonLon, sunLon) {
  const diff = normalizeDeg(moonLon - sunLon);
  const tithiId = Math.floor(diff / 12) + 1; // 1–30

  const isShukla = tithiId <= 15;
  const paksha   = isShukla ? 'Shukla' : 'Krishna';

  let nameIndex = (tithiId - 1) % 15;
  let displayId = tithiId <= 15 ? tithiId : tithiId - 15;

  return {
    id:          tithiId,           // 1–30
    displayId,                       // 1–15 within each paksha
    name:        TITHI_NAMES[nameIndex],
    nameTe:      TITHI_NAMES_TE[nameIndex],
    paksha,
    pakshaId:    isShukla ? 'shukla' : 'krishna',
    degreeStart: (tithiId - 1) * 12,
    degreeEnd:   tithiId * 12,
    currentAngle: diff
  };
}

/* ─── Nakshatra Calculation ─────────────────────────────────── */

const NAKSHATRA_DATA = [
  { id:  1, name: 'Ashwini',         nameTe: 'అశ్విని',        deity: 'Ashwins',      ruler: 'Ketu'    },
  { id:  2, name: 'Bharani',         nameTe: 'భరణి',           deity: 'Yama',         ruler: 'Venus'   },
  { id:  3, name: 'Krittika',        nameTe: 'కృత్తిక',        deity: 'Agni',         ruler: 'Sun'     },
  { id:  4, name: 'Rohini',          nameTe: 'రోహిణి',         deity: 'Brahma',       ruler: 'Moon'    },
  { id:  5, name: 'Mrigashira',      nameTe: 'మృగశిర',         deity: 'Soma',         ruler: 'Mars'    },
  { id:  6, name: 'Ardra',           nameTe: 'ఆర్ద్ర',         deity: 'Rudra',        ruler: 'Rahu'    },
  { id:  7, name: 'Punarvasu',       nameTe: 'పునర్వసు',       deity: 'Aditi',        ruler: 'Jupiter' },
  { id:  8, name: 'Pushya',          nameTe: 'పుష్య',          deity: 'Brihaspati',   ruler: 'Saturn'  },
  { id:  9, name: 'Ashlesha',        nameTe: 'ఆశ్లేష',         deity: 'Sarpa',        ruler: 'Mercury' },
  { id: 10, name: 'Magha',           nameTe: 'మఘ',             deity: 'Pitri',        ruler: 'Ketu'    },
  { id: 11, name: 'Purva Phalguni',  nameTe: 'పూర్వ ఫల్గుణి', deity: 'Bhaga',        ruler: 'Venus'   },
  { id: 12, name: 'Uttara Phalguni', nameTe: 'ఉత్తర ఫల్గుణి', deity: 'Aryaman',      ruler: 'Sun'     },
  { id: 13, name: 'Hasta',           nameTe: 'హస్త',           deity: 'Savitar',      ruler: 'Moon'    },
  { id: 14, name: 'Chitra',          nameTe: 'చిత్ర',          deity: 'Tvashtar',     ruler: 'Mars'    },
  { id: 15, name: 'Swati',           nameTe: 'స్వాతి',         deity: 'Vayu',         ruler: 'Rahu'    },
  { id: 16, name: 'Vishakha',        nameTe: 'విశాఖ',          deity: 'Indra-Agni',   ruler: 'Jupiter' },
  { id: 17, name: 'Anuradha',        nameTe: 'అనురాధ',         deity: 'Mitra',        ruler: 'Saturn'  },
  { id: 18, name: 'Jyeshtha',        nameTe: 'జ్యేష్ఠ',       deity: 'Indra',        ruler: 'Mercury' },
  { id: 19, name: 'Mula',            nameTe: 'మూల',             deity: 'Nirriti',      ruler: 'Ketu'    },
  { id: 20, name: 'Purva Ashadha',   nameTe: 'పూర్వాషాఢ',     deity: 'Apas',         ruler: 'Venus'   },
  { id: 21, name: 'Uttara Ashadha',  nameTe: 'ఉత్తరాషాఢ',     deity: 'Vishwadevas',  ruler: 'Sun'     },
  { id: 22, name: 'Shravana',        nameTe: 'శ్రవణ',          deity: 'Vishnu',       ruler: 'Moon'    },
  { id: 23, name: 'Dhanishtha',      nameTe: 'ధనిష్ఠ',        deity: 'Vasus',        ruler: 'Mars'    },
  { id: 24, name: 'Shatabhisha',     nameTe: 'శతభిష',          deity: 'Varuna',       ruler: 'Rahu'    },
  { id: 25, name: 'Purva Bhadra',    nameTe: 'పూర్వ భాద్ర',   deity: 'Ajaikapada',   ruler: 'Jupiter' },
  { id: 26, name: 'Uttara Bhadra',   nameTe: 'ఉత్తర భాద్ర',   deity: 'Ahirbudhnya',  ruler: 'Saturn'  },
  { id: 27, name: 'Revati',          nameTe: 'రేవతి',          deity: 'Pushan',       ruler: 'Mercury' }
];

/**
 * Calculate Nakshatra from sidereal Moon longitude.
 * Each Nakshatra = 13°20' (360/27 degrees).
 *
 * @param {number} moonSiderealLon - Sidereal Moon longitude (degrees)
 * @returns {{ nakshatra: object, pada: number, degreeInNakshatra: number }}
 */
function calcNakshatra(moonSiderealLon) {
  const NAK_SIZE = 360 / 27;          // 13.3333...°
  const PADA_SIZE = NAK_SIZE / 4;     // 3.3333...°

  const index           = Math.floor(moonSiderealLon / NAK_SIZE);  // 0–26
  const degreeInNakshatra = moonSiderealLon - (index * NAK_SIZE);
  const pada            = Math.floor(degreeInNakshatra / PADA_SIZE) + 1; // 1–4

  return {
    nakshatra: NAKSHATRA_DATA[index % 27],
    pada,
    degreeInNakshatra,
    degreeStart: index * NAK_SIZE,
    degreeEnd:   (index + 1) * NAK_SIZE
  };
}

/* ─── Yoga Calculation ──────────────────────────────────────── */

const YOGA_NAMES = [
  'Vishkambha', 'Priti', 'Ayushman', 'Saubhagya', 'Shobhana',
  'Atiganda',   'Sukarma', 'Dhriti', 'Shula', 'Ganda',
  'Vriddhi',    'Dhruva', 'Vyaghata', 'Harshana', 'Vajra',
  'Siddhi',     'Vyatipata', 'Variyan', 'Parigha', 'Shiva',
  'Siddha',     'Sadhya', 'Shubha', 'Shukla', 'Brahma',
  'Indra',      'Vaidhriti'
];

const YOGA_NAMES_TE = [
  'విష్కంభ', 'ప్రీతి', 'ఆయుష్మాన్', 'సౌభాగ్య', 'శోభన',
  'అతిగండ', 'సుకర్మ', 'ధృతి', 'శూల', 'గండ',
  'వృద్ధి', 'ధ్రువ', 'వ్యాఘాత', 'హర్షణ', 'వజ్ర',
  'సిద్ధి', 'వ్యతీపాత', 'వరీయాన్', 'పరిఘ', 'శివ',
  'సిద్ధ', 'సాధ్య', 'శుభ', 'శుక్ల', 'బ్రహ్మ',
  'ఇంద్ర', 'వైధృతి'
];

/**
 * Calculate Yoga from (Sun + Moon) sidereal longitude.
 * One Yoga = 13°20' = 800' of combined longitude.
 *
 * @param {number} sunSiderealLon  - Sidereal Sun longitude (degrees)
 * @param {number} moonSiderealLon - Sidereal Moon longitude (degrees)
 * @returns {{ id: number, name: string, nameTe: string }}
 */
function calcYoga(sunSiderealLon, moonSiderealLon) {
  const YOGA_SIZE = 360 / 27;
  const combined  = normalizeDeg(sunSiderealLon + moonSiderealLon);
  const index     = Math.floor(combined / YOGA_SIZE) % 27;

  return {
    id:     index + 1,
    name:   YOGA_NAMES[index],
    nameTe: YOGA_NAMES_TE[index],
    degreeStart: index * YOGA_SIZE,
    degreeEnd:   (index + 1) * YOGA_SIZE,
    currentAngle: combined
  };
}

/* ─── Karana Calculation ────────────────────────────────────── */

const KARANA_FIXED = [
  { name: 'Kimstughna', nameTe: 'కింస్తుఘ్న' },
  { name: 'Shakuni',    nameTe: 'శకుని' },
  { name: 'Chatushpada',nameTe: 'చతుష్పద' },
  { name: 'Naga',       nameTe: 'నాగ' }
];

const KARANA_MOVABLE = [
  { name: 'Bava',      nameTe: 'బవ' },
  { name: 'Balava',    nameTe: 'బాలవ' },
  { name: 'Kaulava',   nameTe: 'కౌలవ' },
  { name: 'Taitila',   nameTe: 'తైతిల' },
  { name: 'Gara',      nameTe: 'గర' },
  { name: 'Vanija',    nameTe: 'వణిజ' },
  { name: 'Vishti',    nameTe: 'విష్టి' }
];

/**
 * Calculate Karana (half-Tithi). There are 11 Karanas:
 * 4 fixed and 7 movable repeating.
 *
 * @param {number} tithiId    - Tithi ID (1–30)
 * @param {number} halfTithi  - 0 = first half, 1 = second half of the Tithi
 * @returns {{ name: string, nameTe: string, isFixed: boolean }}
 */
function calcKarana(tithiId, moonLon, sunLon) {
  const diff = normalizeDeg(moonLon - sunLon);
  const karanaIndex = Math.floor(diff / 6); // 0–59 (60 half-Tithis total)

  // First Karana (Kimstughna) is fixed, occurring once: the first half of Shukla Pratipada
  if (karanaIndex === 0) return { ...KARANA_FIXED[0], isFixed: true, index: 0 };

  // Last 3 Karanas are fixed (at specific positions of the 60 karanas)
  if (karanaIndex === 57) return { ...KARANA_FIXED[1], isFixed: true, index: 57 };
  if (karanaIndex === 58) return { ...KARANA_FIXED[2], isFixed: true, index: 58 };
  if (karanaIndex === 59) return { ...KARANA_FIXED[3], isFixed: true, index: 59 };

  // Movable Karanas repeat cyclically from index 1 to 56
  const movableIndex = (karanaIndex - 1) % 7;
  return { ...KARANA_MOVABLE[movableIndex], isFixed: false, index: karanaIndex };
}

/* ─── Vara (Weekday) ────────────────────────────────────────── */

const VARA_DATA = [
  { id: 0, name: 'Ravivara',   nameTe: 'ఆదివారం',   en: 'Sunday',    ruler: 'Sun'    },
  { id: 1, name: 'Somavara',   nameTe: 'సోమవారం',   en: 'Monday',    ruler: 'Moon'   },
  { id: 2, name: 'Mangalavara',nameTe: 'మంగళవారం',  en: 'Tuesday',   ruler: 'Mars'   },
  { id: 3, name: 'Budhavara',  nameTe: 'బుధవారం',   en: 'Wednesday', ruler: 'Mercury'},
  { id: 4, name: 'Guruvara',   nameTe: 'గురువారం',  en: 'Thursday',  ruler: 'Jupiter'},
  { id: 5, name: 'Shukravara', nameTe: 'శుక్రవారం', en: 'Friday',    ruler: 'Venus'  },
  { id: 6, name: 'Shanivara',  nameTe: 'శనివారం',   en: 'Saturday',  ruler: 'Saturn' }
];

/**
 * Get Vara (weekday) from Julian Day.
 * @param {number} jd - Julian Day (for local midnight)
 * @returns {object} Vara data
 */
function calcVara(jd) {
  // JD 0 was a Monday. Day of week from JD:
  const dayOfWeek = ((Math.floor(jd + 1.5)) % 7 + 7) % 7;
  return VARA_DATA[dayOfWeek];
}

/* ─── Rahu Kalam ────────────────────────────────────────────── */

/**
 * Rahu Kalam order by weekday (index from Sunday=0).
 * Convention: Rahu occupies 1 of 8 equal daytime segments.
 * Order: Sun=8, Mon=2, Tue=7, Wed=5, Thu=6, Fri=4, Sat=3
 * (1-indexed segment number from sunrise)
 */
const RAHU_KALAM_SEGMENT = [8, 2, 7, 5, 6, 4, 3]; // [Sun, Mon, Tue, Wed, Thu, Fri, Sat]

/**
 * Calculate Rahu Kalam times.
 *
 * @param {Date} sunrise   - Local sunrise time
 * @param {Date} sunset    - Local sunset time
 * @param {number} weekday - 0=Sunday, 6=Saturday
 * @returns {{ start: Date, end: Date }}
 */
function calcRahuKalam(sunrise, sunset, weekday) {
  const totalMs  = sunset.getTime() - sunrise.getTime();
  const segmentMs = totalMs / 8;
  const segIndex  = RAHU_KALAM_SEGMENT[weekday] - 1; // 0-indexed

  const start = new Date(sunrise.getTime() + segIndex * segmentMs);
  const end   = new Date(start.getTime() + segmentMs);

  return { start, end };
}

/* ─── Yamagandam ─────────────────────────────────────────────── */

/**
 * Yamagandam order by weekday (segment from sunrise).
 * Sun=5, Mon=4, Tue=3, Wed=2, Thu=1, Fri=7, Sat=6
 */
const YAMAGANDAM_SEGMENT = [5, 4, 3, 2, 1, 7, 6];

function calcYamagandam(sunrise, sunset, weekday) {
  const totalMs   = sunset.getTime() - sunrise.getTime();
  const segmentMs = totalMs / 8;
  const segIndex  = YAMAGANDAM_SEGMENT[weekday] - 1;

  const start = new Date(sunrise.getTime() + segIndex * segmentMs);
  const end   = new Date(start.getTime() + segmentMs);

  return { start, end };
}

/* ─── Gulika Kalam ───────────────────────────────────────────── */

/**
 * Gulika Kalam order by weekday (segment from sunrise).
 * Sun=7, Mon=6, Tue=5, Wed=4, Thu=3, Fri=2, Sat=1
 */
const GULIKA_KALAM_SEGMENT = [7, 6, 5, 4, 3, 2, 1];

function calcGulikaKalam(sunrise, sunset, weekday) {
  const totalMs   = sunset.getTime() - sunrise.getTime();
  const segmentMs = totalMs / 8;
  const segIndex  = GULIKA_KALAM_SEGMENT[weekday] - 1;

  const start = new Date(sunrise.getTime() + segIndex * segmentMs);
  const end   = new Date(start.getTime() + segmentMs);

  return { start, end };
}

/* ─── Abhijit Muhurtham ─────────────────────────────────────── */

/**
 * Abhijit Muhurtham is the 8th muhurtham of the day (midday).
 * Duration: 48 minutes centered at solar noon.
 * Solar noon = midpoint between sunrise and sunset.
 *
 * @param {Date} sunrise
 * @param {Date} sunset
 * @returns {{ start: Date, end: Date, solarNoon: Date }}
 */
function calcAbhijitMuhurtham(sunrise, sunset) {
  const noonMs = (sunrise.getTime() + sunset.getTime()) / 2;
  const solarNoon = new Date(noonMs);
  const halfDuration = 24 * 60 * 1000; // 24 minutes in ms

  return {
    start:     new Date(noonMs - halfDuration),
    end:       new Date(noonMs + halfDuration),
    solarNoon
  };
}

/* ─── Dur Muhurtham ─────────────────────────────────────────── */

/**
 * Calculate Dur Muhurtham periods for the day.
 * Daytime is divided into 15 equal Muhurthas (~48 minutes each).
 * Convention based on classical South Indian / Telugu tradition:
 * Sun: 14th; Mon: 8th & 12th; Tue: 4th & 11th; Wed: 5th;
 * Thu: 6th & 7th; Fri: 4th & 9th; Sat: 2nd & 3rd.
 *
 * @param {Date} sunrise
 * @param {Date} sunset
 * @param {number} weekday - 0=Sunday, 6=Saturday
 * @returns {Array<{ start: Date, end: Date, slot: number }>}
 */
function calcDurMuhurtham(sunrise, sunset, weekday) {
  const dayMs = sunset.getTime() - sunrise.getTime();
  const mLen = dayMs / 15;

  const slotsMap = [
    [14],        // Sun
    [8, 12],     // Mon
    [4, 11],     // Tue
    [5],         // Wed
    [6, 7],      // Thu
    [4, 9],      // Fri
    [2, 3]       // Sat
  ];

  const slots = slotsMap[weekday] || [14];
  return slots.map(slot => ({
    start: new Date(sunrise.getTime() + (slot - 1) * mLen),
    end:   new Date(sunrise.getTime() + slot * mLen),
    slot
  }));
}

/* ─── Brahma Muhurtham ──────────────────────────────────────── */

/**
 * Brahma Muhurtham starts 2 muhurthas (96 min) before sunrise and ends 1 muhurtha (48 min) before sunrise.
 * @param {Date} sunrise
 * @returns {{ start: Date, end: Date }}
 */
function calcBrahmaMuhurtham(sunrise) {
  return {
    start: new Date(sunrise.getTime() - 96 * 60 * 1000),
    end:   new Date(sunrise.getTime() - 48 * 60 * 1000)
  };
}

/* ─── Varjyam & Amrit Kalam ─────────────────────────────────── */

const VARJYAM_START_GHATIS = [
  50, 24, 30, 40, 14, 21, 30, 20, 32, // 1–9: Ashwini to Ashlesha
  30, 20, 18, 21, 20, 14, 14, 10, 14, // 10–18: Magha to Jyeshtha
  56, 24, 20, 10, 10, 18, 16, 24, 30  // 19–27: Mula to Revati
];

/**
 * Calculate Varjyam (Tyajyam) and Amrit Kalam based on Nakshatra.
 * Varjyam starts at the designated ghati from Nakshatra start and lasts 4 ghatis (96 min).
 * Amrit Kalam starts 14 ghatis after Varjyam start and lasts 4 ghatis (96 min).
 *
 * @param {number} nakshatraId - 1–27
 * @param {Date|null} nakshatraStart
 * @param {Date|null} nakshatraEnd
 * @returns {{ varjyam: { start: Date, end: Date }, amritKalam: { start: Date, end: Date } }}
 */
function calcVarjyamAndAmritKalam(nakshatraId, nakshatraStart, nakshatraEnd) {
  const ghatiIndex = Math.max(0, Math.min(26, (nakshatraId - 1) % 27));
  const startGhati = VARJYAM_START_GHATIS[ghatiIndex];

  let nStartMs, ghatiMs;
  if (nakshatraStart && nakshatraEnd) {
    nStartMs = nakshatraStart.getTime();
    const spanMs = Math.max(18 * 3600000, Math.min(30 * 3600000, nakshatraEnd.getTime() - nStartMs));
    ghatiMs = spanMs / 60;
  } else if (nakshatraEnd) {
    nStartMs = nakshatraEnd.getTime() - 24 * 3600000;
    ghatiMs = 24 * 60 * 1000;
  } else {
    nStartMs = Date.now();
    ghatiMs = 24 * 60 * 1000;
  }

  const varjyamStart = new Date(nStartMs + startGhati * ghatiMs);
  const varjyamEnd   = new Date(varjyamStart.getTime() + 4 * ghatiMs);

  const amritStart   = new Date(varjyamStart.getTime() + 14 * ghatiMs);
  const amritEnd     = new Date(amritStart.getTime() + 4 * ghatiMs);

  return {
    varjyam:    { start: varjyamStart, end: varjyamEnd },
    amritKalam: { start: amritStart,   end: amritEnd }
  };
}

/* ─── Moon Phase & Illumination ─────────────────────────────── */

/**
 * Calculate Moon Phase, elongation, and illumination fraction.
 *
 * @param {number} jd - Julian Day
 * @returns {{ phaseName: string, phaseNameTe: string, illumination: number, illuminationPercent: string, elongation: number }}
 */
function calcMoonPhaseAndIllumination(jd) {
  const T = jdToT(jd);
  const sunLon  = sunEclipticLongitude(T).longitude;
  const moonLon = moonEclipticLongitude(T).longitude;
  const elongation = normalizeDeg(moonLon - sunLon);

  const k = (1 - Math.cos(elongation * DEG2RAD)) / 2;
  const illuminationPercent = (k * 100).toFixed(1) + '%';

  let phaseName = 'New Moon';
  let phaseNameTe = 'అమావాస్య';

  if (elongation >= 22.5 && elongation < 67.5) {
    phaseName = 'Waxing Crescent';
    phaseNameTe = 'వృద్ధి చెందుతున్న చంద్రుడు';
  } else if (elongation >= 67.5 && elongation < 112.5) {
    phaseName = 'First Quarter';
    phaseNameTe = 'ప్రథమ పాదం';
  } else if (elongation >= 112.5 && elongation < 157.5) {
    phaseName = 'Waxing Gibbous';
    phaseNameTe = 'శుక్ల పక్ష చతుర్దశి దిశగా';
  } else if (elongation >= 157.5 && elongation < 202.5) {
    phaseName = 'Full Moon';
    phaseNameTe = 'పౌర్ణమి';
  } else if (elongation >= 202.5 && elongation < 247.5) {
    phaseName = 'Waning Gibbous';
    phaseNameTe = 'క్షీణిస్తున్న చంద్రుడు';
  } else if (elongation >= 247.5 && elongation < 292.5) {
    phaseName = 'Last Quarter';
    phaseNameTe = 'అంతిమ పాదం';
  } else if (elongation >= 292.5 && elongation < 337.5) {
    phaseName = 'Waning Crescent';
    phaseNameTe = 'కృష్ణ పక్ష క్షీణ చంద్రుడు';
  }

  return {
    phaseName,
    phaseNameTe,
    illumination: k,
    illuminationPercent,
    elongation
  };
}

/* ─── Boundary Crossing Root Finders ────────────────────────── */

/**
 * Find forward crossing where getAngleFn(jd) reaches targetDeg.
 *
 * @param {number} jdStart
 * @param {number} targetDeg
 * @param {function(number): number} getAngleFn
 * @param {number} maxHours
 * @returns {Date|null}
 */
function findBoundaryCrossing(jdStart, targetDeg, getAngleFn, maxHours = 36) {
  const stepDays = 0.5 / 24; // 30-min steps
  const endLimit = jdStart + maxHours / 24;

  function diff(jd) {
    const a = getAngleFn(jd);
    let d = a - targetDeg;
    while (d > 180) d -= 360;
    while (d < -180) d += 360;
    return d;
  }

  let prevDiff = diff(jdStart);

  for (let t = jdStart + stepDays; t <= endLimit; t += stepDays) {
    const curDiff = diff(t);
    if (prevDiff < 0 && curDiff >= 0) {
      let low = t - stepDays, high = t;
      for (let i = 0; i < 22; i++) {
        const mid = (low + high) / 2;
        if (diff(mid) >= 0) high = mid;
        else low = mid;
      }
      const crossingJD = (low + high) / 2;
      return new Date(Math.round((crossingJD - 2440587.5) * 86400000));
    }
    prevDiff = curDiff;
  }
  return null;
}

/**
 * Find backward crossing where getAngleFn(jd) crossed targetDeg prior to jdStart.
 */
function findPriorBoundaryCrossing(jdStart, targetDeg, getAngleFn, maxHours = 36) {
  const stepDays = 0.5 / 24;
  const startLimit = jdStart - maxHours / 24;

  function diff(jd) {
    const a = getAngleFn(jd);
    let d = a - targetDeg;
    while (d > 180) d -= 360;
    while (d < -180) d += 360;
    return d;
  }

  let prevDiff = diff(jdStart);

  for (let t = jdStart - stepDays; t >= startLimit; t -= stepDays) {
    const curDiff = diff(t);
    if (prevDiff >= 0 && curDiff < 0) {
      let low = t, high = t + stepDays;
      for (let i = 0; i < 22; i++) {
        const mid = (low + high) / 2;
        if (diff(mid) >= 0) high = mid;
        else low = mid;
      }
      const crossingJD = (low + high) / 2;
      return new Date(Math.round((crossingJD - 2440587.5) * 86400000));
    }
    prevDiff = curDiff;
  }
  return null;
}

/**
 * Format a Date to local time in a specified timezone.
 */
function formatTimeInTz(date, timezone, format12h = true) {
  if (!date || isNaN(date.getTime())) return null;
  try {
    const dtf = new Intl.DateTimeFormat('en-US', {
      timeZone: timezone,
      hour: format12h ? 'numeric' : '2-digit',
      minute: '2-digit',
      hour12: format12h
    });
    return dtf.format(date);
  } catch (e) {
    return date.toLocaleTimeString('en-US');
  }
}

/**
 * Format a timing interval with start/end strings and human-readable text.
 */
function formatInterval(interval, timezone, format12h = true) {
  if (!interval || !interval.start || !interval.end) return null;
  const sStr = formatTimeInTz(interval.start, timezone, format12h);
  const eStr = formatTimeInTz(interval.end, timezone, format12h);
  return {
    ...interval,
    start: interval.start,
    end:   interval.end,
    startStr: sStr,
    endStr:   eStr,
    text: `${sStr} – ${eStr}`
  };
}

/* ─── Sunrise / Sunset ──────────────────────────────────────── */

/**
 * Calculate solar equatorial coordinates and Equation of Time.
 * Meeus Chapters 25 & 28 / NOAA solar calculations.
 *
 * @param {number} jd - Julian Day Number (UTC)
 * @returns {{ dec: number, EoT: number, ra: number, lambda: number }}
 */
function calcSolarCoordinates(jd) {
  const T = (jd - J2000) / JULIAN_CENTURY;

  // Geometric mean longitude of the Sun (degrees)
  const L0 = normalizeDeg(280.46646 + T * (36000.76983 + T * 0.0003032));

  // Mean anomaly of the Sun (degrees)
  const M = normalizeDeg(357.52911 + T * (35999.05029 - 0.0001537 * T));
  const Mrad = M * DEG2RAD;

  // Eccentricity of Earth's orbit
  const e = 0.016708634 - T * (0.000042037 + 0.0000001267 * T);

  // Equation of center
  const C =
    Math.sin(Mrad) * (1.914602 - T * (0.004817 + 0.000014 * T)) +
    Math.sin(2 * Mrad) * (0.019993 - 0.000101 * T) +
    Math.sin(3 * Mrad) * 0.000289;

  // Sun's true longitude
  const sunTrueLon = normalizeDeg(L0 + C);

  // Apparent longitude (corrected for nutation and aberration)
  const omega = (125.04 - 1934.136 * T) * DEG2RAD;
  const lambda = (sunTrueLon - 0.00569 - 0.00478 * Math.sin(omega)) * DEG2RAD;

  // Obliquity of the ecliptic
  const eps0 = 23 + (26 + ((21.448 - T * (46.815 + T * (0.00059 - T * 0.001813)))) / 60) / 60;
  const eps = (eps0 + 0.00256 * Math.cos(omega)) * DEG2RAD;

  // Declination
  const sinDec = Math.sin(eps) * Math.sin(lambda);
  const dec = Math.asin(Math.max(-1, Math.min(1, sinDec)));

  // Right Ascension
  const cosDec = Math.cos(dec);
  const sinRA = (Math.cos(eps) * Math.sin(lambda)) / (cosDec || 1e-10);
  const cosRA = Math.cos(lambda) / (cosDec || 1e-10);
  const ra = normalizeDeg(Math.atan2(sinRA, cosRA) * RAD2DEG);

  // Equation of Time (Meeus Chapter 28)
  const y = Math.tan(eps / 2) ** 2;
  const L0rad = L0 * DEG2RAD;
  const E_rad =
    y * Math.sin(2 * L0rad) -
    2 * e * Math.sin(Mrad) +
    4 * e * y * Math.sin(Mrad) * Math.cos(2 * L0rad) -
    0.5 * (y ** 2) * Math.sin(4 * L0rad) -
    1.25 * (e ** 2) * Math.sin(2 * Mrad);
  const EoT = E_rad * (720 / Math.PI); // minutes of time

  return { dec, EoT, ra, lambda };
}

/**
 * Calculate sunrise, sunset, and solar noon times using the Meeus algorithm.
 * Meeus Chapters 12, 15, 25, 28.
 *
 * @param {number} year
 * @param {number} month
 * @param {number} day
 * @param {number} lat      - Latitude in decimal degrees (N positive)
 * @param {number} lon      - Longitude in decimal degrees (E positive)
 * @param {number} tzOffset - Timezone offset in hours (e.g., 5.5 for IST)
 * @returns {{ sunrise: Date, sunset: Date, solarNoon: Date, diagnostics: Object } | null}
 */
function calcSunriseSunset(year, month, day, lat, lon, tzOffset) {
  const jd0 = gregorianToJD(year, month, day);

  // Approximate solar transit time in UTC hours (mean solar noon)
  const approxNoonUTC = 12 - lon / 15;
  const jdNoon = jd0 + approxNoonUTC / 24;

  const noonCoords = calcSolarCoordinates(jdNoon);
  // Apparent solar noon in UTC hours
  const noonUTC = 12 - lon / 15 - noonCoords.EoT / 60;

  const latR = lat * DEG2RAD;
  // Standard altitude of center of solar disc at rising/setting:
  // -0.8333 degrees (-50 arcmin = 34 arcmin refraction + 16 arcmin semi-diameter)
  const h0 = -0.8333 * DEG2RAD;

  // Approximate hour angle from noon coordinates
  const cosH0 = (Math.sin(h0) - Math.sin(latR) * Math.sin(noonCoords.dec)) /
                (Math.cos(latR) * Math.cos(noonCoords.dec));

  if (Math.abs(cosH0) > 1) {
    // Polar day or polar night (Sun never rises or never sets)
    return null;
  }

  const H0 = Math.acos(Math.max(-1, Math.min(1, cosH0))) * RAD2DEG; // degrees

  // Refine sunrise by computing Sun's position at approximate rise time
  const jdRise = jd0 + (noonUTC - H0 / 15) / 24;
  const riseCoords = calcSolarCoordinates(jdRise);
  const cosHRise = (Math.sin(h0) - Math.sin(latR) * Math.sin(riseCoords.dec)) /
                   (Math.cos(latR) * Math.cos(riseCoords.dec));
  const HRise = Math.acos(Math.max(-1, Math.min(1, cosHRise))) * RAD2DEG;
  const riseUTC = (12 - lon / 15 - riseCoords.EoT / 60) - HRise / 15;

  // Refine sunset by computing Sun's position at approximate set time
  const jdSet = jd0 + (noonUTC + H0 / 15) / 24;
  const setCoords = calcSolarCoordinates(jdSet);
  const cosHSet = (Math.sin(h0) - Math.sin(latR) * Math.sin(setCoords.dec)) /
                  (Math.cos(latR) * Math.cos(setCoords.dec));
  const HSet = Math.acos(Math.max(-1, Math.min(1, cosHSet))) * RAD2DEG;
  const setUTC = (12 - lon / 15 - setCoords.EoT / 60) + HSet / 15;

  function utcHoursToDate(utcH, yr, mo, dy) {
    const totalMs = Date.UTC(yr, mo - 1, dy) + utcH * 3600000;
    return new Date(totalMs);
  }

  const tzHours = tzOffset != null ? tzOffset : 5.5;

  return {
    sunrise:   utcHoursToDate(riseUTC, year, month, day),
    sunset:    utcHoursToDate(setUTC,  year, month, day),
    solarNoon: utcHoursToDate(noonUTC, year, month, day),
    diagnostics: {
      utcSunrise: riseUTC,
      utcSunset: setUTC,
      localSunrise: riseUTC + tzHours,
      localSunset: setUTC + tzHours,
      timezoneOffset: tzHours,
      longitude: lon,
      latitude: lat,
      solarNoon: noonUTC,
      hourAngle: HRise,
      equationOfTime: noonCoords.EoT,
      solarDeclination: noonCoords.dec * RAD2DEG
    }
  };
}

/* ─── Moonrise / Moonset ─────────────────────────────────────── */

/**
 * Calculate the Moon's equatorial coordinates, horizontal parallax,
 * and standard geometric rise/set altitude.
 * Meeus Chapters 13, 15, 47.
 *
 * @param {number} jd - Julian Day Number (instantaneous UTC)
 * @returns {{ ra: number, dec: number, parallax: number, h0: number, distKm: number }}
 */
function calcMoonPosition(jd) {
  const T = jdToT(jd);
  const { longitude: lambda, latitude: beta } = moonEclipticLongitude(T);

  // Mean obliquity of the ecliptic (Meeus formula)
  const eps = 23.43929111 - 0.013004167 * T - 0.000000164 * T * T + 0.000000504 * T * T * T;

  const lR = lambda * DEG2RAD;
  const bR = beta * DEG2RAD;
  const eR = eps * DEG2RAD;

  // Ecliptic to equatorial coordinates
  const x = Math.cos(bR) * Math.cos(lR);
  const y = Math.cos(bR) * Math.cos(eR) * Math.sin(lR) - Math.sin(bR) * Math.sin(eR);
  const z = Math.sin(bR) * Math.cos(eR) + Math.cos(bR) * Math.sin(eR) * Math.sin(lR);

  const ra = normalizeDeg(Math.atan2(y, x) * RAD2DEG);
  const dec = Math.asin(Math.max(-1, Math.min(1, z))) * RAD2DEG;

  // Moon distance and equatorial horizontal parallax (Meeus Ch 47)
  const D  = normalizeDeg(297.8501921 + 445267.1114034 * T) * DEG2RAD;
  const M  = normalizeDeg(357.5291092 + 35999.0502909 * T) * DEG2RAD;
  const Mp = normalizeDeg(134.9633964 + 477198.8675055 * T) * DEG2RAD;

  const Delta = 385000.56 -
    20905.355 * Math.cos(Mp) -
     3699.111 * Math.cos(2 * D - Mp) -
     2955.968 * Math.cos(2 * D) -
      569.925 * Math.cos(2 * Mp) +
       48.888 * Math.cos(M);

  const parallax = Math.asin(6378.14 / Delta) * RAD2DEG;

  // Standard rise/set geometric altitude for the Moon's center:
  // Upper limb on horizon with atmospheric refraction (34') and semidiameter (0.2725 * pi):
  // h0 = 0° - refraction (34') - semidiameter + horizontal parallax
  // h0 = parallax * (1 - 0.272493) - (34 / 60)° = 0.727507 * parallax - 0.566667°
  // (Meeus Chapter 15, p. 102)
  const h0 = 0.727507 * parallax - 0.566667;

  return { ra, dec, parallax, h0, distKm: Delta };
}

/**
 * Calculate the Moon's geometric altitude and difference from the rise/set threshold.
 *
 * @param {number} jd  - Julian Day Number (instantaneous UTC)
 * @param {number} lat - Latitude in decimal degrees (N positive)
 * @param {number} lon - Longitude in decimal degrees (E positive)
 * @returns {{ alt: number, h0: number, diff: number }}
 */
function calcMoonAltitude(jd, lat, lon) {
  const { ra, dec, h0 } = calcMoonPosition(jd);

  // Greenwich Mean Sidereal Time (Meeus eq 12.4)
  const Ddays = jd - J2000;
  const T = Ddays / JULIAN_CENTURY;
  const gmst = normalizeDeg(280.46061837 + 360.98564736629 * Ddays + 0.000387933 * T * T - (T * T * T / 38710000));
  const lst = normalizeDeg(gmst + lon);

  const H = normalizeDeg180(lst - ra) * DEG2RAD;
  const latR = lat * DEG2RAD;
  const decR = dec * DEG2RAD;

  const sinAlt = Math.sin(latR) * Math.sin(decR) + Math.cos(latR) * Math.cos(decR) * Math.cos(H);
  const alt = Math.asin(Math.max(-1, Math.min(1, sinAlt))) * RAD2DEG;

  return { alt, h0, diff: alt - h0 };
}

/**
 * Numerical bisection to find the exact crossing time where alt == h0.
 */
function findMoonCrossing(jd1, jd2, lat, lon, maxIter = 30) {
  let low = jd1, high = jd2;
  for (let i = 0; i < maxIter; i++) {
    const mid = (low + high) / 2;
    const diff = calcMoonAltitude(mid, lat, lon).diff;
    const diffLow = calcMoonAltitude(low, lat, lon).diff;
    if (diffLow * diff <= 0) {
      high = mid;
    } else {
      low = mid;
    }
  }
  return (low + high) / 2;
}

/**
 * Check if a Julian Day timestamp falls within the requested civil calendar day.
 */
function isSameCivilDay(jd, targetY, targetM, targetD, timezone, tzOffsetHours) {
  const utcMs = Math.round((jd - 2440587.5) * 86400000);
  if (timezone) {
    try {
      const parts = new Intl.DateTimeFormat('en-US', {
        timeZone: timezone,
        year: 'numeric', month: 'numeric', day: 'numeric'
      }).formatToParts(new Date(utcMs));
      const map = {};
      for (const p of parts) map[p.type] = p.value;
      return parseInt(map.year, 10) === targetY &&
             parseInt(map.month, 10) === targetM &&
             parseInt(map.day, 10) === targetD;
    } catch (e) {}
  }
  const localMs = utcMs + tzOffsetHours * 3600000;
  const d = new Date(localMs);
  return d.getUTCFullYear() === targetY && (d.getUTCMonth() + 1) === targetM && d.getUTCDate() === targetD;
}

/**
 * Format local time and ISO timestamp with timezone offset.
 */
function formatEventTime(jd, tzOffsetHours, timezone) {
  if (!jd) {
    return { time: null, datetime: null, status: 'no_event' };
  }

  const utcMs = Math.round((jd - 2440587.5) * 86400000);
  const dateObj = new Date(utcMs);

  if (timezone) {
    try {
      const parts = new Intl.DateTimeFormat('en-US', {
        timeZone: timezone,
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false
      }).formatToParts(dateObj);
      const map = {};
      for (const p of parts) map[p.type] = p.value;
      const y = parseInt(map.year, 10);
      const mo = map.month;
      const d = map.day;
      let h = parseInt(map.hour, 10);
      if (h === 24) h = 0;
      const m = map.minute;
      const s = map.second;

      const localAsUtc = Date.UTC(y, parseInt(mo, 10) - 1, parseInt(d, 10), h, parseInt(m, 10), parseInt(s, 10));
      const offsetMs = localAsUtc - utcMs;
      const offsetMins = Math.round(offsetMs / 60000);
      const offsetSign = offsetMins >= 0 ? '+' : '-';
      const absMins = Math.abs(offsetMins);
      const offH = String(Math.floor(absMins / 60)).padStart(2, '0');
      const offM = String(absMins % 60).padStart(2, '0');
      const offsetStr = `${offsetSign}${offH}:${offM}`;

      const h12 = h % 12 || 12;
      const ampm = h >= 12 ? 'PM' : 'AM';
      const timeStr = `${h12}:${m} ${ampm}`;
      const datetimeStr = `${y}-${mo}-${d}T${String(h).padStart(2, '0')}:${m}:${s}${offsetStr}`;

      return {
        time: timeStr,
        datetime: datetimeStr,
        status: 'normal'
      };
    } catch (e) {
      // Fallback to static offset
    }
  }

  const localMs = utcMs + tzOffsetHours * 3600000;
  const localDate = new Date(localMs);

  const h = localDate.getUTCHours();
  const m = localDate.getUTCMinutes();
  const s = localDate.getUTCSeconds();
  const ampm = h >= 12 ? 'PM' : 'AM';
  const h12 = h % 12 || 12;
  const timeStr = `${h12}:${String(m).padStart(2, '0')} ${ampm}`;

  const offsetSign = tzOffsetHours >= 0 ? '+' : '-';
  const absOffset = Math.abs(tzOffsetHours);
  const offsetH = String(Math.floor(absOffset)).padStart(2, '0');
  const offsetM = String(Math.round((absOffset - Math.floor(absOffset)) * 60)).padStart(2, '0');
  const offsetStr = `${offsetSign}${offsetH}:${offsetM}`;

  const y = localDate.getUTCFullYear();
  const mo = String(localDate.getUTCMonth() + 1).padStart(2, '0');
  const d = String(localDate.getUTCDate()).padStart(2, '0');
  const hh = String(h).padStart(2, '0');
  const mm = String(m).padStart(2, '0');
  const ss = String(s).padStart(2, '0');

  const datetimeStr = `${y}-${mo}-${d}T${hh}:${mm}:${ss}${offsetStr}`;

  return {
    time: timeStr,
    datetime: datetimeStr,
    status: 'normal'
  };
}

/**
 * Calculate Moonrise and Moonset for a given civil date and location.
 *
 * @param {number} year
 * @param {number} month
 * @param {number} day
 * @param {number} lat
 * @param {number} lon
 * @param {string} timezone
 * @returns {{ moonrise: object, moonset: object }}
 */
function calcMoonRiseSet(year, month, day, lat, lon, timezone) {
  try {
    const tzOffset = getTzOffsetHours(timezone, year, month, day);
    const jdMidnightUTC = gregorianToJD(year, month, day) - tzOffset / 24;

    const stepHours = 0.25; // 15-minute search resolution
    let riseJD = null;
    let setJD = null;

    let prevT = -6;
    let prevDiff = calcMoonAltitude(jdMidnightUTC + prevT / 24, lat, lon).diff;

    for (let t = -6 + stepHours; t <= 30; t += stepHours) {
      const jd = jdMidnightUTC + t / 24;
      const diff = calcMoonAltitude(jd, lat, lon).diff;

      if (prevDiff < 0 && diff >= 0) {
        const rootJD = findMoonCrossing(jdMidnightUTC + (t - stepHours) / 24, jd, lat, lon);
        if (isSameCivilDay(rootJD, year, month, day, timezone, tzOffset) && !riseJD) {
          riseJD = rootJD;
        }
      }

      if (prevDiff > 0 && diff <= 0) {
        const rootJD = findMoonCrossing(jdMidnightUTC + (t - stepHours) / 24, jd, lat, lon);
        if (isSameCivilDay(rootJD, year, month, day, timezone, tzOffset) && !setJD) {
          setJD = rootJD;
        }
      }

      prevDiff = diff;
    }

    return {
      moonrise: formatEventTime(riseJD, tzOffset, timezone),
      moonset:  formatEventTime(setJD,  tzOffset, timezone)
    };
  } catch (err) {
    return {
      moonrise: { time: null, datetime: null, status: 'calculation_error', error: err.message },
      moonset:  { time: null, datetime: null, status: 'calculation_error', error: err.message }
    };
  }
}

/* ─── Main Panchangam Calculator ────────────────────────────── */

/**
 * Calculate complete Panchangam for a date and location.
 *
 * @param {object} params
 * @param {number} params.year
 * @param {number} params.month
 * @param {number} params.day
 * @param {number} params.lat
 * @param {number} params.lon
 * @param {string} params.timezone  - IANA timezone identifier (e.g., 'Asia/Kolkata')
 * @param {string} params.ayanamsa  - 'lahiri' (default)
 * @returns {object} Complete Panchangam data
 */
function calculatePanchangam(params) {
  const {
    year, month, day,
    lat = 17.3850,
    lon = 78.4867,
    timezone = 'Asia/Kolkata',
    ayanamsa = 'lahiri',
    locationName = null
  } = params;

  const tzOffset = getTzOffsetHours(timezone, year, month, day);

  // Calculate JD for local noon
  const localNoonUTC = gregorianToJD(year, month, day + 0.5 - tzOffset / 24);
  const T = jdToT(localNoonUTC);

  // Get tropical positions at local noon
  const sunPos  = sunEclipticLongitude(T);
  const moonPos = moonEclipticLongitude(T);

  // Convert to sidereal
  const sunSidereal  = toSidereal(sunPos.longitude,  localNoonUTC, ayanamsa);
  const moonSidereal = toSidereal(moonPos.longitude, localNoonUTC, ayanamsa);

  // Core Panchangam elements at noon
  const tithi     = calcTithi(moonSidereal, sunSidereal);
  const nakshatra = calcNakshatra(moonSidereal);
  const yoga      = calcYoga(sunSidereal, moonSidereal);
  const karana    = calcKarana(tithi.id, moonSidereal, sunSidereal);
  const vara      = calcVara(localNoonUTC);

  // Sunrise / Sunset
  const sunTimes = calcSunriseSunset(year, month, day, lat, lon, tzOffset);

  // Anchor for transition time search: sunrise if available, otherwise noon
  const jdAnchor = sunTimes ? (sunTimes.sunrise.getTime() / 86400000 + 2440587.5) : localNoonUTC;

  // Tithi Transition Times
  const tithiTargetDeg = (tithi.id * 12) % 360;
  const tithiEnd = findBoundaryCrossing(jdAnchor, tithiTargetDeg, (jd) => {
    const Tj = jdToT(jd);
    const s = toSidereal(sunEclipticLongitude(Tj).longitude, jd, ayanamsa);
    const m = toSidereal(moonEclipticLongitude(Tj).longitude, jd, ayanamsa);
    return normalizeDeg(m - s);
  });
  const tithiPriorDeg = ((tithi.id - 1) * 12) % 360;
  const tithiStart = findPriorBoundaryCrossing(jdAnchor, tithiPriorDeg, (jd) => {
    const Tj = jdToT(jd);
    const s = toSidereal(sunEclipticLongitude(Tj).longitude, jd, ayanamsa);
    const m = toSidereal(moonEclipticLongitude(Tj).longitude, jd, ayanamsa);
    return normalizeDeg(m - s);
  });

  // Nakshatra Transition Times
  const nakTargetDeg = (nakshatra.nakshatra.id * (360 / 27)) % 360;
  const nakEnd = findBoundaryCrossing(jdAnchor, nakTargetDeg, (jd) => {
    const Tj = jdToT(jd);
    return toSidereal(moonEclipticLongitude(Tj).longitude, jd, ayanamsa);
  });
  const nakPriorDeg = ((nakshatra.nakshatra.id - 1) * (360 / 27)) % 360;
  const nakStart = findPriorBoundaryCrossing(jdAnchor, nakPriorDeg, (jd) => {
    const Tj = jdToT(jd);
    return toSidereal(moonEclipticLongitude(Tj).longitude, jd, ayanamsa);
  });

  // Yoga Transition Time
  const yogaTargetDeg = (yoga.id * (360 / 27)) % 360;
  const yogaEnd = findBoundaryCrossing(jdAnchor, yogaTargetDeg, (jd) => {
    const Tj = jdToT(jd);
    const s = toSidereal(sunEclipticLongitude(Tj).longitude, jd, ayanamsa);
    const m = toSidereal(moonEclipticLongitude(Tj).longitude, jd, ayanamsa);
    return normalizeDeg(s + m);
  });

  // Karana Transition Time (each Karana is 6 degrees)
  const karanaSubIndex = (karana.id % 2 === 1) ? 1 : 2;
  const karanaTargetDeg = ((tithi.id - 1) * 12 + (karanaSubIndex === 1 ? 6 : 12)) % 360;
  const karanaEnd = findBoundaryCrossing(jdAnchor, karanaTargetDeg, (jd) => {
    const Tj = jdToT(jd);
    const s = toSidereal(sunEclipticLongitude(Tj).longitude, jd, ayanamsa);
    const m = toSidereal(moonEclipticLongitude(Tj).longitude, jd, ayanamsa);
    return normalizeDeg(m - s);
  });

  // Moonrise / Moonset
  const moonTimes = calcMoonRiseSet(year, month, day, lat, lon, timezone);

  // Moon Phase & Illumination
  const moonPhase = calcMoonPhaseAndIllumination(localNoonUTC);

  // Inauspicious & Auspicious Timings
  let rahuKalam = null, yamagandam = null, gulikaKalam = null, abhijit = null;
  let durMuhurthamList = [], brahmaMuhurtham = null, varjyamData = null;

  if (sunTimes) {
    const weekday = vara.id;
    rahuKalam        = calcRahuKalam(sunTimes.sunrise, sunTimes.sunset, weekday);
    yamagandam       = calcYamagandam(sunTimes.sunrise, sunTimes.sunset, weekday);
    gulikaKalam      = calcGulikaKalam(sunTimes.sunrise, sunTimes.sunset, weekday);
    abhijit          = calcAbhijitMuhurtham(sunTimes.sunrise, sunTimes.sunset);
    durMuhurthamList = calcDurMuhurtham(sunTimes.sunrise, sunTimes.sunset, weekday);
    brahmaMuhurtham  = calcBrahmaMuhurtham(sunTimes.sunrise);
    varjyamData      = calcVarjyamAndAmritKalam(nakshatra.nakshatra.id, nakStart, nakEnd);
  }

  const dayLengthMs = sunTimes ? (sunTimes.sunset.getTime() - sunTimes.sunrise.getTime()) : 0;
  const dayLengthHours = Math.floor(dayLengthMs / 3600000);
  const dayLengthMins  = Math.round((dayLengthMs % 3600000) / 60000);

  const isoDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

  return {
    meta: {
      date:                  { year, month, day, iso: isoDate },
      location_name:         locationName || `${lat.toFixed(4)}°N, ${lon.toFixed(4)}°E`,
      latitude:              lat,
      longitude:             lon,
      timezone:              timezone,
      tzOffset:              tzOffset,
      calculation_method:    'Drik Ganita (Surya Siddhanta & Modern Ephemeris)',
      ayanamsa:              ayanamsa,
      calculation_timestamp: new Date().toISOString(),
      engineVersion:         '1.2.0',
      ruleVersion:           'telugu-drik-1.2',
      generatedAt:           new Date().toISOString()
    },
    vara,
    tithi: {
      ...tithi,
      start:    tithiStart,
      end:      tithiEnd,
      startStr: formatTimeInTz(tithiStart, timezone),
      endStr:   formatTimeInTz(tithiEnd, timezone),
      spanStr:  tithiEnd ? `Up to ${formatTimeInTz(tithiEnd, timezone)}` : null
    },
    nakshatra: {
      ...nakshatra,
      start:    nakStart,
      end:      nakEnd,
      startStr: formatTimeInTz(nakStart, timezone),
      endStr:   formatTimeInTz(nakEnd, timezone),
      spanStr:  nakEnd ? `Up to ${formatTimeInTz(nakEnd, timezone)}` : null
    },
    yoga: {
      ...yoga,
      end:     yogaEnd,
      endStr:  formatTimeInTz(yogaEnd, timezone),
      spanStr: yogaEnd ? `Up to ${formatTimeInTz(yogaEnd, timezone)}` : null
    },
    karana: {
      ...karana,
      end:     karanaEnd,
      endStr:  formatTimeInTz(karanaEnd, timezone),
      spanStr: karanaEnd ? `Up to ${formatTimeInTz(karanaEnd, timezone)}` : null
    },
    solar: sunTimes ? {
      sunrise:          sunTimes.sunrise,
      sunset:           sunTimes.sunset,
      solarNoon:        sunTimes.solarNoon,
      sunriseStr:       formatTimeInTz(sunTimes.sunrise, timezone),
      sunsetStr:        formatTimeInTz(sunTimes.sunset, timezone),
      solarNoonStr:     formatTimeInTz(sunTimes.solarNoon, timezone),
      dayLengthMinutes: Math.round(dayLengthMs / 60000),
      dayLengthStr:     `${dayLengthHours}h ${dayLengthMins}m`,
      moonrise:         moonTimes.moonrise.time,
      moonset:          moonTimes.moonset.time,
      diagnostics:      sunTimes.diagnostics
    } : null,
    moonrise: moonTimes.moonrise,
    moonset:  moonTimes.moonset,
    lunar: {
      moonrise:     moonTimes.moonrise,
      moonset:      moonTimes.moonset,
      phase:        moonPhase.phaseName,
      phaseTe:      moonPhase.phaseNameTe,
      illumination: moonPhase.illuminationPercent,
      elongation:   parseFloat(moonPhase.elongation.toFixed(1))
    },
    timings: {
      rahuKalam:        formatInterval(rahuKalam, timezone),
      yamagandam:       formatInterval(yamagandam, timezone),
      gulikaKalam:      formatInterval(gulikaKalam, timezone),
      abhijitMuhurtham: formatInterval(abhijit, timezone),
      durMuhurtham:     durMuhurthamList.map(dm => formatInterval(dm, timezone)),
      varjyam:          varjyamData ? formatInterval(varjyamData.varjyam, timezone) : null,
      amritKalam:       varjyamData ? formatInterval(varjyamData.amritKalam, timezone) : null,
      brahmaMuhurtham:  formatInterval(brahmaMuhurtham, timezone)
    }
  };
}

/* ─── Exports ───────────────────────────────────────────────── */
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    // Core astronomy
    gregorianToJD,
    jdToGregorian,
    jdToT,
    normalizeDeg,
    sunEclipticLongitude,
    moonEclipticLongitude,
    lahiriAyanamsa,
    toSidereal,
    // Panchangam elements
    calcTithi,
    calcNakshatra,
    calcYoga,
    calcKarana,
    calcVara,
    // Timings
    calcSunriseSunset,
    calcMoonPosition,
    calcMoonAltitude,
    calcMoonRiseSet,
    calcRahuKalam,
    calcYamagandam,
    calcGulikaKalam,
    calcAbhijitMuhurtham,
    calcDurMuhurtham,
    calcBrahmaMuhurtham,
    calcVarjyamAndAmritKalam,
    calcMoonPhaseAndIllumination,
    findBoundaryCrossing,
    findPriorBoundaryCrossing,
    formatTimeInTz,
    formatInterval,
    // Main calculator
    calculatePanchangam,
    // Data
    TITHI_NAMES,
    TITHI_NAMES_TE,
    NAKSHATRA_DATA,
    YOGA_NAMES,
    VARA_DATA,
    VARJYAM_START_GHATIS
  };
}

// Browser global
if (typeof window !== 'undefined') {
  window.DJVPanchangamEngine = {
    calculatePanchangam,
    calcMoonRiseSet,
    calcSunriseSunset,
    calcMoonPhaseAndIllumination,
    formatTimeInTz,
    formatInterval,
    NAKSHATRA_DATA,
    TITHI_NAMES,
    TITHI_NAMES_TE,
    YOGA_NAMES,
    VARA_DATA
  };
  // Backward compatibility alias
  window.DJVPanchangam = window.DJVPanchangamEngine;
}

