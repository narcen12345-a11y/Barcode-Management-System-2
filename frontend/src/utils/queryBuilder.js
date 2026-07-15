/**
 * Build query parameters for API requests.
 * Strips out null/undefined/empty values automatically.
 */
export function buildQueryParams({ page, perPage, search, sortBy, sortOrder, filters }) {
  const params = {};

  if (page) params.page = page;
  if (perPage) params.per_page = perPage;
  if (search) params.search = search;
  if (sortBy) params.sort_by = sortBy;
  if (sortOrder) params.sort_order = sortOrder;

  // Merge additional filters
  if (filters && typeof filters === 'object') {
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        params[key] = value;
      }
    });
  }

  return params;
}
