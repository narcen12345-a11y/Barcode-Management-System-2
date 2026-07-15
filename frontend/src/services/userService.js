import apiClient from '../api/client';
import { BaseCrudService } from './BaseCrudService';

class UserService extends BaseCrudService {
  constructor() {
    super('/users');
  }

  async verify(id, payload) {
    const { data } = await apiClient.post(`${this.basePath}/${id}/verify`, payload);
    return data;
  }

  async activate(id) {
    const { data } = await apiClient.post(`${this.basePath}/${id}/activate`);
    return data;
  }

  async deactivate(id) {
    const { data } = await apiClient.post(`${this.basePath}/${id}/deactivate`);
    return data;
  }

  async resetPassword(id) {
    const { data } = await apiClient.post(`${this.basePath}/${id}/reset-password`);
    return data;
  }
}

export const userService = new UserService();
