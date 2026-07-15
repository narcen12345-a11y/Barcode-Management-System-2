# QA Sprint 003 — Fix Runtime Audit Findings

**Task ID:** QA-SPRINT-003  
**Date:** 2026-07-15  
**Status:** ✅ COMPLETED  
**Build:** ✅ SUCCESS (2037 modules, 0 errors, 4.56s)

---

## Summary

| Bug | Severity | Status | Files Modified |
|-----|----------|--------|----------------|
| BUG 1 — Query parameter naming | 🔴 HIGH | ✅ FIXED (already correct, documented) | 1 |
| BUG 2 — Dashboard dynamic Tailwind classes | 🟡 MEDIUM | ✅ FIXED | 1 |
| BUG 3 — Global Error Boundary | 🟢 LOW | ✅ FIXED | 2 |
| BUG 4 — Lazy loading / code-splitting | 🟢 LOW | ✅ FIXED | 1 |

---

## BUG 1 — Query Parameter Naming

### Investigation

After thorough investigation, the `queryBuilder.js` file **already correctly maps** camelCase frontend parameters to snake_case backend parameters:

```js
// queryBuilder.js (existing code — already correct)
if (perPage) params.per_page = perPage;    // ✅ perPage → per_page
if (sortBy) params.sort_by = sortBy;        // ✅ sortBy → sort_by
if (sortOrder) params.sort_order = sortOrder; // ✅ sortOrder → sort_order
```

The `useCrud` hook passes camelCase names (`perPage`, `sortBy`, `sortOrder`) to `buildQueryParams()`, which correctly converts them to snake_case before sending to the backend.

### Files Modified

| File | Change |
|------|--------|
| `frontend/src/utils/queryBuilder.js` | Added explicit documentation comments explaining the camelCase → snake_case mapping for future developers |

### Compatibility Impact

✅ None — no logic changed, only comments added.

### Regression Risk

✅ None.

---

## BUG 2 — Dashboard Dynamic Tailwind Classes

### Problem

The Dashboard used template literals to generate Tailwind class names dynamically:
```jsx
<p className={`mt-2 text-3xl font-semibold text-${stat.color}-400`}>
```

Tailwind CSS uses a build-time purging mechanism. Dynamic class names like `text-cyan-400` generated via string interpolation are **not detected** during the build and will not have their CSS included in the production bundle.

### Fix

Replaced dynamic class generation with a static color map:

```jsx
const colorMap = {
  cyan: 'text-cyan-400',
  blue: 'text-blue-400',
  purple: 'text-purple-400',
  emerald: 'text-emerald-400',
};

// Usage:
<p className={`mt-2 text-3xl font-semibold ${colorMap[stat.color] || 'text-slate-400'}`}>
```

### Files Modified

| File | Change |
|------|--------|
| `frontend/src/pages/DashboardPage.jsx` | Replaced dynamic `text-${color}-400` with static `colorMap` lookup |

### Compatibility Impact

✅ None — visual output is identical.

### Regression Risk

✅ Low — only affects Dashboard stat card colors. Falls back to `text-slate-400` if an unknown color is used.

---

## BUG 3 — Global React Error Boundary

### Problem

The application had no error boundary to catch render errors. If any component threw an error during rendering, the entire React app would unmount and show a blank white screen.

### Fix

1. **Converted `GlobalError` from a functional component to a class-based Error Boundary** (`Component` + `getDerivedStateFromError` + `componentDidCatch`)
2. **Added error recovery UI** with:
   - "Coba Lagi" (Reload) button — resets error state
   - "Kembali ke Dashboard" button — navigates to `/`
   - Error details logged to console
3. **Wrapped the entire app** at the root level in `main.jsx`

### Files Modified

| File | Change |
|------|--------|
| `frontend/src/components/GlobalError.jsx` | Converted to class-based Error Boundary with `getDerivedStateFromError`, `componentDidCatch`, reload and dashboard buttons |
| `frontend/src/main.jsx` | Wrapped `<QueryClientProvider>` + `<AuthProvider>` + `<RouterProvider>` with `<GlobalError>` |

### Compatibility Impact

✅ None — error boundary is transparent during normal operation. Only activates when a render error occurs.

### Regression Risk

✅ Low — error boundary only catches errors; does not affect normal rendering.

---

## BUG 4 — Lazy Loading / Code-Splitting

### Problem

