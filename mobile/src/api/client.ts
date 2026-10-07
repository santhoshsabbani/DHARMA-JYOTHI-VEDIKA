// e:\Ai\DHARMA JYOTHI VEDIKA\mobile\src\api\client.ts

// The WordPress API base URL must come from environment configuration.
// Hardcoded fallback to the production REST API for now if env not set
const BASE_URL = process.env.EXPO_PUBLIC_API_URL || 'https://violet-giraffe-464585.hostingersite.com/wordpress/wp-json/djv/v1';

export async function fetchApi<T>(endpoint: string, params: Record<string, string | number> = {}): Promise<{ success: boolean; data?: T; error?: any; meta?: any }> {
  try {
    const url = new URL(`${BASE_URL}${endpoint}`);
    Object.entries(params).forEach(([key, value]) => {
      url.searchParams.append(key, value.toString());
    });

    const response = await fetch(url.toString(), {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    });

    const data = await response.json();

    if (!response.ok || data.success === false) {
      return { success: false, error: data.error || { message: 'API request failed' } };
    }

    return { success: true, data: data.data, meta: data.meta };
  } catch (error) {
    console.error('API Error:', error);
    return { success: false, error: { message: 'Network error or invalid response' } };
  }
}
