import { lazy, Suspense } from 'react';
import { createBrowserRouter } from 'react-router-dom';
import { MainLayout } from '../layouts/MainLayout';
import { ProtectedRoute } from '../components/ProtectedRoute';
import { Loading } from '../components/ui/Loading';

// Lazy-loaded pages — code-split at route level
const LoginPage = lazy(() => import('../pages/LoginPage'));
const DashboardPage = lazy(() => import('../pages/DashboardPage'));
const NotFoundPage = lazy(() => import('../pages/NotFoundPage'));
const SiteListPage = lazy(() => import('../pages/sites/SiteListPage'));
const SiteFormPage = lazy(() => import('../pages/sites/SiteFormPage'));
const MaterialTypeListPage = lazy(() => import('../pages/material-types/MaterialTypeListPage'));
const MaterialTypeFormPage = lazy(() => import('../pages/material-types/MaterialTypeFormPage'));
const MaterialModelListPage = lazy(() => import('../pages/material-models/MaterialModelListPage'));
const MaterialModelFormPage = lazy(() => import('../pages/material-models/MaterialModelFormPage'));
const MaterialListPage = lazy(() => import('../pages/materials/MaterialListPage'));
const MaterialFormPage = lazy(() => import('../pages/materials/MaterialFormPage'));
const PermissionListPage = lazy(() => import('../pages/permissions/PermissionListPage'));
const PermissionFormPage = lazy(() => import('../pages/permissions/PermissionFormPage'));
const RoleListPage = lazy(() => import('../pages/roles/RoleListPage'));
const RoleFormPage = lazy(() => import('../pages/roles/RoleFormPage'));
const UserListPage = lazy(() => import('../pages/users/UserListPage'));
const UserFormPage = lazy(() => import('../pages/users/UserFormPage'));
const BarcodeListPage = lazy(() => import('../pages/barcodes/BarcodeListPage'));
const BarcodeFormPage = lazy(() => import('../pages/barcodes/BarcodeFormPage'));
const BarcodeDetailPage = lazy(() => import('../pages/barcodes/BarcodeDetailPage'));

// Suspense wrapper for lazy-loaded routes
function SuspenseWrapper({ children }) {
  return <Suspense fallback={<Loading />}>{children}</Suspense>;
}

export const router = createBrowserRouter([
  {
    path: '/login',
    element: (
      <SuspenseWrapper>
        <LoginPage />
      </SuspenseWrapper>
    ),
  },
  {
    path: '/',
    element: (
      <ProtectedRoute>
        <MainLayout />
      </ProtectedRoute>
    ),
    children: [
      {
        index: true,
        element: (
          <SuspenseWrapper>
            <DashboardPage />
          </SuspenseWrapper>
        ),
      },
      // Sites
      {
        path: 'sites',
        element: (
          <SuspenseWrapper>
            <SiteListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'sites/create',
        element: (
          <SuspenseWrapper>
            <SiteFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'sites/:id/edit',
        element: (
          <SuspenseWrapper>
            <SiteFormPage />
          </SuspenseWrapper>
        ),
      },
      // Material Types
      {
        path: 'material-types',
        element: (
          <SuspenseWrapper>
            <MaterialTypeListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'material-types/create',
        element: (
          <SuspenseWrapper>
            <MaterialTypeFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'material-types/:id/edit',
        element: (
          <SuspenseWrapper>
            <MaterialTypeFormPage />
          </SuspenseWrapper>
        ),
      },
      // Material Models
      {
        path: 'material-models',
        element: (
          <SuspenseWrapper>
            <MaterialModelListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'material-models/create',
        element: (
          <SuspenseWrapper>
            <MaterialModelFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'material-models/:id/edit',
        element: (
          <SuspenseWrapper>
            <MaterialModelFormPage />
          </SuspenseWrapper>
        ),
      },
      // Materials
      {
        path: 'materials',
        element: (
          <SuspenseWrapper>
            <MaterialListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'materials/create',
        element: (
          <SuspenseWrapper>
            <MaterialFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'materials/:id/edit',
        element: (
          <SuspenseWrapper>
            <MaterialFormPage />
          </SuspenseWrapper>
        ),
      },
      // Permissions
      {
        path: 'permissions',
        element: (
          <SuspenseWrapper>
            <PermissionListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'permissions/create',
        element: (
          <SuspenseWrapper>
            <PermissionFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'permissions/:id/edit',
        element: (
          <SuspenseWrapper>
            <PermissionFormPage />
          </SuspenseWrapper>
        ),
      },
      // Roles
      {
        path: 'roles',
        element: (
          <SuspenseWrapper>
            <RoleListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'roles/create',
        element: (
          <SuspenseWrapper>
            <RoleFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'roles/:id/edit',
        element: (
          <SuspenseWrapper>
            <RoleFormPage />
          </SuspenseWrapper>
        ),
      },
      // Users
      {
        path: 'users',
        element: (
          <SuspenseWrapper>
            <UserListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'users/create',
        element: (
          <SuspenseWrapper>
            <UserFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'users/:id/edit',
        element: (
          <SuspenseWrapper>
            <UserFormPage />
          </SuspenseWrapper>
        ),
      },
      // Barcodes
      {
        path: 'barcodes',
        element: (
          <SuspenseWrapper>
            <BarcodeListPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'barcodes/create',
        element: (
          <SuspenseWrapper>
            <BarcodeFormPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'barcodes/:id',
        element: (
          <SuspenseWrapper>
            <BarcodeDetailPage />
          </SuspenseWrapper>
        ),
      },
      {
        path: 'barcodes/:id/edit',
        element: (
          <SuspenseWrapper>
            <BarcodeFormPage />
          </SuspenseWrapper>
        ),
      },
    ],
  },
  {
    path: '*',
    element: (
      <SuspenseWrapper>
        <NotFoundPage />
      </SuspenseWrapper>
    ),
  },
]);
