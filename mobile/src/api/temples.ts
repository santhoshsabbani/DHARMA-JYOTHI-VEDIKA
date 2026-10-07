import { fetchApi } from './client';

export const fetchTemples = async (state?: string, deity?: string) => {
  const params: any = {};
  if (state) params.state = state;
  if (deity) params.deity = deity;
  return fetchApi<any>('/temples', params);
};
