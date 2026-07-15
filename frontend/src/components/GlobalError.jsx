import { AlertTriangle } from 'lucide-react';

export function GlobalError({ error, resetErrorBoundary }) {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 text-center">
      <div className="flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10">
        <AlertTriangle className="h-8 w-8 text-red-400" />
      </div>
      <h1 className="mt-4 text-xl font-semibold text-slate-200">Terjadi Kesalahan</h1>
      <p className="mt-2 max-w-md text-sm text-slate-400">
        {error?.message || 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'}
      </p>
      {resetErrorBoundary && (
        <button
          onClick={resetErrorBoundary}
          className="mt-6 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-cyan-600"
        >
          Coba Lagi
        </button>
      )}
    </div>
  );
}
