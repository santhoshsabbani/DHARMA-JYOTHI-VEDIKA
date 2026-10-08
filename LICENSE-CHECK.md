# LICENSE-CHECK.md
# Dharma Jyothi Vedika (DJV) — Dependency License Audit

**Last Updated:** 2026-10-03  
**Auditor:** DJV Engineering Team  
**Status:** ✅ Verified for Phase 1 | 🔄 Pending for Phase 2 (Panchangam Engine)

---

## PANCHANGAM ENGINE

### Core Astronomical Calculation Approach

**Selected:** Pure JavaScript/TypeScript implementation of algorithms from:
- Jean Meeus, *Astronomical Algorithms* (2nd Edition, Willmann-Bell, 1998)
  - The algorithms are mathematical formulas derived from public domain astronomical data
  - Algorithms themselves are not copyrightable
  - Implementation (our code) is original work owned by DJV
  - No dependency on any external paid ephemeris

**Status:** ✅ APPROVED — No license dependencies. Math formulas are not copyrightable.

---

## DIRECT DEPENDENCIES

### Frontend / Website

| Package | Version | License | Commercial Use | Mobile Use | Attribution |
|---------|---------|---------|---------------|------------|-------------|
| (None for Phase 1 — vanilla HTML/CSS/JS) | — | — | ✅ | ✅ | — |

### WordPress Theme

| Package | Version | License | Commercial Use | Mobile Use | Attribution |
|---------|---------|---------|---------------|------------|-------------|
| WordPress Core | 6.x | GPL v2+ | ✅ | ✅ | GPL compliance |
| Custom Theme (DJV) | 1.0.0 | GPL v2+ | ✅ | ✅ | — |
| Custom Plugin (DJV) | 1.0.0 | GPL v2+ | ✅ | ✅ | — |

### Fonts

| Font | Source | License | Commercial Use | Attribution |
|------|--------|---------|---------------|------------|
| Poppins | Google Fonts | SIL OFL 1.1 | ✅ | Not required |
| Playfair Display | Google Fonts | SIL OFL 1.1 | ✅ | Not required |
| Inter | Google Fonts | SIL OFL 1.1 | ✅ | Not required |
| Noto Sans Telugu | Google Fonts | SIL OFL 1.1 | ✅ | Not required |

### Panchangam Engine (Phase 2 — Planned)

| Package | Version | License | Commercial Use | Mobile Use | Notes |
|---------|---------|---------|---------------|------------|-------|
| `astronomy-engine` (Don Cross) | 2.x | MIT | ✅ | ✅ | Alternative evaluation |
| Custom Meeus JS implementation | 1.0.0 | Proprietary (our code) | ✅ | ✅ | Primary approach |

**`astronomy-engine` evaluation:**
- License: MIT
- Source: https://github.com/cosinekitty/astronomy
- Commercial use: ✅ Permitted
- Mobile (React Native): ✅ Permitted
- Attribution: Only in LICENSE file
- Status: 🔄 Under evaluation as optional cross-validation tool

### Mobile App (Phase 4 — Planned)

| Package | Version | License | Commercial Use | Notes |
|---------|---------|---------|---------------|-------|
| React Native | 0.74+ | MIT | ✅ | Primary framework |
| Expo | 51+ | MIT | ✅ | Build tooling |
| Firebase (FCM) | — | Apache 2.0 | ✅ | Push notifications |
| React Navigation | 6+ | MIT | ✅ | Navigation |

---

## REJECTED LIBRARIES

| Library | Reason for Rejection |
|---------|---------------------|
| Swiss Ephemeris (swisseph) | Dual license: AGPL for free use. Commercial license required for closed-source apps. Not suitable without paid license. REJECTED. |
| Astrodienst JPL Ephemeris raw data | Download/redistribution restrictions. REJECTED for bundling in app. |

---

## IMPORTANT NOTES

1. **Swiss Ephemeris:** NOT used in this project. Its dual-license (AGPL/commercial) creates unacceptable obligations for a commercial app. Our own Meeus-based implementation avoids this entirely.

2. **Jean Meeus Algorithms:** Mathematical formulas and algorithms are not copyrightable under US or Indian law. Only specific creative expression (prose, code) is copyrightable. Our implementation is original code written by DJV Engineering.

3. **Panchangam calculation conventions (Lahiri ayanamsa, regional rules):** Traditional knowledge, not subject to copyright.

4. **All Google Fonts** used are served via Google Fonts CDN or can be self-hosted. SIL OFL 1.1 permits commercial use without attribution requirement.

---

## COMPLIANCE REQUIREMENTS

- WordPress GPL v2+ compliance: Distribute plugin/theme source under GPL if distributing the software.
- Expo/React Native: MIT — no distribution requirement beyond LICENSE file in repo.
- Firebase: Apache 2.0 — include attribution in app credits.

---

## OPEN SOURCE DATASETS

### Hindu Temples Seed Dataset

| Property | Details |
|---|---|
| **Dataset Repository** | `rishabhmodi03/hindu-temples` (GitHub) |
| **URL** | https://github.com/rishabhmodi03/hindu-temples |
| **License** | MIT License |
| **Commercial Use** | ✅ Permitted |
| **Attribution Requirement** | ✅ Preserved across DJV documentation, API metadata, and temple profiles |
| **Verification Status** | `needs_verification` (Open source seed data pending independent field audit) |
| **Stored Raw Metadata** | `_djv_source_raw_name`, `_djv_source_raw_state`, `_djv_source_raw_deity`, `_djv_source_raw_description` |

---

## AUDIT HISTORY

| Date | Auditor | Changes |
|------|---------|---------|
| 2026-10-03 | DJV Engineering | Initial audit for Phase 1 |
| 2026-10-08 | DJV Engineering | Audited and added MIT attribution for `rishabhmodi03/hindu-temples` dataset |


