import apiClient from '../api/client';

export const authService = {
  async login(credentials) {
    const { data } = await apiClient.post('/login', credentials);
    return data;
  },

  async logout() {
    const { data } = await apiClient.post('/logout');
    return data;
  },

  async me() {
    const { data } = await apiClient.get('/me');
    return data;
  },

  async changePassword(payload) {
    const { data } = await apiClient.post('/change-password', payload);
    return data;
  },
};
