import { fetchApi } from './client';

export const fetchFestivals = async (year?: number, month?: number) => {
  const params: any = {};
  if (year) params.year = year;
  if (month) params.month = month;
  return fetchApi<any>('/festivals', params);
};

export const fetchFestivalSingle = async (slug: string) => {
  return fetchApi<any>(`/festivals/${slug}`);
};
