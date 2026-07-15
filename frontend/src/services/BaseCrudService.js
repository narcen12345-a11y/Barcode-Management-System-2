import apiClient from '../api/client';

/**
 * BaseCrudService — Generic CRUD service factory.
 *
 * Usage:
 *   const userService = new BaseCrudService('/users');
 *   await userService.getAll({ page: 1, per_page: 15, search: 'john' });
 *   await userService.getById(1);
 *   await userService.create({ name: 'John' });
 *   await userService.update(1, { name: 'Jane' });
 *   await userService.delete(1);
 *   await userService.restore(1);
 */
export class BaseCrudService {
  constructor(basePath) {
    this.basePath = basePath;
  }

  /**
   * Fetch paginated list with optional filters.
   * @param {Object} params - { page, per_page, search, sort_by, sort_order, ...filters }
   */
  async getAll(params = {}) {
    const { data } = await apiClient.get(this.basePath, { params });
    return data;
  }

  /**
   * Fetch all records (no pagination).
   */
  async getAllUnpaginated() {
    const { data } = await apiClient.get(`${this.basePath}/all`);
    return data;
  }

  /**
   * Fetch single record by ID.
   */
  async getById(id) {
    const { data } = await apiClient.get(`${this.basePath}/${id}`);
    return data;
  }

  /**
   * Create a new record.
   */
  async create(payload) {
    const { data } = await apiClient.post(this.basePath, payload);
    return data;
  }

  /**
   * Update an existing record.
   */
  async update(id, payload) {
    const { data } = await apiClient.put(`${this.basePath}/${id}`, payload);
    return data;
  }

  /**
   * Soft-delete a record.
   */
  async delete(id) {
    const { data } = await apiClient.delete(`${this.basePath}/${id}`);
    return data;
  }

  /**
   * Restore a soft-deleted record.
   */
  async restore(id) {
    const { data } = await apiClient.post(`${this.basePath}/${id}/restore`);
    return data;
  }
}
