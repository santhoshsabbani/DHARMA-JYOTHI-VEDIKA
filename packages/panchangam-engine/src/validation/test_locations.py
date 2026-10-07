"""
Multi-City and Edge-Case Astronomical Validation Script for DJV Panchangam Engine
Compares DJV against PyEphem across 10 global locations and specific boundary/no-event cases.
"""

import sys
import json
import subprocess
from datetime import datetime, timezone, timedelta
import zoneinfo
import ephem

NODE_SCRIPT = """
const core = require('./src/core.js');
const args = process.argv.slice(1);
const [year, month, day, lat, lon, tz] = args;
const res = core.calculatePanchangam({
  year: parseInt(year),
  month: parseInt(month),
  day: parseInt(day),
  lat: parseFloat(lat),
  lon: parseFloat(lon),
  timezone: tz
});
process.stdout.write(JSON.stringify({
  moonrise: res.moonrise,
  moonset: res.moonset
}));
"""

def get_djv_times(year, month, day, lat, lon, tz_name):
    cmd = ['node', '-e', NODE_SCRIPT, str(year), str(month), str(day), str(lat), str(lon), tz_name]
    proc = subprocess.run(cmd, capture_output=True, text=True, cwd='packages/panchangam-engine')
    if proc.returncode != 0:
        raise RuntimeError(f"Node error: {proc.stderr}")
    return json.loads(proc.stdout)

def get_ephem_times(year, month, day, lat, lon, tz_name):
    tz = zoneinfo.ZoneInfo(tz_name)
    local_midnight = datetime(year, month, day, 0, 0, 0, tzinfo=tz)
    local_next_midnight = local_midnight + timedelta(days=1)
    
    obs = ephem.Observer()
    obs.lat = str(lat)
    obs.lon = str(lon)
    obs.elevation = 0
    obs.pressure = 1010
    obs.horizon = '0'  # PyEphem standard horizon: includes semi-diameter & atmospheric refraction
    
    moon = ephem.Moon()
    
    utc_start = local_midnight.astimezone(timezone.utc)
    utc_end = local_next_midnight.astimezone(timezone.utc)
    
    obs.date = ephem.Date(utc_start)
    rise_dt = None
    try:
        next_r = obs.next_rising(moon)
        r_utc = next_r.datetime().replace(tzinfo=timezone.utc)
        r_local = r_utc.astimezone(tz)
        if local_midnight <= r_local < local_next_midnight:
            rise_dt = r_local
    except (ephem.AlwaysUpError, ephem.NeverUpError):
        pass
        
    obs.date = ephem.Date(utc_start)
    set_dt = None
    try:
        next_s = obs.next_setting(moon)
        s_utc = next_s.datetime().replace(tzinfo=timezone.utc)
        s_local = s_utc.astimezone(tz)
        if local_midnight <= s_local < local_next_midnight:
            set_dt = s_local
    except (ephem.AlwaysUpError, ephem.NeverUpError):
        pass

    return {
        'moonrise': rise_dt,
        'moonset': set_dt
    }

def time_diff_minutes(dt_str, ephem_dt):
    if not dt_str and not ephem_dt:
        return 0.0  # both agree on no_event
    if (not dt_str and ephem_dt) or (dt_str and not ephem_dt):
        return -999.0  # discrepancy in event presence!
    djv_dt = datetime.fromisoformat(dt_str)
    diff_sec = abs((djv_dt - ephem_dt).total_seconds())
    return diff_sec / 60.0

