import { useState, useCallback } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { parseApiError, toFormErrors } from '../utils/errorParser';
import { toast } from 'sonner';

/**
 * useFormSubmit — Generic form submission hook.
 *
 * Features:
 *   - Loading state
 *   - Validation error mapping
 *   - Success/error toast
 *   - Auto-invalidate query cache
 *   - Success callback
 *
 * Usage:
 *   const form = useFormSubmit({
 *     service: userService,
 *     method: 'create',   // 'create' | 'update'
 *     id: editingId,       // required for update
 *     queryKey: 'users',   // query key to invalidate on success
 *     onSuccess: () => navigate('/users'),
 *   });
 *
 *   await form.submit(data);
 *   form.loading -> boolean
 *   form.errors  -> { field: "message" }
 */
export function useFormSubmit({ service, method, id, queryKey, onSuccess }) {
  const queryClient = useQueryClient();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState(null);

  const submit = useCallback(async (formData) => {
    setLoading(true);
    setErrors(null);

    try {
      if (method === 'update') {
        await service.update(id, formData);
        toast.success('Data berhasil diperbarui');
      } else {
        await service.create(formData);
        toast.success('Data berhasil ditambahkan');
      }

      // Invalidate related queries to refresh lists
      if (queryKey) {
        queryClient.invalidateQueries({ queryKey: [queryKey] });
      }

      onSuccess?.();
      return true;
    } catch (err) {
      const parsed = parseApiError(err);

      if (parsed.hasValidationErrors) {
        setErrors(toFormErrors(parsed.errors));
      } else {
        toast.error(parsed.message);
      }

      return false;
    } finally {
      setLoading(false);
    }
  }, [service, method, id, queryKey, onSuccess, queryClient]);

  const resetErrors = useCallback(() => {
    setErrors(null);
  }, []);

  return {
    submit,
    loading,
    errors,
    resetErrors,
  };
}
