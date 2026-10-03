'use strict';

const http = require('http');
const url = require('url');
const { calculatePanchangam } = require('./src/core.js');

const PORT = process.env.PORT || 3000;

const server = http.createServer((req, res) => {
  const parsedUrl = url.parse(req.url, true);
  const pathname = parsedUrl.pathname;
  const query = parsedUrl.query;

  // CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');

  if (pathname === '/health') {
    res.writeHead(200);
    return res.end(JSON.stringify({
      success: true,
      service: "djv-panchangam-engine",
      status: "healthy",
      version: "1.0.0"
    }));
  }

  if (pathname === '/panchangam') {
    const dateStr = query.date;
    const latStr = query.latitude || query.lat;
    const lonStr = query.longitude || query.lon;
    const timezone = query.timezone || query.tz;
    const region = query.region || 'telugu';
    const ayanamsa = query.ayanamsa || 'lahiri';
    const language = query.language || 'en';

    if (!dateStr || !latStr || !lonStr || !timezone) {
      res.writeHead(400);
      return res.end(JSON.stringify({
        success: false,
        error: {
          code: "invalid_parameter",
          message: "date, latitude, longitude, and timezone are required"
        }
      }));
    }

    const dateMatch = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!dateMatch) {
      res.writeHead(400);
      return res.end(JSON.stringify({
        success: false,
        error: {
          code: "invalid_parameter",
          message: "Invalid date format. Use YYYY-MM-DD."
        }
      }));
    }

    const year = parseInt(dateMatch[1], 10);
    const month = parseInt(dateMatch[2], 10);
    const day = parseInt(dateMatch[3], 10);
    const lat = parseFloat(latStr);
    const lon = parseFloat(lonStr);

    if (isNaN(lat) || lat < -90 || lat > 90) {
      res.writeHead(400);
      return res.end(JSON.stringify({
        success: false,
        error: { code: "invalid_parameter", message: "Invalid latitude. Must be between -90 and 90." }
      }));
    }
    if (isNaN(lon) || lon < -180 || lon > 180) {
      res.writeHead(400);
      return res.end(JSON.stringify({
        success: false,
        error: { code: "invalid_parameter", message: "Invalid longitude. Must be between -180 and 180." }
      }));
    }

    try {
      new Intl.DateTimeFormat('en-US', { timeZone: timezone });
    } catch (e) {
      res.writeHead(400);
      return res.end(JSON.stringify({
        success: false,
        error: { code: "invalid_parameter", message: "Invalid timezone identifier." }
      }));
    }

    try {
      const result = calculatePanchangam({ year, month, day, lat, lon, timezone, region, ayanamsa, language });
      res.writeHead(200);
      return res.end(JSON.stringify({
        success: true,
        data: result,
        meta: {
          date: dateStr,
          latitude: lat,
          longitude: lon,
          timezone: timezone,
          region: region,
          ayanamsa: ayanamsa,
          engine_version: "1.0.0"
        }
      }));
    } catch (err) {
      res.writeHead(500);
      return res.end(JSON.stringify({
        success: false,
        error: {
          code: "calculation_failed",
          message: "Panchangam calculation failed"
        }
      }));
    }
  }

  res.writeHead(404);
  return res.end(JSON.stringify({
    success: false,
    error: {
      code: "not_found",
      message: "Available endpoints: /health, /panchangam"
    }
  }));
});

server.listen(PORT, '0.0.0.0', () => {
  console.log(`DJV Panchangam Engine running at http://0.0.0.0:${PORT}`);
});
