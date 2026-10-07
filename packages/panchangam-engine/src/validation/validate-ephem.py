"""
DJV Panchangam Engine vs PyEphem Reference Validation Script
Validates Moonrise and Moonset calculations against PyEphem (XEphem / ELP-2000/82)
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
    
    # Observer
    obs = ephem.Observer()
    obs.lat = str(lat)
    obs.lon = str(lon)
    obs.elevation = 0
    obs.pressure = 1010
    obs.horizon = '0'  # default ephem rise/set accounts for semidiameter and refraction
    
    # We find all risings and settings in [local_midnight, local_next_midnight)
    moon = ephem.Moon()
    
    # Convert local midnight to UTC ephem Date
    utc_start = local_midnight.astimezone(timezone.utc)
    utc_end = local_next_midnight.astimezone(timezone.utc)
    
    obs.date = ephem.Date(utc_start)
    
    # Find next rising
    rise_dt = None
    try:
        next_r = obs.next_rising(moon)
        # convert to datetime UTC
        r_utc = next_r.datetime().replace(tzinfo=timezone.utc)
        r_local = r_utc.astimezone(tz)
        if local_midnight <= r_local < local_next_midnight:
            rise_dt = r_local
    except (ephem.AlwaysUpError, ephem.NeverUpError):
        pass
        
    # Find next setting
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
    # dt_str format: "2026-10-06T01:56:08+05:30"
    if not dt_str and not ephem_dt:
        return 0  # both no_event
    if (not dt_str and ephem_dt) or (dt_str and not ephem_dt):
        return None  # mismatch in event occurrence!
    djv_dt = datetime.fromisoformat(dt_str)
    diff_sec = abs((djv_dt - ephem_dt).total_seconds())
    return diff_sec / 60.0

def main():
    print("=" * 80)
    print(" DJV PANCHANGAM ENGINE — ASTRONOMICAL VALIDATION (OCTOBER 2026)")
    print(" Independent Reference: PyEphem (XEphem / ELP-2000/82 ephemeris)")
    print("=" * 80)
    
    lat = 17.3850
    lon = 78.4867
    tz_name = 'Asia/Kolkata'
    
    rows = []
    rise_diffs = []
    set_diffs = []
    large_discrepancies = []
    
    for day in range(1, 32):
        date_str = f"2026-10-{day:02d}"
        djv = get_djv_times(2026, 10, day, lat, lon, tz_name)
        ref = get_ephem_times(2026, 10, day, lat, lon, tz_name)
        
        djv_rise = djv['moonrise']['time'] if djv['moonrise']['status'] == 'normal' else 'NO_EVENT'
        djv_set  = djv['moonset']['time'] if djv['moonset']['status'] == 'normal' else 'NO_EVENT'
        
        ref_rise = ref['moonrise'].strftime('%I:%M %p').lstrip('0') if ref['moonrise'] else 'NO_EVENT'
        ref_set  = ref['moonset'].strftime('%I:%M %p').lstrip('0') if ref['moonset'] else 'NO_EVENT'
        
        diff_r = time_diff_minutes(djv['moonrise']['datetime'], ref['moonrise'])
        diff_s = time_diff_minutes(djv['moonset']['datetime'], ref['moonset'])
        
        if diff_r is not None:
            rise_diffs.append(diff_r)
            if diff_r > 3.0:
                large_discrepancies.append((date_str, 'Moonrise', djv_rise, ref_rise, diff_r))
        if diff_s is not None:
            set_diffs.append(diff_s)
            if diff_s > 3.0:
                large_discrepancies.append((date_str, 'Moonset', djv_set, ref_set, diff_s))
                
        r_str = f"{diff_r:.1f}m" if diff_r is not None else "MISMATCH"
        s_str = f"{diff_s:.1f}m" if diff_s is not None else "MISMATCH"
        
        rows.append({
            'date': date_str,
            'djv_rise': djv_rise,
            'ref_rise': ref_rise,
            'diff_rise': r_str,
            'djv_set': djv_set,
            'ref_set': ref_set,
            'diff_set': s_str
        })
        print(f"{date_str} | Rise: DJV {djv_rise:<9} Ref {ref_rise:<9} Diff: {r_str:<6} | Set: DJV {djv_set:<9} Ref {ref_set:<9} Diff: {s_str}")

    avg_rise = sum(rise_diffs) / len(rise_diffs) if rise_diffs else 0
    max_rise = max(rise_diffs) if rise_diffs else 0
    avg_set  = sum(set_diffs) / len(set_diffs) if set_diffs else 0
    max_set  = max(set_diffs) if set_diffs else 0
    
    print("-" * 80)
    print(f"Moonrise Average Difference: {avg_rise:.2f} minutes")
    print(f"Moonrise Maximum Difference: {max_rise:.2f} minutes")
    print(f"Moonset  Average Difference: {avg_set:.2f} minutes")
    print(f"Moonset  Maximum Difference: {max_set:.2f} minutes")
    print(f"Discrepancies > 3 min: {len(large_discrepancies)}")
    for d in large_discrepancies:
        print(f"  - {d[0]} {d[1]}: DJV {d[2]} vs Ref {d[3]} ({d[4]:.2f} min)")

if __name__ == '__main__':
    main()
