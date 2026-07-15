import { Component } from 'react';
import { AlertTriangle, RefreshCw, Home } from 'lucide-react';

/**
 * GlobalError — React Error Boundary component.
 *
 * Catches render errors in the component tree below it.
 * Shows a friendly error screen with:
 *   - Reload button (resets error state)
 *   - Back to Dashboard link
 *   - Error details logged to console
 */
export class GlobalError extends Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null };
  }

  static getDerivedStateFromError(error) {
    return { hasError: true, error };
  }

  componentDidCatch(error, errorInfo) {
    console.error('[GlobalError] Caught render error:', error);
    console.error('[GlobalError] Component stack:', errorInfo?.componentStack);
  }

  handleReload = () => {
    this.setState({ hasError: false, error: null });
  };

  handleBackToDashboard = () => {
    this.setState({ hasError: false, error: null });
    window.location.href = '/';
  };

  render() {
    if (this.state.hasError) {
      return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 text-center">
          <div className="flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10">
            <AlertTriangle className="h-8 w-8 text-red-400" />
          </div>
          <h1 className="mt-4 text-xl font-semibold text-slate-200">Terjadi Kesalahan</h1>
          <p className="mt-2 max-w-md text-sm text-slate-400">
            {this.state.error?.message || 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'}
          </p>

          <div className="mt-6 flex items-center gap-3">
            <button
              onClick={this.handleReload}
              className="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-cyan-600"
            >
              <RefreshCw className="h-4 w-4" />
              Coba Lagi
            </button>
            <button
              onClick={this.handleBackToDashboard}
              className="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
            >
              <Home className="h-4 w-4" />
              Kembali ke Dashboard
            </button>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}