def run_location_tests():
    locations = [
        {"name": "Hyderabad", "lat": 17.3850, "lon": 78.4867, "tz": "Asia/Kolkata"},
        {"name": "Delhi", "lat": 28.6139, "lon": 77.2090, "tz": "Asia/Kolkata"},
        {"name": "Mumbai", "lat": 19.0760, "lon": 72.8777, "tz": "Asia/Kolkata"},
        {"name": "Chennai", "lat": 13.0827, "lon": 80.2707, "tz": "Asia/Kolkata"},
        {"name": "Bengaluru", "lat": 12.9716, "lon": 77.5946, "tz": "Asia/Kolkata"},
        {"name": "Kolkata", "lat": 22.5726, "lon": 88.3639, "tz": "Asia/Kolkata"},
        {"name": "New York", "lat": 40.7128, "lon": -74.0060, "tz": "America/New_York"},
        {"name": "London", "lat": 51.5074, "lon": -0.1278, "tz": "Europe/London"},
        {"name": "Sydney", "lat": -33.8688, "lon": 151.2093, "tz": "Australia/Sydney"},
        {"name": "Tromso", "lat": 69.6492, "lon": 18.9553, "tz": "Europe/Oslo"},
    ]
    
    # Test dates across October 2026: 1, 3, 4, 5, 10, 15, 18, 19, 20, 25, 31
    sample_days = [1, 3, 4, 5, 10, 15, 18, 19, 20, 25, 31]
    
    print("=" * 85)
    print(" MULTI-LOCATION VALIDATION ACROSS 10 CITIES")
    print("=" * 85)
    
    total_checks = 0
    mismatches = 0
    all_diffs_r = []
    all_diffs_s = []
    
    for loc in locations:
        loc_diffs_r = []
        loc_diffs_s = []
        print(f"\n--- Location: {loc['name']} ({loc['tz']}) [Lat: {loc['lat']}, Lon: {loc['lon']}] ---")
        for day in sample_days:
            date_str = f"2026-10-{day:02d}"
            djv = get_djv_times(2026, 10, day, loc['lat'], loc['lon'], loc['tz'])
            ref = get_ephem_times(2026, 10, day, loc['lat'], loc['lon'], loc['tz'])
            
            # API structure check
            assert 'time' in djv['moonrise'] and 'datetime' in djv['moonrise'] and 'status' in djv['moonrise'], "Moonrise API struct invalid"
            assert 'time' in djv['moonset'] and 'datetime' in djv['moonset'] and 'status' in djv['moonset'], "Moonset API struct invalid"
            
            djv_r_dt = djv['moonrise']['datetime'] if djv['moonrise']['status'] == 'normal' else None
            djv_s_dt = djv['moonset']['datetime'] if djv['moonset']['status'] == 'normal' else None
            
            diff_r = time_diff_minutes(djv_r_dt, ref['moonrise'])
            diff_s = time_diff_minutes(djv_s_dt, ref['moonset'])
            
            r_str = f"{diff_r:.2f}m" if diff_r >= 0 else "EVENT_MISMATCH"
            s_str = f"{diff_s:.2f}m" if diff_s >= 0 else "EVENT_MISMATCH"
            
            djv_r_t = djv['moonrise']['time'] or 'NO_EVENT'
            ref_r_t = ref['moonrise'].strftime('%I:%M %p').lstrip('0') if ref['moonrise'] else 'NO_EVENT'
            djv_s_t = djv['moonset']['time'] or 'NO_EVENT'
            ref_s_t = ref['moonset'].strftime('%I:%M %p').lstrip('0') if ref['moonset'] else 'NO_EVENT'
            
            print(f"  {date_str} | Rise: DJV {djv_r_t:<9} Ref {ref_r_t:<9} ({r_str:<6}) | Set: DJV {djv_s_t:<9} Ref {ref_s_t:<9} ({s_str})")
            
            total_checks += 2
            if diff_r < 0 or diff_s < 0:
                mismatches += 1
            if diff_r >= 0 and djv_r_dt:
                loc_diffs_r.append(diff_r)
                all_diffs_r.append(diff_r)
            if diff_s >= 0 and djv_s_dt:
                loc_diffs_s.append(diff_s)
                all_diffs_s.append(diff_s)
                
        avg_r = sum(loc_diffs_r)/len(loc_diffs_r) if loc_diffs_r else 0
        max_r = max(loc_diffs_r) if loc_diffs_r else 0
        avg_s = sum(loc_diffs_s)/len(loc_diffs_s) if loc_diffs_s else 0
        max_s = max(loc_diffs_s) if loc_diffs_s else 0
        print(f"  Summary for {loc['name']}: Rise Avg {avg_r:.2f}m, Max {max_r:.2f}m | Set Avg {avg_s:.2f}m, Max {max_s:.2f}m")

    print("\n" + "=" * 85)
    print(" OVERALL MULTI-LOCATION SUMMARY")
    print(f" Total checks: {total_checks}, Mismatches: {mismatches}")
    print(f" Moonrise Overall Avg Diff: {sum(all_diffs_r)/len(all_diffs_r):.2f} min, Max: {max(all_diffs_r):.2f} min")
    print(f" Moonset  Overall Avg Diff: {sum(all_diffs_s)/len(all_diffs_s):.2f} min, Max: {max(all_diffs_s):.2f} min")
    print("=" * 85)

if __name__ == '__main__':
    run_location_tests()
