import { fetchApi } from './client';

export const fetchMantras = async (deity?: string, language?: string) => {
  const params: any = {};
  if (deity) params.deity = deity;
  if (language) params.language = language;
  return fetchApi<any>('/mantras', params);
};