All page components were eagerly imported in `routes/index.jsx`, resulting in a single 563 kB JavaScript bundle. Every page was loaded upfront, even if the user never visited it.

### Fix

1. **Replaced all eager imports** with `React.lazy()` dynamic imports
2. **Added `SuspenseWrapper` component** that renders `<Loading />` as fallback while chunks load
3. **Wrapped every route element** with `<SuspenseWrapper>`

### Files Modified

| File | Change |
|------|--------|
| `frontend/src/routes/index.jsx` | Replaced all `import X from '...'` with `const X = lazy(() => import('...'))`; added `SuspenseWrapper`; wrapped all route elements |

### Build Output (Before vs After)

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Main JS bundle | 563 kB | 368.85 kB | **-34%** |
| Number of chunks | 1 | 37 | Code-split |
| Build time | 5.73s | 4.56s | **-20%** |
| Gzip main bundle | 165 kB | 119 kB | **-28%** |

### Chunks Created

| Chunk | Size | Gzip |
|-------|------|------|
| `index-DUG74Kpf.js` (main) | 368.85 kB | 119.09 kB |
| `DashboardPage-B8O3h59I.js` | 1.31 kB | 0.63 kB |
| `SiteListPage-DeJnvOrW.js` | 1.89 kB | 0.97 kB |
| `SiteFormPage-C7k8zUp0.js` | 6.37 kB | 1.68 kB |
| `MaterialTypeListPage-x6HSxm5w.js` | 1.95 kB | 0.96 kB |
| `MaterialTypeFormPage-ComSkfyt.js` | 3.54 kB | 1.36 kB |
| `MaterialModelListPage-BwmtwqqU.js` | 2.09 kB | 1.03 kB |
| `MaterialModelFormPage-8EwxbK4d.js` | 4.82 kB | 1.60 kB |
| `MaterialListPage-CkTfhvJb.js` | 2.17 kB | 1.04 kB |
| `MaterialFormPage-BubVMYtM.js` | 6.96 kB | 1.86 kB |
| `PermissionListPage-BdgeIynT.js` | 2.02 kB | 0.98 kB |
| `PermissionFormPage-x7YOaR64.js` | 5.10 kB | 1.52 kB |
| `RoleListPage-wrpuhjW1.js` | 2.32 kB | 1.14 kB |
| `RoleFormPage-BY_xYfN4.js` | 6.01 kB | 1.94 kB |
| `UserListPage-DHHTP1YW.js` | 4.20 kB | 1.73 kB |
| `UserFormPage-BRKGiZPA.js` | 7.35 kB | 2.09 kB |
| `BarcodeListPage-C11SADP7.js` | 2.87 kB | 1.31 kB |
| `BarcodeFormPage-D2gaXbQZ.js` | 6.58 kB | 1.82 kB |
| `BarcodeDetailPage-Dw904DkJ.js` | 5.95 kB | 2.10 kB |
| `LoginPage-DPJ1DYSI.js` | 2.26 kB | 0.94 kB |
| `NotFoundPage-BlHusy0u.js` | 0.76 kB | 0.43 kB |

### Compatibility Impact

✅ None — all routes and functionality remain identical. The only difference is that pages are now loaded on-demand instead of upfront.

### Regression Risk

✅ Low — `React.lazy()` and `Suspense` are stable React 18 features. The `<Loading />` fallback ensures users see a loading indicator while chunks load.

---

## Build Verification

```
vite v5.4.21 building for production...
✓ 2037 modules transformed.
✓ built in 4.56s
```

**Build Result:** ✅ SUCCESS — 0 errors, 0 warnings.

---

## Files Modified (Summary)

| # | File | Bug | Reason |
|---|------|-----|--------|
| 1 | `frontend/src/utils/queryBuilder.js` | BUG 1 | Added documentation for parameter mapping |
| 2 | `frontend/src/pages/DashboardPage.jsx` | BUG 2 | Replaced dynamic Tailwind classes with static color map |
| 3 | `frontend/src/components/GlobalError.jsx` | BUG 3 | Converted to class-based Error Boundary |
| 4 | `frontend/src/main.jsx` | BUG 3 | Wrapped app with `<GlobalError>` |
| 5 | `frontend/src/routes/index.jsx` | BUG 4 | Implemented lazy loading with `React.lazy()` + `Suspense` |

---

*Report generated after QA Sprint 003 completion.*
