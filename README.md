# DHARMA JYOTHI VEDIKA (DJV)
## Project Structure — Phase 1

```
DHARMA JYOTHI VEDIKA/
│
├── 📄 LICENSE-CHECK.md            ← Dependency license audit
├── 📄 README.md                   ← This file
│
├── 📂 website/                    ← Frontend website (HTML/CSS/JS)
│   ├── 📄 index.html              ← ✅ Complete homepage (SEO, structured data)
│   ├── 📂 assets/
│   │   └── 📂 css/
│   │       └── 📄 design-system.css  ← ✅ Central design tokens
│   └── 📂 pages/
│       ├── panchangam/            ← Panchangam pages
│       ├── festivals/             ← Festival pages
│       ├── muhurtham/             ← Muhurtham pages
│       ├── pooja/                 ← Pooja guide pages
│       ├── mantras/               ← Mantra pages
│       ├── temples/               ← Temple pages
│       └── articles/              ← Article pages
│
├── 📂 packages/
│   ├── 📂 panchangam-engine/      ← Core calculation engine
│   │   └── 📂 src/
│   │       ├── 📄 core.js         ← ✅ Full engine (Meeus algorithms)
│   │       └── 📂 validation/
│   │           └── 📄 panchangam.test.js  ← ✅ 31/31 tests passing
│   │
│   └── 📂 festival-engine/        ← Festival date calculator
│       └── 📂 src/
│           └── 📄 festivals.js    ← ✅ Rule-based festival system
│
├── 📂 wordpress/
│   └── 📂 plugins/
│       └── 📂 djv-core/           ← Main WordPress plugin
│           ├── 📄 djv-core.php    ← ✅ Plugin entry point
│           └── 📂 includes/
│               ├── 📄 class-djv-post-types.php  ← ✅ All CPTs
│               ├── 📄 class-djv-rest-api.php     ← ✅ All REST endpoints
│               ├── 📄 class-djv-panchangam.php   ← (Phase 2)
│               ├── 📄 class-djv-festivals.php    ← (Phase 2)
│               ├── 📄 class-djv-cache.php        ← (Phase 2)
│               ├── 📄 class-djv-validation.php   ← (Phase 2)
│               └── 📄 class-djv-admin.php        ← (Phase 2)
│
└── 📂 mobile/                     ← React Native + Expo app (Phase 4)
    ├── 📂 src/
    │   ├── 📂 screens/
    │   ├── 📂 components/
    │   └── 📂 services/
    └── 📄 app.json
```

---

## Phase Completion Status

| Phase | Description | Status |
|-------|-------------|--------|
| **Phase 1** | Brand · Design System · Website UI · WordPress Plugin · REST API | ✅ Complete |
| **Phase 2** | Panchangam Engine · Astronomy · All 5 Angas · Timings · Validation | ✅ Engine built (31/31 tests passing — needs manual validation) |
| **Phase 3** | Festival Engine · Muhurtham · Pooja · Mantras · Temples · Calendar | 🔄 Festival engine built; content needed |
| **Phase 4** | Mobile App · Auth · Favorites · Notifications · Sharing | 🔄 Mobile scaffolding initialized |
| **Phase 5** | AdSense · AdMob · Analytics · SEO · Performance | ⏳ Planned |
| **Phase 6** | Production Testing · Security · Validation · Mobile Testing | ⏳ Planned |

---

## Getting Started

### Run Panchangam Engine Tests
```bash
node packages/panchangam-engine/src/validation/panchangam.test.js
```

### View Website
Open `website/index.html` in any browser.

### WordPress Plugin
Copy `wordpress/plugins/djv-core/` to your WordPress `wp-content/plugins/` directory.

---

## Panchangam Engine

**Algorithm source:** Jean Meeus, *Astronomical Algorithms* (2nd Ed, Willmann-Bell, 1998)

- All code is original. Mathematical algorithms are not copyrightable.
- **No paid ephemeris.** No Swiss Ephemeris. No external paid API.
- Full calculation for: Tithi, Nakshatra (with Pada), Yoga, Karana, Vara, Sunrise, Sunset, Rahu Kalam, Yamagandam, Gulika Kalam, Abhijit Muhurtham
- Ayanamsa: Lahiri (Chitrapaksha) — configurable
- Month system: Amanta — configurable
- Default location: Hyderabad (17.3850°N, 78.4867°E, IST)

### ⚠️ IMPORTANT: Pre-Launch Validation Required

The engine is architecturally sound and all 31 automated tests pass.
However, **astronomical calculations must be manually compared** against
at least 30 dates from a published Panchangam before public launch.

See `packages/panchangam-engine/src/validation/panchangam.test.js`
for the reference comparison framework.

---

## WordPress REST API

All endpoints available at `/wp-json/djv/v1/`:

| Endpoint | Description |
|----------|-------------|
| `GET /panchangam?date=&latitude=&longitude=&timezone=` | Full Panchangam |
| `GET /festivals?year=&month=` | Festival list |
| `GET /festivals/{slug}` | Single festival |
| `GET /muhurtham?date=&latitude=&longitude=` | Muhurtham timings |
| `GET /pooja` | Pooja guide list |
| `GET /pooja/{slug}` | Single pooja guide |
| `GET /mantras?deity=&language=` | Mantras |
| `GET /temples?state=&deity=` | Temple directory |
| `GET /articles?category=` | Articles |
| `GET /today?latitude=&longitude=` | Aggregated home data |

---

## Design System

All visual design tokens are in `website/assets/css/design-system.css`.

| Token | Value | Usage |
|-------|-------|-------|
| `--clr-primary` | `#7A2419` | Deep Maroon — primary brand |
| `--clr-secondary` | `#C89432` | Rich Saffron-Gold |
| `--clr-accent` | `#E7B75A` | Warm gold accent |
| `--clr-bg` | `#FFF9F0` | Warm cream background |
| `--clr-dark` | `#241914` | Near-black dark |
| `--font-primary` | Poppins | Body text |
| `--font-heading` | Playfair Display | Headings |
| `--font-telugu` | Noto Sans Telugu | Telugu text |

---

## License & Data Source Attributions
- **WordPress plugin & theme:** GPL v2+
- **Panchangam engine:** Proprietary (original code)
- **Hindu Temples Seed Dataset:** [rishabhmodi03/hindu-temples](https://github.com/rishabhmodi03/hindu-temples) (MIT License). Open-source seed dataset for Hindu temple cataloging across Indian states and deities, utilized under MIT terms with full source preservation and attribution metadata.
- See `LICENSE-CHECK.md` for full dependency and dataset audit.

