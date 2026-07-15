import { useMemo } from 'react';
import { useAuth } from '../contexts/AuthContext';

export function usePermission() {
  const { user } = useAuth();

  const permissions = useMemo(() => {
    if (!user) return [];

    const userPermissions = new Set();

    // Collect permissions from roles
    if (user.roles) {
      user.roles.forEach((role) => {
        if (role.permissions) {
          role.permissions.forEach((perm) => {
            userPermissions.add(perm.name);
          });
        }
      });
    }

    // Collect direct permissions if any
    if (user.permissions) {
      user.permissions.forEach((perm) => {
        userPermissions.add(perm.name);
      });
    }

    return Array.from(userPermissions);
  }, [user]);

  const hasPermission = useMemo(
    () => (permissionName) => permissions.includes(permissionName),
    [permissions]
  );

  const hasAnyPermission = useMemo(
    () => (permissionNames) => permissionNames.some((name) => permissions.includes(name)),
    [permissions]
  );

  const hasAllPermissions = useMemo(
    () => (permissionNames) => permissionNames.every((name) => permissions.includes(name)),
    [permissions]
  );

  const isSuperAdmin = useMemo(() => {
    return user?.roles?.some((role) => role.name === 'super_admin') ?? false;
  }, [user]);

  return { permissions, hasPermission, hasAnyPermission, hasAllPermissions, isSuperAdmin };
}
