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

/* ─── Sunrise / Sunset ──────────────────────────────────────── */

/**
 * Calculate sunrise and sunset times using the Meeus algorithm.
 * Meeus, Chapter 15.
 *
 * @param {number} year
 * @param {number} month
 * @param {number} day
 * @param {number} lat      - Latitude in decimal degrees (N positive)
 * @param {number} lon      - Longitude in decimal degrees (E positive)
 * @param {number} tzOffset - Timezone offset in hours (e.g., 5.5 for IST)
 * @returns {{ sunrise: Date, sunset: Date, solarNoon: Date } | null}
 */
function calcSunriseSunset(year, month, day, lat, lon, tzOffset) {
  const jd = gregorianToJD(year, month, day);
  const T  = jdToT(jd);

  const { longitude: sunLon } = sunEclipticLongitude(T);

  // Obliquity of the ecliptic (degrees)
  const eps   = 23.439 - 0.0000004 * (jd - J2000);
  const epsR  = eps * DEG2RAD;
  const sunLonR = sunLon * DEG2RAD;

  // Sun's declination
  const sinDec = Math.sin(epsR) * Math.sin(sunLonR);
  const dec    = Math.asin(sinDec) * RAD2DEG;

  // Sun's right ascension
  const RA = Math.atan2(Math.cos(epsR) * Math.sin(sunLonR), Math.cos(sunLonR)) * RAD2DEG;

  const latR   = lat * DEG2RAD;
  const decR   = dec * DEG2RAD;

  // Hour angle for sunrise (solar altitude = -0.8333 degrees for refraction + disc radius)
  const cosH = (Math.sin(-0.8333 * DEG2RAD) - Math.sin(latR) * Math.sin(decR)) /
               (Math.cos(latR) * Math.cos(decR));

  if (Math.abs(cosH) > 1) {
    // Sun never rises or sets (polar regions)
    return null;
  }

  const H = Math.acos(cosH) * RAD2DEG; // Hour angle at sunrise (degrees)

  // Equation of time approximation
  const L0   = 280.46646 + 36000.76983 * T;
  const M    = 357.52911 + 35999.05029 * T;
  const Mrad = (M % 360) * DEG2RAD;
  const EoT  = (-0.0057183 + (-0.0001 * Math.sin(Mrad)) + 0.00255 * Math.sin(2 * sunLonR) -
                0.00015 * Math.sin(Mrad + sunLonR)) * 24 * 60; // minutes

  // Solar noon in UTC hours
  const noonUTC = 12 - lon / 15 - EoT / 60;

  const riseUTC = noonUTC - H / 15;
  const setUTC  = noonUTC + H / 15;

  function utcHoursToDate(utcH, yr, mo, dy) {
    const totalMs = Date.UTC(yr, mo - 1, dy) + utcH * 3600000;
    return new Date(totalMs);
  }

  return {
    sunrise:   utcHoursToDate(riseUTC, year, month, day),
    sunset:    utcHoursToDate(setUTC,  year, month, day),
    solarNoon: utcHoursToDate(noonUTC, year, month, day)
  };
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
 * @param {number} params.tzOffset  - Hours offset from UTC (e.g., 5.5 for IST)
 * @param {string} params.ayanamsa  - 'lahiri' (default)
 * @returns {object} Complete Panchangam data
 */
function calculatePanchangam(params) {
  const {
    year, month, day,
    lat = 17.3850,
    lon = 78.4867,
    tzOffset = 5.5,
    ayanamsa = 'lahiri'
  } = params;

  // Calculate JD for local noon (approximate for planet positions)
  const localNoonUTC = gregorianToJD(year, month, day + 0.5 - tzOffset / 24);
  const T = jdToT(localNoonUTC);

  // Get tropical positions
  const sunPos  = sunEclipticLongitude(T);
  const moonPos = moonEclipticLongitude(T);

  // Convert to sidereal
  const sunSidereal  = toSidereal(sunPos.longitude,  localNoonUTC, ayanamsa);
  const moonSidereal = toSidereal(moonPos.longitude, localNoonUTC, ayanamsa);

  // Core Panchangam elements
  const tithi    = calcTithi(moonSidereal, sunSidereal);
  const nakshatra = calcNakshatra(moonSidereal);
  const yoga     = calcYoga(sunSidereal, moonSidereal);
  const karana   = calcKarana(tithi.id, moonSidereal, sunSidereal);
  const vara     = calcVara(localNoonUTC);

  // Sunrise / Sunset
  const sunTimes = calcSunriseSunset(year, month, day, lat, lon, tzOffset);

  let rahuKalam = null, yamagandam = null, gulikaKalam = null, abhijit = null;

  if (sunTimes) {
    const weekday = vara.id;
    rahuKalam  = calcRahuKalam(sunTimes.sunrise, sunTimes.sunset, weekday);
    yamagandam = calcYamagandam(sunTimes.sunrise, sunTimes.sunset, weekday);
    gulikaKalam= calcGulikaKalam(sunTimes.sunrise, sunTimes.sunset, weekday);
    abhijit    = calcAbhijitMuhurtham(sunTimes.sunrise, sunTimes.sunset);
  }

  return {
    meta: {
      date:         { year, month, day },
      location:     { lat, lon, tzOffset },
      ayanamsa,
      engineVersion: '1.0.0',
      ruleVersion:   'telugu-1.0',
      generatedAt:   new Date().toISOString()
    },
    vara,
    tithi,
    nakshatra,
    yoga,
    karana,
    solar: sunTimes ? {
      sunrise:   sunTimes.sunrise,
      sunset:    sunTimes.sunset,
      solarNoon: sunTimes.solarNoon
    } : null,
    timings: {
      rahuKalam,
      yamagandam,
      gulikaKalam,
      abhijitMuhurtham: abhijit
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
    calcRahuKalam,
    calcYamagandam,
    calcGulikaKalam,
    calcAbhijitMuhurtham,
    // Main calculator
    calculatePanchangam,
    // Data
    TITHI_NAMES,
    TITHI_NAMES_TE,
    NAKSHATRA_DATA,
    YOGA_NAMES,
    VARA_DATA
  };
}

// Browser global
if (typeof window !== 'undefined') {
  window.DJVPanchangam = { calculatePanchangam, NAKSHATRA_DATA, TITHI_NAMES, YOGA_NAMES };
}
