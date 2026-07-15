import apiClient from '../api/client';
import { BaseCrudService } from './BaseCrudService';

class MaterialModelService extends BaseCrudService {
  constructor() {
    super('/material-models');
  }

  /**
   * Fetch material models filtered by material type ID.
   * Used for dependent dropdowns in Material form.
   */
  async getByMaterialType(materialTypeId) {
    const { data } = await apiClient.get(
      `${this.basePath}/by-material-type/${materialTypeId}`
    );
    return data;
  }
}

export const materialModelService = new MaterialModelService();
