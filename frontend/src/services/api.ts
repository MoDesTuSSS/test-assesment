// API service for connecting to Symfony backend
// Update the BASE_URL to match your Symfony API endpoint

const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';
const BACKEND_URL = BASE_URL.replace('/api', '');

export interface NewsArticle {
  id?: number;
  title: string;
  body: string;
  image: string | File | null;
  createdAt?: string;
  updatedAt?: string;
}

class ApiService {
  private async request(endpoint: string, options: RequestInit = {}) {
    const url = `${BASE_URL}${endpoint}`;
    
    try {
      const response = await fetch(url, {
        ...options,
        headers: {
          'Content-Type': 'application/json',
          ...options.headers,
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error('API request failed:', error);
      throw error;
    }
  }

  private async uploadImage(file: File): Promise<string> {
    const formData = new FormData();
    formData.append('image', file);

    try {
      const response = await fetch(`${BASE_URL}/news/upload`, {
        method: 'POST',
        body: formData,
        // Don't set Content-Type header - browser will set it with boundary
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Image upload failed');
      }

      const data = await response.json();
      return data.imageUrl;
    } catch (error) {
      console.error('Image upload failed:', error);
      throw error;
    }
  }

  // News CRUD operations
  async getNews(): Promise<NewsArticle[]> {
    return this.request('/news');
  }

  async getNewsById(id: number): Promise<NewsArticle> {
    return this.request(`/news/${id}`);
  }

  async createNews(article: NewsArticle): Promise<NewsArticle> {
    let imageUrl = article.image;

    // If image is a File, upload it first
    if (article.image instanceof File) {
      imageUrl = await this.uploadImage(article.image);
    }

    return this.request('/news', {
      method: 'POST',
      body: JSON.stringify({
        ...article,
        image: imageUrl,
      }),
    });
  }

  async updateNews(id: number, article: Partial<NewsArticle>): Promise<NewsArticle> {
    let imageUrl = article.image;

    // If image is a File, upload it first
    if (article.image instanceof File) {
      imageUrl = await this.uploadImage(article.image);
    }

    return this.request(`/news/${id}`, {
      method: 'PUT',
      body: JSON.stringify({
        ...article,
        image: imageUrl,
      }),
    });
  }

  async deleteNews(id: number): Promise<void> {
    return this.request(`/news/${id}`, {
      method: 'DELETE',
    });
  }

  // Get full image URL
  getImageUrl(imagePath: string | null): string {
    if (!imagePath) return '';
    
    // If it's already a full URL, return as is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }
    
    // If it's a relative path, prepend backend URL
    return `${BACKEND_URL}${imagePath.startsWith('/') ? '' : '/'}${imagePath}`;
  }
}

export const apiService = new ApiService();
