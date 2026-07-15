import apiClient from '../api/client';
import { BaseCrudService } from './BaseCrudService';

class BarcodeService extends BaseCrudService {
  constructor() {
    super('/barcodes');
  }

  async getHistory(id, params = {}) {
    const { data } = await apiClient.get(`${this.basePath}/${id}/history`, { params });
    return data;
  }
}

export const barcodeService = new BarcodeService();
