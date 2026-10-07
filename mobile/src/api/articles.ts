import { fetchApi } from './client';

export const fetchArticles = async (category?: string) => {
  const params: any = {};
  if (category) params.category = category;
  return fetchApi<any>('/articles', params);
};
