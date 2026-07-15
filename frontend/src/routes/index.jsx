import { createBrowserRouter } from 'react-router-dom';
import { MainLayout } from '../layouts/MainLayout';
import { ProtectedRoute } from '../components/ProtectedRoute';
import { LoginPage } from '../pages/LoginPage';
import { DashboardPage } from '../pages/DashboardPage';
import { NotFoundPage } from '../pages/NotFoundPage';
import { SiteListPage } from '../pages/sites/SiteListPage';
import { SiteFormPage } from '../pages/sites/SiteFormPage';
import { MaterialTypeListPage } from '../pages/material-types/MaterialTypeListPage';
import { MaterialTypeFormPage } from '../pages/material-types/MaterialTypeFormPage';
import { MaterialModelListPage } from '../pages/material-models/MaterialModelListPage';
import { MaterialModelFormPage } from '../pages/material-models/MaterialModelFormPage';
import { MaterialListPage } from '../pages/materials/MaterialListPage';
import { MaterialFormPage } from '../pages/materials/MaterialFormPage';
import { PermissionListPage } from '../pages/permissions/PermissionListPage';
import { PermissionFormPage } from '../pages/permissions/PermissionFormPage';
import { RoleListPage } from '../pages/roles/RoleListPage';
import { RoleFormPage } from '../pages/roles/RoleFormPage';
import { UserListPage } from '../pages/users/UserListPage';
import { UserFormPage } from '../pages/users/UserFormPage';
import { BarcodeListPage } from '../pages/barcodes/BarcodeListPage';
import { BarcodeFormPage } from '../pages/barcodes/BarcodeFormPage';
import { BarcodeDetailPage } from '../pages/barcodes/BarcodeDetailPage';

export const router = createBrowserRouter([
  {
    path: '/login',
    element: <LoginPage />,
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
        element: <DashboardPage />,
      },
      // Sites
      {
        path: 'sites',
        element: <SiteListPage />,
      },
      {
        path: 'sites/create',
        element: <SiteFormPage />,
      },
      {
        path: 'sites/:id/edit',
        element: <SiteFormPage />,
      },
      // Material Types
      {
        path: 'material-types',
        element: <MaterialTypeListPage />,
      },
      {
        path: 'material-types/create',
        element: <MaterialTypeFormPage />,
      },
      {
        path: 'material-types/:id/edit',
        element: <MaterialTypeFormPage />,
      },
      // Material Models
      {
        path: 'material-models',
        element: <MaterialModelListPage />,
      },
      {
        path: 'material-models/create',
        element: <MaterialModelFormPage />,
      },
      {
        path: 'material-models/:id/edit',
        element: <MaterialModelFormPage />,
      },
      // Materials
      {
        path: 'materials',
        element: <MaterialListPage />,
      },
      {
        path: 'materials/create',
        element: <MaterialFormPage />,
      },
      {
        path: 'materials/:id/edit',
        element: <MaterialFormPage />,
      },
      // Permissions
      {
        path: 'permissions',
        element: <PermissionListPage />,
      },
      {
        path: 'permissions/create',
        element: <PermissionFormPage />,
      },
      {
        path: 'permissions/:id/edit',
        element: <PermissionFormPage />,
      },
      // Roles
      {
        path: 'roles',
        element: <RoleListPage />,
      },
      {
        path: 'roles/create',
        element: <RoleFormPage />,
      },
      {
        path: 'roles/:id/edit',
        element: <RoleFormPage />,
      },
      // Users
      {
        path: 'users',
        element: <UserListPage />,
      },
      {
        path: 'users/create',
        element: <UserFormPage />,
      },
      {
        path: 'users/:id/edit',
        element: <UserFormPage />,
      },
      // Barcodes
      {
        path: 'barcodes',
        element: <BarcodeListPage />,
      },
      {
        path: 'barcodes/create',
        element: <BarcodeFormPage />,
      },
      {
        path: 'barcodes/:id',
        element: <BarcodeDetailPage />,
      },
      {
        path: 'barcodes/:id/edit',
        element: <BarcodeFormPage />,
      },
    ],
  },
  {
    path: '*',
    element: <NotFoundPage />,
  },
]);
