import { createContext, useContext, useState, useCallback, useEffect } from 'react';
import { authService } from '../services/authService';
import { tokenStorage } from '../auth/storage';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(tokenStorage.getUser());
  const [loading, setLoading] = useState(true);

  const fetchUser = useCallback(async () => {
    const token = tokenStorage.getToken();
    if (!token) {
      setLoading(false);
      return;
    }

    try {
      const response = await authService.me();
      const userData = response.data || response.user || response;
      setUser(userData);
      tokenStorage.setUser(userData);
    } catch {
      tokenStorage.clear();
      setUser(null);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchUser();
  }, [fetchUser]);

  const login = useCallback(async (credentials) => {
    const response = await authService.login(credentials);
    const { token, user: userData } = response.data || response;

    tokenStorage.setToken(token);
    tokenStorage.setUser(userData);
    setUser(userData);

    return userData;
  }, []);

  const logout = useCallback(async () => {
    try {
      await authService.logout();
    } catch {
      // Even if API fails, clear local state
    } finally {
      tokenStorage.clear();
      setUser(null);
    }
  }, []);

  const isAuthenticated = !!user;

  return (
    <AuthContext.Provider value={{ user, login, logout, loading, isAuthenticated, fetchUser }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
