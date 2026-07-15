/**
 * Permission helper — generates permission strings for CRUD operations.
 *
 * Backend convention:
 *   create-{resource}
 *   read-{resource}
 *   update-{resource}
 *   delete-{resource}
 */
export const perm = (action, resource) => `${action}-${resource}`;

export const permissions = {
  create: (resource) => `create-${resource}`,
  read: (resource) => `read-${resource}`,
  update: (resource) => `update-${resource}`,
  delete: (resource) => `delete-${resource}`,
};

/**
 * Predefined resource keys matching backend permission names.
 */
export const Resources = {
  USER: 'user',
  ROLE: 'role',
  PERMISSION: 'permission',
  SITE: 'site',
  MATERIAL_TYPE: 'material-type',
  MATERIAL_MODEL: 'material-model',
  MATERIAL: 'material',
  BARCODE: 'barcode',
};
