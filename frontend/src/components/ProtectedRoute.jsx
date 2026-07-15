import { Navigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { Spinner } from './ui/Spinner';

export function ProtectedRoute({ children, permission }) {
  const { isAuthenticated, loading, user } = useAuth();

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-slate-950">
        <Spinner size="lg" />
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  // Permission check
  if (permission) {
    const hasPermission = user?.roles?.some((role) =>
      role.permissions?.some((p) => p.name === permission)
    );

    if (!hasPermission) {
      return <Navigate to="/" replace />;
    }
  }

  return children;
}
