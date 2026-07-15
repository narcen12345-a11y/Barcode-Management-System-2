/**
 * Build query parameters for API requests.
 * Strips out null/undefined/empty values automatically.
 *
 * IMPORTANT: Maps camelCase frontend params to snake_case backend params.
 * Backend controllers read:
 *   - per_page  (not perPage)
 *   - sort_by   (not sortBy)
 *   - sort_order (not sortOrder)
 *   - page
 *   - search
 *   - filter keys (e.g. is_active, site_id, material_type_id, etc.)
 */
export function buildQueryParams({ page, perPage, search, sortBy, sortOrder, filters }) {
  const params = {};

  if (page) params.page = page;
  if (perPage) params.per_page = perPage;
  if (search) params.search = search;
  if (sortBy) params.sort_by = sortBy;
  if (sortOrder) params.sort_order = sortOrder;

  // Merge additional filters — keys are passed as-is (must match backend expectations)
  if (filters && typeof filters === 'object') {
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        params[key] = value;
      }
    });
  }

  return params;
}
