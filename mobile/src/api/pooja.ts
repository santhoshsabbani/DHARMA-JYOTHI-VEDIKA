import { fetchApi } from './client';

export const fetchPoojaList = async () => {
  return fetchApi<any>('/pooja');
};

export const fetchPoojaSingle = async (slug: string) => {
  return fetchApi<any>(`/pooja/${slug}`);
};
