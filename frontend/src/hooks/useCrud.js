import { useState, useCallback, useRef } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { buildQueryParams } from '../utils/queryBuilder';
import { parseApiError } from '../utils/errorParser';
import { toast } from 'sonner';

/**
 * useCrud — Generic CRUD hook.
 *
 * Features:
 *   - Pagination, search, filters, sorting
 *   - Auto-refresh on mutation
 *   - Loading states
 *   - Delete + restore with toast feedback
 *
 * Usage:
 *   const crud = useCrud(userService, 'users', { perPage: 15 });
 *   crud.data        -> paginated response { data: [...], meta: {...} }
 *   crud.rows        -> array of records
 *   crud.meta        -> pagination meta
 *   crud.loading     -> boolean
 *   crud.setPage(n)  -> change page
 *   crud.setSearch(s)-> search
 *   crud.refresh()   -> refetch
 *   crud.remove(id)  -> delete with confirm
 *   crud.restore(id) -> restore
 */
export function useCrud(service, queryKey, options = {}) {
  const { perPage = 15, defaultSortBy, defaultSortOrder } = options;

  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [filters, setFilters] = useState({});
  const [sortBy, setSortBy] = useState(defaultSortBy);
  const [sortOrder, setSortOrder] = useState(defaultSortOrder);
  const debounceRef = useRef(null);

  // Debounced search to avoid excessive API calls
  const [debouncedSearch, setDebouncedSearch] = useState('');

  const handleSearch = useCallback((value) => {
    setSearch(value);
    setPage(1);
    if (debounceRef.current) clearTimeout(debounceRef.current);
    debounceRef.current = setTimeout(() => {
      setDebouncedSearch(value);
    }, 400);
  }, []);

  const queryParams = buildQueryParams({
    page,
    perPage,
    search: debouncedSearch,
    sortBy,
    sortOrder,
    filters,
  });

  const queryKeyStr = [queryKey, queryParams];

  const { data, isLoading, isFetching, refetch } = useQuery({
    queryKey: queryKeyStr,
    queryFn: () => service.getAll(queryParams),
    placeholderData: (prev) => prev, // keep previous data while fetching
  });

  // Extract rows and meta from various API response shapes
  const rows = data?.data ?? data?.rows ?? [];
  const meta = data?.meta ?? data?.pagination ?? null;

  // Delete mutation
  const deleteMutation = useMutation({
    mutationFn: (id) => service.delete(id),
    onSuccess: () => {
      toast.success('Data berhasil dihapus');
      queryClient.invalidateQueries({ queryKey: [queryKey] });
    },
    onError: (err) => {
      const parsed = parseApiError(err);
      toast.error(parsed.message);
    },
  });

  // Restore mutation
  const restoreMutation = useMutation({
    mutationFn: (id) => service.restore(id),
    onSuccess: () => {
      toast.success('Data berhasil dipulihkan');
      queryClient.invalidateQueries({ queryKey: [queryKey] });
    },
    onError: (err) => {
      const parsed = parseApiError(err);
      toast.error(parsed.message);
    },
  });

  const remove = useCallback((id) => {
    deleteMutation.mutate(id);
  }, [deleteMutation]);

  const restore = useCallback((id) => {
    restoreMutation.mutate(id);
  }, [restoreMutation]);

  const handlePageChange = useCallback((newPage) => {
    setPage(newPage);
  }, []);

  const handleSort = useCallback((column) => {
    setSortBy((prev) => {
      if (prev === column) {
        setSortOrder((o) => (o === 'asc' ? 'desc' : 'asc'));
        return prev;
      }
      setSortOrder('asc');
      return column;
    });
  }, []);

  const handleFilterChange = useCallback((newFilters) => {
    setFilters((prev) => ({ ...prev, ...newFilters }));
    setPage(1);
  }, []);

  const handleResetFilters = useCallback(() => {
    setFilters({});
    setSearch('');
    setDebouncedSearch('');
    setPage(1);
  }, []);

  return {
    // Data
    data,
    rows,
    meta,

    // Loading
    loading: isLoading,
    fetching: isFetching,

    // Pagination
    page,
    setPage: handlePageChange,
    perPage,

    // Search
    search,
    setSearch: handleSearch,

    // Filters
    filters,
    setFilters: handleFilterChange,
    resetFilters: handleResetFilters,

    // Sorting
    sortBy,
    sortOrder,
    setSort: handleSort,

    // Actions
    refresh: refetch,
    remove,
    restore,
    deleting: deleteMutation.isPending,
    restoring: restoreMutation.isPending,
  };
}
