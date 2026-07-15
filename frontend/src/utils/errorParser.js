/**
 * Parse API error response into a structured format.
 *
 * Backend Laravel errors come in two shapes:
 *   { message: "...", errors: { field: ["..."] } }
 *   { message: "..." }
 */
export function parseApiError(error) {
  // Already normalized by axios interceptor
  if (error?.message && error?.status !== undefined) {
    return {
      message: error.message || 'Terjadi kesalahan',
      errors: error.errors || null,
      status: error.status || 0,
      hasValidationErrors: !!error.errors,
    };
  }

  // Raw axios error
  if (error?.response) {
    const data = error.response.data;
    return {
      message: data?.message || error.message || 'Terjadi kesalahan',
      errors: data?.errors || null,
      status: error.response.status,
      hasValidationErrors: !!data?.errors,
    };
  }

  // Network or unknown error
  return {
    message: error?.message || 'Terjadi kesalahan jaringan',
    errors: null,
    status: 0,
    hasValidationErrors: false,
  };
}

/**
 * Extract first validation error message for a specific field.
 */
export function getFieldError(errors, field) {
  if (!errors) return null;
  const fieldErrors = errors[field];
  if (Array.isArray(fieldErrors) && fieldErrors.length > 0) {
    return fieldErrors[0];
  }
  return null;
}

/**
 * Convert validation errors object to a flat record for form libraries.
 * { name: ["Name is required"] } -> { name: "Name is required" }
 */
export function toFormErrors(errors) {
  if (!errors) return {};
  const result = {};
  Object.entries(errors).forEach(([key, messages]) => {
    if (Array.isArray(messages) && messages.length > 0) {
      result[key] = messages[0];
    }
  });
  return result;
}
