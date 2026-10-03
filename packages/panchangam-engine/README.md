# DJV Panchangam Engine

Standalone Node.js HTTP microservice for astronomical calculations supporting the Dharma Jyothi Vedika (DJV) platform.

## Architecture

This service acts as the calculation layer. Due to Hostinger hosting restrictions (which block `shell_exec` and Node subprocesses from PHP), this engine has been architected as an independent HTTP microservice.

The API layer securely validates parameters, invokes the mathematical core engine (`core.js`), and returns clean JSON to the WordPress REST API client.

## API Documentation

### 1. Health Endpoint
**`GET /health`**

Validates the engine is running and responding.
**Response:**
```json
{
  "success": true,
  "service": "djv-panchangam-engine",
  "status": "healthy",
  "version": "1.0.0"
}
```

### 2. Panchangam Endpoint
**`GET /panchangam`**

Calculates Panchangam data for a specific date and location.

#### Parameters
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `date` | string | **Yes** | ISO-8601 Date (YYYY-MM-DD) | `2026-10-03` |
| `latitude` | number | **Yes** | Decimal latitude | `17.3850` |
| `longitude` | number | **Yes** | Decimal longitude | `78.4867` |
| `timezone` | string | **Yes** | IANA Timezone Identifier | `Asia/Kolkata` |
| `region` | string | No | Regional context (default: telugu) | `telugu` |
| `ayanamsa` | string | No | Ayanamsa model (default: lahiri) | `lahiri` |
| `language` | string | No | Output language code | `en` |

**Example Request:**
```
GET /panchangam?date=2026-10-03&latitude=17.3850&longitude=78.4867&timezone=Asia/Kolkata
```

## Startup

This is a dependency-free Node.js package utilizing core modules (`http`, `url`).

```bash
# Start the server (Defaults to Port 3000)
npm start

# Custom Port
PORT=8080 npm start
```

## Current Calculation Methodology

The engine calculates core astronomical phenomena locally based on algorithms derived from Jean Meeus' "Astronomical Algorithms" (2nd Edition). The engine processes Tithi, Nakshatra, Yoga, Karana, Vara, Sunrise, and Sunset directly via native JavaScript math, relying heavily on Julian Day epoch transformations and Ecliptic Longitudes.

## Known Limitations

- **Astronomical Accuracy:** Calculations are mathematical approximations suitable for standard calendar generation. They are NOT certified by authoritative astronomical observatories and should not be used where high-precision nautical or scientific accuracy is required.
- **Dependency Free:** The engine intentionally avoids external astrology/astronomy NPM packages to prevent licensing conflicts. As a result, certain highly advanced planetary retrogrades are not tracked.
