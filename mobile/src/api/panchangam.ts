// e:\Ai\DHARMA JYOTHI VEDIKA\mobile\src\api\panchangam.ts
import { fetchApi } from './client';

export interface PanchangamParams {
  date?: string; // YYYY-MM-DD
  latitude?: number;
  longitude?: number;
  timezone?: string; // IANA timezone
}

export const fetchTodayPanchangam = async (params: PanchangamParams = {}) => {
  const defaultParams = {
    latitude: 17.3850,
    longitude: 78.4867,
    timezone: 'Asia/Kolkata',
    ...params
  };
  return fetchApi<any>('/today', defaultParams);
};

export const fetchPanchangam = async (params: PanchangamParams = {}) => {
  const defaultParams = {
    latitude: 17.3850,
    longitude: 78.4867,
    timezone: 'Asia/Kolkata',
    ...params
  };
  return fetchApi<any>('/panchangam', defaultParams);
};
