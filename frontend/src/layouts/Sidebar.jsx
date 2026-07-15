import { NavLink } from 'react-router-dom';
import {
  LayoutDashboard,
  Users,
  Shield,
  Key,
  Building2,
  Package,
  Layers,
  ClipboardList,
  Barcode,
  LogOut,
  X,
  ChevronLeft,
} from 'lucide-react';
import { cn } from '../utils/cn';
import { useAuth } from '../contexts/AuthContext';
import { usePermission } from '../hooks/usePermission';

const menuItems = [
  { label: 'Dashboard', icon: LayoutDashboard, path: '/', permission: null },
  {
    label: 'User Management',
    icon: Users,
    children: [
      { label: 'Users', icon: Users, path: '/users', permission: 'read-user' },
      { label: 'Roles', icon: Shield, path: '/roles', permission: 'read-role' },
      { label: 'Permissions', icon: Key, path: '/permissions', permission: 'read-permission' },
    ],
  },
  {
    label: 'Master Data',
    icon: ClipboardList,
    children: [
      { label: 'Sites', icon: Building2, path: '/sites', permission: 'read-site' },
      { label: 'Material Types', icon: Package, path: '/material-types', permission: 'read-material-type' },
      { label: 'Material Models', icon: Layers, path: '/material-models', permission: 'read-material-model' },
      { label: 'Materials', icon: ClipboardList, path: '/materials', permission: 'read-material' },
    ],
  },
  { label: 'Barcodes', icon: Barcode, path: '/barcodes', permission: 'read-barcode' },
];

export function Sidebar({ open, onClose }) {
  const { user, logout } = useAuth();
  const { hasPermission } = usePermission();

  const handleLogout = async () => {
    await logout();
  };

  const renderNavItem = (item) => {
    if (item.permission && !hasPermission(item.permission)) return null;

    if (item.children) {
      return (
        <div key={item.label} className="mb-1">
          <div className="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
            {item.label}
          </div>
          <div className="ml-2 space-y-0.5">
            {item.children.map((child) => renderNavItem(child))}
          </div>
        </div>
      );
    }

    return (
      <NavLink
        key={item.path}
        to={item.path}
        end={item.path === '/'}
        onClick={onClose}
        className={({ isActive }) =>
          cn(
            'flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition',
            isActive
              ? 'bg-cyan-500/10 text-cyan-400'
              : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
          )
        }
      >
        <item.icon className="h-4 w-4" />
        {item.label}
      </NavLink>
    );
  };

  return (
    <>
      {/* Mobile overlay */}
      {open && (
        <div className="fixed inset-0 z-40 bg-black/60 lg:hidden" onClick={onClose} />
      )}

      {/* Sidebar */}
      <aside
        className={cn(
          'fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-800 bg-slate-900 transition-transform lg:static lg:z-auto lg:translate-x-0',
          open ? 'translate-x-0' : '-translate-x-full'
        )}
      >
        {/* Logo */}
        <div className="flex h-16 items-center justify-between border-b border-slate-800 px-6">
          <div>
            <p className="text-xs uppercase tracking-[0.3em] text-cyan-400">Barcode MS</p>
          </div>
          <button onClick={onClose} className="text-slate-500 hover:text-slate-300 lg:hidden">
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto p-4 space-y-1">
          {menuItems.map((item) => renderNavItem(item))}
        </nav>

        {/* User info + logout */}
        <div className="border-t border-slate-800 p-4">
          <div className="mb-3 flex items-center gap-3">
            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500/20 text-xs font-medium text-cyan-400">
              {user?.full_name?.charAt(0) || user?.username?.charAt(0) || 'U'}
            </div>
            <div className="min-w-0 flex-1">
              <p className="truncate text-sm font-medium text-slate-200">{user?.full_name || user?.username}</p>
              <p className="truncate text-xs text-slate-500">{user?.email}</p>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-400 transition hover:bg-slate-800 hover:text-red-400"
          >
            <LogOut className="h-4 w-4" />
            Logout
          </button>
        </div>
      </aside>
    </>
  );
}
